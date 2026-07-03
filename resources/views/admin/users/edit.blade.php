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

        <form action="{{ route('account.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>名前</label>
                <input type="text" name="name" value="{{ $user->name }}">
            </div>

            <div class="form-group">
                <label>メールアドレス</label>
                <input type="email" name="email" value="{{ $user->email }}">
            </div>

            <div class="form-group">
                <label>新しいパスワード</label>
                <input type="password" name="password">
            </div>

            <button type="submit" class="submit-btn">
                更新する
            </button>

        </form>

    </div>
</div>