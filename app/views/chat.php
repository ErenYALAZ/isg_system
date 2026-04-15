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
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        /* GİZLENEBİLİR SIDEBAR - DİĞER SAYFALARLA AYNI */
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
            z-index: 1001;
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
            z-index: 1000;
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

        .app-content {
            flex: 1;
            display: flex;
            overflow: hidden;
            width: 100%;
            transition: 0.4s;
        }

        /* USER LIST SIDEBAR */
        .chat-sidebar {
            width: 340px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .chat-sidebar-h {
            padding: 40px 30px 20px;
            font-size: 22px;
            font-weight: 800;
            color: var(--text-main);
        }

        .user-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            scrollbar-width: none;
        }

        .user-item {
            padding: 15px;
            border-radius: 20px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid transparent;
            position: relative;
        }

        .user-item:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .user-item.active {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .avatar {
            width: 44px;
            height: 44px;
            background: #334155;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            color: #fff;
            flex-shrink: 0;
        }

        /* MAIN CHAT AREA */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-main);
            position: relative;
        }

        .chat-h {
            height: 85px;
            padding: 0 35px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-header);
        }

        .chat-msgs {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }

        /* HAMBURGER BTN */
        .menu-toggle {
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            transition: 0.2s;
        }

        .menu-toggle:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* BUBBLES */
        .msg-wrap {
            display: flex;
            flex-direction: column;
            max-width: 65%;
            position: relative;
        }

        .msg-wrap.me {
            align-self: flex-end;
            align-items: flex-end;
        }

        .msg-wrap.other {
            align-self: flex-start;
            align-items: flex-start;
        }

        .bubble {
            padding: 15px 22px;
            border-radius: 24px;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.6;
            transition: 0.2s;
            position: relative;
        }

        .msg-wrap.me .bubble {
            background: var(--accent);
            color: #fff;
            border-bottom-right-radius: 4px;
            text-align: right;
        }

        .msg-wrap.other .bubble {
            background: var(--bg-card);
            color: var(--text-main);
            border-bottom-left-radius: 4px;
            text-align: left;
            border: 1px solid var(--border-color);
        }

        .bubble.img-only {
            background: transparent !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            width: auto;
        }

        .m-imgs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 8px 0;
        }

        .msg-wrap.me .m-imgs {
            justify-content: flex-end;
        }

        .msg-wrap.other .m-imgs {
            justify-content: flex-start;
        }

        .m-imgs img {
            max-width: 280px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            cursor: zoom-in;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .msg-time {
            font-size: 10px;
            font-weight: 800;
            color: var(--text-m);
            margin-top: 6px;
            opacity: 0.6;
        }

        .bubble-menu-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            visibility: hidden;
            position: absolute;
            top: 0;
        }

        .msg-wrap:hover .bubble-menu-btn {
            visibility: visible;
        }

        .msg-wrap.me .bubble-menu-btn {
            left: -40px;
        }

        .msg-wrap.other .bubble-menu-btn {
            right: -40px;
        }

        .context-menu {
            position: fixed;
            background: #0f172a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 8px;
            width: 170px;
            z-index: 2000;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
            display: none;
        }

        .menu-item {
            padding: 12px 15px;
            font-size: 13px;
            font-weight: 700;
            color: #f1f5f9;
            cursor: pointer;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-item:hover {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent);
        }

        .menu-item.danger:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        /* INPUT AREA */
        .chat-input-w {
            padding: 30px 40px;
            border-top: 1px solid var(--border-color);
            background: var(--bg-header);
        }

        .chat-box {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 22px;
            padding: 12px;
            display: flex;
            align-items: flex-end;
            gap: 15px;
            box-shadow: var(--shadow-card);
        }

        textarea {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            padding: 10px;
            color: var(--text-main);
            font-size: 16px;
            resize: none;
            max-height: 150px;
            scrollbar-width: none;
        }

        .btn-send {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: var(--accent);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
            flex-shrink: 0;
        }

        .btn-send:hover {
            transform: scale(1.05);
        }

        /* GALLERY MODAL */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal img {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 12px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6);
        }

        .deleted {
            font-style: italic !important;
            opacity: 0.6;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8 !important;
            border: 1.5px dashed rgba(255, 255, 255, 0.1) !important;
            background: transparent !important;
        }

        .unread-badge {
            position: absolute;
            top: 15px;
            right: 20px;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
            z-index: 5;
        }

        /* QUOTE / SHARE UI */
        .ann-quote {
            background: rgba(0, 0, 0, 0.2);
            border-left: 3px solid #fff;
            padding: 10px 14px;
            border-radius: 12px;
            margin-bottom: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ann-quote:hover {
            background: rgba(0, 0, 0, 0.3);
        }

        .ann-quote-title {
            font-weight: 800;
            color: #fff;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .share-bar {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 16px;
            padding: 10px 20px;
            margin-bottom: 12px;
            display: none;
            align-items: center;
            justify-content: space-between;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .app-content {
                flex-direction: column;
            }

            .chat-sidebar {
                width: 100%;
                height: 35vh;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .chat-main {
                height: 65vh;
            }

            .chat-h {
                padding: 0 15px;
                height: 65px;
            }

            .chat-input-w {
                padding: 15px;
            }

            .msg-wrap {
                max-width: 85%;
            }

            .chat-msgs {
                padding: 20px 15px;
            }

            .top-header,
            .u-info span:nth-child(2),
            .u-info div {
                display: none !important;
            }

            /* Hide extra top parts if any */
            /* User list header items compact */
            .chat-sidebar-h {
                padding: 20px 15px 10px;
            }

            .user-item {
                padding: 10px;
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

    <!-- OVERLAY -->
    <div class="sidebar-overlay" id="overlay" onclick="toggleM()"></div>

    <?php if ($isHigh): ?>
        <aside class="sidebar" id="sb">
            <div style="padding:0 40px; margin-bottom:50px;">
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
                <li><a href="/isg/index.php?url=duyurular/istatistik" class="nav-link"><svg width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg> İstatistikler</a></li>
                <li><a href="/isg/index.php?url=duyurular/departman_istatistik" class="nav-link"><svg width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                            <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                        </svg> Departman İstatistiği</a></li>
            </ul>
            <div style="margin-top:auto; padding: 0 15px;">
                <a href="/isg/index.php?url=chat" class="nav-link active" style="color:#10b981;"><svg width="18" height="18"
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
            <a href="/isg/index.php?url=chat" class="b-nav-link active" style="color:#10b981;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                    </path>
                </svg>
                Op1 (Mesajlaşma)
            </a>
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

            .app-content {
                padding-bottom: 80px;
            }
        </style>
    <?php endif; ?>

    <main class="app-content">
        <!-- USER LIST (STAYS ON BOARD) -->
        <aside class="chat-sidebar">
            <div class="chat-sidebar-h">
                <div style="display:flex; align-items:center; justify-content:space-between; width:100%;">
                    <div style="display:flex; align-items:center;">
                        <?php if ($isHigh): ?>
                            <button class="menu-toggle" onclick="toggleM()" style="color:var(--text-main);">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3">
                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <line x1="3" y1="18" x2="21" y2="18"></line>
                                </svg>
                            </button>
                        <?php endif; ?>
                        Sohbetler
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span
                            style="font-size:11px; font-weight:800; color:var(--accent); background:rgba(16,185,129,0.1); padding:4px 10px; border-radius:8px;"><?= htmlspecialchars($unvan) ?></span>
                        <div style="font-size:12px; color:var(--text-muted); font-weight:600;">Hoşgeldiniz, <b
                                style="color:var(--text-main);"><?= htmlspecialchars($kullaniciAdi) ?></b></div>


                    </div>
                </div>
            </div>
            <div class="user-list">
                <?php foreach ($kullanicilar as $u): ?>
                    <div class="user-item" data-id="<?= $u['id'] ?>"
                        onclick="openChat(<?= $u['id'] ?>, '<?= htmlspecialchars($u['kuladsoyad']) ?>', this)">
                        <span class="unread-badge" id="badge-<?= $u['id'] ?>" style="display:none">0</span>
                        <div class="avatar"><?= mb_substr($u['kuladsoyad'], 0, 1, 'UTF-8') ?></div>
                        <div style="flex:1">
                            <div style="font-weight:800; font-size:15px; color:var(--text-main);">
                                <?= htmlspecialchars($u['kuladsoyad']) ?>
                            </div>
                            <?php
                            $rMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
                            ?>
                            <div style="font-size:11px; color:var(--text-muted); font-weight:700;">
                                <?= $rMap[$u['role']] ?? 'Görevli' ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>

        <!-- CHAT AREA -->
        <section class="chat-main" id="chatMain">
            <div id="noChat"
                style="flex:1; display:flex; align-items:center; justify-content:center; flex-direction:column; opacity:0.3;">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <path
                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                    </path>
                </svg>
                <p style="margin-top:20px; font-weight:800; font-size:18px;">Bir sohbet seçerek mesajlaşmaya başlayın
                </p>
            </div>

            <div id="chatActive" style="display:none; flex:1; flex-direction:column; overflow:hidden;">
                <header class="chat-h">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <div class="avatar" id="hAvatar" style="background:var(--accent); color:#fff;">?</div>
                        <div>
                            <div style="font-weight:800; font-size:18px; color:var(--text-main);" id="hName">...</div>
                            <div style="font-size:11px; color:var(--accent); font-weight:800; letter-spacing:1px;">AKTİF
                            </div>
                        </div>
                    </div>
                </header>

                <div class="chat-msgs" id="msgBox"></div>

                <footer class="chat-input-w">
                    <form id="msgForm" onsubmit="sendMsg(event)">
                        <input type="hidden" name="receiver_id" id="targetId">
                        <input type="hidden" name="announcement_id" id="shareIdInp">

                        <div class="share-bar" id="shareBar">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)"
                                    stroke-width="3">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <div style="font-size:13px; font-weight:700;">Etiketlenen Duyuru: <span id="shareTitle"
                                        style="color:#fff;">...</span></div>
                            </div>
                            <button type="button" onclick="closeShare()"
                                style="background:none; border:none; color:var(--text-m); cursor:pointer;"><svg
                                    width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg></button>
                        </div>

                        <div class="chat-box">
                            <label style="cursor:pointer; color:var(--text-m); padding: 5px 10px;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path
                                        d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                    </path>
                                </svg>
                                <input type="file" name="msg_images[]" multiple accept="image/*" id="fileInp"
                                    style="display:none;" onchange="prevImgs()">
                            </label>
                            <textarea name="message" id="msgInp" placeholder="Bir mesaj yazın..." rows="1"
                                onkeydown="handleEnter(event)"></textarea>
                            <button type="submit" class="btn-send">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="3">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                </svg>
                            </button>
                        </div>
                        <div id="pGrid" style="display:flex; gap:10px; margin-top:15px; overflow-x:auto;"></div>
                    </form>
                </footer>
            </div>
        </section>
    </main>

    <!-- UI COMPONENTS -->
    <div id="mMenu" class="context-menu">
        <div class="menu-item" onclick="delMe()">Benden sil</div>
        <div class="menu-item danger" id="delAllBtn" onclick="delAll()">Herkesten sil</div>
    </div>

    <div id="imgModal" class="modal" onclick="this.style.display='none'">
        <img id="modalImg" src="">
    </div>

    <script>
        function toggleM() { document.getElementById('sb').classList.toggle('active'); document.getElementById('overlay').classList.toggle('active'); }
        function handleEnter(e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMsg(); } }

        let activeTarget = null;
        let poll = null;
        let selMsgId = null;

        function openChat(id, name, el) {
            document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            activeTarget = id;
            document.getElementById('targetId').value = id;
            document.getElementById('noChat').style.display = 'none';
            document.getElementById('chatActive').style.display = 'flex';
            document.getElementById('hName').innerText = name;
            document.getElementById('hAvatar').innerText = name.substring(0, 1);

            // Badge'i temizle
            const badge = document.getElementById('badge-' + id);
            if (badge) badge.style.display = 'none';

            loadMsgs();
            if (poll) clearInterval(poll);
            poll = setInterval(loadMsgs, 3000);
        }

        async function loadUnreadCounts() {
            try {
                const res = await fetch('/isg/index.php?url=chat/get_unread_counts');
                const data = await res.json();

                // Aktif olmayan tüm badge'leri temizle veya güncelle
                document.querySelectorAll('.unread-badge').forEach(b => {
                    if (b.id !== 'badge-' + activeTarget) b.style.display = 'none';
                });

                data.forEach(item => {
                    if (item.sender_id != activeTarget) {
                        const badge = document.getElementById('badge-' + item.sender_id);
                        if (badge) {
                            badge.innerText = item.unread_count;
                            badge.style.display = 'flex';
                        }
                    }
                });
            } catch (e) { }
        }
        setInterval(loadUnreadCounts, 5000);
        loadUnreadCounts();

        async function loadMsgs() {
            if (!activeTarget) return;
            const res = await fetch(`/isg/index.php?url=chat/get_messages&id=${activeTarget}`);
            const data = await res.json();
            const box = document.getElementById('msgBox');
            const wasAtBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 100;

            box.innerHTML = '';
            data.forEach(m => {
                const isMe = m.sender_id == <?= $_SESSION['user_id'] ?>;
                const wrap = document.createElement('div');
                wrap.className = `msg-wrap ${isMe ? 'me' : 'other'}`;

                let content = '';
                if (m.is_deleted_everyone == 1) {
                    content = `<div class="bubble deleted">Bu mesaj silindi.</div>`;
                } else {
                    let quote = '';
                    if (m.announcement_id && m.ann_title) {
                        quote = `
                            <div class="ann-quote" onclick="location.href='/isg/index.php?url=duyurular&focus_id=${m.announcement_id}'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                <div class="ann-quote-title">${m.ann_title}</div>
                            </div>
                        `;
                    }
                    let txt = m.message ? `<div class="bubble">${quote}${m.message}</div>` : (quote ? `<div class="bubble">${quote}</div>` : '');
                    let imgs = '';
                    if (m.images && m.images.length > 0) {
                        imgs = `<div class="bubble img-only"><div class="m-imgs">${m.images.map(i => `<img src="/isg/${i}" onclick="viewImg(this.src)">`).join('')}</div></div>`;
                    }
                    content = txt + imgs;
                }

                let menu = !m.is_deleted_everyone ? `<div class="bubble-menu-btn" onclick="showMenu(event, ${m.id}, ${isMe})"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg></div>` : '';

                wrap.innerHTML = `${content}<div class="msg-time">${m.created_at.substring(11, 16)}</div>${menu}`;
                box.appendChild(wrap);
            });
            if (wasAtBottom) box.scrollTop = box.scrollHeight;
        }

        function viewImg(src) { document.getElementById('modalImg').src = src; document.getElementById('imgModal').style.display = 'flex'; }

        function showMenu(e, mid, isMe) {
            e.stopPropagation();
            selMsgId = mid;
            const m = document.getElementById('mMenu');
            m.style.left = e.clientX - 170 + 'px';
            m.style.top = e.clientY + 'px';
            m.style.display = 'block';
            document.getElementById('delAllBtn').style.display = isMe ? 'flex' : 'none';
        }

        window.onclick = () => document.getElementById('mMenu').style.display = 'none';

        async function delMe() { await fetch('/isg/index.php?url=chat/delete_me', { method: 'POST', body: new URLSearchParams({ id: selMsgId }) }); loadMsgs(); }
        async function delAll() { await fetch('/isg/index.php?url=chat/delete_everyone', { method: 'POST', body: new URLSearchParams({ id: selMsgId }) }); loadMsgs(); }

        async function sendMsg(e) {
            if (e) e.preventDefault();
            const f = document.getElementById('msgForm');
            const d = new FormData(f);
            if (!document.getElementById('msgInp').value.trim() && !document.getElementById('fileInp').files.length && !document.getElementById('shareIdInp').value) return;
            const res = await fetch('/isg/index.php?url=chat/send', { method: 'POST', body: d });
            const result = await res.json();
            if (result.success) {
                f.reset();
                closeShare();
                document.getElementById('pGrid').innerHTML = '';
                loadMsgs();
                setTimeout(() => { const b = document.getElementById('msgBox'); b.scrollTop = b.scrollHeight; }, 50);
            }
        }

        function closeShare() {
            document.getElementById('shareIdInp').value = '';
            document.getElementById('shareBar').style.display = 'none';
        }

        // URL'den paylaşılan duyuruyu yakala
        window.addEventListener('load', async () => {
            const params = new URLSearchParams(window.location.search);
            const shareId = params.get('share_id');
            if (shareId) {
                const res = await fetch('/isg/index.php?url=chat/get_announcement_info&id=' + shareId);
                const data = await res.json();
                if (data && data.title) {
                    document.getElementById('shareIdInp').value = data.id;
                    document.getElementById('shareTitle').innerText = data.title;
                    document.getElementById('shareBar').style.display = 'flex';
                }
            }
        });

        function prevImgs() {
            const fs = document.getElementById('fileInp').files;
            const g = document.getElementById('pGrid');
            g.innerHTML = '';
            Array.from(fs).forEach(f => {
                const r = new FileReader();
                r.onload = (ev) => { g.innerHTML += `<img src="${ev.target.result}" style="width:60px; height:60px; border-radius:12px; object-fit:cover; border:1px solid rgba(255,255,255,0.1);">`; };
                r.readAsDataURL(f);
            });
        }
    </script>
</body>

</html>