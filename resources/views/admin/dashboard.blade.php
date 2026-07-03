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

        <h1 class="page-title">申請一覧（管理者）</h1>

        <table class="table">

            <thead>
                <tr>
                    <th>ユーザー名</th>
                    <th>日付</th>
                    <th>開始時間</th>
                    <th>終了時間</th>
                    <th>ステータス</th>
                    <th>操作</th>
                </tr>
            </thead>

            <tbody>

                @if(isset($requests) && count($requests) > 0)

                @foreach($requests as $req)
                <tr class="
                    {{ $req->status == 'pending' ? 'pending-row' : '' }}
                    {{ $req->status == 'approved' ? 'approved-row' : '' }}
                    {{ $req->status == 'rejected' ? 'rejected-row' : '' }}
                    ">
                    <td>{{ $req->user->name ?? '未設定ユーザー' }}</td>
                    <td>{{ $req->date ?? '-' }}</td>
                    <td>{{ $req->start_time ?? '-' }}</td>
                    <td>{{ $req->end_time ?? '-' }}</td>

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

                        <form action="{{ route('admin.shifts.approve', $req->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn approve">承認</button>
                        </form>

                        <form action="{{ route('admin.shifts.reject', $req->id) }}" method="POST">
                            @csrf
                            <input type="text" name="comment" placeholder="コメント">
                            <button type="submit" class="btn reject">差し戻し</button>
                        </form>

                    </td>
                </tr>
                @endforeach

                @else
                <tr>
                    <td colspan="6">申請データがありません</td>
                </tr>
                @endif

            </tbody>

        </table>

    </div>

</body>

</html>