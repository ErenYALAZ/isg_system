<?php

class KullaniciController {

    public function index() {
        // Oturum kontrolü
        if (!isset($_SESSION['user_id'])) {
            header("Location: /isg/index.php?url=login");
            exit;
        }

        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $roleSayisi = (int)($_SESSION['role'] ?? 1);
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$roleSayisi] ?? 'Personel';
        $sayfaBasligi = "Kullanıcı Yönetimi - MAISG";

        // Veritabanı Bağlantısı
        $dbClass = new Database();
        $db = $dbClass->getConnection();

        // Tüm kullanıcıları çekiyoruz
        $query = "SELECT * FROM uye ORDER BY kuladsoyad ASC";
        $stmt = $db->query($query);
        $kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'app/views/kullanicilar.php';
    }
}
?>
