@php
use App\Models\UserCharacter;
use App\Models\ShiftRequest;

$headerCharacter = null;
$titleImage = null;
$rejectedCount = 0;
$rejectedShiftMonths = collect();

$userLevel = 1;
$userExp = 0;
$nextExp = 100;
$titleName = '新人ワーカー';

if (session()->has('userId')) {
$userId = session('userId');

$headerCharacter = UserCharacter::where('user_id', $userId)->first();

if ($headerCharacter) {
$level = $headerCharacter->level;
$userLevel = $headerCharacter->level ?? 1;
$userExp = $headerCharacter->exp ?? 0;
$nextExp = 100;

if ($level >= 150) { $titleImage = 'Lv150.png'; $titleName = '伝説のワーカー'; }
elseif ($level >= 140) { $titleImage = 'Lv140.png'; $titleName = '神速ワーカー'; }
elseif ($level >= 130) { $titleImage = 'Lv130.png'; $titleName = '超越ワーカー'; }
elseif ($level >= 120) { $titleImage = 'Lv120.png'; $titleName = '王者ワーカー'; }
elseif ($level >= 110) { $titleImage = 'Lv110.png'; $titleName = '達人ワーカー'; }
elseif ($level >= 100) { $titleImage = 'Lv100.png'; $titleName = 'マスターワーカー'; }
elseif ($level >= 90) { $titleImage = 'Lv90.png'; $titleName = '精鋭ワーカー'; }
elseif ($level >= 80) { $titleImage = 'Lv80.png'; $titleName = '熟練ワーカー'; }
elseif ($level >= 70) { $titleImage = 'Lv70.png'; $titleName = '上級ワーカー'; }
elseif ($level >= 60) { $titleImage = 'Lv60.png'; $titleName = '頼れるワーカー'; }
elseif ($level >= 50) { $titleImage = 'Lv50.png'; $titleName = 'ベテランワーカー'; }
elseif ($level >= 40) { $titleImage = 'Lv40.png'; $titleName = '中堅ワーカー'; }
elseif ($level >= 30) { $titleImage = 'Lv30.png'; $titleName = '一人前ワーカー'; }
elseif ($level >= 20) { $titleImage = 'Lv20.png'; $titleName = '成長ワーカー'; }
elseif ($level >= 10) { $titleImage = 'Lv10.png'; $titleName = '見習いワーカー'; }
else { $titleImage = 'Lv1.png'; $titleName = '新人ワーカー'; }
}

$rejectedShiftMonths = ShiftRequest::where('user_id', $userId)
->where('status', 'rejected')
->orderBy('work_date', 'desc')
->get()
->groupBy(function ($shift) {
return $shift->work_date->format('Y-m');
});

$rejectedCount = $rejectedShiftMonths->count();
}

$expPercent = $nextExp > 0 ? min(100, ($userExp / $nextExp) * 100) : 0;
@endphp

