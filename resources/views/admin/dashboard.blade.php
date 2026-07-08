<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>管理者ダッシュボード</title>

    <link rel="icon" type="image/png" href="{{ asset('images/小林大地首.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admindashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>

    @include('Layouts.admin-header')

    <div class="container">

        <h1 class="page-title">申請管理</h1>

        @if(session('success'))
        <p style="color: green; font-weight: bold;">
            {{ session('success') }}
        </p>
        @endif

        <div class="tab-menu">
            <button class="tab-btn active" onclick="showTab('shift', event)">
                シフト申請
            </button>

            <button class="tab-btn" onclick="showTab('attendance', event)">
                打刻修正申請
            </button>
        </div>

        <div id="shift" class="tab-content active">

            <table class="table">
                <thead>
                    <tr>
                        <th>申請者名</th>
                        <th>対象月</th>
                        <th>提出日</th>
                        <th>申請件数</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($monthlyShiftRequests as $key => $requests)
                    @php
                    $first = $requests->first();
                    $latest = $requests->sortByDesc('submitted_at')->first();
                    $user = $first->user;
                    $year = \Carbon\Carbon::parse($first->work_date)->year;
                    $month = \Carbon\Carbon::parse($first->work_date)->month;
                    $submittedAt = $latest->submitted_at ?? $latest->created_at;
                    @endphp

                    <tr>
                        <td>{{ $user->name ?? '未設定ユーザー' }}</td>
                        <td>{{ $year }}年{{ $month }}月分</td>
                        <td>{{ \Carbon\Carbon::parse($submittedAt)->format('Y-m-d H:i') }}</td>
                        <td>{{ $requests->count() }}件</td>
                        <td><span class="pending">申請中</span></td>

                        <td>
                            <button
                                type="button"
                                class="btn approve"
                                onclick="openCalendarModal('{{ $key }}')">
                                詳細を見る
                            </button>

                            <form action="{{ route('admin.shifts.month.approve') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $first->user_id }}">
                                <input type="hidden" name="year" value="{{ $year }}">
                                <input type="hidden" name="month" value="{{ $month }}">

                                <button
                                    class="btn approve"
                                    onclick="event.stopPropagation();">
                                    承認
                                </button>
                            </form>

                            <button
                                type="button"
                                class="btn reject"
                                onclick="openMonthRejectModal('{{ $first->user_id }}', '{{ $year }}', '{{ $month }}')">
                                差し戻し
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">シフト申請はありません</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        @foreach($monthlyShiftRequests as $key => $requests)
        @php
        $first = $requests->first();
        $year = \Carbon\Carbon::parse($first->work_date)->year;
        $month = \Carbon\Carbon::parse($first->work_date)->month;

        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $startDay = $firstDay->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endDay = $firstDay->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);
        $currentDay = $startDay->copy();

        $shiftMap = $requests->keyBy(function ($req) {
        return \Carbon\Carbon::parse($req->work_date)->format('Y-m-d');
        });
        @endphp

        <div id="calendarModal-{{ $key }}" class="calendar-modal">
            <div class="calendar-modal-box">

                <h2>
                    {{ $first->user->name ?? '未設定ユーザー' }}さん
                    {{ $year }}年{{ $month }}月分シフト申請
                </h2>

                <table class="calendar">
                    <thead>
                        <tr class="calendar-header">
                            <th>日</th>
                            <th>月</th>
                            <th>火</th>
                            <th>水</th>
                            <th>木</th>
                            <th>金</th>
                            <th>土</th>
                        </tr>
                    </thead>

                    <tbody>
                        @while($currentDay <= $endDay)
                            <tr>
                            @for($i = 0; $i < 7; $i++)
                                @php
                                $dateKey=$currentDay->format('Y-m-d');
                                $req = $shiftMap->get($dateKey);
                                @endphp

                                <td>
                                    @if($currentDay->month == $month)
                                    <strong>{{ $currentDay->day }}</strong>

                                    @if($req)
                                    @php
                                    $submittedAt = $req->submitted_at ?? $req->created_at;
                                    @endphp

                                    <div class="shift-box">
                                        <div>{{ $req->remote ? 'リモート' : '出社' }}</div>

                                        <div>
                                            {{ \Carbon\Carbon::parse($req->start_time)->format('H:i') }}
                                            〜
                                            {{ \Carbon\Carbon::parse($req->end_time)->format('H:i') }}
                                        </div>

                                        <div>
                                            {{ $req->request_type === 'change' ? '変更申請' : '申請' }}
                                        </div>

                                        <div>
                                            提出日：
                                            {{ \Carbon\Carbon::parse($submittedAt)->format('m/d H:i') }}
                                        </div>
                                    </div>
                                    @else
                                    <div class="no-shift">申請なし</div>
                                    @endif
                                    @endif
                                </td>

                                @php
                                $currentDay->addDay();
                                @endphp
                                @endfor
                                </tr>
                                @endwhile
                    </tbody>
                </table>

                <div class="modal-actions">
                    <form action="{{ route('admin.shifts.month.approve') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $first->user_id }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">

                        <button class="btn approve">
                            この月を承認
                        </button>
                    </form>

                    <button
                        type="button"
                        class="btn reject"
                        onclick="openMonthRejectModal('{{ $first->user_id }}', '{{ $year }}', '{{ $month }}')">
                        この月を差し戻し
                    </button>

                    <button type="button" class="btn-close" onclick="closeCalendarModal('{{ $key }}')">
                        閉じる
                    </button>
                </div>

            </div>
        </div>
        @endforeach

        <div id="attendance" class="tab-content">

            <table class="table">

                <thead>
                    <tr>
                        <th>申請者名</th>
                        <th>申請日付</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($attendanceRequests as $req)

                    <tr
                        onclick="openAttendanceModal(this)"
                        data-name="{{ $req->user->name ?? '未設定ユーザー' }}"
                        data-date="{{ \Carbon\Carbon::parse($req->work_date)->format('Y-m-d') }}"
                        data-before-in="{{ optional($req->attendance)->clock_in }}"
                        data-before-out="{{ optional($req->attendance)->clock_out }}"
                        data-after-in="{{ $req->requested_clock_in }}"
                        data-after-out="{{ $req->requested_clock_out }}"
                        data-reason="{{ $req->reason }}"
                        data-id="{{ $req->id }}"
                        data-status="{{ $req->status }}">

                        <td>
                            {{ $req->user->name ?? '未設定ユーザー' }}
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($req->work_date)->format('Y-m-d') }}
                        </td>

                        <td>

                            @if($req->status === 'pending')

                            <span class="pending">
                                申請中
                            </span>

                            @elseif($req->status === 'approved')

                            <span class="approved">
                                承認
                            </span>

                            @else

                            <span class="rejected">
                                差し戻し
                            </span>

                            @endif

                        </td>

                        <td>

                            @if($req->status === 'pending')

                            <form
                                action="{{ route('admin.attendance.approve', $req->id) }}"
                                method="POST"
                                style="display:inline;"
                                onclick="event.stopPropagation();">

                                @csrf

                                <button class="btn approve">
                                    承認
                                </button>

                            </form>

                            <form
                                action="{{ route('admin.attendance.reject', $req->id) }}"
                                method="POST"
                                style="display:inline;"
                                onclick="event.stopPropagation();">

                                @csrf

                                <button class="btn reject">
                                    差し戻し
                                </button>

                            </form>

                            @elseif($req->status === 'approved')


                            <span style="color:green;font-weight:bold;">
                                承認済み
                            </span>


                            @elseif($req->status === 'rejected')


                            <span style="color:red;font-weight:bold;">
                                差し戻し済み
                            </span>


                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="4">
                            打刻修正申請はありません
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div id="monthRejectModal" class="reject-modal" style="display:none;">
        <div class="reject-modal-box">

            <form action="{{ route('admin.shifts.month.reject') }}" method="POST">
                @csrf

                <input type="hidden" name="user_id" id="monthRejectUserId">
                <input type="hidden" name="year" id="monthRejectYear">
                <input type="hidden" name="month" id="monthRejectMonth">

                <textarea name="comment" placeholder="差し戻しコメント" required></textarea>

                <div class="reject-modal-buttons">
                    <button type="submit" class="btn-submit">送信</button>
                    <button type="button" class="btn-close" onclick="closeMonthRejectModal()">閉じる</button>
                </div>
            </form>

        </div>
    </div>

    <!-- 打刻修正詳細モーダル -->
    <div id="attendanceModal" class="attendance-modal" onclick="closeAttendanceModal()">

        <div class="attendance-modal-box" onclick="event.stopPropagation();">

            <h2>打刻修正申請詳細</h2>

            <div class="attendance-detail">

                <p>
                    申請者：
                    <span id="attendanceName"></span>
                </p>

                <p>
                    日付：
                    <span id="attendanceDate"></span>
                </p>

                <hr>

                <h3>修正前</h3>

                <p>
                    出勤：
                    <span id="beforeClockIn"></span>
                </p>

                <p>
                    退勤：
                    <span id="beforeClockOut"></span>
                </p>


                <h3>修正後</h3>

                <p>
                    出勤：
                    <span id="afterClockIn"></span>
                </p>

                <p>
                    退勤：
                    <span id="afterClockOut"></span>
                </p>


                <p>
                    修正理由：
                    <span id="attendanceReason"></span>
                </p>

            </div>


            <button
                type="button"
                class="btn-close"
                onclick="closeAttendanceModal()">
                閉じる
            </button>

        </div>

    </div>

    <script>
        function openAttendanceModal(row) {

            document.getElementById("attendanceName").textContent = row.dataset.name;
            document.getElementById("attendanceDate").textContent = row.dataset.date;
            document.getElementById("beforeClockIn").textContent = row.dataset.beforeIn;
            document.getElementById("beforeClockOut").textContent = row.dataset.beforeOut;
            document.getElementById("afterClockIn").textContent = row.dataset.afterIn;
            document.getElementById("afterClockOut").textContent = row.dataset.afterOut;
            document.getElementById("attendanceReason").textContent = row.dataset.reason;

            document.getElementById("attendanceModal").style.display = "block";

            document.body.style.overflow = "hidden";
        }

        function closeAttendanceModal() {

            document.getElementById("attendanceModal").style.display = "none";

            document.body.style.overflow = "";

        }

        function showTab(tabName, event) {
            // タブ内容を全部非表示
            document.querySelectorAll('.tab-content').forEach(function(tab) {
                tab.classList.remove('active');
            });

            // ボタンのactive解除
            document.querySelectorAll('.tab-btn').forEach(function(btn) {
                btn.classList.remove('active');
            });

            // 選択したタブを表示
            document.getElementById(tabName).classList.add('active');

            // 押したボタンをactive
            event.currentTarget.classList.add('active');
        }
    </script>

</body>

</html>