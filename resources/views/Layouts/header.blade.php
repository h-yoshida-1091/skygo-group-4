<link rel="stylesheet" href="{{ asset('css/header.css') }}">

<header class="app-header">
    <button id="menuBtn" class="app-menu-btn" type="button">
        ☰
    </button>

    <h2 class="app-logo">勤怠管理システム</h2>
</header>

<div id="overlay" class="app-overlay"></div>

<nav id="menu" class="app-side-menu">
    <div class="app-menu-title">メニュー</div>

    <a href="#">ダッシュボード</a>
    <a href="#">勤怠一覧</a>
    <a href="#">勤怠申請</a>
    <a href="{{ route('shift.index') }}">シフト一覧、シフト修正</a>

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