<head>
    <style>
        body {
            margin: 0;
            padding-top: 90px;
        }

        .app-header {

    height: 76px;

    background:
        linear-gradient(rgba(0,0,0,.25), rgba(0,0,0,.25)),
        url("/images/ヘッダー画像.png");

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    color: #fff;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    padding: 0 30px;
    box-shadow: 0 4px 16px rgba(0,0,0,.35);

    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;

    border-bottom: 3px solid #d4af37; /* 金色 */
}
            .app-header-left {
                display: flex;
                align-items: center;
                gap: 18px;
                justify-self: start;
            }

            .app-menu-btn {
                border: 2px solid rgba(255, 255, 255, .35);
                background: rgba(255, 255, 255, .18);
                color: #fff;
                font-size: 30px;
                width: 54px;
                height: 54px;
                border-radius: 16px;
                cursor: pointer;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, .3);
            }

            .app-menu-btn:hover {
                background: rgba(255, 255, 255, .28);
                transform: translateY(-1px);
            }

            .app-logo-area {
                display: flex;
                align-items: center;
                height: 54px;
                /* 周りのボタンや称号の高さ(54px)に合わせます */
            }

            .app-logo-img {
                height: 54px;
                /* ヘッダー内で綺麗に収まる高さ */
                width: auto;
                /* 横幅はアスペクト比を維持 */
                object-fit: contain;
                display: block;
            }

            /* .app-logo {
            font-size: 30px;
            margin: 0;
            font-weight: 900;
            letter-spacing: 2px;
            white-space: nowrap;
            text-shadow: 2px 2px 0 rgba(0,0,0,.25);
        }

        .app-logo-sub {
            font-size: 12px;
            color: #fff9c4;
            font-weight: bold;
            letter-spacing: 1px;
        } */

            .app-title-badge {
                justify-self: center;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 4px 18px;
            }

            .app-title-badge img {
                height: 54px;
                max-width: 430px;
                object-fit: contain;
                display: block;
            }

            .app-user {
                justify-self: end;
                font-size: 16px;
                font-weight: bold;
                white-space: nowrap;
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .app-notification-container {
                position: relative;
            }

            .app-notification-btn {
                background: rgba(255, 255, 255, .18);
                border: 2px solid rgba(255, 255, 255, .35);
                font-size: 20px;
                cursor: pointer;
                color: #fff;
                padding: 9px 13px;
                border-radius: 999px;
                display: flex;
                align-items: center;
                gap: 6px;
                font-weight: bold;
            }

            .app-notification-btn:hover {
                background: rgba(255, 255, 255, .28);
            }

            .app-notification-label {
                font-size: 13px;
            }

            .app-notification-badge {
                position: absolute;
                top: -7px;
                right: -7px;
                background: #e53935;
                color: white;
                font-size: 12px;
                font-weight: bold;
                border-radius: 50%;
                min-width: 22px;
                height: 22px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 2px solid #fff;
            }

            .app-notification-dropdown {
                display: none;
                position: absolute;
                top: 52px;
                right: 0;
                width: 300px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.22);
                color: #333;
                padding: 12px;
                z-index: 2000;
                white-space: normal;
            }

            .app-notification-dropdown.show {
                display: block;
            }

            .app-notification-dropdown h4 {
                margin: 0 0 8px 0;
                font-size: 15px;
                border-bottom: 1px solid #eee;
                padding-bottom: 8px;
                color: #1976d2;
            }

            .app-notification-list {
                list-style: none;
                padding: 0;
                margin: 0;
                max-height: 220px;
                overflow-y: auto;
            }

            .app-notification-item {
                padding: 9px 0;
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
                margin-top: 5px;
            }

            .app-user-card {
                display: flex;
                align-items: center;
                gap: 10px;
                background: rgba(255, 255, 255, .18);
                border: 2px solid rgba(255, 255, 255, .32);
                border-radius: 18px;
                padding: 8px 12px;
            }

            .app-user-icon {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background: #fff;
                color: #1976d2;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 19px;
            }

            .app-user-info {
                display: flex;
                flex-direction: column;
                gap: 3px;
            }

            .app-user-name {
                font-size: 15px;
                line-height: 1;
            }

            .app-user-level {
                font-size: 12px;
                color: #fff9c4;
                line-height: 1;
            }

            .app-exp-bar {
                width: 90px;
                height: 7px;
                background: rgba(255, 255, 255, .35);
                border-radius: 999px;
                overflow: hidden;
            }

            .app-exp-fill {
                height: 100%;

                width: {
                        {
                        $expPercent
                    }
                }

                %;
                background: linear-gradient(90deg, #ffd54f, #fff176);
                border-radius: 999px;
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
                height: 86px;
                font-size: 22px;
                font-weight: bold;
                color: #1976d2;
                border-bottom: 1px solid #eee;
                background: #e3f2fd;
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
                font-weight: bold;
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

        <div class="app-logo-area">
            <a href="{{ route('dashboard') }}" style="display: block;">
                <img src='images/kintai-logo.png' alt="勤怠クエスト" class="app-logo-img">
            </a>
        </div>
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
                🔔 <span class="app-notification-label">通知</span>

                @if($rejectedCount > 0)
                <span class="app-notification-badge">{{ $rejectedCount }}</span>
                @endif
            </button>

            <div id="notiDropdown" class="app-notification-dropdown">
                <h4>差し戻し通知</h4>

                <ul class="app-notification-list">
                    @forelse($rejectedShiftMonths as $yearMonth => $shifts)
                    @php
                    [$year, $month] = explode('-', $yearMonth);
                    @endphp

                    <li class="app-notification-item">
                        <span style="color:#e53935; font-weight:bold;">【要修正】</span>
                        {{ $year }}年{{ (int)$month }}月のシフト申請が差し戻されました。<br>

                        <a href="{{ route('shift.index', ['year' => $year, 'month' => $month]) }}">
                            修正画面へ
                        </a>
                    </li>
                    @empty
                    <li class="app-notification-item" style="color:#888; text-align:center;">
                        新着の差し戻しはありません
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="app-user-card">
            <div class="app-user-icon">⚔️</div>

            <div class="app-user-info">
                <div class="app-user-name">
                    {{ session('userName') }} さん
                </div>

                <div class="app-user-level">
                    Lv.{{ $userLevel }} / {{ $titleName }}
                </div>

                <div class="app-exp-bar">
                    <div class="app-exp-fill"></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</header>

<div id="overlay" class="app-overlay"></div>

<nav id="menu" class="app-side-menu">
    <div class="app-menu-title">メニュー</div>

    <a href="{{ route('dashboard') }}">ダッシュボード</a>
    <a href="{{ route('workschedule') }}">勤務表</a>
    <a href="{{ route('shift.index') }}">シフト一覧・シフト修正</a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="app-logout-btn">ログアウト</button>
    </form>
</nav>

<script>
    const menuBtn = document.getElementById("menuBtn");
    const menu = document.getElementById("menu");
    const overlay = document.getElementById("overlay");

    const notiBtn = document.getElementById("notiBtn");
    const notiDropdown = document.getElementById("notiDropdown");

    menuBtn.addEventListener("click", () => {
        menu.classList.toggle("open");
        overlay.classList.toggle("show");

        if (notiDropdown) {
            notiDropdown.classList.remove("show");
        }
    });

    overlay.addEventListener("click", () => {
        menu.classList.remove("open");
        overlay.classList.remove("show");
    });

    if (notiBtn) {
        notiBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            notiDropdown.classList.toggle("show");
        });

        notiDropdown.addEventListener("click", (e) => {
            e.stopPropagation();
        });

        document.addEventListener("click", () => {
            notiDropdown.classList.remove("show");
        });
    }
</script>