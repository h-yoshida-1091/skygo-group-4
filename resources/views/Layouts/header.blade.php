<header class="header">
    <button id="menuBtn" class="menu-btn">
        ☰
    </button>

    <h2 class="logo">勤怠管理システム</h2>
</header>

<nav id="menu" class="menu">

    <a href="#">🏠 ダッシュボード</a>
    <a href="#">📅 勤怠一覧</a>
    <a href="#">📝 勤怠申請</a>
    <a href="#">📋 シフト一覧</a>
    <a href="#">✏️ シフト修正</a>

    <form action="#" method="POST">
        @csrf
        <button type="submit" class="logout-btn">🚪 ログアウト</button>
    </form>

</nav>

<script>
const menuBtn = document.getElementById("menuBtn");
const menu = document.getElementById("menu");

menuBtn.addEventListener("click", () => {
    menu.classList.toggle("open");
});
</script>