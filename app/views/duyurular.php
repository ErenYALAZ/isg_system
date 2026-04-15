<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
$uRole = (int) ($_SESSION['role'] ?? 1);
$isHigh = in_array($uRole, [2, 4, 5]);
$kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];

function getDeptColor($dept)
{
    $map = [
        'Kalite' => '#0ea5e9',
        'Üretim' => '#10b981',
        'Lojistik' => '#f59e0b',
        'IT' => '#3b82f6',
        'OT' => '#6366f1',
        'Bakım' => '#eab308',
        'İSG' => '#ef4444',
        'İdari İşler' => '#8b5cf6',
        'İK' => '#ec4899',
        'Genel' => '#64748b'
    ];
    return $map[trim($dept)] ?? '#3b82f6';
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sayfaBasligi ?? 'MAISG - Duyurular'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="/isg/assets/theme-engine.css" rel="stylesheet" />
    <script src="/isg/assets/theme-engine.js"></script>
    <style>
        :root {
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            height: 100vh;
            position: fixed;
            left: -280px;
            top: 0;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 40px 0;
            z-index: 1000;
            transition: 0.4s;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .nav-list {
            list-style: none;
            padding: 0 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 25px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            border-radius: 12px;
            transition: 0.2s;
        }

        .nav-link:hover { color: var(--text-main); background: var(--border-color); }
        .nav-link.active { color: #fff; background: var(--accent); }

        .nav-submenu { list-style: none; padding-left: 55px; max-height: 0; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); opacity: 0; }
        .nav-submenu.open { max-height: 200px; opacity: 1; padding-bottom: 10px; }
        .nav-sub-link { display: block; padding: 10px 0; color: var(--text-muted); text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s; position: relative; }
        .nav-sub-link:hover { color: var(--text-main); }
        .nav-sub-link.active { color: var(--accent); }
        .nav-sub-link::before { content: '•'; position: absolute; left: -15px; opacity: 0.5; }

        .has-submenu { position: relative; }
        .submenu-toggle { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; padding: 5px; color: var(--text-muted); transition: 0.3s; z-index: 5; }
        .has-submenu.open .submenu-toggle { transform: translateY(-50%) rotate(180deg); color: var(--accent); }

        .page-wrapper { width: 100%; min-height: 100vh; display: flex; flex-direction: column; }

        .top-header {
            height: 75px;
            padding: 0 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-header);
            backdrop-filter: var(--glass-blur);
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .u-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .u-role {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .main-body {
            padding: 40px;
            max-width: 1500px;
            width: 100%;
            margin: 0 auto;
        }

        /* === GRID LAYOUT === */
        .duyuru-list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .p-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 22px;
            padding: 0;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 440px;
            content-visibility: auto;
            contain-intrinsic-size: 0 440px;
            box-shadow: var(--shadow-card);
        }
        .p-card.highlight-focus {
            box-shadow: 0 0 0 4px var(--accent);
            animation: pulse-focus 2s infinite;
        }
        @keyframes pulse-focus {
            0% { box-shadow: 0 0 0 0px var(--accent); }
            50% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0.3); }
            100% { box-shadow: 0 0 0 0px var(--accent); }
        }
        @keyframes pulse-danger {
            0% { transform: scale(1); filter: brightness(1); }
            50% { transform: scale(1.15); filter: brightness(1.3); }
            100% { transform: scale(1); filter: brightness(1); }
        }
        .danger-pill { display: inline-block; font-size: 9px; font-weight: 900; padding: 2px 8px; border-radius: 6px; letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; }
        .danger-high { background: #ef4444; color: #fff; border-color: #ef4444; font-size: 10px; box-shadow: 0 0 10px rgba(239, 68, 68, 0.4); text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
        .danger-medium { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
        .danger-low { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }
        .pulse-high { animation: pulse-danger 0.8s infinite; will-change: transform, filter; }

        .p-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .p-card.status-pending {
            border-left: 5px solid #f59e0b;
        }

        .p-card.status-critical {
            border-left: 5px solid #ef4444;
        }

        .p-card.status-resolved {
            border-left: 5px solid #10b981;
        }

        .unread-dot {
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .pending-b {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .critical-b {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .btn-split {
            display: flex;
            align-items: stretch;
            border-radius: 14px;
            overflow: hidden;
            transition: 0.3s;
            text-decoration: none;
            border-width: 1.5px;
            border-style: solid;
        }

        .btn-split .s-main {
            padding: 10px 16px;
            font-size: 12px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: inherit;
        }

        .btn-split .s-pdf {
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            text-decoration: none;
            color: inherit;
            border-left: 1.5px solid rgba(255, 255, 255, 0.13);
        }

        .btn-split .s-main:hover,
        .btn-split .s-pdf:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Thumbnail resim */
        .card-thumb {
            width: 100%;
            height: 185px;
            overflow: hidden;
            position: relative;
            background: rgba(15, 23, 42, 0.6);
            flex-shrink: 0;
        }

        .card-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: brightness(0.85);
            transition: 0.3s;
        }

        .card-thumb:hover img {
            filter: brightness(1);
            transform: scale(1.04);
        }

        .card-thumb-count {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 11px;
            font-weight: 800;
            color: #fff;
        }

        /* Kart içeriği */
        .card-body {
            padding: 18px 20px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            overflow: hidden;
            min-height: 0;
        }

        /* İkon satırı */
        .icon-row {
            display: flex;
            gap: 6px;
            background: rgba(15, 23, 42, 0.6);
            padding: 5px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .icon-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: var(--text-m);
            transition: 0.2s;
            cursor: pointer;
            position: relative;
        }

        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .icon-btn.del {
            color: #f87171;
        }

        .icon-btn.ok {
            color: #10b981;
        }

        .icon-btn.ok:hover {
            background: rgba(16, 185, 129, 0.1);
        }

        .wa-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--accent);
            color: #fff;
            font-size: 9px;
            padding: 1px 5px;
            border-radius: 5px;
            font-weight: 800;
        }

        .tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin: 0;
        }

        .tag-p {
            font-size: 9px;
            font-weight: 800;
            padding: 4px 9px;
            border-radius: 7px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .img-feed {
            display: none;
        }

        /* eski img-feed artık kullanılmıyor, card-thumb ile değiştirildi */

        .wa-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding: 10px 20px 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
        }

        .solved-b {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* Metin kırpma */
        .card-title {
            font-size: 16px;
            font-weight: 800;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-meta {
            font-size: 11px;
            color: var(--text-m);
            font-weight: 700;
        }

        .card-content {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.7;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Üst satır: badge + ikonlar */
        .card-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-o {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.98);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(25px);
        }

        .modal-c {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            width: 95%;
            max-width: 900px;
            padding: 50px;
            position: relative;
        }

        /* Bottom Nav for restricted roles */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-sidebar);
            backdrop-filter: var(--glass-blur);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-around;
            padding: 12px 10px;
            z-index: 2000;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -15px 40px rgba(0, 0, 0, 0.5);
        }

        .b-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: var(--text-m);
            text-decoration: none;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }

        .b-nav-link.active {
            color: var(--accent);
            transform: translateY(-3px);
        }

        .b-nav-link:hover {
            color: #fff;
        }

        /* === RESPONSIVE === */
        @media (max-width: 1100px) {
            .duyuru-list {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 680px) {
            .duyuru-list {
                grid-template-columns: 1fr;
            }

            .main-body {
                padding: 15px 12px 90px;
            }

            .modal-c {
                padding: 25px 15px;
                border-radius: 28px;
            }

            .top-header {
                padding: 0 15px;
                height: 62px;
            }

            /* Başlık satırı mobilde dikey */
            .page-header-row {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 14px;
                margin-bottom: 28px !important;
            }

            .page-header-row h2 {
                font-size: 26px !important;
                letter-spacing: -0.5px !important;
            }

            /* Buton satırı mobilde küçülüp alt alta saracak */
            .header-btn-row {
                flex-wrap: wrap !important;
                justify-content: flex-start !important;
                width: 100%;
                gap: 8px !important;
                padding-bottom: 0px;
                overflow-x: visible;
            }
            .header-btn-row > a, 
            .header-btn-row > button,
            .header-btn-row > .btn-split {
                flex-shrink: 0;
            }
            .header-btn-row > a, 
            .header-btn-row > button {
                font-size: 10px !important;
                padding: 8px 12px !important;
            }
            .header-btn-row .btn-split .s-main {
                font-size: 10px !important;
                padding: 8px 10px !important;
            }
            .header-btn-row .btn-split .s-pdf {
                padding: 8px 10px !important;
            }
            .header-btn-row svg {
                width: 13px !important;
                height: 13px !important;
            }

            /* Kart içerik yazıları mobilde */
            .card-title {
                font-size: 15px;
            }

            .u-info span:nth-child(2) {
                display: none;
            }

            .modal-c h3 {
                font-size: 20px;
            }
        }

        @media (max-width: 420px) {
            .duyuru-list {
                gap: 14px;
            }
            .p-card {
                height: auto;
                min-height: 360px;
            }
        }
    </style>
    <?php
    $uRole = $_SESSION['role'] ?? 1;
    $isHigh = in_array($uRole, [2, 4, 5]);
    ?>
    <!-- html2pdf artık kullanılmıyor, PDF yeni endpoint ile alınıyor -->
</head>

<body>

    <?php if ($isHigh): ?>
        <div class="sidebar-overlay" id="overlay" onclick="toggleM()"></div>
        <aside class="sidebar" id="sb">
            <div style="padding:0 35px; margin-bottom:50px;">
                <div style="font-size:26px; font-weight:800; color:#fff;">MA<span
                        style="color:var(--accent); font-weight:300;">ISG</span></div>
            </div>
            <ul class="nav-list">
                <li><a href="/isg/index.php?url=home" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg> Ana Panel</a></li>
                <li><a href="/isg/index.php?url=kullanici" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg> Kullanıcı Paneli</a></li>
                <li><a href="/isg/index.php?url=duyurular" class="nav-link active"><svg width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg> Duyuru Akışı</a></li>

                <li style="margin-top:20px; margin-left:10px; font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:10px; padding-left:15px; opacity:0.5;">Analiz & İstatistik</li>
                
                <li class="has-submenu" id="menu-tehlike">
                    <a href="/isg/index.php?url=duyurular/tehlike_analizi" class="nav-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        Tehlike Analizi
                    </a>
                    <div class="submenu-toggle" onclick="toggleSub('menu-tehlike', event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <ul class="nav-submenu">
                        <li><a href="/isg/index.php?url=duyurular/tehlike_analizi" class="nav-sub-link">Genel Analiz</a></li>
                        <li><a href="/isg/index.php?url=duyurular/tehlike_analizi&ay=<?= date('Y-m') ?>" class="nav-sub-link">Aylık Rapor</a></li>
                    </ul>
                </li>

                <li class="has-submenu" id="menu-perf">
                    <a href="/isg/index.php?url=duyurular/istatistik" class="nav-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        Performans
                    </a>
                    <div class="submenu-toggle" onclick="toggleSub('menu-perf', event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <ul class="nav-submenu">
                        <li><a href="/isg/index.php?url=duyurular/istatistik" class="nav-sub-link">Genel Performans</a></li>
                        <li><a href="/isg/index.php?url=duyurular/istatistik&ay=<?= date('Y-m') ?>" class="nav-sub-link">Aylık Rapor</a></li>
                    </ul>
                </li>

                <li class="has-submenu" id="menu-dept">
                    <a href="/isg/index.php?url=duyurular/departman_istatistik" class="nav-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                        Departman Analizi
                    </a>
                    <div class="submenu-toggle" onclick="toggleSub('menu-dept', event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <ul class="nav-submenu">
                        <li><a href="/isg/index.php?url=duyurular/departman_istatistik" class="nav-sub-link">Genel Analiz</a></li>
                        <li><a href="/isg/index.php?url=duyurular/departman_istatistik&ay=<?= date('Y-m') ?>" class="nav-sub-link">Aylık Bölüm Bazlı</a></li>
                    </ul>
                </li>
            </ul>
            <div style="margin-top:auto; padding: 0 15px;">
                <a href="/isg/index.php?url=chat" class="nav-link" style="color:#10b981;"><svg width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                        </path>
                    </svg> Op1 (Mesajlaşma)</a>
                <a href="/isg/index.php?url=login/logout" class="nav-link" style="color:#ef4444;"><svg width="18"
                        height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg> Güvenli Çıkış</a>
            </div>
        </aside>
    <?php else: ?>
        <div class="bottom-nav">
            <a href="/isg/index.php?url=duyurular" class="b-nav-link active">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                Duyuru Akışı
            </a>
            <?php if ($uRole == 3): ?>
                <a href="/isg/index.php?url=chat" class="b-nav-link" style="color:#10b981;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                        </path>
                    </svg>
                    Op1 (Mesajlaşma)
                </a>
            <?php endif; ?>
            <a href="/isg/index.php?url=login/logout" class="b-nav-link" style="color:#ef4444;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Çıkış Yap
            </a>
        </div>
        <style>
            .sidebar {
                display: none !important;
            }

            .page-wrapper {
                margin-left: 0 !important;
                padding-bottom: 80px;
            }
        </style>
    <?php endif; ?>

    <div class="page-wrapper">
        <header class="top-header">
            <div style="display:flex; align-items:center;">
                <?php if ($isHigh): ?><button onclick="toggleM()"
                        style="background:none; border:none; color:var(--text-main); cursor:pointer;"><svg width="30" height="30"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg></button><?php endif; ?>
            </div>

            <div class="u-info">
                <span class="u-role"><?= htmlspecialchars($unvan) ?></span>
                <span
                    style="font-size:14px; color:var(--text-muted); font-weight:500; margin-left: 10px;">Hoşgeldiniz,
                    <b style="color:var(--text-main);"><?= htmlspecialchars($kullaniciAdi) ?></b></span>
                
                <div class="theme-toggle js-theme-toggle" title="Tema Değiştir" style="margin-left: 10px;">
                    <svg class="sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </div>

                <div style="width:32px; height:32px; background:var(--accent); border-radius:10px; margin-left: 15px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:800;"><?= mb_substr($kullaniciAdi, 0, 1) ?></div>
            </div>
        </header>

        <main class="main-body">
            <div class="page-header-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:50px; gap:16px;">
                <h2 style="font-size:38px; font-weight:800; letter-spacing:-1.5px; white-space:nowrap;">Duyuru Akışı</h2>
                <div class="header-btn-row" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                    <?php 
                    $ayP = isset($_GET['ay']) ? '&ay='.htmlspecialchars($_GET['ay']) : '';
                    $yilP = isset($_GET['yil']) ? '&yil='.htmlspecialchars($_GET['yil']) : '';
                    ?>
                    <?php if ($isHigh): ?>
                        <a href="/isg/index.php?url=duyurular<?= $ayP . $yilP ?>"
                            style="background:rgba(59, 130, 246, 0.1); color:#3b82f6; border: 1.5px solid rgba(59, 130, 246, 0.2); padding:10px 16px; border-radius:14px; font-weight:800; font-size:12px; cursor:pointer; transition:0.3s; display:flex; align-items:center; gap:6px; text-decoration:none;"
                            title="Tüm Duyurular">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                            </svg>
                            TÜMÜ
                        </a>
                        <div class="btn-split"
                            style="background:rgba(245, 158, 11, 0.1); color:#f59e0b; border-color:rgba(245, 158, 11, 0.2);">
                            <a href="/isg/index.php?url=duyurular&tip=cozulmemis<?= $ayP . $yilP ?>" class="s-main" title="Çözülmeyenleri Gör">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                ÇÖZÜLMEYENLER
                            </a>
                            <a href="/isg/index.php?url=duyurular/pdf_indir&tip=cozulmemis<?= $ayP . $yilP ?>" target="_blank" class="s-pdf"
                                title="Çözülmemiş PDF İndir">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </a>
                        </div>

                        <div class="btn-split"
                            style="background:rgba(16, 185, 129, 0.1); color:#10b981; border-color:rgba(16, 185, 129, 0.2);">
                            <a href="/isg/index.php?url=duyurular&tip=cozulmus<?= $ayP . $yilP ?>" class="s-main" title="Çözülenleri Gör">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                ÇÖZÜLENLER
                            </a>
                            <a href="/isg/index.php?url=duyurular/pdf_indir&tip=cozulmus<?= $ayP . $yilP ?>" target="_blank" class="s-pdf"
                                title="Çözülmüş PDF İndir">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </a>
                        </div>
                        <button onclick="document.getElementById('sys_clean_m').style.display='flex'"
                            style="background:rgba(239, 68, 68, 0.1); color:#ef4444; border: 1.5px solid rgba(239, 68, 68, 0.2); padding:10px 16px; border-radius:14px; font-weight:800; font-size:12px; cursor:pointer; transition:0.3s; display:flex; align-items:center; gap:6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6V20a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                            </svg>
                            SİSTEMİ TEMİZLE
                        </button>
                    <?php endif; ?>
                    <?php if ($isHigh): ?>
                        <a href="/isg/index.php?url=duyurular/ekle"
                            style="background:#fff; color:#000; padding:10px 22px; border-radius:14px; text-decoration:none; font-weight:800; font-size:12px; box-shadow:0 15px 35px rgba(255,255,255,0.1); display:flex; align-items:center; gap:4px;">
                            YENİ DUYURU +
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            $secilenYil  = isset($_GET['yil']) && is_numeric($_GET['yil']) ? (int)$_GET['yil'] : (int)date('Y');
            $tipParam    = isset($_GET['tip']) ? '&tip='.htmlspecialchars($_GET['tip']) : '';
            $ayIsimleri  = ['01'=>'Ocak','02'=>'Şubat','03'=>'Mart','04'=>'Nisan',
                            '05'=>'Mayıs','06'=>'Haziran','07'=>'Temmuz','08'=>'Ağustos',
                            '09'=>'Eylül','10'=>'Ekim','11'=>'Kasım','12'=>'Aralık'];
            $aktifAyAdi  = $secilenAy ? ($ayIsimleri[substr($secilenAy,5)] ?? '').' '.$secilenYil : 'Ay Seç';
            ?>
            <style>
                .ay-dropdown { position:relative; display:inline-block; margin-bottom:28px; }
                .ay-dd-btn {
                    display:inline-flex; align-items:center; gap:8px;
                    padding:10px 18px; border-radius:14px;
                    background:var(--bg-card); border:1.5px solid <?= $secilenAy ? 'var(--accent)' : 'var(--border-color)' ?>;
                    color:<?= $secilenAy ? 'var(--accent)' : 'var(--text-main)' ?>;
                    font-size:12px; font-weight:800; cursor:pointer; transition:0.2s;
                    box-shadow:var(--shadow-card);
                }
                .ay-dd-btn:hover { border-color:var(--accent); color:var(--accent); }
                /* Panel is now FIXED via JS so it never overlaps cards */
                .ay-dd-panel {
                    display:none; position:fixed; z-index:9999;
                    background:var(--bg-sidebar); border:1.5px solid var(--border-color);
                    border-radius:18px; padding:18px; box-shadow:0 25px 70px rgba(0,0,0,0.4);
                    width:280px;
                }
                .ay-dd-panel.open { display:block; animation:ddIn 0.15s ease; }
                @keyframes ddIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:none} }
                .ay-dd-yil { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid var(--border-color); }
                .ay-dd-yil-btn { display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:8px; border:1px solid var(--border-color); background:var(--bg-item); color:var(--text-main); text-decoration:none; transition:0.2s; }
                .ay-dd-yil-btn:hover { background:var(--accent); color:#fff; border-color:var(--accent); }
                .ay-dd-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:7px; }
                .ay-dd-item { display:flex; align-items:center; justify-content:center; padding:10px 4px; border-radius:10px; font-size:12px; font-weight:800; text-decoration:none; text-align:center; transition:0.2s; border:1.5px solid var(--border-color); background:var(--bg-item); color:var(--text-main); }
                .ay-dd-item:hover { border-color:var(--accent); color:var(--accent); background:rgba(59,130,246,0.08); }
                .ay-dd-item.active { background:var(--accent); color:#fff !important; border-color:var(--accent); }
                .ay-dd-item.disabled { opacity:0.3; pointer-events:none; }
            </style>

            <div class="ay-dropdown" id="ayDropdown">
                <div class="ay-dd-btn" onclick="toggleAyDD()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?= htmlspecialchars($aktifAyAdi) ?>
                    <?php if ($secilenAy): ?>
                    <a href="/isg/index.php?url=duyurular&yil=<?= $secilenYil ?><?= $tipParam ?>" onclick="event.stopPropagation();" style="margin-left:4px; color:var(--text-muted); display:flex;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </a>
                    <?php else: ?>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    <?php endif; ?>
                </div>

                <div class="ay-dd-panel" id="ayPanel">
                    <!-- Yıl nav -->
                    <div class="ay-dd-yil">
                        <a href="/isg/index.php?url=duyurular&yil=<?= $secilenYil-1 ?><?= $tipParam ?>" class="ay-dd-yil-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                        <span style="font-size:14px; font-weight:800; color:var(--text-main);"><?= $secilenYil ?></span>
                        <?php if ($secilenYil < (int)date('Y')): ?>
                        <a href="/isg/index.php?url=duyurular&yil=<?= $secilenYil+1 ?><?= $tipParam ?>" class="ay-dd-yil-btn">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                        <?php else: ?><div style="width:28px;"></div><?php endif; ?>
                    </div>
                    <!-- Ay grid 3 sütun -->
                    <div class="ay-dd-grid">
                        <?php foreach ($ayIsimleri as $ayNo => $ayAd):
                            $ayKodu   = $secilenYil.'-'.$ayNo;
                            $isActive = ($secilenAy === $ayKodu);
                            $isFuture = ($ayKodu > date('Y-m'));
                            $href     = $isActive
                                ? '/isg/index.php?url=duyurular&yil='.$secilenYil.$tipParam
                                : '/isg/index.php?url=duyurular&ay='.$ayKodu.'&yil='.$secilenYil.$tipParam;
                        ?>
                        <a href="<?= $href ?>"
                           class="ay-dd-item<?= $isActive?' active':'' ?><?= $isFuture?' disabled':'' ?>">
                            <?= $ayAd ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <script>
            function toggleAyDD() {
                const panel = document.getElementById('ayPanel');
                const btn   = document.getElementById('ayDropdown');
                if (panel.classList.contains('open')) {
                    panel.classList.remove('open');
                    return;
                }
                // Position panel below the button using fixed coords
                const rect = btn.getBoundingClientRect();
                panel.style.top  = (rect.bottom + 8) + 'px';
                panel.style.left = rect.left + 'px';
                // Ensure it doesn't go off screen right
                const panelW = 280;
                if (rect.left + panelW > window.innerWidth) {
                    panel.style.left = (window.innerWidth - panelW - 16) + 'px';
                }
                panel.classList.add('open');
            }
            document.addEventListener('click', function(e) {
                const dd = document.getElementById('ayDropdown');
                const panel = document.getElementById('ayPanel');
                if (!dd.contains(e.target) && !panel.contains(e.target)) {
                    panel.classList.remove('open');
                }
            });
            </script>

            <div class="duyuru-list" id="duyuru-list">
                <?php foreach ($islenenDuyurular as $d): ?>
                    <?php
                    $res = ($d['is_hazard'] == 0);
                    $saatFarki = floor((time() - strtotime($d['tarih'])) / 3600);
                    if ($res) {
                        $statusClass = 'status-resolved';
                    } else {
                        $statusClass = ($saatFarki >= 32) ? 'status-critical' : 'status-pending';
                    }
                    $danger = $d['danger_level'] ?? 'Düşük';
                    $dangerClass = ($danger == 'Yüksek' && !$res) ? 'pulse-high' : '';
                    ?>
                    <div class="p-card <?= $statusClass ?>" id="duyuru-<?= $d['id'] ?>" data-solved="<?= $res ? 'true' : 'false' ?>"
                        onclick="location.href='/isg/index.php?url=duyurular/okundu_yap&id=<?= $d['id'] ?>'">

                        <?php if (!empty($d['resimler'])): 
                            $details = [
                                'baslik' => $d['baslik'],
                                'tarih' => $d['tarih'],
                                'kategori' => $d['kategori'],
                                'icerik' => $d['icerik'],
                                'depts' => array_map(function($dept) { 
                                    return ['name' => trim($dept), 'color' => getDeptColor($dept)]; 
                                }, explode(', ', $d['departman'])),
                                'res' => $res,
                                'gun_farki' => $d['gun_farki']
                            ];
                        ?>
                            <div class="card-thumb"
                                onclick="event.stopPropagation(); openGallery(<?= htmlspecialchars(json_encode($d['resimler'])) ?>, <?= htmlspecialchars(json_encode($details)) ?>)">
                                <img src="/isg/<?= htmlspecialchars($d['resimler'][0]) ?>" alt="" loading="lazy">
                                <?php if (count($d['resimler']) > 1): ?>
                                    <div class="card-thumb-count">+<?= count($d['resimler']) - 1 ?> GÖRSEL</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="card-top-row">
                                <div>
                                    <?php if ($res): ?>
                                        <div class="solved-b"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="4">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg> ÇÖZÜLDÜ</div>
                                    <?php elseif($saatFarki >= 32): ?>
                                        <div class="critical-b"><span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block;"></span> GECİKMİŞ</div>
                                    <?php else: ?>
                                        <div class="pending-b"><span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;"></span> BEKLEYEN</div>
                                    <?php endif; ?>
                                </div>
                                    <div class="icon-row" onclick="event.stopPropagation()">
                                        <div class="icon-btn" onclick="showReads('<?= $d['id'] ?>')" title="Görüntüleme Detayları">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            <span class="wa-badge"><?= $d['okunma_sayisi'] ?></span>
                                        </div>
                                        <a href="/isg/index.php?url=chat&share_id=<?= $d['id'] ?>" class="icon-btn" style="color:#3b82f6;" title="Sohbette Paylaş">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                            </svg>
                                        </a>
                                        <?php if ($isHigh): ?>
                                        <?php if (!$res): ?>
                                            <div class="icon-btn ok" title="Çözüldü Olarak İşaretle"
                                                onclick="showOnay('<?= $d['id'] ?>')">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        <a href="/isg/index.php?url=duyurular/duzenle&id=<?= $d['id'] ?>" class="icon-btn">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            </svg>
                                        </a>
                                        <div class="icon-btn del"
                                            onclick="window.dID='<?= $d['id'] ?>'; document.getElementById('del_m').style.display='flex'">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.5">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6V20a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-title">
                                <?php if (!$d['okundu_mu']): ?><span class="unread-dot" title="Okunmadı"></span><?php endif; ?>
                                <?= htmlspecialchars($d['baslik']) ?>
                            </div>
                            <div class="card-meta"><?= $d['tarih'] ?> &bull; <?= htmlspecialchars($d['kategori']) ?></div>

                            <div class="tag-row">
                                <?php $depts = explode(', ', $d['departman']);
                                foreach ($depts as $dept):
                                    $c = getDeptColor($dept); ?>
                                    <span class="tag-p"
                                        style="background:<?= $c ?>15; color:<?= $c ?>; border: 1.5px solid <?= $c ?>30;"><?= htmlspecialchars(trim($dept)) ?></span>
                                <?php endforeach; ?>
                            </div>

                            <p class="card-content"><?= nl2br(htmlspecialchars($d['icerik'])) ?></p>
                        </div>

                        <div class="wa-footer" style="gap: 10px;">
                            <div style="font-size:12px; color:#475569; font-weight:800; white-space: nowrap;">
                                <?= date('H:i', strtotime($d['tarih'])) ?>
                            </div>

                            <div style="flex:1; display:flex; justify-content:center;">
                                <?php 
                                $dl = $d['danger_level'] ?? 'Düşük';
                                $dl_c = $dl == 'Yüksek' ? 'danger-high' : ($dl == 'Orta' ? 'danger-medium' : 'danger-low');
                                $p_class = ($dl == 'Yüksek' && !$res) ? 'pulse-high' : '';
                                ?>
                                <span class="danger-pill <?= $dl_c ?> <?= $p_class ?>"><?= mb_strtoupper($dl) ?> TEHLİKE</span>
                            </div>

                            <?php if (!$d['okundu_mu'] && $saatFarki >= 32): ?>
                                <div style="font-size:9px; color:#ef4444; font-weight:900; letter-spacing:0.4px; text-align:center; flex:1;">
                                    DUYURU OKUMADINIZ LÜTFEN SÜRESİ GEÇMEDEN OKUYUNUZ !
                                </div>
                            <?php endif; ?>
                            <?php if ($d['okundu_mu']): ?>
                                <svg style="width:20px; stroke:#3b82f6" viewBox="0 0 24 24" fill="none" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                    <polyline points="24 6 13 17 8 12"></polyline>
                                </svg>
                            <?php else: ?>
                                <svg style="width:19px; stroke:#4b5563; opacity:0.5" viewBox="0 0 24 24" fill="none"
                                    stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($toplamDuyuru > $perPage): ?>
            <div id="load-more-wrapper" style="text-align:center; margin: 40px 0 20px;">
                <button id="load-more-btn" onclick="loadMore()"
                    style="background:rgba(59,130,246,0.1); color:#3b82f6; border:1.5px solid rgba(59,130,246,0.25); padding:14px 40px; border-radius:18px; font-weight:800; font-size:13px; cursor:pointer; transition:0.3s; letter-spacing:0.5px;">
                    DAHA FAZLA YÜKLE &nbsp;(<?= $toplamDuyuru - count($islenenDuyurular) ?> duyuru daha)
                </button>
            </div>
            <?php else: ?>
            <div id="load-more-wrapper"></div>
            <?php endif; ?>

        </main>
    </div>

    <!-- MODALS -->
    <div id="gal_m" class="modal-o" onclick="this.style.display='none'">
        <div class="modal-c" onclick="event.stopPropagation()" style="max-height:90vh; overflow-y:auto; border-radius:32px;">
            <div id="gal_list" style="display:flex; flex-direction:column; gap:20px; padding:10px;"></div>
            
            <div id="gal_details" style="padding:30px; border-top:1px solid rgba(255,255,255,0.06); margin-top:10px;">
                <!-- İçerik buraya JS ile dolacak -->
            </div>

            <button onclick="document.getElementById('gal_m').style.display='none'"
                style="margin:20px auto 40px; background:rgba(255,255,255,0.05); color:#fff; border:1px solid rgba(255,255,255,0.1); padding:16px 40px; border-radius:18px; font-weight:800; cursor:pointer; display:block; transition:0.3s;">PENCEREYİ KAPAT</button>
        </div>
    </div>
    <div id="read_m" class="modal-o">
        <div class="modal-c">
            <h3>Okunma Durumu</h3><input type="text" id="m_s"
                style="width:100%; background:#020617; border:1px solid rgba(255,255,255,0.1); padding:22px; border-radius:20px; color:#fff; margin:25px 0; outline:none; font-weight:600;"
                placeholder="Birim ara..." onkeyup="fU()">
            <div id="m_l" style="max-height:450px; overflow-y:auto;"></div><button
                onclick="document.getElementById('read_m').style.display='none'"
                style="margin-top:30px; background:none; border:none; color:var(--text-m); font-weight:800; cursor:pointer; width:100%;">KAPAT</button>
        </div>
    </div>

    <!-- APPROVE CONFIRM (Item 2) -->
    <div id="ok_m" class="modal-o">
        <div class="modal-c" style="max-width:450px; text-align:center;">
            <div
                style="width:80px; height:80px; background:rgba(59, 130, 246, 0.1); border-radius:100px; display:flex; align-items:center; justify-content:center; margin:0 auto 30px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="4"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 style="margin-bottom:15px; font-size:24px;">Çözüldü Onayı</h3>
            <p style="color:var(--text-m); margin-bottom:40px; font-weight:600; line-height:1.6;">Bu duyuruyu çözüldü
                olarak işaretleyeceksiniz, emin misiniz?</p>
            <div style="display:flex; gap:15px; justify-content:center;"><button
                    onclick="document.getElementById('ok_m').style.display='none'"
                    style="padding:16px 35px; border-radius:18px; background:#1e293b; color:#fff; border:none; cursor:pointer; font-weight:800;">VAZGEÇ</button><button
                    id="ok_confirm_btn"
                    style="padding:16px 35px; border-radius:18px; background:var(--accent); color:#fff; border:none; cursor:pointer; font-weight:800;">EVET,
                    ÇÖZÜLDÜ</button></div>
        </div>
    </div>

    <!-- SUCCESS MODAL (Item 3) -->
    <div id="success_m" class="modal-o">
        <div class="modal-c" style="max-width:400px; text-align:center;">
            <div
                style="width:100px; height:100px; background:rgba(16, 185, 129, 0.1); border-radius:100px; display:flex; align-items:center; justify-content:center; margin:0 auto 30px; animation: scaleIn 0.5s ease-out;">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="4"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 style="color:#10b981; font-size:22px; margin-bottom:10px;">Başarılı!</h3>
            <p style="color:var(--text-m); font-weight:700;">Sorun başarıyla çözüldü.</p>
        </div>
    </div>

    <div id="del_m" class="modal-o">
        <div class="modal-c" style="max-width:400px; text-align:center;">
            <h3 style="color:#ef4444; margin-bottom:20px;">Duyuruyu Sil?</h3>
            <p style="color:var(--text-m); margin-bottom:40px; font-weight:600;">Bu işlem geri alınamaz patron. Silinsin
                mi?</p>
            <div style="display:flex; gap:15px; justify-content:center;"><button
                    onclick="document.getElementById('del_m').style.display='none'"
                    style="padding:15px 30px; border-radius:18px; background:#1e293b; color:#fff; border:none; cursor:pointer; font-weight:800;">HAYIR</button><button
                    onclick="location.href='/isg/index.php?url=duyurular/sil&id='+window.dID"
                    style="padding:15px 30px; border-radius:18px; background:#ef4444; color:#fff; border:none; cursor:pointer; font-weight:800;">EVET,
                    SİL</button></div>
        </div>
    </div>

    <div id="sys_clean_m" class="modal-o">
        <div class="modal-c" style="max-width:480px; text-align:center;">
            <div
                style="width:100px; height:100px; background:rgba(239, 68, 68, 0.1); border-radius:100px; display:flex; align-items:center; justify-content:center; margin:0 auto 30px;">
                <svg width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="3"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6V20a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <h3 style="margin-bottom:15px; font-size:26px; color:#ef4444; font-weight:800;">Sistemi Sıfırla?</h3>
            <p style="color:var(--text-m); margin-bottom:30px; font-weight:600; line-height:1.6; font-size:15px;">
                Duyurular, mesajlaşmalar, raporlar ve tüm görseller kalıcı olarak silinecektir. Bu işlem <b
                    style="color:#fff;">Geri Alınamaz.</b></p>

            <div
                style="margin-bottom:35px; background:rgba(255,255,255,0.02); padding:25px; border-radius:24px; border: 1px solid rgba(255,255,255,0.05);">
                <p
                    style="font-size:12px; color:#ef4444; font-weight:800; margin-bottom:15px; text-transform:uppercase; letter-spacing:1px;">
                    Lütfen şu metni yazın:</p>
                <p style="color:#fff; font-weight:800; margin-bottom:15px; font-size:18px;">sistemi silmek istiyorum</p>
                <input type="text" id="sys_clean_input" oninput="checkSysClean(this.value)" autocomplete="off"
                    style="width:100%; background:#020617; border:1px solid rgba(255,255,255,0.1); padding:20px; border-radius:16px; color:#fff; outline:none; font-weight:700; text-align:center; font-size:16px;"
                    placeholder="...">
            </div>

            <div style="display:flex; gap:15px;">
                <button onclick="document.getElementById('sys_clean_m').style.display='none'"
                    style="flex:1; padding:20px; border-radius:20px; background:#1e293b; color:#fff; border:none; cursor:pointer; font-weight:800; font-size:14px;">VAZGEÇ</button>
                <button id="sys_clean_btn" disabled
                    onclick="location.href='/isg/index.php?url=duyurular/sistemi_temizle'"
                    style="flex:1.5; padding:20px; border-radius:20px; background:#ef4444; color:#fff; border:none; cursor:not-allowed; font-weight:800; font-size:14px; opacity:0.3; transition:0.3s;">SİSTEMİ
                    ŞİMDİ SİL</button>
            </div>
        </div>
    </div>

    <script>
        function toggleM() { document.getElementById('sb').classList.toggle('active'); document.getElementById('overlay').classList.toggle('active'); }

        // Item 2 & 3: Onay ve Başarı Mesajı
        function showOnay(aid) {
            const m = document.getElementById('ok_m');
            const btn = document.getElementById('ok_confirm_btn');
            m.style.display = 'flex';
            btn.onclick = async () => {
                m.style.display = 'none';
                // Başarı ekranını göster
                document.getElementById('success_m').style.display = 'flex';
                // 1.5 saniye sonra yönlendir
                setTimeout(() => {
                    location.href = '/isg/index.php?url=duyurular/onayla&id=' + aid;
                }, 1500);
            };
        }

        async function showReads(aid) {
            const l = document.getElementById('m_l'); l.innerHTML = 'Veriler geliyor...'; document.getElementById('read_m').style.display = 'flex';
            const r = await fetch(`/isg/index.php?url=duyurular/okunma_detay&id=${aid}`);
            const data = await r.json(); data.sort((a, b) => (a.read_at && !b.read_at ? -1 : 1)); window.uD = data; renderM(data);
        }
        function renderM(users) {
            const l = document.getElementById('m_l'); l.innerHTML = '';
            users.forEach(u => {
                const tick = u.read_at ? `<svg style="width:24px; stroke:#3b82f6" viewBox="0 0 24 24" fill="none" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline><polyline points="24 6 13 17 8 12"></polyline></svg>` : `<svg style="width:24px; stroke:#374151" viewBox="0 0 24 24" fill="none" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
                let displayTime = 'OKUNMADI';
                if (u.read_at) {
                    // Saniyeleri at (YYYY-MM-DD HH:MM:SS -> YYYY-MM-DD HH:MM)
                    displayTime = u.read_at.substring(0, 16);
                }
                const row = document.createElement('div'); row.style.display = 'flex'; row.style.justifyContent = 'space-between'; row.style.padding = '22px'; row.style.background = 'rgba(255,255,255,0.02)'; row.style.borderRadius = '20px'; row.style.marginBottom = '10px';
                row.innerHTML = `<div><div style="font-weight:800; font-size:17px;">${u.kuladsoyad}</div><div style="font-size:12px; color:var(--accent); font-weight:700; font-style:italic; margin-top:3px; opacity:0.8;">${displayTime}</div></div><div>${tick}</div>`;
                l.appendChild(row);
            });
        }
        function fU() { const t = document.getElementById('m_s').value.toLowerCase(); renderM(window.uD.filter(u => u.kuladsoyad.toLowerCase().includes(t))); }
        function openGallery(imgs, d) {
            const l = document.getElementById('gal_list'); l.innerHTML = '';
            imgs.forEach(src => {
                const img = document.createElement('img'); img.src = '/isg/' + src; img.style.width = '100%'; img.style.borderRadius = '20px'; img.style.border = '2px solid rgba(255,255,255,0.05)'; img.style.cursor = 'zoom-in';
                img.onclick = () => { if (img.requestFullscreen) img.requestFullscreen(); else window.open(img.src, '_blank'); };
                l.appendChild(img);
            });

            // Detayları doldur
            const det = document.getElementById('gal_details');
            let statusHtml = '';
            if (d.res) {
                statusHtml = `<div class="solved-b" style="margin-bottom:15px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"></polyline></svg> ÇÖZÜLDÜ</div>`;
            } else if (d.gun_farki >= 2) {
                statusHtml = `<div class="critical-b" style="margin-bottom:15px;"><span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block;"></span> GECİKMİŞ</div>`;
            } else {
                statusHtml = `<div class="pending-b" style="margin-bottom:15px;"><span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;"></span> BEKLEYEN</div>`;
            }

            let deptHtml = '';
            d.depts.forEach(dept => {
                deptHtml += `<span class="tag-p" style="background:${dept.color}15; color:${dept.color}; border: 1.5px solid ${dept.color}30; margin-right:5px; margin-bottom:5px; display:inline-block;">${dept.name}</span>`;
            });

            det.innerHTML = `
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                    ${statusHtml.replace('style="margin-bottom:15px;"', '')}
                    <span class="danger-pill ${d.danger_level == 'Yüksek' ? 'danger-high pulse-high' : (d.danger_level == 'Orta' ? 'danger-medium' : 'danger-low')}" style="padding:4px 12px; font-size:11px;">${d.danger_level.toUpperCase()} TEHLİKE</span>
                </div>
                <div style="font-size:32px; font-weight:800; color:#fff; line-height:1.2; margin-bottom:10px;">${d.baslik}</div>
                <div style="font-size:14px; color:var(--text-m); font-weight:700; margin-bottom:20px;">${d.tarih} &bull; ${d.kategori}</div>
                <div style="margin-bottom:25px;">${deptHtml}</div>
                <div style="font-size:16px; color:#cbd5e1; line-height:1.8; white-space: pre-wrap; background:rgba(255,255,255,0.02); padding:25px; border-radius:24px; border:1px solid rgba(255,255,255,0.05);">${d.icerik}</div>
            `;

            document.getElementById('gal_m').style.display = 'flex';
        }

        function checkSysClean(val) {
            const btn = document.getElementById('sys_clean_btn');
            if (val.trim().toLowerCase() === 'sistemi silmek istiyorum') {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                btn.style.boxShadow = '0 10px 30px rgba(239, 68, 68, 0.4)';
            } else {
                btn.disabled = true;
                btn.style.opacity = '0.3';
                btn.style.cursor = 'not-allowed';
                btn.style.boxShadow = 'none';
            }
        }

        // PDF artık yeni sayfada açılıyor: /duyurular/pdf_indir?tip=cozulmemis veya cozulmus

        // === DAHA FAZLA YÜKLE (AJAX Pagination) ===
        let currentPage = 1;
        const tipParam = new URLSearchParams(window.location.search).get('tip') || '';
        const ayParam  = new URLSearchParams(window.location.search).get('ay')  || '';
        let isLoading = false;

        // Sayfa yüklendiğinde focus_id varsa oraya git
        window.addEventListener('load', () => {
            const params = new URLSearchParams(window.location.search);
            const focusId = params.get('focus_id');
            if(focusId) {
                // DOM'un tamamen yerleşmesi için hafif bir gecikme
                setTimeout(() => {
                    const el = document.getElementById('duyuru-' + focusId);
                    if(el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.classList.add('highlight-focus');
                        // Vurgu efekti biraz daha uzun kalsın (6 saniye)
                        setTimeout(() => { el.classList.remove('highlight-focus'); }, 6000);
                    }
                }, 300);
            }
        });

        async function loadMore() {
            if (isLoading) return;
            isLoading = true;
            const btn = document.getElementById('load-more-btn');
            if (btn) { btn.textContent = 'Yükleniyor...'; btn.style.opacity='0.6'; }

            currentPage++;
            const url = `/isg/index.php?url=duyurular/ajax_load&sayfa=${currentPage}&tip=${tipParam}&ay=${ayParam}`;
            try {
                const res = await fetch(url);
                const data = await res.json();
                const list = document.getElementById('duyuru-list');

                data.cards.forEach(html => {
                    const div = document.createElement('div');
                    div.innerHTML = html;
                    const card = div.firstElementChild;
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'opacity 0.4s, transform 0.4s';
                    list.appendChild(card);
                    requestAnimationFrame(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    });
                });

                const wrapper = document.getElementById('load-more-wrapper');
                if (data.hasMore) {
                    btn.textContent = `DAHA FAZLA YÜKLE \u00a0(${data.remaining} duyuru daha)`;
                    btn.style.opacity = '1';
                } else {
                    wrapper.innerHTML = '<p style="color:#475569; font-weight:700; text-align:center; padding:20px;">Tüm duyurular yüklendi.</p>';
                }
            } catch(e) {
                if (btn) { btn.textContent = 'Hata oluştu. Tekrar dene.'; btn.style.opacity='1'; }
            }
            isLoading = false;
        }
    </script>
    <style>
        @keyframes scaleIn {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</body>

</html>