<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>

<body>

    <h1>ログイン</h1>

    @if ($errors->any())
        <p style="color:red;">
            {{ $errors->first() }}
        </p>
    @endif

    <form action="/login" method="POST">
        @csrf

        <div>
            <label>メールアドレス</label><br>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                required>
        </div>

        <br>

        <div>
            <label>パスワード</label><br>
            <input
                type="password"
                name="password"
                required>
        </div>

        <br>

        <button type="submit">
            ログイン
        </button>

    </form>

</body>

</html>