@php
    use App\Models\UserCharacter;

    $headerCharacter = null;

    if (session()->has('userId')) {
        $headerCharacter = UserCharacter::where('user_id', session('userId'))->first();
    }
@endphp

<head>
    <style>
        body {
            margin: 0;
            padding-top: 65px;
        }

        .app-header {
            height: 65px;
            background: #1976d2;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 0 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .app-menu-btn {
            border: none;
            background: rgba(255, 255, 255, .18);
            color: #fff;
            font-size: 28px;
            width: 58px;
            height: 58px;
            border-radius: 14px;
            cursor: pointer;
            margin-right: 22px;
        }

        .app-logo {
            font-size: 28px;
            margin: 0;
            font-weight: bold;
        }

        .app-user {
            margin-left: 20px;
            font-size: 15px;
            font-weight: bold;
            opacity: .95;
        }

        .app-character {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, .18);
            padding: 6px 14px;
            border-radius: 16px;
        }

        .app-character img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
        }

        .app-character-info {
            display: flex;
            flex-direction: column;
            font-size: 13px;
            line-height: 1.3;
        }

        .app-character-name {
            font-weight: bold;
        }

        .app-character-title {
            font-size: 12px;
            opacity: .9;
        }

        .app-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .35);
            z-index: 1050;
        }

        .app-overlay.show {
            display: block;
        }

        .app-side-menu {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px;
            height: 100vh;
            background: #fff;
            box-shadow: 3px 0 14px rgba(0, 0, 0, .22);
            transition: .3s;
            overflow-y: auto;
            z-index: 1100;
        }

        .app-side-menu.open {
            left: 0;
        }

        .app-menu-title {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80px;
            font-size: 22px;
            font-weight: bold;
            color: #1976d2;
            border-bottom: 1px solid #eee;
        }

        .app-side-menu a {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 18px 0;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            font-size: 17px;
        }

        .app-side-menu a:hover {
            background: #e3f2fd;
            color: #1976d2;
        }

        .app-side-menu .app-logout-btn {
            width: calc(100% - 56px);
            margin: 24px 28px;
            padding: 14px 0;
            border: none;
            background-color: #e53935;
            color: #fff;
            font-size: 17px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
        }

        .app-side-menu .app-logout-btn:hover {
            background-color: #c62828;
        }
    </style>
</head>

<header class="app-header">
    <button id="menuBtn" class="app-menu-btn" type="button">
        ☰
    </button>

    <h2 class="app-logo">勤怠</h2>

    @if(session()->has('userName'))
        <div class="app-user">
            {{ session('userName') }} さん
        </div>
    @endif

    @if($headerCharacter)
        <div class="app-character">
            <img src="{{ asset('images/characters/' . $headerCharacter->image) }}" alt="相棒">

            <div class="app-character-info">
                <span class="app-character-name">
                    {{ $headerCharacter->nickname ?? $headerCharacter->character_type }}
                    Lv.{{ $headerCharacter->level }}
                </span>
                <span class="app-character-title">
                    {{ $headerCharacter->title }}
                </span>
            </div>
        </div>
    @endif
</header>

<div id="overlay" class="app-overlay"></div>

<nav id="menu" class="app-side-menu">
    <div class="app-menu-title">メニュー</div>

    <a href="{{ route('dashboard') }}">ダッシュボード</a>
    <a href="{{ route('workschedule') }}">勤務表</a>
    <a href="{{ route('shift.index') }}">シフト一覧、シフト修正</a>
    <a href="{{ route('character.index') }}">相棒</a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="app-logout-btn">ログアウト</button>
    </form>
</nav>

<script>
    const menuBtn = document.getElementById("menuBtn");
    const menu = document.getElementById("menu");
    const overlay = document.getElementById("overlay");

    menuBtn.addEventListener("click", () => {
        menu.classList.toggle("open");
        overlay.classList.toggle("show");
    });

    overlay.addEventListener("click", () => {
        menu.classList.remove("open");
        overlay.classList.remove("show");
    });
</script>