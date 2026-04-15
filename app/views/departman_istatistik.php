<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
$uRole = (int) ($_SESSION['role'] ?? 1);
$isHigh = in_array($uRole, [2, 4, 5]);
$kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departman Bazlı Performans - MAISG</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="/isg/assets/theme-engine.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .nav-link:hover {
            color: var(--text-main);
            background: var(--border-color);
        }

        .nav-link.active {
            color: #fff;
            background: var(--accent);
        }

        .nav-submenu {
            list-style: none;
            padding-left: 55px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
        }

        .nav-submenu.open {
            max-height: 200px;
            opacity: 1;
            padding-bottom: 10px;
        }

        .nav-sub-link {
            display: block;
            padding: 10px 0;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
            position: relative;
        }

        .nav-sub-link:hover {
            color: var(--text-main);
        }

        .nav-sub-link.active {
            color: var(--accent);
        }

        .nav-sub-link::before {
            content: '•';
            position: absolute;
            left: -15px;
            opacity: 0.5;
        }

        .has-submenu {
            position: relative;
        }

        .submenu-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            padding: 5px;
            color: var(--text-muted);
            transition: 0.3s;
            z-index: 5;
        }

        .has-submenu.open .submenu-toggle {
            transform: translateY(-50%) rotate(180deg);
            color: var(--accent);
        }

        .page-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

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
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 32px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-card);
        }

        /* TABLE */
        .stat-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .stat-table th {
            text-align: left;
            padding: 0 20px;
            color: var(--text-m);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .stat-row {
            background: rgba(255, 255, 255, 0.02);
            transition: 0.2s;
        }

        .stat-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .stat-row td {
            padding: 22px;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-style: solid none;
        }

        .stat-row td:first-child {
            border-left-style: solid;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .stat-row td:last-child {
            border-right-style: solid;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 10px;
            transition: 1s ease-in-out;
        }

        .tag-d {
            font-weight: 800;
            font-size: 16px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tag-color {
            width: 8px;
            height: 18px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .main-body {
                padding: 20px;
            }

            .glass-card {
                padding: 20px;
                overflow-x: auto;
            }

            .stat-table {
                min-width: 600px;
            }

            .top-header {
                padding: 0 15px;
            }

            .u-info span:nth-child(2) {
                display: none;
            }

            h1 {
                font-size: 28px !important;
                margin-bottom: 25px !important;
            }

            .dept-grid {
                grid-template-columns: 1fr;
                margin-top: 25px;
            }

            .dept-metrics {
                flex-direction: column;
                gap: 10px;
            }

            .ay-dropdown {
                width: 100%;
                margin-top: 10px;
            }

            .ay-dd-btn {
                width: 100%;
                justify-content: space-between;
            }

            .ay-dd-panel {
                width: 100%;
                left: 0;
                right: 0;
            }

            [style*="justify-content:space-between; align-items:center"],
            [style*="justify-content:space-between; align-items:flex-end"] {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 15px !important;
            }
        }
    </style>
</head>

<body>

    <?php if ($isHigh): ?>
        <div class="sidebar-overlay" id="overlay" onclick="toggleM()"></div>
        <aside class="sidebar" id="sb">
            <div style="padding:0 35px; margin-bottom:50px;">
                <div style="font-size:26px; font-weight:800; color:var(--text-main);">MA<span
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
                <li><a href="/isg/index.php?url=duyurular" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg> Duyuru Akışı</a></li>

                <li
                    style="margin-top:20px; margin-left:10px; font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:10px; padding-left:15px; opacity:0.5;">
                    Analiz & İstatistik</li>

                <li><a href="/isg/index.php?url=duyurular/tehlike_analizi" class="nav-link"><svg width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path
                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                            </path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg> Tehlike Analizi</a></li>

                <li class="has-submenu" id="menu-perf">
                    <a href="/isg/index.php?url=duyurular/istatistik" class="nav-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        Performans
                    </a>
                    <div class="submenu-toggle" onclick="toggleSub('menu-perf', event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <ul class="nav-submenu">
                        <li><a href="/isg/index.php?url=duyurular/istatistik" class="nav-sub-link">Genel Performans</a></li>
                        <li><a href="/isg/index.php?url=duyurular/istatistik&ay=<?= date('Y-m') ?>"
                                class="nav-sub-link">Aylık Rapor</a></li>
                    </ul>
                </li>

                <li class="has-submenu open" id="menu-dept">
                    <a href="/isg/index.php?url=duyurular/departman_istatistik"
                        class="nav-link <?= !$secilenAy ? 'active' : '' ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                            <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                        </svg>
                        Departman Analizi
                    </a>
                    <div class="submenu-toggle" onclick="toggleSub('menu-dept', event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <ul class="nav-submenu open">
                        <li><a href="/isg/index.php?url=duyurular/departman_istatistik"
                                class="nav-sub-link <?= !$secilenAy ? 'active' : '' ?>">Genel Analiz</a></li>
                        <li><a href="/isg/index.php?url=duyurular/departman_istatistik&ay=<?= date('Y-m') ?>"
                                class="nav-sub-link <?= $secilenAy ? 'active' : '' ?>">Aylık Bölüm Bazlı</a></li>
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
        <div class="bottom-nav" data-html2canvas-ignore="true">
            <a href="/isg/index.php?url=duyurular" class="b-nav-link">
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
        </style>
    <?php endif; ?>

    <div class="page-wrapper">
        <header class="top-header" data-html2canvas-ignore="true">
            <div style="display:flex; align-items:center;">
                <?php if ($isHigh): ?><button onclick="toggleM()"
                        style="background:none; border:none; color:var(--text-main); cursor:pointer;"><svg width="28"
                            height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg></button><?php else: ?>
                    <div></div><?php endif; ?>
            </div>
            <div class="u-info">
                <span class="u-role"><?= htmlspecialchars($unvan) ?></span>
                <span style="font-size:14px; color:var(--text-muted); font-weight:500; margin-left: 10px;">Hoşgeldiniz,
                    <b style="color:var(--text-main);"><?= htmlspecialchars($kullaniciAdi) ?></b></span>

                <div class="theme-toggle js-theme-toggle" title="Tema Değiştir" style="margin-left: 10px;">
                    <svg class="sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </div>

                <div
                    style="width:32px; height:32px; background:var(--accent); border-radius:10px; margin-left: 15px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:800;">
                    <?= mb_substr($kullaniciAdi, 0, 1) ?>
                </div>
            </div>
        </header>

        <main class="main-body" id="pdfContent">
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:50px; gap:20px; flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:38px; font-weight:800; letter-spacing:-1.5px;">
                        <?= $secilenAy ? 'Aylık Departman Analizi' : 'Departman Performans Analizi' ?>
                    </h1>

                    <?php
                    $secilenYil = $secilenAy ? (int) substr($secilenAy, 0, 4) : (int) date('Y');
                    $ayIsimleri = [
                        '01' => 'Ocak',
                        '02' => 'Şubat',
                        '03' => 'Mart',
                        '04' => 'Nisan',
                        '05' => 'Mayıs',
                        '06' => 'Haziran',
                        '07' => 'Temmuz',
                        '08' => 'Ağustos',
                        '09' => 'Eylül',
                        '10' => 'Ekim',
                        '11' => 'Kasım',
                        '12' => 'Aralık'
                    ];
                    $aktifAyAdi = $secilenAy ? ($ayIsimleri[substr($secilenAy, 5)] ?? '') . ' ' . $secilenYil : 'Tüm Zamanlar';
                    ?>

                    <style>
                        .ay-dropdown {
                            position: relative;
                            display: inline-block;
                            margin-top: 15px;
                        }

                        .ay-dd-btn {
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            padding: 10px 18px;
                            border-radius: 14px;
                            background: var(--bg-card);
                            border: 1.5px solid
                                <?= $secilenAy ? 'var(--accent)' : 'var(--border-color)' ?>
                            ;
                            color:
                                <?= $secilenAy ? 'var(--accent)' : 'var(--text-main)' ?>
                            ;
                            font-size: 12px;
                            font-weight: 800;
                            cursor: pointer;
                            transition: 0.2s;
                            box-shadow: var(--shadow-card);
                        }

                        .ay-dd-btn:hover {
                            border-color: var(--accent);
                            color: var(--accent);
                        }

                        .ay-dd-panel {
                            display: none;
                            position: fixed;
                            z-index: 9999;
                            background: var(--bg-sidebar);
                            border: 1.5px solid var(--border-color);
                            border-radius: 18px;
                            padding: 18px;
                            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
                            width: 280px;
                        }

                        .ay-dd-panel.open {
                            display: block;
                            animation: ddIn 0.15s ease;
                        }

                        @keyframes ddIn {
                            from {
                                opacity: 0;
                                transform: translateY(-6px)
                            }

                            to {
                                opacity: 1;
                                transform: none
                            }
                        }

                        .ay-dd-yil {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            margin-bottom: 14px;
                            padding-bottom: 12px;
                            border-bottom: 1px solid var(--border-color);
                        }

                        .ay-dd-yil-btn {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 30px;
                            height: 30px;
                            border-radius: 8px;
                            border: 1px solid var(--border-color);
                            background: var(--bg-item);
                            color: var(--text-main);
                            text-decoration: none;
                            transition: 0.2s;
                        }

                        .ay-dd-yil-btn:hover {
                            background: var(--accent);
                            color: #fff;
                            border-color: var(--accent);
                        }

                        .ay-dd-grid {
                            display: grid;
                            grid-template-columns: repeat(3, 1fr);
                            gap: 7px;
                        }

                        .ay-dd-item {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 10px 4px;
                            border-radius: 10px;
                            font-size: 12px;
                            font-weight: 800;
                            text-decoration: none;
                            text-align: center;
                            transition: 0.2s;
                            border: 1.5px solid var(--border-color);
                            background: var(--bg-item);
                            color: var(--text-main);
                        }

                        .ay-dd-item:hover {
                            border-color: var(--accent);
                            color: var(--accent);
                            background: rgba(59, 130, 246, 0.08);
                        }

                        .ay-dd-item.active {
                            background: var(--accent);
                            color: #fff !important;
                            border-color: var(--accent);
                        }

                        .ay-dd-item.disabled {
                            opacity: 0.3;
                            pointer-events: none;
                        }
                    </style>

                    <div class="ay-dropdown" id="ayDropdown">
                        <div class="ay-dd-btn" onclick="toggleAyDD()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <?= htmlspecialchars($aktifAyAdi) ?>
                            <?php if ($secilenAy): ?>
                                <a href="/isg/index.php?url=duyurular/departman_istatistik"
                                    onclick="event.stopPropagation();"
                                    style="margin-left:4px; color:var(--text-muted); display:flex;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="3">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>
                                </a>
                            <?php else: ?>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            <?php endif; ?>
                        </div>

                        <div class="ay-dd-panel" id="ayPanel">
                            <div class="ay-dd-yil">
                                <a href="/isg/index.php?url=duyurular/departman_istatistik&yil=<?= $secilenYil - 1 ?>"
                                    class="ay-dd-yil-btn">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5">
                                        <polyline points="15 18 9 12 15 6" />
                                    </svg>
                                </a>
                                <span
                                    style="font-size:14px; font-weight:800; color:var(--text-main);"><?= $secilenYil ?></span>
                                <a href="/isg/index.php?url=duyurular/departman_istatistik&yil=<?= $secilenYil + 1 ?>"
                                    class="ay-dd-yil-btn">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5">
                                        <polyline points="9 18 15 12 9 6" />
                                    </svg>
                                </a>
                            </div>
                            <div class="ay-dd-grid">
                                <?php foreach ($ayIsimleri as $ayNo => $ayAd):
                                    $ayKodu = $secilenYil . '-' . $ayNo;
                                    $isActive = ($secilenAy === $ayKodu);
                                    $href = '/isg/index.php?url=duyurular/departman_istatistik&ay=' . $ayKodu;
                                    ?>
                                    <a href="<?= $href ?>" class="ay-dd-item<?= $isActive ? ' active' : '' ?>">
                                        <?= $ayAd ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="/isg/index.php?url=duyurular/pdf_departman<?= $secilenAy ? '&ay=' . $secilenAy : '' ?>"
                    target="_blank"
                    style="background:linear-gradient(135deg,#8b5cf6,#6d28d9); color:#fff; padding:15px 30px; border-radius:18px; text-decoration:none; font-weight:800; font-size:14px; box-shadow:0 15px 35px rgba(139,92,246,0.3); display:flex; align-items:center; gap:10px; transition:0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="6 9 6 2 18 2 18 9" />
                        <path d="M6 18H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2" />
                        <rect x="6" y="14" width="12" height="8" />
                    </svg>
                    <?= $secilenAy ? 'AYLIK PDF İNDİR' : 'GENEL PDF İNDİR' ?>
                </a>
            </div>

            <!-- BAR CHART (Item 2) -->
            <div class="glass-card">
                <div style="height:400px; width:100%;">
                    <canvas id="deptChart"></canvas>
                </div>
            </div>

            <style>
                .dept-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                    gap: 25px;
                    margin-top: 40px;
                }

                .dept-card {
                    background: var(--bg-card);
                    border: 1px solid var(--border-color);
                    border-radius: 28px;
                    padding: 25px;
                    transition: 0.3s;
                }

                .dept-card:hover {
                    transform: translateY(-5px);
                    background: var(--bg-header);
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
                }

                .dept-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                }

                .dept-title {
                    font-size: 18px;
                    font-weight: 800;
                    color: var(--text-main);
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .dept-title-dot {
                    width: 8px;
                    height: 18px;
                    border-radius: 4px;
                }

                .dept-metrics {
                    display: flex;
                    gap: 15px;
                    margin-bottom: 20px;
                }

                .m-item {
                    flex: 1;
                    padding: 15px;
                    background: rgba(255, 255, 255, 0.02);
                    border-radius: 18px;
                    border: 1px solid rgba(255, 255, 255, 0.03);
                }

                .m-label {
                    font-size: 10px;
                    font-weight: 800;
                    color: var(--text-m);
                    text-transform: uppercase;
                    margin-bottom: 5px;
                }

                .m-val {
                    font-size: 20px;
                    font-weight: 800;
                    color: var(--text-main);
                }
            </style>

            <div class="dept-grid">
                <?php
                $colors = ['#0ea5e9', '#10b981', '#f59e0b', '#3b82f6', '#6366f1', '#eab308', '#ef4444', '#8b5cf6', '#ec4899'];
                $i = 0;
                foreach ($stats as $name => $data):
                    if ($data['toplam'] == 0)
                        continue;
                    $perc = round(($data['cozulmus'] / $data['toplam']) * 100);
                    $c = $colors[$i % count($colors)];
                    $i++;
                    ?>
                    <div class="dept-card">
                        <div class="dept-header">
                            <div class="dept-title">
                                <div class="dept-title-dot" style="background:<?= $c ?>;"></div>
                                <?= $name ?>
                            </div>
                            <div style="font-size:12px; font-weight:800; color:<?= $c ?>;">%<?= $perc ?> BAŞARI</div>
                        </div>

                        <div class="dept-metrics">
                            <div class="m-item">
                                <div class="m-label">ÇÖZÜLEN</div>
                                <div class="m-val" style="color:#10b981;"><?= $data['cozulmus'] ?></div>
                            </div>
                            <div class="m-item">
                                <div class="m-label">AÇIK RİSK</div>
                                <div class="m-val" style="color:#ef4444;"><?= $data['cozulmemis'] ?></div>
                            </div>
                            <div class="m-item">
                                <div class="m-label">TOPLAM</div>
                                <div class="m-val"><?= $data['toplam'] ?></div>
                            </div>
                        </div>

                        <div class="progress-bar">
                            <div class="progress-fill"
                                style="width:<?= $perc ?>%; background:<?= $c ?>; box-shadow: 0 0 15px <?= $c ?>50;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleM() { document.getElementById('sb').classList.toggle('active'); document.getElementById('overlay').classList.toggle('active'); }

        // BAR CHART
        const labels = <?= json_encode(array_keys($stats)) ?>;
        const dataResolved = <?= json_encode(array_column($stats, 'cozulmus')) ?>;
        const dataUnresolved = <?= json_encode(array_column($stats, 'cozulmemis')) ?>;

        const ctx = document.getElementById('deptChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Çözülen İşler',
                        data: dataResolved,
                        backgroundColor: '#10b981',
                        borderRadius: 10,
                        barThickness: 25
                    },
                    {
                        label: 'Çözülmeyen Riskler',
                        data: dataUnresolved,
                        backgroundColor: '#ef4444',
                        borderRadius: 10,
                        barThickness: 25
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'Outfit', weight: 'bold' } } },
                    y: { stacked: true, grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#94a3b8' } }
                },
                plugins: {
                    legend: { position: 'top', labels: { color: '#fff', font: { family: 'Outfit', weight: 'bold' } } },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 15,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 }
                    }
                },
                animation: { duration: 2000, easing: 'easeOutQuart' }
            }
        });

        // PDF artık yeni sekmede açılıyor
        function toggleAyDD() {
            const panel = document.getElementById('ayPanel');
            const btn = document.getElementById('ayDropdown');
            if (panel.classList.contains('open')) {
                panel.classList.remove('open');
                return;
            }
            const rect = btn.getBoundingClientRect();
            panel.style.top = (rect.bottom + 8) + 'px';
            panel.style.left = rect.left + 'px';
            const panelW = 280;
            if (rect.left + panelW > window.innerWidth) {
                panel.style.left = (window.innerWidth - panelW - 16) + 'px';
            }
            panel.classList.add('open');
        }
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#ayDropdown')) {
                const p = document.getElementById('ayPanel');
                if (p) p.classList.remove('open');
            }
        });

        function toggleSub(id, e) {
            if (e) e.preventDefault();
            const el = document.getElementById(id);
            const sub = el.querySelector('.nav-submenu');
            el.classList.toggle('open');
            sub.classList.toggle('open');
        }
    </script>
</body>

</html>