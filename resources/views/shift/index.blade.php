<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>シフト申請</title>
    <link rel="stylesheet" href="{{ asset('css/shift.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/小林大地首.png') }}">
</head>

<body>
    @include('layouts.header')
    <br>
    <div class="month-header">
        <a class="month-button" href="{{ route('shift.index', ['year' => $month == 1 ? $year - 1 : $year, 'month' => $month == 1 ? 12 : $month - 1]) }}">＜</a>
        <h1>{{ $year }}年{{ $month }}月</h1>
        <a class="month-button" href="{{ route('shift.index', ['year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1]) }}">＞</a>
    </div>

    <div class="status-box">
        現在の状態：<strong>{{ $shiftStatusText }}</strong>
    </div>

    @if (session('success'))
    <p>{{ session('success') }}</p>
    @endif

    <!-- シフトパターンを選択するポップアップを開くトリガーボタン -->
    <div class="select-card">
        <button style="background-color: green;" id="openPatternModal" class="edit-button">シフトパターンを選択</button>
        <div id="selectedPatternDisplay" style="margin-top: 10px; font-weight: bold;">
            選択中: 未選択
        </div>
    </div>

    <!-- ポップアップ本体（dialogタグ） -->
    <dialog id="patternDialog">
        <h2 style="background-color: green; color: white;">シフトパターンを選択</h2>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <button type="button" class="shift-pattern-btn" data-type="出社" data-start="09:00" data-end="17:30">出社 09:00〜17:30</button>
            <button type="button" class="shift-pattern-btn" data-type="リモート" data-start="09:00" data-end="17:30">リモート 09:00〜17:30</button>
            <button type="button" class="shift-pattern-btn" data-type="出社" data-start="08:00" data-end="17:00">出社 08:00〜17:00</button>
            <button type="button" class="shift-pattern-btn" data-type="リモート" data-start="10:00" data-end="19:00">リモート 10:00〜19:00</button>
        </div>
        <button type="button" id="closePatternModal" class="close-dialog-btn">閉じる</button>
    </dialog>

    <form id="shiftForm" method="POST" action="{{ route('shift.store') }}">
        @csrf

        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="shifts_json" id="shiftsJson">

        @if ($shiftStatusText === '変更申請中')
        <button type="button" id="mainShiftButton" disabled>
            変更申請中
        </button>
        @else
        <button
            class="edit-button"
            type="{{ $shiftStatusText === '未提出' ? 'submit' : 'button' }}"
            id="mainShiftButton"
            data-status="{{ $shiftStatusText }}">
            @if ($shiftStatusText === '未提出')
            シフトを提出
            @else
            編集する
            @endif
        </button>
        @endif
    </form>

    <table>
        <tr>
            <th class="sun">日</th>
            <th>月</th>
            <th>火</th>
            <th>水</th>
            <th>木</th>
            <th>金</th>
            <th>土</th>
        </tr>

        @php
        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $startDay = $firstDay->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endDay = $firstDay->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $currentDay = $startDay->copy();

        $displayShifts = $pendingRequests->isNotEmpty() ? $pendingRequests : $shifts;

        $shiftMap = $displayShifts->keyBy(function ($shift) {
            return $shift->work_date->format('Y-m-d');
        });
        @endphp

        @while ($currentDay <= $endDay)
            <tr>
            @for ($i = 0; $i < 7; $i++)
                @php
                $dateKey=$currentDay->format('Y-m-d');
                $savedShift = $shiftMap->get($dateKey);
                $typeText = $savedShift && $savedShift->remote ? 'リモート' : '出社';
                @endphp

                <td class="calendar-day" data-date="{{ $dateKey }}">
                    @if ($currentDay->month == $month)
                    <strong
                        class="
                        @if($currentDay->dayOfWeek === 0)
                            sunday
                        @elseif($currentDay->dayOfWeek === 6)
                            saturday
                        @endif
                    ">
                        {{ $currentDay->day }}
                    </strong> 

                    <div
                        class="shift-text
                                @if ($savedShift)
                                    has-shift
                                    {{ $savedShift->remote ? 'remote-shift' : 'office-shift' }}
                                @endif
                            "
                        @if ($savedShift)
                        data-type="{{ $typeText }}"
                        data-start="{{ \Carbon\Carbon::parse($savedShift->start_time)->format('H:i') }}"
                        data-end="{{ \Carbon\Carbon::parse($savedShift->end_time)->format('H:i') }}"
                        @endif>
                        @if ($savedShift)
                        {{ $typeText }}<br>
                        {{ \Carbon\Carbon::parse($savedShift->start_time)->format('H:i') }}
                        〜
                        {{ \Carbon\Carbon::parse($savedShift->end_time)->format('H:i') }}
                        @endif
                    </div>
                    @else
                    <span>{{ $currentDay->day }}</span>
                    @endif
                </td>

                @php
                $currentDay->addDay();
                @endphp
                @endfor
                </tr>
                @endwhile
    </table>

    <hr>

    <script>
        // 状態管理変数の初期化
        const mainShiftButton = document.getElementById('mainShiftButton');
        let isEditMode = (mainShiftButton && mainShiftButton.dataset.status === '未提出') ? true : false;
        let selectedPattern = null; 

        // モーダル関連の要素取得
        const patternDialog = document.getElementById('patternDialog');
        const openPatternModal = document.getElementById('openPatternModal');
        const closePatternModal = document.getElementById('closePatternModal');
        const selectedPatternDisplay = document.getElementById('selectedPatternDisplay');

        // モーダルを開く・閉じる処理
        if (openPatternModal) {
            openPatternModal.addEventListener('click', () => patternDialog.showModal());
        }
        if (closePatternModal) {
            closePatternModal.addEventListener('click', () => patternDialog.close());
        }

        // シフトパターンの選択処理
        document.querySelectorAll('.shift-pattern-btn').forEach(button => {
            button.addEventListener('click', function() {
                selectedPattern = {
                    type: this.dataset.type,
                    start: this.dataset.start,
                    end: this.dataset.end
                };

                // アクティブクラスの切り替え
                document.querySelectorAll('.shift-pattern-btn').forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');

                // 選択中のパターンを画面に表示
                selectedPatternDisplay.textContent = `選択中: ${selectedPattern.type} ${selectedPattern.start}〜${selectedPattern.end}`;

                // パターンを選んだら自動でポップアップを閉じる
                patternDialog.close();
            });
        });

        // カレンダー押下時の反映ロジック
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.addEventListener('click', function() {
                if (!isEditMode) {
                    return;
                }

                const shiftText = this.querySelector('.shift-text');
                if (!shiftText) {
                    return;
                }

                // すでにシフトが入っている場合は削除処理
                if (shiftText.innerHTML.trim() !== '') {
                    shiftText.innerHTML = '';
                    shiftText.removeAttribute('data-type');
                    shiftText.removeAttribute('data-start');
                    shiftText.removeAttribute('data-end');
                    shiftText.classList.remove('has-shift', 'office-shift', 'remote-shift');
                    return;
                }

                // ポップアップで選択されたパターンのチェック
                if (!selectedPattern) {
                    alert('シフトパターンを選択してください');
                    return;
                }

                // 選択されたパターンを反映
                shiftText.dataset.type = selectedPattern.type;
                shiftText.dataset.start = selectedPattern.start;
                shiftText.dataset.end = selectedPattern.end;

                shiftText.classList.add('has-shift');

                if (selectedPattern.type === 'リモート') {
                    shiftText.classList.add('remote-shift');
                    shiftText.classList.remove('office-shift');
                } else {
                    shiftText.classList.add('office-shift');
                    shiftText.classList.remove('remote-shift');
                }

                shiftText.innerHTML = `
                    ${selectedPattern.type}<br>
                    ${selectedPattern.start}〜${selectedPattern.end}
                `;
            });
        });

        // 編集ボタン・提出ボタンの切り替え処理
        if (mainShiftButton && !mainShiftButton.disabled) {
            mainShiftButton.addEventListener('click', function(event) {
                if (!isEditMode && this.dataset.status !== '未提出') {
                    event.preventDefault();

                    isEditMode = true;
                    this.type = 'submit';
                    this.classList.add('editing');

                    if (this.dataset.status === '申請中') {
                        this.textContent = '申請内容を修正';
                    } else if (this.dataset.status === '提出済み') {
                        this.textContent = 'シフト変更を申請';
                    }
                }
            });
        }

        // フォーム送信時の処理
        document.getElementById('shiftForm').addEventListener('submit', function(event) {
            const shifts = [];
            let hasWeekendShift = false;

            document.querySelectorAll('.calendar-day').forEach(day => {
                const shiftText = day.querySelector('.shift-text');

                if (!shiftText || shiftText.innerHTML.trim() === '') {
                    return;
                }

                const date = new Date(day.dataset.date);
                const dayOfWeek = date.getDay();

                if (dayOfWeek === 0 || dayOfWeek === 6) {
                    hasWeekendShift = true;
                }

                shifts.push({
                    work_date: day.dataset.date,
                    remote: shiftText.dataset.type === 'リモート' ? 1 : 0,
                    start_time: shiftText.dataset.start,
                    end_time: shiftText.dataset.end
                });
            });

            if (shifts.length === 0) {
                event.preventDefault();
                alert('シフトが選択されていません。');
                return;
            }

            if (hasWeekendShift) {
                const weekendConfirm = confirm('土曜日または日曜日にシフトが入っています。このまま申請してもよろしいですか？');

                if (!weekendConfirm) {
                    event.preventDefault();
                    return;
                }
            }

            const submitConfirm = confirm('シフトを申請します。よろしいですか？');

            if (!submitConfirm) {
                event.preventDefault();
                return;
            }

            document.getElementById('shiftsJson').value = JSON.stringify(shifts);
        });
    </script>
</body>
</html>