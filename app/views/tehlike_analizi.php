<?php
if(!isset($stats)) { exit; }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sayfaBasligi ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="/isg/assets/theme-engine.css" rel="stylesheet" />
    <script src="/isg/assets/theme-engine.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
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
        .top-header { height: 75px; padding: 0 40px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-color); background: var(--bg-header); backdrop-filter: var(--glass-blur); position: sticky; top: 0; z-index: 900; }
        
        .main-body { padding: 25px 40px; max-width: 1600px; width: 100%; margin: 0 auto; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 30px; padding: 25px; box-shadow: var(--shadow-card); }
        
        .chart-container { position: relative; height: 330px; width: 100%; margin-top: 15px; }

        @media (max-width: 1024px) {
            .page-wrapper { margin-left: 0; width: 100%; }
            .top-header { padding: 0 20px; }
            .stats-grid { grid-template-columns: 1fr; }
            .stats-grid > div { grid-column: span 1 !important; }
        }

        @media (max-width: 768px) {
            .main-body { padding: 20px; }
            .stats-grid { grid-template-columns: 1fr; gap: 20px; margin-top: 20px; }
            .stats-grid > div { grid-column: span 1 !important; }
            .stat-card { padding: 20px; border-radius: 20px; }
            h1 { font-size: 26px !important; margin-bottom: 20px !important; }
            .ay-dropdown { width: 100%; margin-top: 10px; }
            .ay-dd-btn { width: 100%; justify-content: space-between; }
            .ay-dd-panel { width: 100%; left: 0; right: 0; }
            .chart-container { height: 260px; }
            [style*="justify-content:space-between; align-items:center"] { flex-direction: column !important; align-items: flex-start !important; gap: 15px !important; }
            [style*="justify-content:space-between; align-items:flex-end"] { flex-direction: column !important; align-items: flex-start !important; gap: 15px !important; }
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="overlay" onclick="toggleM()"></div>
    <aside class="sidebar" id="sb">
        <div style="padding:0 35px; margin-bottom:50px;"><div style="font-size:26px; font-weight:800; color:var(--text-main);">MA<span style="color:var(--accent); font-weight:300;">ISG</span></div></div>
        <ul class="nav-list">
            <li><a href="/isg/index.php?url=home" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg> Ana Panel</a></li>
            <li><a href="/isg/index.php?url=kullanici" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Kullanıcı Paneli</a></li>
            <li><a href="/isg/index.php?url=duyurular" class="nav-link"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg> Duyuru Akışı</a></li>
            
            <li style="margin-top:20px; margin-left:10px; font-size:11px; font-weight:800; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:10px; padding-left:15px; opacity:0.5;">Analiz & İstatistik</li>
            
            <li class="has-submenu open" id="menu-tehlike">
                <a href="/isg/index.php?url=duyurular/tehlike_analizi" class="nav-link <?= !$secilenAy ? 'active' : '' ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    Tehlike Analizi
                </a>
                <div class="submenu-toggle" onclick="toggleSub('menu-tehlike', event)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
                <ul class="nav-submenu open">
                    <li><a href="/isg/index.php?url=duyurular/tehlike_analizi" class="nav-sub-link <?= !$secilenAy ? 'active' : '' ?>">Genel Analiz</a></li>
                    <li><a href="/isg/index.php?url=duyurular/tehlike_analizi&ay=<?= date('Y-m') ?>" class="nav-sub-link <?= $secilenAy ? 'active' : '' ?>">Aylık Rapor</a></li>
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
                    <li><a href="/isg/index.php?url=duyurular/departman_istatistik&ay=<?= date('Y-m') ?>" class="nav-sub-link">Aylık Rapor</a></li>
                </ul>
            </li>
        </ul>
        <div style="margin-top:auto; padding: 0 15px;">
            <a href="/isg/index.php?url=chat" class="nav-link" style="color:#10b981;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg> Op1 (Mesajlaşma)</a>
            <a href="/isg/index.php?url=login/logout" class="nav-link" style="color:#ef4444;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> Güvenli Çıkış</a>
        </div>
    </aside>

    <div class="page-wrapper">
        <header class="top-header">
            <div style="display:flex; align-items:center;">
                <button onclick="toggleM()" style="background:none; border:none; color:var(--text-main); cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <div class="theme-toggle js-theme-toggle" title="Tema Değiştir" style="cursor:pointer; width:40px; height:40px; display:flex; align-items:center; justify-content:center; background:var(--bg-card); border-radius:12px; border:1px solid var(--border-color);">
                    <svg class="sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line></svg>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="text-align:right;">
                        <div style="font-size:11px; color:var(--text-muted); font-weight:800; text-transform:uppercase;"><?= $unvan ?></div>
                        <div style="font-size:14px; font-weight:700; color:var(--text-main);"><?= $kullaniciAdi ?></div>
                    </div>
                    <div style="width:36px; height:36px; background:var(--accent); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px; font-weight:800;"><?= mb_substr($kullaniciAdi, 0, 1) ?></div>
                </div>
            </div>
        </header>

        <main class="main-body" id="pdfContent">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:50px; gap:20px; flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:38px; font-weight:800; letter-spacing:-1.5px;"><?= $secilenAy ? 'Aylık Tehlike Analizi' : 'Tehlike Analizi' ?></h1>
                    
                    <?php
                    $secilenYil = $secilenAy ? (int) substr($secilenAy, 0, 4) : (int) date('Y');
                    $ayIsimleri = [
                        '01' => 'Ocak', '02' => 'Şubat', '03' => 'Mart', '04' => 'Nisan',
                        '05' => 'Mayıs', '06' => 'Haziran', '07' => 'Temmuz', '08' => 'Ağustos',
                        '09' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık'
                    ];
                    $aktifAyAdi = $secilenAy ? ($ayIsimleri[substr($secilenAy, 5)] ?? '') . ' ' . $secilenYil : 'Tüm Zamanlar';
                    ?>

                    <style>
                        .ay-dropdown { position: relative; display: inline-block; margin-top: 15px; }
                        .ay-dd-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 14px; background: var(--bg-card); border: 1.5px solid <?= $secilenAy ? 'var(--accent)' : 'var(--border-color)' ?>; color: <?= $secilenAy ? 'var(--accent)' : 'var(--text-main)' ?>; font-size: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; box-shadow: var(--shadow-card); }
                        .ay-dd-btn:hover { border-color: var(--accent); color: var(--accent); }
                        .ay-dd-panel { display: none; position: absolute; z-index: 9999; left: 0; top: 110%; background: var(--bg-sidebar); border: 1.5px solid var(--border-color); border-radius: 18px; padding: 18px; box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4); width: 280px; }
                        .ay-dd-panel.open { display: block; animation: ddIn 0.15s ease; }
                        @keyframes ddIn { from { opacity: 0; transform: translateY(-6px) } to { opacity: 1; transform: none } }
                        .ay-dd-yil { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color); }
                        .ay-dd-yil-btn { display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-item); color: var(--text-main); text-decoration: none; transition: 0.2s; }
                        .ay-dd-yil-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
                        .ay-dd-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 7px; }
                        .ay-dd-item { display: flex; align-items: center; justify-content: center; padding: 10px 4px; border-radius: 10px; font-size: 12px; font-weight: 800; text-decoration: none; text-align: center; transition: 0.2s; border: 1.5px solid var(--border-color); background: var(--bg-item); color: var(--text-main); }
                        .ay-dd-item:hover { border-color: var(--accent); color: var(--accent); background: rgba(59, 130, 246, 0.08); }
                        .ay-dd-item.active { background: var(--accent); color: #fff !important; border-color: var(--accent); }
                    </style>

                    <div class="ay-dropdown" id="ayDropdown">
                        <div class="ay-dd-btn" onclick="toggleAyDD()">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" /></svg>
                            <?= htmlspecialchars($aktifAyAdi) ?>
                            <?php if ($secilenAy): ?>
                                <a href="/isg/index.php?url=duyurular/tehlike_analizi" onclick="event.stopPropagation();" style="margin-left:4px; color:var(--text-muted); display:flex;">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
                                </a>
                            <?php else: ?>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9" /></svg>
                            <?php endif; ?>
                        </div>

                        <div class="ay-dd-panel" id="ayPanel">
                            <div class="ay-dd-yil">
                                <a href="/isg/index.php?url=duyurular/tehlike_analizi&yil=<?= $secilenYil - 1 ?>" class="ay-dd-yil-btn"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6" /></svg></a>
                                <span style="font-size:14px; font-weight:800; color:var(--text-main);"><?= $secilenYil ?></span>
                                <a href="/isg/index.php?url=duyurular/tehlike_analizi&yil=<?= $secilenYil + 1 ?>" class="ay-dd-yil-btn"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6" /></svg></a>
                            </div>
                            <div class="ay-dd-grid">
                                <?php foreach ($ayIsimleri as $ayNo => $ayAd): 
                                    $ayKodu = $secilenYil . '-' . $ayNo;
                                    $isActive = ($secilenAy === $ayKodu);
                                ?>
                                    <a href="/isg/index.php?url=duyurular/tehlike_analizi&ay=<?= $ayKodu ?>" class="ay-dd-item<?= $isActive ? ' active' : '' ?>"><?= $ayAd ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div style="display:flex; align-items:center; gap:20px;">
                    <a href="/isg/index.php?url=duyurular/pdf_tehlike_analizi<?= $secilenAy ? '&ay=' . $secilenAy : '' ?>" target="_blank" style="background: linear-gradient(135deg, #7c3aed, #6366f1); color: #fff; text-decoration: none; padding: 14px 30px; border-radius: 50px; font-size: 13px; font-weight: 800; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 25px rgba(124, 58, 237, 0.3); transition: 0.3s; height: 55px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 30px rgba(124, 58, 237, 0.45)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 10px 25px rgba(124, 58, 237, 0.3)';">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        <?= $secilenAy ? 'AYLIK PDF İNDİR' : 'GENEL PDF İNDİR' ?>
                    </a>
                    <div style="background:var(--accent); color:#fff; padding:0 30px; border-radius:20px; text-align:center; height: 55px; display: flex; flex-direction: column; justify-content: center;">
                        <div style="font-size:10px; font-weight:800; opacity:0.8; text-transform:uppercase;">Genel Çözüm Oranı</div>
                        <div style="font-size:22px; font-weight:800;">%<?= ($genel['toplam'] > 0) ? round(($genel['cozulmus'] / $genel['toplam']) * 100, 1) : 0 ?></div>
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3 style="font-weight:800; font-size:16px; margin-bottom:20px; color:#10b981;">Çözülen Tehlike Dağılımı</h3>
                    <div class="chart-container">
                        <canvas id="solvedChart"></canvas>
                    </div>
                </div>

                <div class="stat-card">
                    <h3 style="font-weight:800; font-size:16px; margin-bottom:20px; color:#ef4444;">Çözülmeyen Tehlike Dağılımı</h3>
                    <div class="chart-container">
                        <canvas id="unsolvedChart"></canvas>
                    </div>
                </div>

                <div class="stat-card" style="grid-column: span 2;">
                    <h3 style="font-weight:800; font-size:16px; margin-bottom:25px;">Seviye Bazlı Performans Detayları</h3>
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px;">
                        <?php foreach($stats as $s): 
                            $perc = ($s['toplam'] > 0) ? round(($s['cozulmus'] / $s['toplam']) * 100, 1) : 0;
                            $color = $s['danger_level'] == 'Yüksek' ? '#ef4444' : ($s['danger_level'] == 'Orta' ? '#f59e0b' : '#3b82f6');
                        ?>
                        <div style="background:var(--bg-item); padding:20px; border-radius:20px; border:1px solid var(--border-color);">
                            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                <div style="font-weight:800; color:<?= $color ?>; font-size:14px;"><?= mb_strtoupper($s['danger_level']) ?> TEHLİKE</div>
                                <div style="font-weight:800; font-size:14px;">%<?= $perc ?> Başarı</div>
                            </div>
                            <div style="height:8px; background:var(--border-color); border-radius:4px; overflow:hidden;">
                                <div style="width:<?= $perc ?>%; height:100%; background:<?= $color ?>;"></div>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:10px; font-size:11px; font-weight:700; color:var(--text-muted);">
                                <span><?= $s['cozulmus'] ?> Çözüldü</span>
                                <span><?= $s['cozulmemis'] ?> Çözülmedi</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        Chart.register(ChartDataLabels);

        function toggleM() { document.getElementById('sb').classList.toggle('active'); document.getElementById('overlay').classList.toggle('active'); }
        function toggleSub(id, e) {
            if(e) e.preventDefault();
            const el = document.getElementById(id);
            const sub = el.querySelector('.nav-submenu');
            el.classList.toggle('open');
            sub.classList.toggle('open');
        }

        function toggleAyDD() {
            document.getElementById('ayPanel').classList.toggle('open');
        }

        window.onclick = function(e) {
            if (!e.target.closest('.ay-dropdown')) {
                const dd = document.getElementById('ayPanel');
                if (dd && dd.classList.contains('open')) dd.classList.remove('open');
            }
        };

        <?php
        $labels = []; $solvedData = []; $unsolvedData = []; $colors = [];
        $order = ['Yüksek', 'Orta', 'Düşük'];
        $sortedStats = [];
        foreach($order as $o) { foreach($stats as $s) { if($s['danger_level'] == $o) { $sortedStats[] = $s; break; } } }
        if(empty($sortedStats)) $sortedStats = $stats;
        foreach($sortedStats as $s) {
            $labels[] = $s['danger_level'] . ' Tehlike';
            $solvedData[] = $s['cozulmus'];
            $unsolvedData[] = $s['cozulmemis'];
            $colors[] = $s['danger_level'] == 'Yüksek' ? '#ef4444' : ($s['danger_level'] == 'Orta' ? '#f59e0b' : '#3b82f6');
        }
        ?>

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'bottom', labels: { font: { weight: '800', size: 10 }, color: '#64748b' } },
                datalabels: {
                    color: '#fff',
                    font: { weight: '800', size: 12 },
                    formatter: (value, ctx) => {
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(data => { sum += data; });
                        let percentage = (value * 100 / sum).toFixed(1) + "%";
                        return value > 0 ? percentage : null;
                    }
                }
            },
            cutout: '70%', borderRadius: 5
        };

        new Chart(document.getElementById('solvedChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($solvedData) ?>,
                    backgroundColor: <?= json_encode($colors) ?>,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: commonOptions
        });

        new Chart(document.getElementById('unsolvedChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($unsolvedData) ?>,
                    backgroundColor: <?= json_encode($colors) ?>,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: commonOptions
        });
    </script>
</body>
</html>
