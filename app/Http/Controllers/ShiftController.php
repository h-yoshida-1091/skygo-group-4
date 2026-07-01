<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>シフト申請</title>
    @vite(['resources/css/shift.css'])
</head>
<body>

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

<table>
    <tr>
        <th>日</th>
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

        $shiftMap = $shiftRequests->keyBy(function ($shift) {
            return $shift->work_date->format('Y-m-d');
        });
    @endphp

    @while ($currentDay <= $endDay)
        <tr>
            @for ($i = 0; $i < 7; $i++)
                @php
                    $dateKey = $currentDay->format('Y-m-d');
                    $savedShift = $shiftMap->get($dateKey);
                    $typeText = $savedShift && $savedShift->remote ? 'リモート' : '出社';
                @endphp

                <td class="calendar-day" data-date="{{ $dateKey }}">
                    @if ($currentDay->month == $month)
                        <strong>{{ $currentDay->day }}</strong>

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
                            @endif
                        >
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

<div class="select-card">
    <h2>シフトを選択</h2>
    <button type="button" class="work-type-btn" data-type="出社">出社</button>
    <button type="button" class="work-type-btn" data-type="リモート">リモート</button>
</div>

<div class="select-card">
    <h2>時間を選択</h2>
    <button type="button" class="time-btn" data-start="09:00" data-end="17:30">09:00〜17:30</button>
    <button type="button" class="time-btn" data-start="08:00" data-end="17:00">08:00〜17:00</button>
    <button type="button" class="time-btn" data-start="10:00" data-end="19:00">10:00〜19:00</button>
</div>

<form id="shiftForm" method="POST" action="{{ route('shift.store') }}">
    @csrf

    <input type="hidden" name="year" value="{{ $year }}">
    <input type="hidden" name="month" value="{{ $month }}">
    <input type="hidden" name="shifts_json" id="shiftsJson">

    <button
        type="{{ $shiftStatusText === '未提出' ? 'submit' : 'button' }}"
        id="mainShiftButton"
        data-status="{{ $shiftStatusText }}"
    >
        {{ $shiftStatusText === '未提出' ? 'シフトを提出' : '編集する' }}
    </button>
</form>

<script>
    let selectedType = null;
    let selectedStart = null;
    let selectedEnd = null;
    let isEditMode = "{{ $shiftStatusText }}" === "未提出";

    document.querySelectorAll('.work-type-btn').forEach(button => {
        button.addEventListener('click', function () {
            selectedType = this.dataset.type;

            document.querySelectorAll('.work-type-btn').forEach(btn => {
                btn.classList.remove('selected');
            });

            this.classList.add('selected');
        });
    });

    document.querySelectorAll('.time-btn').forEach(button => {
        button.addEventListener('click', function () {
            selectedStart = this.dataset.start;
            selectedEnd = this.dataset.end;

            document.querySelectorAll('.time-btn').forEach(btn => {
                btn.classList.remove('selected');
            });

            this.classList.add('selected');
        });
    });

    document.querySelectorAll('.calendar-day').forEach(day => {
        day.addEventListener('click', function () {
            if (!isEditMode) {
                return;
            }

            const shiftText = this.querySelector('.shift-text');

            if (!shiftText) {
                return;
            }

            if (shiftText.innerHTML.trim() !== '') {
                shiftText.innerHTML = '';
                shiftText.removeAttribute('data-type');
                shiftText.removeAttribute('data-start');
                shiftText.removeAttribute('data-end');
                shiftText.classList.remove('has-shift', 'office-shift', 'remote-shift');
                return;
            }

            if (!selectedType || !selectedStart || !selectedEnd) {
                alert('出社・リモートと時間を選択してください');
                return;
            }

            shiftText.dataset.type = selectedType;
            shiftText.dataset.start = selectedStart;
            shiftText.dataset.end = selectedEnd;

            shiftText.classList.add('has-shift');

            if (selectedType === 'リモート') {
                shiftText.classList.add('remote-shift');
                shiftText.classList.remove('office-shift');
            } else {
                shiftText.classList.add('office-shift');
                shiftText.classList.remove('remote-shift');
            }

            shiftText.innerHTML = `
                ${selectedType}<br>
                ${selectedStart}〜${selectedEnd}
            `;
        });
    });

    const mainShiftButton = document.getElementById('mainShiftButton');

    mainShiftButton.addEventListener('click', function () {
        if (this.dataset.status !== '未提出' && !isEditMode) {
            isEditMode = true;
            this.type = 'submit';
            this.textContent = 'シフトを変更';
            alert('編集できるようになりました。変更したい日付を選択してください。');
        }
    });

    document.getElementById('shiftForm').addEventListener('submit', function (event) {
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
            const weekendConfirm = confirm('土曜日または日曜日にシフトが入っています。このまま提出してもよろしいですか？');

            if (!weekendConfirm) {
                event.preventDefault();
                return;
            }
        }

        const submitConfirm = confirm('シフトを保存します。よろしいですか？');

        if (!submitConfirm) {
            event.preventDefault();
            return;
        }

        document.getElementById('shiftsJson').value = JSON.stringify(shifts);
    });
</script>

</body>
</html>