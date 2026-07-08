<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>勤怠管理アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/小林大地首.png') }}">
</head>

<body>
    @include('layouts.header')

    <div class="dashboard-container">

        <section class="character-panel
        @if($character)
            @if($character->character_type === 'トカゲ')
                lizard-panel
            @elseif($character->character_type === 'かめ')
                turtle-panel
            @elseif($character->character_type === 'カエル')
                frog-panel
            @endif
        @endif
    ">

            @if($character)
            <img class="character-image" src="{{ asset('images/characters/' . $character->image) }}" alt="相棒">

            <h2>{{ $character->nickname ?? $character->character_type }}</h2>
            <p class="character-title">称号：{{ $character->title }}</p>
            <p>Lv.{{ $character->level }}</p>

            @php
            $nextExp = $character->level * 100;
            $expPercent = min(100, ($character->exp / $nextExp) * 100);
            @endphp

            <div class="exp-area">
                <div class="exp-text">EXP {{ $character->exp }} / {{ $nextExp }}</div>
                <div class="exp-bar">
                    <div class="exp-fill" style="--exp-width: {{ $expPercent }}%;"></div>
                </div>
            </div>
            @else
            <h2>相棒がいません</h2>
            <p>メニューの「相棒」から<br>キャラクターを選択してください。</p>
            <a class="character-link" href="{{ route('character.index') }}">相棒を選ぶ</a>
            @endif
        </section>

        <section class="attendance-panel">

            <div class="time-box">
                <h2>現在時刻</h2>
                <div id="currentTime" class="current-time">--:--:--</div>
            </div>

            @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="message error">{{ session('error') }}</div>
            @endif

            <div class="work-main-area">

                <div class="button-column">
                    <form action="/attendances/clock-in" method="POST" onsubmit="return confirmClockIn();">
                        @csrf
                        <button type="submit" class="work-btn clock-in">出勤</button>
                    </form>

                    <form action="/attendances/clock-out" method="POST" onsubmit="return confirmClockOut();">
                        @csrf
                        <button type="submit" class="work-btn clock-out">退勤</button>
                    </form>
                </div>

                <div class="history-box">
                    <h2 class="history-title">勤怠履歴</h2>

                    <div class="history-scroll">
                        @forelse($attendances as $attendance)
                        <div class="history-day">
                            <div class="history-date">
                                {{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}
                            </div>

                            @if($attendance->clock_in)
                            <div class="history-row"
                                data-id="{{ $attendance->id }}"
                                data-date="{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y-m-d') }}"
                                data-clock-in="{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}"
                                data-clock-out="{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}"
                                onclick="openModal(this)">
                                <span>出勤</span>
                                <span>{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}</span>
                            </div>
                            @endif

                            @if($attendance->clock_out)
                            <div class="history-row"
                                data-id="{{ $attendance->id }}"
                                data-date="{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y-m-d') }}"
                                data-clock-in="{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}"
                                data-clock-out="{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}"
                                onclick="openModal(this)">
                                <span>退勤</span>
                                <span>{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}</span>
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="no-history">勤怠履歴はまだありません。</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </section>
    </div>

    <div class="modal-overlay" id="editModal" onclick="closeModal(event)">
        <div class="modal-body" onclick="event.stopPropagation()">
            <div class="modal-date" id="modalDate"></div>

            <form id="editForm" action="" method="POST">
                @csrf

                <label>希望出勤時間</label>
                <input type="time" name="requested_clock_in" required>

                <label>希望退勤時間</label>
                <input type="time" name="requested_clock_out">

                <textarea class="modal-textbox" name="reason" placeholder="修正理由を入力してください" required></textarea>

                <button type="submit" class="modal-submit-btn">申請する</button>
            </form>
        </div>
    </div>

    <script>
    function updateTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent =
            now.toLocaleTimeString('ja-JP');
    }

    updateTime();
    setInterval(updateTime, 1000);

    function openModal(element) {
        const id = element.getAttribute('data-id');
        const dateStr = element.getAttribute('data-date');

        const clockInVal = element.getAttribute('data-clock-in');
        const clockOutVal = element.getAttribute('data-clock-out');

        const dateParts = dateStr.split('-');
        const formattedDate = `${dateParts[0]}年${dateParts[1]}月${dateParts[2]}日`;

        document.getElementById('modalDate').innerText = formattedDate;
        document.querySelector('input[name="requested_clock_in"]').value = clockInVal || '';
        document.querySelector('input[name="requested_clock_out"]').value = clockOutVal || '';
        document.getElementById('editForm').action = `/attendances/${id}/request`;
        document.getElementById('editModal').classList.add('is-open');
    }

    function closeModal(event) {
        document.getElementById('editModal').classList.remove('is-open');
    }

    const scheduledStartTime = "{{ $todayShift ? \Carbon\Carbon::parse($todayShift->start_time)->format('H:i') : '' }}";
    const scheduledEndTime = "{{ $todayShift ? \Carbon\Carbon::parse($todayShift->end_time)->format('H:i') : '' }}";

    function getNowTimeText() {
        const now = new Date();

        return String(now.getHours()).padStart(2, '0') + ':' +
               String(now.getMinutes()).padStart(2, '0');
    }

    function confirmClockIn() {
        if (!scheduledStartTime) {
            return true;
        }

        const nowTime = getNowTimeText();

        if (nowTime > scheduledStartTime) {
            return confirm(
                `現在時刻は ${nowTime} です。\n` +
                `シフト開始時間 ${scheduledStartTime} を過ぎています。\n` +
                `遅刻になりますが、出勤しますか？`
            );
        }

        return true;
    }

    function confirmClockOut() {
        if (!scheduledEndTime) {
            return true;
        }

        const nowTime = getNowTimeText();

        if (nowTime < scheduledEndTime) {
            return confirm(
                `現在時刻は ${nowTime} です。\n` +
                `シフト終了時間 ${scheduledEndTime} より前です。\n` +
                `早退になりますが、退勤しますか？`
            );
        }

        return true;
    }

    const sound = "{{ session('sound') }}";

    if (sound === "clock-in") {
        new Audio("{{ asset('audio/characters/小林大地出勤.mp3') }}").play();
    } else if (sound === "clock-out") {
        new Audio("{{ asset('audio/characters/小林大地退勤.mp3') }}").play();
    }
</script>
</body>

</html>