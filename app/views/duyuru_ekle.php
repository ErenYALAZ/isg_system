<?php
$isEdit = isset($duyuru);
$actionUrl = $isEdit ? "/isg/index.php?url=duyurular/guncelle" : "/isg/index.php?url=duyurular/kontrol";
$departments = ["Kalite", "Üretim", "Lojistik", "IT", "OT", "Bakım", "İSG", "İdari İşler", "İK"];
$selectedDepts = $isEdit ? explode(', ', $duyuru['department_tag']) : [];
if (!isset($unvan)) {
    $unvan = 'Görevli';
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Duyuruyu Düzenle' : 'Yeni Duyuru Ekle'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }

        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 40px;
            padding: 50px;
            box-shadow: var(--shadow-card);
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 20px;
            color: var(--text-main);
            font-size: 16px;
            outline: none;
            transition: 0.3s;
            box-shadow: var(--shadow-card);
        }

        input:focus,
        textarea:focus {
            border-color: var(--accent);
            background: var(--bg-item);
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.1);
        }

        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }

        .cat-item input {
            display: none;
        }

        .cat-label {
            display: block;
            padding: 15px;
            background: var(--bg-item);
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            font-size: 11px;
            font-weight: 800;
            transition: 0.2s;
            color: var(--text-muted);
        }

        .cat-item input:checked+.cat-label {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .dept-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dept-item input {
            display: none;
        }

        .dept-label {
            padding: 10px 22px;
            background: var(--bg-item);
            border: 1px solid var(--border-color);
            border-radius: 30px;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s;
            color: var(--text-muted);
        }

        .dept-item input:checked+.dept-label {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .drag-box {
            border: 2px dashed var(--border-color);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            position: relative;
            cursor: pointer;
            transition: 0.3s;
        }

        .drag-box:hover {
            border-color: var(--accent);
            background: var(--bg-item);
        }

        .drag-box input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .pv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 15px;
            margin-top: 25px;
        }

        .pv-item {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            border-radius: 15px;
            overflow: hidden;
            border: 1.5px solid var(--border-color);
        }

        .pv-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pv-del {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 30px;
            height: 30px;
            background: rgba(239, 68, 68, 0.9);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-submit {
            width: 100%;
            background: var(--text-main);
            color: var(--bg-main);
            padding: 20px;
            border-radius: 18px;
            border: none;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 40px;
            letter-spacing: 1px;
            box-shadow: var(--shadow-card);
        }

        .btn-submit:hover {
            background: var(--accent);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
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
            box-shadow: var(--shadow-card);
        }

        .b-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: var(--text-muted);
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
            color: var(--text-main);
        }

        @media (max-width: 768px) {
            .main-body {
                padding: 20px;
            }

            .form-card {
                padding: 25px;
                border-radius: 25px;
            }

            .cat-grid {
                grid-template-columns: 1fr 1fr;
            }

            .pv-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
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
            <h1 style="font-size:38px; font-weight:800; letter-spacing:-1.5px; margin-bottom:45px; text-align:left;">
                <?= $isEdit ? 'Duyuru Güncelle' : 'Yeni Duyuru Ekle'; ?></h1>

            <div class="form-card">
                <form action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data" id="main_form">
                    <?php if ($isEdit): ?> <input type="hidden" name="id"
                            value="<?= htmlspecialchars($duyuru['id']); ?>"> <?php endif; ?>

                    <div class="form-group">
                        <label>Duyuru Başlığı</label>
                        <input type="text" name="baslik" value="<?= htmlspecialchars($duyuru['title'] ?? ''); ?>"
                            required placeholder="Kısa ve net bir başlık girin">
                    </div>

                    <div class="form-group">
                        <label>Detaylı Açıklama</label>
                        <textarea name="icerik" rows="5" required
                            placeholder="İSG protokollerini detaylandırın..."><?= htmlspecialchars($duyuru['content'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Risk Kategori Seçimi</label>
                        <div class="cat-grid">
                            <?php
                            $cats = ["Elektrik Kaçağı", "Yangın Riski", "Kimyasal Sızıntı", "Yüksekten Düşme", "Ekipman Arızası", "Ramak Kala", "İSG Eğitimi", "Genel Duyuru"];
                            foreach ($cats as $cat):
                                $checked = ($isEdit && $duyuru['hazard_category'] == $cat) ? 'checked' : ($cat == 'Genel Duyuru' && !$isEdit ? 'checked' : '');
                                ?>
                                <div class="cat-item">
                                    <input type="radio" name="kategori" id="c_<?= $cat ?>" value="<?= $cat ?>" <?= $checked ?>>
                                    <label class="cat-label" for="c_<?= $cat ?>"><?= $cat ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tehlike Derecesi</label>
                        <div class="cat-grid" style="grid-template-columns: repeat(3, 1fr);">
                            <?php
                            $dangers = ["Düşük", "Orta", "Yüksek"];
                            foreach ($dangers as $d):
                                $checked = ($isEdit && $duyuru['danger_level'] == $d) ? 'checked' : ($d == 'Düşük' && !$isEdit ? 'checked' : '');
                                $color = $d == 'Yüksek' ? '#ef4444' : ($d == 'Orta' ? '#f59e0b' : '#3b82f6');
                                ?>
                                <style>
                                    #dl_<?= $d ?>:checked+.dl-label {
                                        background:
                                            <?= $color ?>
                                        ;
                                        border-color:
                                            <?= $color ?>
                                        ;
                                        color: #fff;
                                    }
                                </style>
                                <div class="cat-item">
                                    <input type="radio" name="danger_level" id="dl_<?= $d ?>" value="<?= $d ?>" <?= $checked ?>>
                                    <label class="cat-label dl-label" for="dl_<?= $d ?>"><?= $d ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alakalı Departmanlar</label>
                        <div class="dept-grid">
                            <?php foreach ($departments as $dept): ?>
                                <div class="dept-item">
                                    <input type="checkbox" name="departmanlar[]" id="d_<?= $dept ?>" value="<?= $dept ?>"
                                        <?= in_array($dept, $selectedDepts) ? 'checked' : '' ?>>
                                    <label class="dept-label" for="d_<?= $dept ?>"><?= htmlspecialchars($dept) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Görsel Denetimi</label>
                        <div class="drag-box">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="var(--accent)"
                                stroke-width="2.5" style="margin-bottom:15px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <p style="font-weight:700; font-size:13px; color:var(--text-m);">Görselleri Sürükle veya
                                Buraya Tıkla</p>
                            <input type="file" name="duyuru_gorselleri[]" id="file_f" accept="image/*" multiple>
                        </div>

                        <div id="pv_grid" class="pv-grid">
                            <?php if ($isEdit): ?>
                                <?php
                                $db = (new Database())->getConnection();
                                $imgs = $db->prepare("SELECT id, image_data FROM announcement_images WHERE announcement_id = :aid");
                                $imgs->execute(['aid' => $duyuru['id']]);
                                foreach ($imgs->fetchAll() as $im):
                                    ?>
                                    <div class="pv-item" id="pv_<?= $im['id'] ?>">
                                        <img src="/isg/<?= htmlspecialchars($im['image_data']) ?>">
                                        <button type="button" class="pv-del" onclick="markDel('<?= $im['id'] ?>')">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6V20a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit"
                        class="btn-submit"><?= $isEdit ? 'DEĞİŞİKLİKLERİ KAYDET' : 'DUYURUYU YAYINLA'; ?></button>
                    <a href="/isg/index.php?url=duyurular"
                        style="display:block; text-align:center; margin-top:25px; color:var(--text-m); text-decoration:none; font-weight:800; font-size:12px;">Vazgeç,
                        Geri Dön</a>
                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleM() { document.getElementById('sb').classList.toggle('active'); document.getElementById('overlay').classList.toggle('active'); }

        document.getElementById('file_f').addEventListener('change', function (e) {
            const list = document.getElementById('pv_grid');
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const div = document.createElement('div'); div.className = 'pv-item';
                    div.innerHTML = `<img src="${ev.target.result}"><button type="button" class="pv-del" onclick="this.parentElement.remove()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6V20a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg></button>`;
                    list.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        function markDel(imgId) {
            const el = document.getElementById('pv_' + imgId);
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'silinecek_gorseller[]';
            input.value = imgId;
            document.getElementById('main_form').appendChild(input);
            el.style.opacity = '0.3';
            el.style.pointerEvents = 'none';
        }
    </script>
</body>

</html>