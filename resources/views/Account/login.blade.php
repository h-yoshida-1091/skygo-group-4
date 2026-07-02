<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-page">

        <div class="word-background" id="wordBackground"></div>

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

    <script>
const words = [
    "OMG", "有給", "残業", "定時", "会議", "3時間勤務",
    "リモートがいいな", "腹減った", "退勤", "定時ですよ？",
    "週休5日", "在宅", "休み", "帰ろう"
];

const background = document.getElementById("wordBackground");
const placedWords = [];
const wordCount = 19;

function isOverlapping(newRect) {
    return placedWords.some(rect => {
        return !(
            newRect.right < rect.left ||
            newRect.left > rect.right ||
            newRect.bottom < rect.top ||
            newRect.top > rect.bottom
        );
    });
}

for (let i = 0; i < wordCount; i++) {
    const word = document.createElement("span");
    word.className = "floating-word";
    word.textContent = words[Math.floor(Math.random() * words.length)];

    const fontSize = 30 + Math.random() * 10;
    word.style.fontSize = fontSize + "px";

    background.appendChild(word);

    let placed = false;

    for (let tryCount = 0; tryCount < 100; tryCount++) {
        const left = Math.random() * 85;
        const top = Math.random() * 85;

        word.style.left = left + "vw";
        word.style.top = top + "vh";

        const rect = word.getBoundingClientRect();

        const newRect = {
            left: rect.left - 30,
            right: rect.right + 30,
            top: rect.top - 20,
            bottom: rect.bottom + 20
        };

        if (!isOverlapping(newRect)) {
            placedWords.push(newRect);
            placed = true;
            break;
        }
    }

    if (!placed) {
        word.remove();
        continue;
    }

    word.style.animationDuration = (14 + Math.random() * 14) + "s";
    word.style.animationDelay = (Math.random() * -10) + "s";

    word.style.setProperty("--move-x", (Math.random() * 160 - 80) + "px");
    word.style.setProperty("--move-y", (Math.random() * 160 - 80) + "px");
    word.style.setProperty("--rotate", (Math.random() * 20 - 10) + "deg");
}
</script>

</body>

</html>