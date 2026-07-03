@include('Layouts.admin-header')

<link rel="icon" type="image/png" href="{{ asset('images/小林大地首.png') }}">
<link rel="stylesheet" href="{{ asset('css/account-edit.css') }}">

<div class="edit-wrapper">

    <h1 class="edit-title">アカウント編集</h1>

    @if(session('success'))
    <p class="success">{{ session('success') }}</p>
    @endif

    @if($errors->any())
    <p class="error">{{ $errors->first() }}</p>
    @endif

    <div class="edit-card">

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <input type="text" name="name" value="{{ $user->name }}">

            <input type="email" name="email" value="{{ $user->email }}">

            <input type="password" name="password" placeholder="変更する場合のみ入力">

            <select name="role">
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>
                    一般ユーザー
                </option>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                    管理者
                </option>
            </select>

            <select name="department">
                <option value="人事部" {{ $user->department == '人事部' ? 'selected' : '' }}>人事部</option>
                <option value="営業部" {{ $user->department == '営業部' ? 'selected' : '' }}>営業部</option>
                <option value="開発部" {{ $user->department == '開発部' ? 'selected' : '' }}>開発部</option>
                <option value="総務部" {{ $user->department == '総務部' ? 'selected' : '' }}>総務部</option>
                <option value="監査部" {{ $user->department == '監査部' ? 'selected' : '' }}>監査部</option>
            </select>

            <button type="submit">更新</button>
        </form>

    </div>
</div>