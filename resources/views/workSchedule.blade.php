<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>勤務表</title>
    <link rel="stylesheet" href="{{ asset('css/workSchedule.css') }}">
</head>

<body>

@include('layouts.header')

<main class="work-page">

    <section class="summary-area">
        @foreach([
            ['合計勤務時間', $summary['total_work']],
            ['規定労働時間', $summary['prescribed_work']],
            ['勤務日数', $summary['work_days']],
            ['休憩時間', $summary['total_break']],
            ['遅刻回数', $summary['late_early']],
            ['欠勤日数', $summary['absent_days']]
        ] as $item)
            <div class="summary-card">
                <div class="summary-label">{{ $item[0] }}</div>
                <div class="summary-value">{{ $item[1] }}</div>
            </div>
        @endforeach
    </section>

    <section class="month-nav">
        <a href="?tab={{ $tab }}&month={{ $prevMonth }}" class="month-btn">
            ◀ 先月
        </a>

        <h1>{{ \Carbon\Carbon::parse($currentMonth)->format('Y年n月') }}</h1>

        <a href="?tab={{ $tab }}&month={{ $nextMonth }}" class="month-btn">
            次月 ▶
        </a>
    </section>

    <section class="table-card">
        <div class="table-scroll">
            <table class="work-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>曜日</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>遅刻</th>
                        <th>勤務時間</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($workSchedule as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['day_of_week'] }}</td>
                            <td>{{ $row['clock_in'] }}</td>
                            <td>{{ $row['clock_out'] }}</td>
                            <td>{{ $row['break_time'] }}</td>
                            <td>{{ $row['late_count'] }}</td>
                            <td class="total-work">{{ $row['total_work_time'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

</main>

</body>
</html>