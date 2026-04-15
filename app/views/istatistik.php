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
    <title><?= $sayfaBasligi ?></title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 32px;
            padding: 40px;
            position: relative;
            box-shadow: var(--shadow-card);
        }

        .chart-container {
            position: relative;
            width: 280px;
            height: 280px;
            margin: 0 auto 20px;
        }

        .chart-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .center-perc {
            font-size: 42px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
            display: block;
        }

        .center-lbl {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-m);
            text-transform: uppercase;
            margin-top: 5px;
            display: block;
        }

        .num-box {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }

        .num-item {
            display: flex;
            justify-content: space-between;
            padding: 22px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .num-val {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
        }

        .num-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-m);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .search-box {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 18px 25px;
            color: var(--text-main);
            width: 350px;
            outline: none;
            font-weight: 600;
            font-size: 15px;
            box-shadow: var(--shadow-card);
        }

        .filter-tabs {
            display: flex;
            gap: 12px;
        }

        .tab-btn {
            padding: 12px 24px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(255, 255, 255, 0.02);
            color: var(--text-m);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s;
        }

        .tab-btn.active {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            transform: scale(1.05);
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .data-table th {
            text-align: left;
            padding: 0 20px;
            color: var(--text-m);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .data-row {
            background: rgba(255, 255, 255, 0.02);
            transition: 0.2s;
            cursor: pointer;
        }

        .data-row td {
            padding: 22px;
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-style: solid none;
        }

        .data-row td:first-child {
            border-left-style: solid;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .data-row td:last-child {
            border-right-style: solid;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .badge {
            padding: 7px 16px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.ok {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .badge.err {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .data-row:hover {
            background: rgba(255, 255, 255, 0.04);
            transform: translateX(3px);
        }

        .dept-card:hover {
            transform: translateY(-5px);
            border-color: rgba(16, 185, 129, 0.2);
            background: rgba(30, 41, 59, 0.4);
        }

        /* —— MODAL (Duyurular kart tasarımı) —— */
        .detail-modal-o {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.92);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(20px);
        }

        .detail-modal-o.open {
            display: flex;
        }

        .detail-modal-c {
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 36px;
            width: 96%;
            max-width: 860px;
            max-height: 92vh;
            overflow-y: auto;
            position: relative;
            animation: modalIn 0.3s cubic-bezier(.34, 1.56, .64, 1);
        }

        @keyframes modalIn {
            from {
                transform: translateY(40px) scale(0.96);
                opacity: 0;
            }

            to {
                transform: none;
                opacity: 1;
            }
        }

        .dm-close {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
            display: flex;
            justify-content: flex-end;
            padding: 20px 24px 0;
        }

        .dm-close-btn {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #94a3b8;
            font-size: 20px;
            transition: 0.2s;
        }

        .dm-close-btn:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }

        /* Galeri */
        .dm-gallery {
            width: 100%;
            max-height: 320px;
            overflow: hidden;
            position: relative;
            border-radius: 0;
        }

        .dm-gallery img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            display: block;
            filter: brightness(0.9);
        }

        .dm-gallery-count {
            position: absolute;
            bottom: 14px;
            right: 14px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            font-size: 12px;
            font-weight: 800;
            color: #fff;
            cursor: pointer;
        }

        /* Kart body */
        .dm-body {
            padding: 30px 40px 40px;
        }

        .dm-status {
            margin-bottom: 18px;
        }

        .dm-solved {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .dm-open {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .dm-title {
            font-size: 30px;
            font-weight: 900;
            line-height: 1.25;
            color: #fff;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .dm-meta {
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .dm-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 22px;
        }

        .dm-tag {
            font-size: 10px;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dm-content {
            font-size: 15px;
            font-weight: 400;
            color: #94a3b8;
            line-height: 1.8;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 28px;
            white-space: pre-wrap;
        }

        /* Navigasyon */
        .dm-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dm-nav-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 13px 22px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s;
        }

        .dm-nav-btn:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #fff;
            border-color: rgba(59, 130, 246, 0.3);
        }

        .dm-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .dm-counter {
            font-size: 13px;
            font-weight: 800;
            color: #475569;
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

        @media (max-width: 768px) {
            .main-body {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .glass-card {
                padding: 25px;
                overflow-x: auto;
            }

            .list-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }

            .filter-tabs {
                flex-wrap: wrap;
            }

            .data-table {
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

            .ay-dropdown { width: 100%; margin-top: 10px; }
            .ay-dd-btn { width: 100%; justify-content: space-between; }
            .ay-dd-panel { width: 100%; left: 0; right: 0; }
            [style*="justify-content:space-between; align-items:center"], 
            [style*="justify-content:space-between; align-items:flex-end"] { flex-direction: column !important; align-items: flex-start !important; gap: 15px !important; }
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

                <li class="has-submenu open" id="menu-perf">
                    <a href="/isg/index.php?url=duyurular/istatistik" class="nav-link <?= !$secilenAy ? 'active' : '' ?>">
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
                    <ul class="nav-submenu open">
                        <li><a href="/isg/index.php?url=duyurular/istatistik"
                                class="nav-sub-link <?= !$secilenAy ? 'active' : '' ?>">Genel Performans</a></li>
                        <li><a href="/isg/index.php?url=duyurular/istatistik&ay=<?= date('Y-m') ?>"
                                class="nav-sub-link <?= $secilenAy ? 'active' : '' ?>">Aylık Rapor</a></li>
                    </ul>
                </li>

                <li class="has-submenu" id="menu-dept">
                    <a href="/isg/index.php?url=duyurular/departman_istatistik" class="nav-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg>
                            Departman Analizi
                    </a>
                    <div class="submenu-toggle" onclick="toggleSub('menu-dept', event)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <ul class="nav-submenu">
                        <li><a href="/isg/index.php?url=duyurular/departman_istatistik" class="nav-sub-link">Genel
                                Analiz</a></li>
                        <li><a href="/isg/index.php?url=duyurular/departman_istatistik&ay=<?= date('Y-m') ?>"
                                class="nav-sub-link">Aylık Bölüm Bazlı</a></li>
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
                    <?= mb_substr($kullaniciAdi, 0, 1) ?></div>
            </div>
        </header>

        <main class="main-body" id="pdfContent">
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:50px; gap:20px; flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:38px; font-weight:800; letter-spacing:-1.5px;">
                        <?= $secilenAy ? 'Aylık Performans' : 'Performans Raporu' ?></h1>

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
                            color: <?= $secilenAy ? 'var(--accent)' : 'var(--text-main)' ?>;
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
                                <a href="/isg/index.php?url=duyurular/istatistik" onclick="event.stopPropagation();"
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
                                <a href="/isg/index.php?url=duyurular/istatistik&yil=<?= $secilenYil - 1 ?>"
                                    class="ay-dd-yil-btn">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5">
                                        <polyline points="15 18 9 12 15 6" />
                                    </svg>
                                </a>
                                <span
                                    style="font-size:14px; font-weight:800; color:var(--text-main);"><?= $secilenYil ?></span>
                                <a href="/isg/index.php?url=duyurular/istatistik&yil=<?= $secilenYil + 1 ?>"
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
                                    $href = '/isg/index.php?url=duyurular/istatistik&ay=' . $ayKodu;
                                    ?>
                                    <a href="<?= $href ?>" class="ay-dd-item<?= $isActive ? ' active' : '' ?>">
                                        <?= $ayAd ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="/isg/index.php?url=duyurular/pdf_istatistik<?= $secilenAy ? '&ay=' . $secilenAy : '' ?>"
                    target="_blank"
                    style="background:linear-gradient(135deg,#3b82f6,#2563eb); color:#fff; padding:15px 30px; border-radius:18px; text-decoration:none; font-weight:800; font-size:14px; box-shadow:0 15px 35px rgba(59,130,246,0.3); display:flex; align-items:center; gap:10px; transition:0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <polyline points="6 9 6 2 18 2 18 9" />
                        <path d="M6 18H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2" />
                        <rect x="6" y="14" width="12" height="8" />
                    </svg>
                    <?= $secilenAy ? 'AYLIK PDF İNDİR' : 'GENEL PDF İNDİR' ?>
                </a>
            </div>

            <div class="stats-grid">
                <div class="glass-card" style="text-align:center;">
                    <div class="chart-container">
                        <canvas id="perfChart"></canvas>
                        <div class="chart-center-text">
                            <span id="centerPercText" class="center-perc">0%</span>
                            <span class="center-lbl">ÇÖZÜM ORANI</span>
                        </div>
                    </div>
                    <div style="font-weight:800; font-size:16px; color:#fff; letter-spacing:1px; margin-top:20px;">GENEL
                        İSTATİSTİK</div>
                </div>

                <div class="glass-card num-box">
                    <div class="num-item">
                        <div>
                            <div class="num-label">TOPLAM İŞ GÜCÜ</div>
                            <div class="num-val"><?= $stats['toplam'] ?> <span
                                    style="font-size:14px; color:var(--text-m); font-weight:400;">Duyuru</span></div>
                        </div>
                        <div
                            style="width:60px; height:60px; background:rgba(59, 130, 246, 0.1); border-radius:18px; display:flex; align-items:center; justify-content:center;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--accent)"
                                stroke-width="2.5">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg></div>
                    </div>
                    <div class="num-item">
                        <div>
                            <div class="num-label">BAŞARIYLA TAMAMLANAN</div>
                            <div class="num-val" style="color:#10b981;"><?= $stats['cozulmus'] ?> <span
                                    style="font-size:14px; color:var(--text-m); font-weight:400;">İş</span></div>
                        </div>
                        <div
                            style="width:60px; height:60px; background:rgba(16, 185, 129, 0.1); border-radius:18px; display:flex; align-items:center; justify-content:center;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10b981"
                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg></div>
                    </div>
                    <div class="num-item">
                        <div>
                            <div class="num-label">AKTİF RİSKLER (ÇÖZÜLMEDİ)</div>
                            <div class="num-val" style="color:#ef4444;"><?= $stats['cozulmemis'] ?> <span
                                    style="font-size:14px; color:var(--text-m); font-weight:400;">Adet</span></div>
                        </div>
                        <div
                            style="width:60px; height:60px; background:rgba(239, 68, 68, 0.1); border-radius:18px; display:flex; align-items:center; justify-content:center;">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444"
                                stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg></div>
                    </div>
                </div>
            </div>

            <div class="glass-card">
                <div class="list-header" data-html2canvas-ignore="true">
                    <input type="text" id="srch" class="search-box" placeholder="İş adına göre süz..." onkeyup="fD()">
                    <div class="filter-tabs">
                        <button class="tab-btn active" onclick="setF('all', this)">TÜMÜ</button>
                        <button class="tab-btn" onclick="setF('1', this)">AÇIK</button>
                        <button class="tab-btn" onclick="setF('0', this)">BİTMİŞ</button>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="120">DURUM</th>
                            <th>BRANŞ / KONU</th>
                            <th>İLGİLİ BİRİM</th>
                            <th>İŞLEM TARİHİ</th>
                        </tr>
                    </thead>
                    <tbody id="l_body">
                        <?php foreach ($duyuruListesi as $it): ?>
                            <tr class="data-row" data-stat="<?= $it['is_hazard'] ?>" data-id="<?= $it['id'] ?>"
                                onclick="openModal('<?= $it['id'] ?>')" data-html2canvas-ignore="">
                                <td>
                                    <?php if ($it['is_hazard'] == 0): ?>
                                        <span class="badge ok">ÇÖZÜLDÜ</span>
                                    <?php else: ?>
                                        <span class="badge err">ÇÖZÜLMEDİ</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight:800; font-size:16px;"><?= htmlspecialchars($it['title']) ?></td>
                                <td style="font-size:13px; font-weight:700; color:var(--text-m);">
                                    <?= htmlspecialchars($it['department_tag']) ?></td>
                                <td style="font-size:12px; font-weight:800; color:#475569;">
                                    <?= date('d.m.Y H:i', strtotime($it['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- —— DETAY MODAL (Duyurular kart tasarımı) —— -->
    <div class="detail-modal-o" id="detailModal" onclick="handleOverlayClick(event)">
        <div class="detail-modal-c" onclick="event.stopPropagation()">
            <div class="dm-close">
                <button class="dm-close-btn" onclick="closeModal()">✕</button>
            </div>

            <div id="dmGallery" class="dm-gallery" style="display:none" onclick="openGalleryModal()">
                <img id="dmThumb" src="" alt="">
                <div id="dmGalCount" class="dm-gallery-count" style="display:none"></div>
            </div>

            <div class="dm-body">
                <div class="dm-status" id="dmStatus"></div>
                <div class="dm-title" id="dmTitle"></div>
                <div class="dm-meta" id="dmMeta"></div>
                <div class="dm-tags" id="dmTags"></div>
                <div class="dm-content" id="dmContent"></div>

                <div class="dm-nav">
                    <button class="dm-nav-btn" id="btnPrev" onclick="navModal(-1)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                        Önceki
                    </button>
                    <span class="dm-counter" id="mCounter"></span>
                    <button class="dm-nav-btn" id="btnNext" onclick="navModal(1)">
                        Sonraki
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="gal_m"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.98); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(25px);"
        onclick="this.style.display='none'">
        <div style="background:#0f172a; border:1px solid rgba(255,255,255,0.1); border-radius:40px; width:95%; max-width:900px; padding:50px; position:relative;"
            onclick="event.stopPropagation()">
            <h2 style="margin-bottom:30px; text-align:center;">Galeri</h2>
            <div id="gal_list"
                style="display:flex; flex-direction:column; gap:30px; max-height:65vh; overflow-y:auto; padding:10px;">
            </div>
            <button onclick="document.getElementById('gal_m').style.display='none'"
                style="margin:30px auto 0; background:#fff; color:#000; border:none; padding:16px 50px; border-radius:20px; font-weight:800; cursor:pointer; display:block;">KAPAT</button>
        </div>
    </div>

    <script>
        function toggleM() { document.getElementById('sb').classList.toggle('active'); document.getElementById('overlay').classList.toggle('active'); }

        const c = <?= $stats['cozulmus'] ?>;
        const u = <?= $stats['cozulmemis'] ?>;
        const total = <?= $stats['toplam'] ?>;
        const perc = total > 0 ? Math.round((c / total) * 100) : 0;

        document.getElementById('centerPercText').innerText = perc + '%';

        new Chart(document.getElementById('perfChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Bitmiş İşler', 'Açık Riskler'],
                datasets: [{
                    data: [c, u],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderColor: 'transparent',
                    borderWidth: 0,
                    hoverOffset: 15,
                    borderRadius: (total > 0 && u > 0 && c > 0) ? 20 : 0
                }]
            },
            options: {
                cutout: '82%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#0f172a',
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        padding: 15,
                        displayColors: true,
                        callbacks: {
                            label: (ctx) => {
                                let v = ctx.raw;
                                let p = total > 0 ? ((v / total) * 100).toFixed(1) : 0;
                                return ` ${ctx.label}: ${v} Adet (${p}%)`;
                            }
                        }
                    }
                }
            }
        });

        let curFilter = 'all';
        function setF(val, btn) {
            curFilter = val;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            fD();
        }
        function fD() {
            const s = document.getElementById('srch').value.toLowerCase();
            document.querySelectorAll('.data-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                const stat = row.getAttribute('data-stat');
                const okS = (curFilter === 'all' || stat === curFilter);
                const okT = text.includes(s);
                row.style.display = (okS && okT) ? '' : 'none';
            });
        }

        var deptColorMap = {
            'Kalite': '#0ea5e9', 'Üretim': '#10b981', 'Lojistik': '#f59e0b', 'IT': '#3b82f6',
            'OT': '#6366f1', 'Bakım': '#eab308', 'İSG': '#ef4444', 'İdari İşler': '#8b5cf6', 'İK': '#ec4899'
        };
        function getDeptColor(d) { return deptColorMap[d.trim()] || '#3b82f6'; }

        var dmAllImgs = [];

        const duyurular = <?= json_encode(array_values($duyuruListesi), JSON_UNESCAPED_UNICODE) ?>;
        const idxMap = {};
        duyurular.forEach(function (d, i) { idxMap[d.id] = i; });

        var visibleIds = [];
        var currentVisIdx = 0;

        function getVisibleIds() {
            var ids = [];
            document.querySelectorAll('.data-row').forEach(function (row) {
                if (row.style.display !== 'none') {
                    ids.push(row.getAttribute('data-id'));
                }
            });
            return ids;
        }

        function openModal(id) {
            visibleIds = getVisibleIds();
            currentVisIdx = visibleIds.indexOf(String(id));
            if (currentVisIdx === -1) currentVisIdx = 0;
            renderModal(visibleIds[currentVisIdx]);
            document.getElementById('detailModal').classList.add('open');
            document.getElementById('detailModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function renderModal(id) {
            var d = duyurular[idxMap[id]];
            if (!d) return;

            var imgs = d.tum_resimler ? d.tum_resimler.split('|').filter(Boolean) : [];
            dmAllImgs = imgs;
            var gal = document.getElementById('dmGallery');
            if (imgs.length > 0) {
                gal.style.display = 'block';
                document.getElementById('dmThumb').src = '/isg/' + imgs[0];
                var cnt = document.getElementById('dmGalCount');
                if (imgs.length > 1) { cnt.style.display = 'block'; cnt.textContent = '+' + (imgs.length - 1) + ' GÖRSEL'; }
                else { cnt.style.display = 'none'; }
            } else {
                gal.style.display = 'none';
            }

            var statusEl = document.getElementById('dmStatus');
            if (d.is_hazard == 0) {
                statusEl.innerHTML = '<span class="dm-solved"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg> ÇÖZÜLDÜ</span>';
            } else {
                statusEl.innerHTML = '<span class="dm-open"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> AÇIK RİSK</span>';
            }

            document.getElementById('dmTitle').textContent = d.title || '—';

            var dt = new Date(d.created_at.replace(' ', 'T'));
            var tarihStr = dt.toLocaleDateString('tr-TR', { day: '2-digit', month: 'long', year: 'numeric' }) +
                ' • ' + dt.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('dmMeta').textContent = tarihStr + (d.hazard_category ? ' • ' + d.hazard_category : '');

            var tagsEl = document.getElementById('dmTags');
            tagsEl.innerHTML = '';
            var depts = d.department_tag ? d.department_tag.split(', ') : [];
            depts.forEach(function (dep) {
                var col = getDeptColor(dep);
                var span = document.createElement('span');
                span.className = 'dm-tag';
                span.style.background = col + '15';
                span.style.color = col;
                span.style.border = '1.5px solid ' + col + '30';
                span.textContent = dep.trim();
                tagsEl.appendChild(span);
            });

            document.getElementById('dmContent').textContent = d.content || 'Açıklama girilmemiş.';

            document.getElementById('mCounter').textContent = (currentVisIdx + 1) + ' / ' + visibleIds.length;
            document.getElementById('btnPrev').disabled = (currentVisIdx <= 0);
            document.getElementById('btnNext').disabled = (currentVisIdx >= visibleIds.length - 1);
        }

        function navModal(dir) {
            var newIdx = currentVisIdx + dir;
            if (newIdx < 0 || newIdx >= visibleIds.length) return;
            currentVisIdx = newIdx;
            renderModal(visibleIds[currentVisIdx]);
        }

        function closeModal() {
            document.getElementById('detailModal').classList.remove('open');
            document.getElementById('detailModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        function handleOverlayClick(e) {
            if (e.target === document.getElementById('detailModal')) closeModal();
        }

        function openGalleryModal() {
            var l = document.getElementById('gal_list'); l.innerHTML = '';
            dmAllImgs.forEach(function (src) {
                var img = document.createElement('img');
                img.src = '/isg/' + src;
                img.style.width = '100%'; img.style.borderRadius = '20px';
                img.style.border = '2px solid rgba(255,255,255,0.05)';
                img.style.cursor = 'zoom-in';
                img.onclick = function () { if (img.requestFullscreen) img.requestFullscreen(); else window.open(img.src, '_blank'); };
                l.appendChild(img);
            });
            document.getElementById('gal_m').style.display = 'flex';
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
            if (e.key === 'ArrowLeft') navModal(-1);
            if (e.key === 'ArrowRight') navModal(1);
        });

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