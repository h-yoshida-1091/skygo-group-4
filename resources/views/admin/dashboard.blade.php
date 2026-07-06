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

        <!--シフト申請-->

        <div id="shift" class="tab-content active">

            <table class="table">
                <thead>
                    <tr>
                        <th>申請者名</th>
                        <th>申請日付</th>
                        <th>シフト日付</th>
                        <th>出社形態</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($shiftRequests as $req)
                    <tr>

                        <!-- 申請者名 -->
                        <td>{{ $req->user->name ?? '未設定ユーザー' }}</td>

                        <!-- 申請日付 -->
                        <td>
                            {{ optional($req->submitted_at)->format('Y-m-d') 
                    ?? $req->created_at->format('Y-m-d') }}
                        </td>

                        <!-- シフト日付 -->
                        <td>{{ \Carbon\Carbon::parse($req->work_date)->format('Y-m-d') }}</td>

                        <!-- 出社形態 -->
                        <td>
                            @if($req->remote)
                            リモート
                            @else
                            出社
                            @endif
                        </td>

                        <!-- ステータス -->
                        <td>
                            @if($req->status === 'pending')
                            <span class="pending">申請中</span>
                            @elseif($req->status === 'approved')
                            <span class="approved">承認</span>
                            @else
                            <span class="rejected">差し戻し</span>
                            @endif
                        </td>

                        <!-- 操作 -->
                        <td>
                            @if($req->status === 'pending')

                            <!-- 承認 -->
                            <form action="{{ route('admin.shifts.approve', $req->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn approve">承認</button>
                            </form>

                            <!-- 差し戻し -->
                            <button type="button" class="btn reject" onclick="openRejectModal('{{ $req->id }}')">
                                差し戻し
                            </button>

                            @elseif($req->status === 'approved')

                            <span style="color:green; font-weight:bold;">承認済み</span>

                            @elseif($req->status === 'rejected')

                            <span style="color:red; font-weight:bold;">差し戻し済み</span>

                            @endif
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


        <!--打刻修正申請-->

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

                            <form action="{{ route('admin.shift.approve', $req->id) }}" method="POST">
                                @csrf
                                <button class="btn approve">承認</button>
                            </form>

                            <form action="{{ route('admin.shift.reject', $req->id) }}" method="POST">
                                @csrf
                                <button class="btn reject">差し戻し</button>
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

    <!-- 差し戻しモーダル -->
    <div id="rejectModal" class="reject-modal">

        <div class="reject-modal-box">

            <form id="rejectForm" method="POST">
                @csrf

                <textarea name="comment" placeholder="コメント" required></textarea>

                <div class="reject-modal-buttons">
                    <button type="submit" class="btn-submit">送信</button>
                    <button type="button" class="btn-close" onclick="closeRejectModal()">閉じる</button>
                </div>

            </form>

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

        let rejectId = null;

        function openRejectModal(id) {
            rejectId = id;
            document.getElementById('rejectModal').style.display = 'block';

            const form = document.getElementById('rejectForm');
            form.action = `/admin/shifts/${id}/reject`;
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
    </script>

</body>

</html>