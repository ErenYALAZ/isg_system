<?php

class LoginController {

    public function index() {
        // Oturum varsa ve giriş yapılmışsa ana sayfaya at
        if (isset($_SESSION['user_id'])) {
            header("Location: /isg/index.php?url=home");
            exit;
        }

        // ── Beni Hatırla cookie kontrolü ──
        if (!empty($_COOKIE['isg_remember_id']) && !empty($_COOKIE['isg_remember_token'])) {
            $db = (new Database())->getConnection();
            $stmt = $db->prepare("SELECT * FROM uye WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $_COOKIE['isg_remember_id']]);
            $user = $stmt->fetch();
            if ($user) {
                $expectedToken = md5($user['id'] . $user['kulsifre'] . 'isg_salt_2024');
                if (hash_equals($expectedToken, $_COOKIE['isg_remember_token'])) {
                    $this->_setSession($user);
                    header("Location: /isg/index.php?url=home");
                    exit;
                }
            }
            // Geçersiz cookie - temizle
            setcookie('isg_remember_id',    '', time() - 3600, '/');
            setcookie('isg_remember_token', '', time() - 3600, '/');
        }

        // Hata mesajı kontrolü
        $error    = $_SESSION['error'] ?? null;
        $loginTab = $_SESSION['login_tab'] ?? 'email'; // aktif sekme
        unset($_SESSION['error'], $_SESSION['login_tab']);

        $sayfaBasligi = "Giriş Yap - İSG Yönetim Sistemi";
        require_once 'app/views/login.php';
    }

    public function auth() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Boşluklu yazılma veya kopyalama ihtimaline karşı trim() ile sağ/sol boşlukları siliyoruz
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Veritabanı bağlantısı nesnesi oluştur
            $dbClass = new Database();
            $db = $dbClass->getConnection();

            // Formdan gelen düz şifreyi (örneğin 123456), veritabanında olduğu gibi MD5 ile kriptolanmış haline çeviriyoruz
            $hashed_password = md5($password);
            
            $query = "SELECT * FROM uye WHERE kulmail = :username AND kulsifre = :password LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password); 
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user) {
                $this->_setSession($user);

                // Beni Hatırla - 180 gün
                if (!empty($_POST['remember'])) {
                    $expire = time() + (180 * 24 * 60 * 60);
                    setcookie('isg_remember_id',    $user['id'], $expire, '/', '', false, true);
                    setcookie('isg_remember_token', md5($user['id'] . $user['kulsifre'] . 'isg_salt_2024'), $expire, '/', '', false, true);
                }
                
                header("Location: /isg/index.php?url=home");
                exit;
            } else {
                // Hatalı Giriş
                $_SESSION['error'] = "Kullanıcı adı veya şifre hatalı!";
                header("Location: /isg/index.php?url=login");
                exit;
            }
        }
    }

    public function auth_tc() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tc = trim($_POST['tc'] ?? '');

            if (strlen($tc) !== 11 || !ctype_digit($tc)) {
                $_SESSION['error']     = "Lütfen geçerli bir 11 haneli TC kimlik numarası giriniz!";
                $_SESSION['login_tab'] = 'tc';
                header("Location: /isg/index.php?url=login");
                exit;
            }

            $db = (new Database())->getConnection();
            $stmt = $db->prepare("SELECT * FROM uye WHERE tc = :tc LIMIT 1");
            $stmt->execute(['tc' => $tc]);
            $user = $stmt->fetch();

            if ($user) {
                $this->_setSession($user);

                // Beni Hatırla - 180 gün
                if (!empty($_POST['remember_tc'])) {
                    $expire = time() + (180 * 24 * 60 * 60);
                    setcookie('isg_remember_id',    $user['id'], $expire, '/', '', false, true);
                    setcookie('isg_remember_token', md5($user['id'] . $user['kulsifre'] . 'isg_salt_2024'), $expire, '/', '', false, true);
                }

                header("Location: /isg/index.php?url=home");
                exit;
            } else {
                $_SESSION['error']     = "Bu TC no ile kayıtlı kullanıcı bulunamadı!";
                $_SESSION['login_tab'] = 'tc';
                header("Location: /isg/index.php?url=login");
                exit;
            }
        }
    }

    private function _setSession($user) {
        $_SESSION['user_id']        = $user['id'];
        $_SESSION['username']       = $user['kulmail'];
        $_SESSION['user_full_name'] = $user['kuladsoyad'] ?? $user['kulad'];
        $_SESSION['role']           = (int)($user['role'] ?? 0);
    }

    public function logout() {
        session_destroy();
        setcookie('isg_remember_id',    '', time() - 3600, '/');
        setcookie('isg_remember_token', '', time() - 3600, '/');
        header("Location: /isg/index.php?url=login");
        exit;
    }
}
?>
