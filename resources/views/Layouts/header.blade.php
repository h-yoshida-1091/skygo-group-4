@php
    use App\Models\UserCharacter;
    use App\Models\ShiftRequest; // 💡 追加：シフト申請モデルをインポート

    $headerCharacter = null;
    $titleImage = null;
    $rejectedCount = 0;
    $rejectedShifts = collect();

    if (session()->has('userId')) {
        $userId = session('userId');

        // キャラクター情報取得
        $headerCharacter = UserCharacter::where('user_id', $userId)->first();
        if ($headerCharacter) {
            $level = $headerCharacter->level;
            if ($level >= 150) { $titleImage = 'Lv150.png'; }
            elseif ($level >= 140) { $titleImage = 'Lv140.png'; }
            elseif ($level >= 130) { $titleImage = 'Lv30.png'; }
            elseif ($level >= 120) { $titleImage = 'Lv120.png'; }
            elseif ($level >= 110) { $titleImage = 'Lv110.png'; }
            elseif ($level >= 100) { $titleImage = 'Lv100.png'; }
            elseif ($level >= 90) { $titleImage = 'Lv90.png'; }
            elseif ($level >= 80) { $titleImage = 'Lv80.png'; }
            elseif ($level >= 70) { $titleImage = 'Lv70.png'; }
            elseif ($level >= 60) { $titleImage = 'Lv60.png'; }
            elseif ($level >= 50) { $titleImage = 'Lv50.png'; }
            elseif ($level >= 40) { $titleImage = 'Lv40.png'; }
            elseif ($level >= 30) { $titleImage = 'Lv30.png'; }
            elseif ($level >= 20) { $titleImage = 'Lv20.png'; }
            elseif ($level >= 10) { $titleImage = 'Lv10.png'; }
            else { $titleImage = 'Lv1.png'; }
        }

        // 💡 既存テーブルから「差し戻し」のデータを直接取得
        $rejectedShifts = ShiftRequest::where('user_id', $userId)
                                      ->where('status', 'rejected')
                                      ->get();
        $rejectedCount = $rejectedShifts->count();
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
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            padding: 0 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .app-header-left {
            display: flex;
            align-items: center;
            gap: 18px;
            justify-self: start;
        }

        .app-menu-btn {
            border: none;
            background: rgba(255, 255, 255, .18);
            color: #fff;
            font-size: 28px;
            width: 50px;
            height: 50px;
            border-radius: 14px;
            cursor: pointer;
        }

        .app-logo {
            font-size: 28px;
            margin: 0;
            font-weight: bold;
            white-space: nowrap;
        }

        .app-title-badge {
            justify-self: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .app-title-badge img {
            height: 52px;
            max-width: 430px;
            object-fit: contain;
            display: block;
        }

        /* 💡 ユーザー名の左側に通知を並べるためにflex化 */
        .app-user {
            justify-self: end;
            font-size: 16px;
            font-weight: bold;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 15px; /* 通知ボタンとユーザー名の間隔 */
        }

        /* 🔔 通知ボタンのスタイル設定 */
        .app-notification-container {
            position: relative;
        }

        .app-notification-btn {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: #fff;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .app-notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #e53935;
            color: white;
            font-size: 11px;
            font-weight: bold;
            border-radius: 50%;
            padding: 2px 6px;
            transform: translate(20%, -20%);
        }

        /* 📄 通知ドロップダウンのスタイル */
        .app-notification-dropdown {
            display: none;
            position: absolute;
            top: 45px;
            right: 0;
            width: 280px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            color: #333;
            padding: 12px;
            z-index: 2000;
            white-space: normal; /* 折り返しを有効に */
        }

        .app-notification-dropdown.show {
            display: block;
        }

        .app-notification-dropdown h4 {
            margin: 0 0 8px 0;
            font-size: 14px;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
            color: #1976d2;
        }

        .app-notification-list {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 200px;
            overflow-y: auto;
        }

        .app-notification-item {
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
        }

        .app-notification-item:last-child {
            border-bottom: none;
        }

        .app-notification-item a {
            color: #1976d2;
            text-decoration: none;
            font-weight: bold;
            display: block;
            margin-top: 4px;
        }

        .app-notification-item a:hover {
            text-decoration: underline;
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
    <div class="app-header-left">
        <button id="menuBtn" class="app-menu-btn" type="button">
            ☰
        </button>
        <h2 class="app-logo">勤怠</h2>
    </div>

    <div class="app-title-badge">
        @if($titleImage)
            <img src="{{ asset('images/titles/' . $titleImage) }}" alt="称号">
        @endif
    </div>

    <div class="app-user">
        @if(session()->has('userName'))
            
            <div class="app-notification-container">
                <button id="notiBtn" class="app-notification-btn" type="button">
                    🔔
                    @if($rejectedCount > 0)
                        <span class="app-notification-badge">{{ $rejectedCount }}</span>
                    @endif
                </button>
                
                <div id="notiDropdown" class="app-notification-dropdown">
                    <h4>差し戻し通知</h4>
                    <ul class="app-notification-list">
                        @forelse($rejectedShifts as $shift)
                            <li class="app-notification-item">
                                <span style="color:#e53935; font-weight:bold;">【要修正】</span>
                                対象日: {{ \Carbon\Carbon::parse($shift->work_date)->format('Y-m-d') }}<br>
                                シフト申請が差し戻されました。
                                <a href="{{ route('shift.index') }}">修正画面へ</a>
                            </li>
                        @empty
                            <li class="app-notification-item" style="color:#888; text-align:center;">
                                新着の差し戻しはありません
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <span>{{ session('userName') }} さん</span>
        @endif
    </div>
</header>

<div id="overlay" class="app-overlay"></div>

<nav id="menu" class="app-side-menu">
    <div class="app-menu-title">メニュー</div>
    <a href="{{ route('dashboard') }}">ダッシュボード</a>
    <a href="{{ route('workschedule') }}">勤務表</a>
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
    
    // 💡 通知用の要素を取得
    const notiBtn = document.getElementById("notiBtn");
    const notiDropdown = document.getElementById("notiDropdown");

    // 左サイドメニューの開閉
    menuBtn.addEventListener("click", () => {
        menu.classList.toggle("open");
        overlay.classList.toggle("show");
        // メニュー開くときは通知を閉じる
        if(notiDropdown) notiDropdown.classList.remove("show");
    });

    overlay.addEventListener("click", () => {
        menu.classList.remove("open");
        overlay.classList.remove("show");
    });

    // 💡 通知ドロップダウンの開閉
    if (notiBtn) {
        notiBtn.addEventListener("click", (e) => {
            e.stopPropagation(); // ヘッダー外クリック判定に干渉させない
            notiDropdown.classList.toggle("show");
        });

        // 画面のどこかをクリックしたら通知ドロップダウンを閉じる
        document.addEventListener("click", () => {
            notiDropdown.classList.remove("show");
        });
    }
</script>