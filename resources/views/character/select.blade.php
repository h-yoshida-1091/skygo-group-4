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

            <label class="character-card lizard-card">
                <input type="radio"
                    name="character_type"
                    value="トカゲ"
                    required
                    data-voice="{{ asset('audio/characters/きいち出勤.mp3') }}">
                <img src="{{ asset('images/characters/トカゲ.png') }}" alt="トカゲ">
                <h3>歩くトカゲ</h3>
                <p>لا أخشى الأرض الحارّة! إلى السماء الواسعة مع الطيور!</p>
            </label>

            <label class="character-card turtle-card">
                <input type="radio"
                    name="character_type"
                    value="かめ"
                    data-voice="{{ asset('audio/characters/cat.mp3') }}">
                <img src="{{ asset('images/characters/かめ.png') }}" alt="かめ">
                <h3>空飛ぶ亀</h3>
                <p>새들과 함께 드넓은 하늘로!</p>
            </label>

            <label class="character-card frog-card">
                <input type="radio"
                    name="character_type"
                    value="カエル"
                    data-voice="{{ asset('audio/characters/cat.mp3') }}">
                <img src="{{ asset('images/characters/カエル.png') }}" alt="カエル">
                <h3>泳ぐカエル</h3>
                <p>Mit den Vögeln in den weiten Himmel!</p>
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