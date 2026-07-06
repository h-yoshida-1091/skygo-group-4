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

        <!-- タブ -->
        <div class="tab-menu">
            <button class="tab-btn active" onclick="showTab('shift')">
                シフト申請
            </button>

            <button class="tab-btn" onclick="showTab('attendance')">
                打刻修正申請
            </button>
        </div>

        <!-- =============================
         シフト申請
    ============================== -->

        <div id="shift" class="tab-content active">

            <table class="table">

                <thead>
                    <tr>
                        <th>申請者名</th>
                        <th>日付</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($shiftRequests as $req)

                    <tr>

                        <td>{{ $req->user->name ?? '未設定ユーザー' }}</td>

                        <td>{{ $req->date }}</td>

                        <td>

                            @if($req->status=="pending")
                            <span class="pending">申請中</span>
                            @elseif($req->status=="approved")
                            <span class="approved">承認</span>
                            @else
                            <span class="rejected">差し戻し</span>
                            @endif

                        </td>

                        <td>

                            <form action="{{ route('admin.shifts.approve',$req->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn approve">
                                    承認
                                </button>
                            </form>

                            <form action="{{ route('admin.shifts.reject',$req->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn reject">
                                    差し戻し
                                </button>
                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="4">
                            シフト申請はありません
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- =============================
         打刻修正申請
    ============================== -->

        <div id="attendance" class="tab-content">

            <table class="table">

                <thead>

                    <tr>
                        <th>申請者名</th>
                        <th>日付</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($attendanceRequests as $req)

                    <tr>

                        <td>{{ $req->user->name ?? '未設定ユーザー' }}</td>

                        <td>{{ $req->date }}</td>

                        <td>

                            @if($req->status=="pending")
                            <span class="pending">申請中</span>
                            @elseif($req->status=="approved")
                            <span class="approved">承認</span>
                            @else
                            <span class="rejected">差し戻し</span>
                            @endif

                        </td>

                        <td>

                            <form action="{{ route('admin.attendance.approve',$req->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn approve">
                                    承認
                                </button>
                            </form>

                            <form action="{{ route('admin.attendance.reject',$req->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn reject">
                                    差し戻し
                                </button>
                            </form>

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

    <script>
        function showTab(tabName) {

            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            document.getElementById(tabName).classList.add('active');

            event.target.classList.add('active');
        }
    </script>

</body>

</html>