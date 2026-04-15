<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sayfaBasligi ?></title>
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

        /* USER LIST TABLE */
        .user-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .user-table th {
            text-align: left;
            padding: 0 25px;
            color: var(--text-m);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .user-row {
            background: rgba(255, 255, 255, 0.015);
            transition: 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-left: 6px solid transparent;
        }

        .user-row:hover {
            background: rgba(59, 130, 246, 0.08);
            transform: translateX(12px) scale(1.01);
            border-left: 6px solid var(--accent);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            z-index: 10;
            position: relative;
        }

        .user-row td {
            padding: 22px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
        }

        .user-row td:first-child {
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .user-row td:last-child {
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .avatar {
            width: 45px;
            height: 45px;
            background: var(--accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            color: #fff;
        }

        .u-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-main);
        }

        .u-mail {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .role-badge {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .role-1 {
            background: rgba(148, 163, 184, 0.1);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        /* Personel */
        .role-2 {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* İSG Uzmanı */
        .role-3 {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Operatör */
        .role-4 {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* Yönetici */
        .role-5 {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        /* Admin */

        .search-container {
            position: relative;
            width: 400px;
            margin-bottom: 35px;
        }

        .search-input {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 20px 25px;
            color: var(--text-main);
            outline: none;
            font-weight: 600;
            font-size: 15px;
            box-shadow: var(--shadow-card);
        }

        .search-input:focus {
            border-color: var(--accent);
            background: rgba(15, 23, 42, 0.8);
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

            .glass-card {
                padding: 20px;
                overflow-x: auto;
            }

            .user-table {
                min-width: 600px;
            }

            .search-container {
                width: 100%;
                margin-bottom: 20px;
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
        }
    </style>
    <?php
    $uRole = $_SESSION['role'] ?? 1;
    $isHigh = in_array($uRole, [2, 4, 5]);
    $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
    ?>
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
                <li><a href="/isg/index.php?url=kullanici" class="nav-link active"><svg width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg> Kullanıcı Paneli</a></li>
                <li><a href="/isg/index.php?url=duyurular" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5">
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
        <header class="top-header">
            <div style="display:flex; align-items:center;">
                <?php if ($isHigh): ?><button onclick="toggleM()"
                        style="background:none; border:none; color:var(--text-main); cursor:pointer;"><svg width="30"
                            height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
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

        <main class="main-body">
            <h1 style="font-size:38px; font-weight:800; letter-spacing:-1.5px; margin-bottom:45px;">Kurumsal Üye Rehberi
            </h1>

            <div class="search-container">
                <input type="text" id="userSearch" class="search-input" placeholder="İsim veya e-posta ile ara..."
                    onkeyup="filterUsers()">
            </div>

            <div class="glass-card">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th width="80">PROFİL</th>
                            <th>KULLANICI BİLGİSİ</th>
                            <th width="200">SİSTEM ROLÜ</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <?php
                        $roleNameMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
                        foreach ($kullanicilar as $u):
                            $r = $u['role'] ?? 1;
                            $name = $u['kuladsoyad'] ?? $u['kulad'] ?? 'İsimsiz Üye';
                            $initial = mb_substr($name, 0, 1, 'UTF-8');
                            ?>
                            <tr class="user-row">
                                <td>
                                    <div class="avatar"><?= $initial ?></div>
                                </td>
                                <td>
                                    <div class="u-name"><?= htmlspecialchars($name) ?></div>
                                    <div class="u-mail"><?= htmlspecialchars($u['kulmail']) ?></div>
                                </td>
                                <td>
                                    <span class="role-badge role-<?= $r ?>"><?= $roleNameMap[$r] ?? 'Bilinmiyor' ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function toggleM() { document.getElementById('sb').classList.toggle('active'); document.getElementById('overlay').classList.toggle('active'); }

        function toggleSub(id, e) {
            if(e) e.preventDefault();
            const el = document.getElementById(id);
            const sub = el.querySelector('.nav-submenu');
            el.classList.toggle('open');
            sub.classList.toggle('open');
        }

        function filterUsers() {
            const val = document.getElementById('userSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.user-row');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(val) ? '' : 'none';
            });
        }
    </script>
</body>

</html>