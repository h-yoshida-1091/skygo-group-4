<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー管理</title>

    <link rel="icon" type="image/png" href="{{ asset('images/小林大地首.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>

<body>

    @include('Layouts.admin-header')

    <div class="user-layout">

        <div class="user-panel">

            <h2>ユーザー一覧</h2>

            <form method="GET" action="{{ route('admin.users.index') }}" class="search-form">

                <input
                    type="text"
                    name="keyword"
                    placeholder="ユーザー検索"
                    value="{{ request('keyword') }}">

                <select name="role">
                    <option value="">すべて</option>
                    <option value="user"
                        {{ request('role')=='user' ? 'selected' : '' }}>
                        一般
                    </option>

                    <option value="admin"
                        {{ request('role')=='admin' ? 'selected' : '' }}>
                        管理者
                    </option>
                </select>

                <select name="sort">
                    <option value="desc"
                        {{ request('sort')=='desc' ? 'selected' : '' }}>
                        新しい順
                    </option>

                    <option value="asc"
                        {{ request('sort')=='asc' ? 'selected' : '' }}>
                        古い順
                    </option>
                </select>

                <button type="submit" class="search-btn">
                    検索
                </button>

            </form>

            @foreach($users as $user)

            <div class="user-card">

                <div class="user-name">
                    {{ $user->name }}
                </div>

                <div class="user-role">
                    {{ $user->role }}
                </div>

                <div class="user-date">
                    登録日：{{ $user->created_at->format('Y-m-d') }}
                </div>

                <div class="user-action">

                    <a href="{{ route('admin.users.edit',$user->id) }}"
                        class="edit-btn">
                        編集
                    </a>

                    <form action="{{ route('admin.users.destroy',$user->id) }}"
                        method="POST">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="delete-btn"
                            onclick="return confirm('削除しますか？')">
                            削除
                        </button>

                    </form>

                </div>

            </div>

            @endforeach
            <div class="pagination">
                {{ $users->links() }}
            </div>

        </div>

        <div class="add-panel">

            <h2>ユーザー追加</h2>

            @if(session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST">

                @csrf

                <input type="text" name="name" placeholder="ユーザー名">

                <input type="email" name="email" placeholder="メールアドレス">

                <input type="password" name="password" placeholder="パスワード">

                <select name="role">
                    <option value="user">一般ユーザー</option>
                    <option value="admin">管理者</option>
                </select>

                <select name="department">
                    <option value="">部署を選択</option>

                    <option value="人事部">人事部</option>
                    <option value="営業部">営業部</option>
                    <option value="開発部">開発部</option>
                    <option value="総務部">総務部</option>
                    <option value="監査部">監査部</option>
                </select>

                <button class="add-btn">
                    追加
                </button>

            </form>

        </div>

    </div>

</body>

</html>