<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー管理</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>

    @include('Layouts.admin-header')

    <div class="container">

        <h1 class="page-title">ユーザー管理</h1>

        <table class="admin-table">

            <thead>
                <tr>
                    <th>名前</th>
                    <th>メール</th>
                    <th>権限</th>
                    <th>登録日</th>
                    <th>操作</th>
                </tr>
            </thead>

            <tbody>

                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role ?? 'user' }}</td>
                    <td>{{ $user->created_at }}</td>

                    <td class="actions">

                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn edit">
                            編集
                        </a>

                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn reject"
                                onclick="return confirm('削除しますか？')">
                                削除
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach

            </tbody>

        </table>
        {{ $users->links() }}
    </div>

</body>

</html>