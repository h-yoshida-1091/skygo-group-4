<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="icon" type="image/png" href="{{ asset('images/小林大地首.png') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-page">

        <div class="login-card">
            <h1>ログイン</h1>

            @if ($errors->any())
                <p class="error-message">
                    {{ $errors->first() }}
                </p>
            @endif

            <form action="/login" method="POST">
                @csrf

                <div class="form-group">
                    <label>メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label>パスワード</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="login-button">
                    ログイン
                </button>
            </form>
        </div>

    </div>

</body>

</html>