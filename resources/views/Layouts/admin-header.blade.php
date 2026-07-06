<link rel="stylesheet" href="{{ asset('css/header.css') }}">

<header class="header">
    <button id="menuBtn" class="menu-btn">
        ☰
    </button>

    <h2 class="logo">勤怠管理システム（管理者）</h2>
</header>

<nav id="menu" class="menu">

    <a href="/admin/dashboard">📊 管理者ダッシュボード</a>

    <a href="#">📋 申請管理</a>

    <a href="/admin/users">👤 ユーザー管理</a>


    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn">
            🚪 ログアウト
        </button>
    </form>

</nav>

<script>
    const menuBtn = document.getElementById("menuBtn");
    const menu = document.getElementById("menu");

    menuBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        menu.classList.toggle("open");
    });

    menu.addEventListener("click", (e) => {
        e.stopPropagation();
    });

    document.addEventListener("click", () => {
        menu.classList.remove("open");
    });
</script>