<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>相棒を選ぼう</title>
    <link rel="stylesheet" href="{{ asset('css/character.css') }}">
</head>

<body>

    <h1>相棒を選ぼう！</h1>

    <p>一緒に成長する相棒を選択してください。</p>

    <form action="{{ route('character.select') }}" method="POST">
        @csrf

        <div class="character-list">

            <label class="character-card">
                <input type="radio"
                    name="character_type"
                    value="D"
                    required
                    data-voice="{{ asset('audio/characters/きいち出勤.mp3') }}">
                <img src="{{ asset('images/characters/D.png') }}" alt="猫">
                <h3>永山貴一</h3>
                <p>元気いっぱい！</p>
            </label>

            <label class="character-card">
                <input type="radio"
                    name="character_type"
                    value="bear"
                    data-voice="{{ asset('audio/characters/cat.mp3') }}">
                <img src="{{ asset('images/characters/D.png') }}" alt="熊">
                <h3>クマ</h3>
                <p>頼れる相棒！</p>
            </label>

            <label class="character-card">
                <input type="radio"
                    name="character_type"
                    value="dog"
                    data-voice="{{ asset('audio/characters/cat.mp3') }}">
                <img src="{{ asset('images/characters/D.png') }}" alt="犬">
                <h3>イヌ</h3>
                <p>いつも元気！</p>
            </label>

        </div>

        <br>

        <label>
            ニックネーム
            <input type="text" name="nickname" maxlength="50">
        </label>

        <br><br>

        <button type="submit">この相棒に決定！</button>
        <audio id="voicePlayer"></audio>

        <script>
            const player = document.getElementById('voicePlayer');

            document.querySelectorAll('input[name="character_type"]').forEach(radio => {

                radio.addEventListener('change', function() {

                    player.pause();
                    player.currentTime = 0;

                    player.src = this.dataset.voice;

                    player.play().catch(error => {
                        console.log(error);
                    });
                });

            });
        </script>
    </form>

</body>

</html>