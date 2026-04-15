<?php 
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
$uRole = (int)($_SESSION['role'] ?? 1);
$isHigh = in_array($uRole, [2, 4, 5]);
$kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
// $unvan, $sayfaBasligi gibi değişkenler controllerdan geliyor.
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sayfaBasligi ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="/isg/assets/theme-engine.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/isg/assets/theme-engine.js"></script>
    <style>
        :root { --sidebar-width: 280px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background: var(--bg-main); color: var(--text-main); min-height: 100vh; display: flex; overflow-x: hidden; }

        .sidebar { width: var(--sidebar-width); background: var(--bg-sidebar); height: 100vh; position: fixed; left: -280px; top: 0; border-right: 1px solid var(--border-color); display: flex; flex-direction: column; padding: 40px 0; z-index: 1000; transition: 0.4s; }
        .sidebar.active { left: 0; }
        .sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 999; display: none; }
        .sidebar-overlay.active { display: block; }

        .nav-list { list-style: none; padding: 0 15px; }
        .nav-link { display: flex; align-items: center; gap: 15px; padding: 14px 25px; color: var(--text-muted); text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 12px; transition: 0.2s; }
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
        .top-header { height: 75px; padding: 0 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); background: var(--bg-header); backdrop-filter: var(--glass-blur); position: sticky; top:0; z-index:900; }
        .u-info { display: flex; align-items: center; gap: 15px; }
        .u-role { font-size: 14px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px; }

        .main-body { padding: 40px; max-width: 1400px; width: 100%; margin: 0 auto; }
        
        .hero { margin-bottom: 40px; }
        .hero h1 { font-size: 38px; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 8px; background: linear-gradient(to right, var(--text-main), var(--text-muted)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-muted); font-size: 15px; font-weight: 500; }

        /* Shortcuts Bar */
        .shortcuts-bar { display: flex; gap: 12px; margin-bottom: 40px; overflow-x: auto; padding: 10px 5px; scrollbar-width: none; }
        .shortcuts-bar::-webkit-scrollbar { display: none; }
        .s-item { padding: 12px 24px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.15); border-radius: 100px; color: var(--accent); text-decoration: none; font-size: 13px; font-weight: 700; white-space: nowrap; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
        .s-item:hover { background: var(--accent); color: #fff; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); }

        /* Metrics */
        .widget-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .w-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 28px; padding: 24px; display: flex; align-items: center; gap: 20px; text-decoration:none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; box-shadow: var(--shadow-card); }
        .w-card:hover { transform: translateY(-5px); border-color: var(--accent); background: var(--bg-card); box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
        
        .w-icon { width: 54px; height: 54px; border-radius: 18px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); transition: 0.3s; color: var(--text-main); }
        .w-card:hover .w-icon { background: var(--accent); color: #fff; border-color: transparent; }
        .w-data { display: flex; flex-direction: column; gap: 4px; }

        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: var(--bg-sidebar); backdrop-filter: var(--glass-blur); border-top: 1px solid var(--border-color); display: flex; justify-content: space-around; padding: 12px 10px; z-index: 2000; border-radius: 20px 20px 0 0; box-shadow: 0 -15px 40px rgba(0,0,0,0.3); }
        .b-nav-link { display: flex; flex-direction: column; align-items: center; gap: 4px; color: var(--text-muted); text-decoration: none; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; }
        .b-nav-link.active { color: var(--accent); transform: translateY(-3px); }
        .b-nav-link:hover { color: var(--text-main); }
    </style>
    <?php 
    $uRole = $_SESSION['role'] ?? 1;
    $isHigh = in_array($uRole, [2, 4, 5]);
    $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
    ?>
    <style>
        .w-label { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .w-val { font-size: 32px; font-weight: 800; color: var(--text-main); }

        .dash-layout { display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; }
        .glass-box { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 36px; padding: 35px; box-shadow: var(--shadow-card); }

        .son-liste { display: flex; flex-direction: column; gap: 12px; margin-top: 25px; }
        .son-item { padding: 18px 24px; background: var(--bg-item); border-radius: 22px; border: 1px solid var(--border-color); text-decoration:none; color:inherit; display:flex; justify-content:space-between; align-items:center; transition: 0.3s; }
        .son-item:hover { background: var(--bg-card); transform: translateX(10px); border-color: var(--accent); }

        .c-box { position: relative; width: 100%; height: 280px; }

        @media (max-width: 1024px) {
            .widget-grid { grid-template-columns: repeat(2, 1fr); }
            .dash-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .widget-grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 28px; }
            .main-body { padding: 20px; }
            .top-header { padding: 0 20px; }
            .glass-box { padding: 25px; }
            .u-info > div:nth-child(2) { display: none; }
        }
    </style>
