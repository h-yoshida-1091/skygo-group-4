<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>管理者ダッシュボード</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>

@include('layouts.admin_header')

<div class="container">

    <h1 class="title">シフト申請一覧</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ユーザー</th>
                <th>日付</th>
                <th>開始</th>
                <th>終了</th>
                <th>ステータス</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $req->user->name }}</td>
                <td>{{ $req->date }}</td>
                <td>{{ $req->start_time }}</td>
                <td>{{ $req->end_time }}</td>

                <td>
                    @if($req->status == 'pending')
                        <span class="pending">申請中</span>
                    @elseif($req->status == 'approved')
                        <span class="approved">承認</span>
                    @else
                        <span class="rejected">差し戻し</span>
                    @endif
                </td>

                <td class="actions">

                    <!-- 承認 -->
                    <form action="{{ route('admin.shifts.approve', $req->id) }}" method="POST">
                        @csrf
                        <button class="btn approve">承認</button>
                    </form>

                    <!-- 差し戻し -->
                    <form action="{{ route('admin.shifts.reject', $req->id) }}" method="POST">
                        @csrf
                        <input type="text" name="comment" placeholder="コメント">
                        <button class="btn reject">差し戻し</button>
                    </form>

                </td>
            </tr>
        </tbody>
    </table>

</div>

</body>
</html>