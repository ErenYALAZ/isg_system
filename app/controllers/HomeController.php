<?php

class HomeController
{

    public function index()
    {
        // Eğer giriş yapılmamışsa login sayfasına yönlendir
        if (!isset($_SESSION['user_id'])) {
            header("Location: /isg/index.php?url=login");
            exit;
        }

        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $role = (int) ($_SESSION['role'] ?? 1);

        // Ünvan eşleştirmesi
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';

        // Yönetici (4), ISG Uzmanı (2), Admin (5) dışındakiler sadece duyuruları görebilir
        if (!in_array($role, [2, 4, 5])) {
            header("Location: /isg/index.php?url=duyurular");
            exit;
        }

        $db = (new Database())->getConnection();

        // 1. Genel İstatistikler
        $genelQ = "SELECT 
                    SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
                    SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
                    COUNT(*) as toplam
                  FROM announcements";
        $genelStats = $db->query($genelQ)->fetch(PDO::FETCH_ASSOC);

        // 2. Departman Bazlı Veriler (Küçük grafik için)
        $deptData = $db->query("SELECT department_tag, is_hazard FROM announcements")->fetchAll(PDO::FETCH_ASSOC);
        $depts = ["Kalite", "Üretim", "Lojistik", "IT", "OT", "Bakım", "İSG", "İdari İşler", "İK"];
        $deptStats = [];
        foreach ($depts as $d) {
            $deptStats[$d] = ['c' => 0, 'u' => 0];
        }
        foreach ($deptData as $it) {
            $tags = explode(', ', $it['department_tag']);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if (isset($deptStats[$tag])) {
                    if ($it['is_hazard'] == 0)
                        $deptStats[$tag]['c']++;
                    else
                        $deptStats[$tag]['u']++;
                }
            }
        }

        // 3. Son 3 Duyuru
        $sonDuyurular = $db->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

        $sayfaBasligi = "MAISG - Master Dashboard";
        require_once 'app/views/home.php';
    }
}
?>