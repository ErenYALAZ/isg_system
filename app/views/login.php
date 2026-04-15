<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($sayfaBasligi) ? $sayfaBasligi : 'Giriş Yap - İSG Portalı'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, #00BCD4 0%, #002855 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 560px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            margin: 20px;
        }

        /* SOL PANEL */
        .left-panel {
            flex: 1.1;
            position: relative;
            padding: 70px 50px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            background: radial-gradient(circle at 100% 100%, #db870a 0%, transparent 65%),
                        linear-gradient(145deg, #04395a 0%, #061c2c 100%);
            color: white;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: repeating-linear-gradient(45deg, rgba(255,255,255,0.05) 0px, rgba(255,255,255,0.05) 2px, transparent 2px, transparent 30px);
            pointer-events: none;
            z-index: 1;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to right, transparent 0%, rgba(255,255,255,0.03) 50%, transparent 100%);
            pointer-events: none;
            z-index: 1;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 2;
            margin-bottom: 70px;
        }

        .welcome-text {
            z-index: 2;
        }

        .welcome-text h1 {
            font-size: 42px;
            line-height: 1.2;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .welcome-text p {
            font-size: 14px;
            font-weight: 400;
            opacity: 0.9;
            color: #f1f5f9;
        }

        /* SAĞ PANEL */
        .right-panel {
            flex: 0.9;
            padding: 40px 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        /* ── SEKME SİSTEMİ ── */
        .tab-switcher {
            display: flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 5px;
            gap: 4px;
            margin-bottom: 28px;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 10px;
            border: none;
            border-radius: 9px;
            background: transparent;
            color: #6b7280;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            letter-spacing: 0.2px;
        }

        .tab-btn.active {
            background: #fff;
            color: #1e3a8a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        .tab-btn svg { flex-shrink: 0; }

        /* ── FORM ALANLARI ── */
        .form-panel {
            display: none;
            flex-direction: column;
        }
        .form-panel.active { display: flex; }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 15px 16px;
            font-size: 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            color: #374151;
            transition: all 0.2s ease;
            background: #fff;
        }

        .form-control::placeholder { color: #9ca3af; }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* TC alanı */
        .form-control.tc-input {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 3px;
            text-align: center;
        }

        .tc-hint {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
        }

        .password-toggle:hover { color: #4b5563; }

        /* Beni Hatırla */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .remember-row label {
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
            user-select: none;
        }

        .remember-row .remember-note {
            margin-left: auto;
            font-size: 11px;
            color: #10b981;
            font-weight: 600;
        }

        /* Giriş Butonu */
        .btn-login {
            width: 100%;
            padding: 17px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(to right, #ffffff, #ffe4c4);
            border: 1px solid #fed7aa;
            color: #1e3a8a;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(255, 140, 0, 0.1);
            margin-bottom: 28px;
        }

        .btn-login:hover {
            box-shadow: 0 6px 12px -2px rgba(255, 140, 0, 0.25);
            transform: translateY(-1px);
        }

        /* TC Giriş Butonu */
        .btn-login-tc {
            width: 100%;
            padding: 17px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(to right, #e0f2fe, #bae6fd);
            border: 1px solid #7dd3fc;
            color: #1e3a8a;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.1);
            margin-bottom: 28px;
        }

        .btn-login-tc:hover {
            box-shadow: 0 6px 12px -2px rgba(14, 165, 233, 0.3);
            transform: translateY(-1px);
        }

        /* Hata Kutusu */
        .error-box {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            text-align: center;
            border: 1px solid #fecaca;
        }

        /* Daha Fazla */
        .more-info-container {
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 22px;
        }

        .more-info-text {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 12px;
        }

        .btn-more {
            width: 100%;
            padding: 17px;
            border: none;
            border-radius: 8px;
            background: #ea870f;
            color: white;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-more:hover { background: #d1780b; }

        /* Mobil */
        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; height: auto; max-width: 95%; }
            .left-panel { padding: 40px 30px; min-height: 220px; }
            .left-panel .logo-container { margin-bottom: 25px; }
            .welcome-text h1 { font-size: 30px; }
            .right-panel { padding: 30px 25px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- SOL PANEL -->
    <div class="left-panel">
        <div class="logo-container">
            <img src="/isg/assets/logo.png" alt="Magna Logo" style="height:47px; width:auto; object-fit:contain;">
        </div>
        <div class="welcome-text">
            <h1>Hoşgeldiniz,<br>İSG Portalı</h1>
            <p>İş sağlığı ve güvenliği yönetim sistemimize hoş geldiniz.</p>
        </div>
    </div>

    <!-- SAĞ PANEL -->
    <div class="right-panel">

        <?php if (!empty($error)): ?>
            <div class="error-box"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- SEKME GEÇİŞ DÜĞME -->
        <div class="tab-switcher">
            <button class="tab-btn <?= (isset($loginTab) && $loginTab === 'tc') ? '' : 'active' ?>"
                    id="btnTabEmail" onclick="switchTab('email')" type="button">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                E-posta ile Giriş
            </button>
            <button class="tab-btn <?= (isset($loginTab) && $loginTab === 'tc') ? 'active' : '' ?>"
                    id="btnTabTc" onclick="switchTab('tc')" type="button">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 10h.01M12 10h.01M8 10h.01M6 14h12"/></svg>
                TC ile Giriş
            </button>
        </div>

        <!-- ── E-POSTA FORMU ── -->
        <div class="form-panel <?= (isset($loginTab) && $loginTab === 'tc') ? '' : 'active' ?>" id="panelEmail">
            <form action="/isg/index.php?url=login/auth" method="POST">
                <div class="form-group">
                    <input type="email" name="username" class="form-control" placeholder="E-posta Adresi" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Şifre" required>
                    <div class="password-toggle" onclick="togglePassword()" title="Şifreyi Göster/Gizle">
                        <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                </div>
                <div class="remember-row">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Beni hatırla</label>
                    <span class="remember-note">180 gün</span>
                </div>
                <button type="submit" class="btn-login">Giriş Yap</button>
            </form>
        </div>

        <!-- ── TC NUMARASI FORMU ── -->
        <div class="form-panel <?= (isset($loginTab) && $loginTab === 'tc') ? 'active' : '' ?>" id="panelTc">
            <form action="/isg/index.php?url=login/auth_tc" method="POST">
                <div class="form-group">
                    <input type="text" name="tc" id="tcInput" class="form-control tc-input"
                           placeholder="_ _ _ _ _ _ _ _ _ _ _"
                           maxlength="11" pattern="\d{11}"
                           inputmode="numeric"
                           oninput="this.value=this.value.replace(/\D/g,'')"
                           autocomplete="off" required>
                </div>
                <p class="tc-hint">11 haneli TC kimlik numaranızı giriniz</p>
                <div class="remember-row">
                    <input type="checkbox" name="remember_tc" id="remember_tc">
                    <label for="remember_tc">Beni hatırla</label>
                    <span class="remember-note">180 gün</span>
                </div>
                <button type="submit" class="btn-login-tc">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:-3px;margin-right:6px;"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 10h.01M12 10h.01M8 10h.01M6 14h12"/></svg>
                    TC ile Giriş Yap
                </button>
            </form>
        </div>

        <!-- DAHA FAZLA -->
        <div class="more-info-container">
            <div class="more-info-text">Daha fazla merak ettikleriniz için</div>
            <a href="https://www.magna.com/company/company-information/magna-groups/magna-seating"
               target="_blank" rel="noopener" class="btn-more">Daha Fazla</a>
        </div>
    </div>
</div>

<script>
    // Sekme geçişi
    function switchTab(tab) {
        const isEmail = tab === 'email';
        document.getElementById('btnTabEmail').classList.toggle('active', isEmail);
        document.getElementById('btnTabTc').classList.toggle('active', !isEmail);
        document.getElementById('panelEmail').classList.toggle('active', isEmail);
        document.getElementById('panelTc').classList.toggle('active', !isEmail);
        if (!isEmail) { setTimeout(() => document.getElementById('tcInput').focus(), 50); }
    }

    // Şifre Göster / Gizle
    function togglePassword() {
        const inp = document.getElementById('password');
        const ico = document.getElementById('eye-icon');
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
            ico.style.color = '#3b82f6';
        } else {
            inp.type = 'password';
            ico.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
            ico.style.color = '#9ca3af';
        }
    }

    // TC alanına sadece rakam, boşluk engelle
    document.getElementById('tcInput').addEventListener('keypress', function(e) {
        if (!/\d/.test(e.key)) e.preventDefault();
    });
</script>

</body>
</html>