</head>
<body>

    <?php if($isHigh): ?>
    <div class="sidebar-overlay" id="overlay" onclick="toggleM()"></div>
    <aside class="sidebar" id="sb">
        <div style="padding:0 35px; margin-bottom:50px;"><div style="font-size:26px; font-weight:800; color:var(--text-main);">MA<span style="color:var(--accent); font-weight:300;">ISG</span></div></div>
        <ul class="nav-list">
            <li><a href="/isg/index.php?url=home" class="nav-link active"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg> Ana Panel</a></li>
            <li><a href="/isg/index.php?url=kullanici" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Kullanıcı Paneli</a></li>
            <li><a href="/isg/index.php?url=duyurular" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg> Duyuru Akışı</a></li>
            
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
            <a href="/isg/index.php?url=chat" class="nav-link" style="color:#10b981;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg> Op1 (Mesajlaşma)</a>
            <a href="/isg/index.php?url=login/logout" class="nav-link" style="color:#ef4444;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> Güvenli Çıkış</a>
        </div>
    </aside>
    <?php else: ?>
    <div class="bottom-nav">
        <a href="/isg/index.php?url=duyurular" class="b-nav-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            Duyuru Akışı
        </a>
        <?php if($uRole == 3): ?>
        <a href="/isg/index.php?url=chat" class="b-nav-link" style="color:#10b981;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            Op1 (Mesajlaşma)
        </a>
        <?php endif; ?>
        <a href="/isg/index.php?url=login/logout" class="b-nav-link" style="color:#ef4444;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Çıkış Yap
        </a>
    </div>
    <style> .sidebar { display:none !important; } .page-wrapper { margin-left: 0 !important; padding-bottom: 80px; } </style>
    <?php endif; ?>

    <div class="page-wrapper">
        <header class="top-header">
            <div style="display:flex; align-items:center;">
                <?php if($isHigh): ?><button onclick="toggleM()" style="background:none; border:none; color:var(--text-main); cursor:pointer;"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></button><?php endif; ?>
            </div>

            <div class="u-info">
                <span class="u-role"><?= htmlspecialchars($unvan) ?></span>
                <span style="font-size:14px; color:var(--text-muted); font-weight:500; margin-left: 10px;">Hoşgeldiniz, <b style="color:var(--text-main);"><?= htmlspecialchars($kullaniciAdi) ?></b></span>
                
                <div class="theme-toggle js-theme-toggle" title="Tema Değiştir" style="margin-left: 10px;">
                    <svg class="sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </div>

                <div style="width:32px; height:32px; background:var(--accent); border-radius:10px; margin-left: 15px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:800;"><?= mb_substr($kullaniciAdi, 0, 1) ?></div>
            </div>
        </header>

        <main class="main-body">
            <div class="hero">
                <h1>Merhaba, <?= htmlspecialchars($kullaniciAdi) ?></h1>
                <p>İSG Yönetim Portalı üzerinde genel durumunuzu ve performansınızı tek bakışta analiz edin.</p>
            </div>

            <div class="shortcuts-bar">
                <a href="/isg/index.php?url=duyurular" class="s-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path></svg> Yeni Duyuru Ekle
                </a>
                <a href="/isg/index.php?url=chat" class="s-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg> Operatör Sohbeti
                </a>
                <a href="/isg/index.php?url=duyurular/istatistik" class="s-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg> Raporlar & İstatistik
                </a>
            </div>

            <div class="widget-grid">
                <a href="/isg/index.php?url=duyurular" class="w-card">
                    <div class="w-icon" style="color:#6366f1;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg></div>
                    <div class="w-data">
                        <span class="w-label">Toplam Duyuru</span>
                        <span class="w-val"><?= $genelStats['toplam'] ?></span>
                    </div>
                </a>
                <div class="w-card">
                    <div class="w-icon" style="color:#10b981;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
                    <div class="w-data">
                        <span class="w-label">Çözülen İşler</span>
                        <span class="w-val" style="color:#10b981;"><?= $genelStats['cozulmus'] ?></span>
                    </div>
                </div>
                <div class="w-card">
                    <div class="w-icon" style="color:#f43f5e;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></div>
                    <div class="w-data">
                        <span class="w-label">Açık Riskler</span>
                        <span class="w-val" style="color:#f43f5e;"><?= $genelStats['cozulmemis'] ?></span>
                    </div>
                </div>
                <div class="w-card">
                    <div class="w-icon" style="color:#f59e0b;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path><path d="M22 12A10 10 0 0 0 12 2v10z"></path></svg></div>
                    <div class="w-data">
                        <span class="w-label">Başarı Oranı</span>
                        <span class="w-val"><?= $genelStats['toplam'] > 0 ? round(($genelStats['cozulmus']/$genelStats['toplam'])*100) : 0 ?>%</span>
                    </div>
                </div>
            </div>

            <div class="dash-layout">
                <div class="glass-box">
                    <h3 style="font-size:20px; font-weight:800; margin-bottom:25px;">Departman Bazlı Performans</h3>
                    <div class="c-box"><canvas id="hDeptChart"></canvas></div>
                </div>

                <div class="glass-box">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h3 style="font-size:20px; font-weight:800;">Son Duyurular</h3>
                        <a href="/isg/index.php?url=duyurular" style="color:var(--accent); font-size:12px; font-weight:800; text-decoration:none;">TÜMÜNÜ GÖR</a>
                    </div>
                    <div class="son-liste">
                        <?php foreach($sonDuyurular as $sd): ?>
                        <a href="/isg/index.php?url=duyurular" class="son-item">
                            <div>
                                <div style="font-weight:800; font-size:15px;"><?= htmlspecialchars($sd['title']) ?></div>
                                <div style="font-size:11px; color:var(--text-m); margin-top:4px; font-weight:700;"><?= htmlspecialchars($sd['department_tag']) ?></div>
                            </div>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="3"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
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

        // MINI DEPT CHART (Stacked with Gradients)
        const ctx = document.getElementById('hDeptChart').getContext('2d');
        
        const gradC = ctx.createLinearGradient(0, 0, 0, 400);
        gradC.addColorStop(0, '#10b981');
        gradC.addColorStop(1, '#059669');

        const gradU = ctx.createLinearGradient(0, 0, 0, 400);
        gradU.addColorStop(0, '#f43f5e');
        gradU.addColorStop(1, '#be123c');

        const d_labels = <?= json_encode(array_keys($deptStats)) ?>;
        const d_c = <?= json_encode(array_column($deptStats, 'c')) ?>;
        const d_u = <?= json_encode(array_column($deptStats, 'u')) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: d_labels,
                datasets: [
                    {
                        label: 'Çözülen',
                        data: d_c,
                        backgroundColor: gradC,
                        borderRadius: 8,
                        barThickness: 14
                    },
                    {
                        label: 'Açık',
                        data: d_u,
                        backgroundColor: gradU,
                        borderRadius: 8,
                        barThickness: 14
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { color: '#64748b', font: { size: 10, weight: '600' } } },
                    y: { stacked: true, grid: { color: 'rgba(255,255,255,0.03)', drawBorder: false }, ticks: { color: '#64748b', font: { size: 10 } } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        cornerRadius: 12,
                        displayColors: true
                    }
                }
            }
        });
    </script>
</body>
</html>
