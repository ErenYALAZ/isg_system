<?php

class DuyurularController {

    public function index() {
        // Oturum kontrolü
        if (!isset($_SESSION['user_id'])) {
            header("Location: /isg/index.php?url=login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $role = (int)($_SESSION['role'] ?? 1);
        $isHigh = in_array($role, [2, 4, 5]);
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';
        $sayfaBasligi = "Duyurular - MAISG";

        // Veritabanı Bağlantısı
        $dbClass = new Database();
        $db = $dbClass->getConnection();

        $where = "";
        $whereParams = [];

        // Ay filtresi (YYYY-MM formatında)
        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;

        // Yıl filtresi — ay seçilmemişse yıla göre filtrele (tüm ayları göster)
        $secilenYil = isset($_GET['yil']) && is_numeric($_GET['yil']) ? (int)$_GET['yil'] : null;
        // Ay seçildiyse yılı ondan al
        if ($secilenAy && !$secilenYil) {
            $secilenYil = (int)substr($secilenAy, 0, 4);
        }

        if (isset($_GET['tip'])) {
            if ($_GET['tip'] == 'cozulmus') {
                $where = "WHERE a.is_hazard = 0";
            } elseif ($_GET['tip'] == 'cozulmemis') {
                $where = "WHERE a.is_hazard = 1";
            }
        }

        // Ay filtresi ekle
        if ($secilenAy) {
            $where = $where ? $where . " AND DATE_FORMAT(a.created_at, '%Y-%m') = :ay"
                           : "WHERE DATE_FORMAT(a.created_at, '%Y-%m') = :ay";
            $whereParams[':ay'] = $secilenAy;
        }

        // Mevcut ayları çek (dropdown için) - en fazla 24 ay geriye git
        $ayListesiStmt = $db->query("SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') as ay_kodu,
                                     DATE_FORMAT(created_at, '%M %Y') as ay_adi
                                     FROM announcements
                                     ORDER BY ay_kodu DESC
                                     LIMIT 24");
        $mevcutAylar = $ayListesiStmt->fetchAll();

        // Türkçe ay adları için dizi
        $ayTr = ['January'=>'Ocak','February'=>'Şubat','March'=>'Mart','April'=>'Nisan',
                 'May'=>'Mayıs','June'=>'Haziran','July'=>'Temmuz','August'=>'Ağustos',
                 'September'=>'Eylül','October'=>'Ekim','November'=>'Kasım','December'=>'Aralık'];
        foreach ($mevcutAylar as &$ay) {
            foreach ($ayTr as $en => $tr) {
                $ay['ay_adi'] = str_replace($en, $tr, $ay['ay_adi']);
            }
        }
        unset($ay);

        $perPage = 15;
        $sayfa = max(1, (int)($_GET['sayfa'] ?? 1));
        $offset = ($sayfa - 1) * $perPage;

        // 1. Toplam sayı (pagination butonu için)
        $cStmt = $db->prepare("SELECT COUNT(*) FROM announcements a $where");
        $cStmt->execute($whereParams);
        $toplamDuyuru = (int)$cStmt->fetchColumn();

        // 2. Duyuruları ve TÜM görsellerini Çekme (LIMIT ile sayfalandırılmış)
        $duyurularQuery = "SELECT a.*, GROUP_CONCAT(ai.image_data SEPARATOR '|') as tum_resimler 
                           FROM announcements a 
                           LEFT JOIN announcement_images ai ON a.id = ai.announcement_id 
                           $where
                           GROUP BY a.id 
                           ORDER BY a.created_at DESC
                           LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($duyurularQuery);
        foreach ($whereParams as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $tumDuyurular = $stmt->fetchAll();


        // 3. Okunma (Read) Durumu Bilgilerini Çekme

        $okunanDuyuruIds = [];
        try {
            $readQuery = "SELECT announcement_id FROM announcement_reads WHERE user_id = :uye_id";
            $rStmt = $db->prepare($readQuery);
            $rStmt->bindParam(':uye_id', $userId);
            $rStmt->execute();
            while ($row = $rStmt->fetch()) { $okunanDuyuruIds[] = $row['announcement_id']; }
        } catch (PDOException $e) {}

        // 4. Okunma Sayısı
        $readCountQuery = "SELECT announcement_id, COUNT(*) as c FROM announcement_reads GROUP BY announcement_id";
        $rcStmt = $db->query($readCountQuery);
        $readCounts = $rcStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // --- FOCUS ID: Sohbetten gelen duyuruyu ilk sıraya al ---
        $focusId = isset($_GET['focus_id']) ? trim($_GET['focus_id']) : null;
        $focusedRaw = null;
        if ($focusId) {
            $fStmt = $db->prepare("SELECT a.*, GROUP_CONCAT(ai.image_data SEPARATOR '|') as tum_resimler 
                                   FROM announcements a 
                                   LEFT JOIN announcement_images ai ON a.id = ai.announcement_id 
                                   WHERE a.id = :fid GROUP BY a.id");
            $fStmt->execute([':fid' => $focusId]);
            $focusedRaw = $fStmt->fetch();
        }

        $islenenDuyurular = [];

        if ($focusedRaw) {
            $isResolved = $focusedRaw['is_hazard'] ?? 0;
            $tarihSaat = date('d.m.Y H:i', strtotime($focusedRaw['created_at']));
            $islenenDuyurular[] = [
                'id' => $focusedRaw['id'],
                'baslik' => $focusedRaw['title'] ?? 'İsimsiz',
                'icerik' => $focusedRaw['content'] ?? '',
                'is_hazard' => $isResolved,
                'okundu_mu' => in_array($focusedRaw['id'], $okunanDuyuruIds),
                'resimler' => !empty($focusedRaw['tum_resimler']) ? explode('|', $focusedRaw['tum_resimler']) : [],
                'departman' => $focusedRaw['department_tag'] ?? '',
                'kategori' => $focusedRaw['hazard_category'] ?? 'Genel',
                'unvan' => $unvan,
                'okunma_sayisi' => $readCounts[$focusedRaw['id']] ?? 0,
                'tarih' => $tarihSaat,
                'gun_farki' => floor((time() - strtotime($focusedRaw['created_at'])) / 86400),
                'danger_level' => $focusedRaw['danger_level'] ?? 'Düşük'
            ];
        }

        foreach ($tumDuyurular as $duyuru) {
            if ($focusId && (string)$duyuru['id'] === (string)$focusId) continue;

            $isResolved = $duyuru['is_hazard'] ?? 0;
            $okunduMu = in_array($duyuru['id'], $okunanDuyuruIds);
            $tarihSaat = date('d.m.Y H:i', strtotime($duyuru['created_at']));

            $islenenDuyurular[] = [
                'id' => $duyuru['id'],
                'baslik' => $duyuru['title'] ?? 'İsimsiz',
                'icerik' => $duyuru['content'] ?? '',
                'is_hazard' => $isResolved,
                'okundu_mu' => $okunduMu,
                'resimler' => !empty($duyuru['tum_resimler']) ? explode('|', $duyuru['tum_resimler']) : [],
                'departman' => $duyuru['department_tag'] ?? '',
                'kategori' => $duyuru['hazard_category'] ?? 'Genel',
                'unvan' => $unvan,
                'okunma_sayisi' => $readCounts[$duyuru['id']] ?? 0,
                'tarih' => $tarihSaat,
                'gun_farki' => floor((time() - strtotime($duyuru['created_at'])) / 86400),
                'danger_level' => $duyuru['danger_level'] ?? 'Düşük'
            ];
        }

        require_once 'app/views/duyurular.php';
    }

    public function ajax_load() {
        if (!isset($_SESSION['user_id'])) { echo json_encode(['cards' => [], 'hasMore' => false]); exit; }

        $userId = $_SESSION['user_id'];
        $role = (int)($_SESSION['role'] ?? 1);
        $isHigh = in_array($role, [2, 4, 5]);
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';

        $dbClass = new Database();
        $db = $dbClass->getConnection();

        $where = '';
        $whereParams = [];

        if (isset($_GET['tip'])) {
            if ($_GET['tip'] == 'cozulmus') $where = 'WHERE a.is_hazard = 0';
            elseif ($_GET['tip'] == 'cozulmemis') $where = 'WHERE a.is_hazard = 1';
        }

        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;
        if ($secilenAy) {
            $where = $where ? $where . " AND DATE_FORMAT(a.created_at, '%Y-%m') = :ay"
                           : "WHERE DATE_FORMAT(a.created_at, '%Y-%m') = :ay";
            $whereParams[':ay'] = $secilenAy;
        }

        $perPage = 15;
        $sayfa = max(1, (int)($_GET['sayfa'] ?? 2));
        $offset = ($sayfa - 1) * $perPage;

        $cStmt = $db->prepare("SELECT COUNT(*) FROM announcements a $where");
        $cStmt->execute($whereParams);
        $toplamDuyuru = (int)$cStmt->fetchColumn();

        $stmt = $db->prepare("SELECT a.*, GROUP_CONCAT(ai.image_data SEPARATOR '|') as tum_resimler
            FROM announcements a LEFT JOIN announcement_images ai ON a.id = ai.announcement_id
            $where GROUP BY a.id ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset");
        foreach ($whereParams as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $readQuery = "SELECT announcement_id FROM announcement_reads WHERE user_id = :uid";
        $rStmt = $db->prepare($readQuery);
        $rStmt->bindParam(':uid', $userId);
        $rStmt->execute();
        $okunanIds = $rStmt->fetchAll(PDO::FETCH_COLUMN);

        $rcStmt = $db->query("SELECT announcement_id, COUNT(*) as c FROM announcement_reads GROUP BY announcement_id");
        $readCounts = $rcStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $deptColorMap = [
            'Kalite' => '#0ea5e9','Üretim' => '#10b981','Lojistik' => '#f59e0b','IT' => '#3b82f6',
            'OT' => '#6366f1','Bakım' => '#eab308','İSG' => '#ef4444','İdari İşler' => '#8b5cf6',
            'İK' => '#ec4899','Genel' => '#64748b'
        ];
        $getColor = function($d) use ($deptColorMap) { return $deptColorMap[trim($d)] ?? '#3b82f6'; };

        $cards = [];
        foreach ($rows as $duyuru) {
            $res = ($duyuru['is_hazard'] == 0);
            $okundu = in_array($duyuru['id'], $okunanIds);
            $tarih = date('d.m.Y H:i', strtotime($duyuru['created_at']));
            $saatFarki = floor((time() - strtotime($duyuru['created_at'])) / 3600);
            $resimler = !empty($duyuru['tum_resimler']) ? explode('|', $duyuru['tum_resimler']) : [];
            $danger = $duyuru['danger_level'] ?? 'Düşük';
            $dangerClass = ($danger == 'Yüksek' && !$res) ? 'pulse-high' : '';
            $statusClass = $res ? 'status-resolved' : ($saatFarki >= 32 ? 'status-critical' : 'status-pending');

            ob_start(); ?>
            <div class="p-card <?= $statusClass ?>" id="duyuru-<?= $duyuru['id'] ?>" data-solved="<?= $res ? 'true' : 'false' ?>"
                onclick="location.href='/isg/index.php?url=duyurular/okundu_yap&id=<?= $duyuru['id'] ?>'">

                <?php if (!empty($resimler)):
                    $details = [
                        'baslik' => $duyuru['title'] ?? '',
                        'tarih' => $tarih, 'kategori' => $duyuru['hazard_category'] ?? 'Genel',
                        'icerik' => $duyuru['content'] ?? '', 'res' => $res, 'saat_farki' => $saatFarki,
                        'danger_level' => $duyuru['danger_level'] ?? 'Düşük',
                        'depts' => array_map(function($dep) use ($getColor) { return ['name' => trim($dep), 'color' => $getColor($dep)]; }, $depts)
                    ]; ?>
                    <div class="card-thumb" onclick="event.stopPropagation(); openGallery(<?= htmlspecialchars(json_encode($resimler)) ?>, <?= htmlspecialchars(json_encode($details)) ?>)">
                        <img src="/isg/<?= htmlspecialchars($resimler[0]) ?>" alt="" loading="lazy">
                        <?php if (count($resimler) > 1): ?>
                            <div class="card-thumb-count">+<?= count($resimler) - 1 ?> GÖRSEL</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="card-body">
                    <div class="card-top-row">
                        <div>
                            <?php if ($res): ?>
                                <div class="solved-b"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"></polyline></svg> ÇÖZÜLDÜ</div>
                            <?php elseif ($saatFarki >= 32): ?>
                                <div class="critical-b"><span style="width:8px;height:8px;background:#ef4444;border-radius:50%;display:inline-block;"></span> GECİKMİŞ</div>
                            <?php else: ?>
                                <div class="pending-b"><span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;"></span> BEKLEYEN</div>
                            <?php endif; ?>
                        </div>
                        <div class="icon-row" onclick="event.stopPropagation()">
                            <div class="icon-btn" onclick="showReads('<?= $duyuru['id'] ?>')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span class="wa-badge"><?= $readCounts[$duyuru['id']] ?? 0 ?></span>
                            </div>
                            <?php if ($isHigh): ?>
                                <?php if (!$res): ?>
                                    <div class="icon-btn ok" onclick="showOnay('<?= $duyuru['id'] ?>')">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                <?php endif; ?>
                                <a href="/isg/index.php?url=duyurular/duzenle&id=<?= $duyuru['id'] ?>" class="icon-btn">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path></svg>
                                </a>
                                <div class="icon-btn del" onclick="window.dID='<?= $duyuru['id'] ?>'; document.getElementById('del_m').style.display='flex'">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6V20a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-title">
                        <?php if (!$okundu): ?><span class="unread-dot"></span><?php endif; ?>
                        <?= htmlspecialchars($duyuru['title'] ?? '') ?>
                    </div>
                    <div class="card-meta"><?= $tarih ?> &bull; <?= htmlspecialchars($duyuru['hazard_category'] ?? 'Genel') ?></div>

                    <div class="tag-row">
                        <?php foreach ($depts as $dep): $c = $getColor($dep); ?>
                            <span class="tag-p" style="background:<?= $c ?>15; color:<?= $c ?>; border:1.5px solid <?= $c ?>30;"><?= htmlspecialchars(trim($dep)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <p class="card-content"><?= nl2br(htmlspecialchars($duyuru['content'] ?? '')) ?></p>
                </div>

                <div class="wa-footer" style="gap: 10px;">
                    <div style="font-size:12px; color:#475569; font-weight:800; white-space: nowrap;"><?= date('H:i', strtotime($duyuru['created_at'])) ?></div>
                    
                    <div style="flex:1; display:flex; justify-content:center;">
                        <?php 
                        $dl = $duyuru['danger_level'] ?? 'Düşük';
                        $dl_c = $dl == 'Yüksek' ? 'danger-high' : ($dl == 'Orta' ? 'danger-medium' : 'danger-low');
                        $p_class = ($dl == 'Yüksek' && !$res) ? 'pulse-high' : '';
                        ?>
                        <span class="danger-pill <?= $dl_c ?> <?= $p_class ?>"><?= mb_strtoupper($dl) ?> TEHLİKE</span>
                    </div>

                    <?php if (!$okundu && $saatFarki >= 32): ?>
                        <div style="font-size:9px; color:#ef4444; font-weight:900; letter-spacing:0.4px; text-align:center; flex:1;">
                            DUYURU OKUMADINIZ LÜTFEN SÜRESİ GEÇMEDEN OKUYUNUZ !
                        </div>
                    <?php endif; ?>

                    <?php if ($okundu): ?>
                        <svg style="width:20px; stroke:#3b82f6" viewBox="0 0 24 24" fill="none" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"></polyline><polyline points="24 6 13 17 8 12"></polyline></svg>
                    <?php else: ?>
                        <svg style="width:19px; stroke:#4b5563; opacity:0.5" viewBox="0 0 24 24" fill="none" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $cards[] = ob_get_clean();
        }

        $loaded = $offset + count($rows);
        header('Content-Type: application/json');
        echo json_encode([
            'cards' => $cards,
            'hasMore' => $loaded < $toplamDuyuru,
            'remaining' => max(0, $toplamDuyuru - $loaded)
        ]);
        exit;
    }

    public function duzenle() {
        if (!isset($_GET['id']) || !in_array($_SESSION['role'], [2, 4, 5])) header("Location: /isg/");
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT * FROM announcements WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        $duyuru = $stmt->fetch();
        if (!$duyuru) exit("Bulunamadı.");
        
        $role = $_SESSION['role'] ?? 1;
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';
        
        $sayfaBasligi = "Duyuruyu Düzenle";
        require_once 'app/views/duyuru_ekle.php'; // Ekle sayfasını düzenle olarak da kullanabiliriz (IF ile)
    }

    public function guncelle() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $baslik = $_POST['baslik'];
            $icerik = $_POST['icerik'];
            $kategori = $_POST['kategori'];
            $deptString = implode(', ', $_POST['departmanlar'] ?? []);
            
            $danger = $_POST['danger_level'] ?? 'Düşük';
            
            $db = (new Database())->getConnection();
            $up = $db->prepare("UPDATE announcements SET title=:t, content=:c, hazard_category=:k, department_tag=:d, danger_level=:dl WHERE id=:id");
            $up->execute(['t'=>$baslik, 'c'=>$icerik, 'k'=>$kategori, 'd'=>$deptString, 'dl'=>$danger, 'id'=>$id]);

            // Belirli görselleri silme (Talebe Göre: Anında Silebilirsiniz)
            if (isset($_POST['silinecek_gorseller']) && is_array($_POST['silinecek_gorseller'])) {
                foreach ($_POST['silinecek_gorseller'] as $imgId) {
                    // Önce dosya yolunu alalım ki klasörden silelim
                    $pathSt = $db->prepare("SELECT image_data FROM announcement_images WHERE id = :iid");
                    $pathSt->execute(['iid' => $imgId]);
                    $path = $pathSt->fetchColumn();
                    if ($path && file_exists($path)) @unlink($path);

                    $delSt = $db->prepare("DELETE FROM announcement_images WHERE id = :iid");
                    $delSt->execute(['iid' => $imgId]);
                }
            }

            // Toplu Resim Silme (Eskiden kalan mantık, hala lazım olabilir diye duruyor)
            if (isset($_POST['eski_resimleri_sil']) && $_POST['eski_resimleri_sil'] == '1') {
                $db->prepare("DELETE FROM announcement_images WHERE announcement_id = :aid")->execute(['aid' => $id]);
            }

            if (isset($_FILES['duyuru_gorselleri'])) {
                $targetDir = "assets/uploads/announcements/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                foreach ($_FILES['duyuru_gorselleri']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['duyuru_gorselleri']['error'][$key] == 0) {
                        $fileName = time() . '_' . $key . '_' . basename($_FILES["duyuru_gorselleri"]["name"][$key]);
                        $targetFilePath = $targetDir . $fileName;
                        if (move_uploaded_file($tmp_name, $targetFilePath)) {
                            $imageId = $this->generateUUID();
                            $insImg = $db->prepare("INSERT INTO announcement_images (id, announcement_id, image_data) VALUES (:id, :aid, :url)");
                            $insImg->execute(['id' => $imageId, 'aid' => $id, 'url' => $targetFilePath]);
                        }
                    }
                }
            }
            
            header("Location: /isg/index.php?url=duyurular");
        }
    }

    public function okunma_detay() {
        $aid = $_GET['id'] ?? '';
        $db = (new Database())->getConnection();
        
        // Tüm üyeler ve o duyuruyu okuma durumları
        $q = "SELECT u.kuladsoyad, (SELECT read_at FROM announcement_reads r WHERE r.announcement_id = :aid AND r.user_id = u.id) as read_at 
              FROM uye u ORDER BY kuladsoyad ASC";
        $st = $db->prepare($q);
        $st->execute(['aid' => $aid]);
        $list = $st->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($list);
    }

    public function onayla() {
        if (!isset($_GET['id']) || !in_array($_SESSION['role'], [2, 4, 5])) exit;
        $db = (new Database())->getConnection();
        $aid = $_GET['id'];
        
        // is_hazard = 0 yaparsak "ÇÖZÜLDÜ/ONAYLANDI" anlamına gelecek.
        $up = $db->prepare("UPDATE announcements SET is_hazard = 0 WHERE id = :aid");
        $up->execute(['aid' => $aid]);
        header("Location: /isg/index.php?url=duyurular");
    }

    public function istatistik() {
        if (!isset($_SESSION['user_id'])) { header("Location: /isg/index.php?url=login"); exit; }
        $role = $_SESSION['role'] ?? 1;
        if (!in_array($role, [2, 4, 5])) {
            header("Location: /isg/index.php?url=duyurular");
            exit;
        }

        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;
        $db = (new Database())->getConnection();
        
        $where = "";
        $params = [];
        if ($secilenAy) {
            $where = " WHERE DATE_FORMAT(created_at, '%Y-%m') = :ay";
            $params[':ay'] = $secilenAy;
        }

        $q = "SELECT 
                SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
                SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
                COUNT(*) as toplam
              FROM announcements $where";
        $stmtS = $db->prepare($q);
        $stmtS->execute($params);
        $stats = $stmtS->fetch(PDO::FETCH_ASSOC);

        $listQ = "SELECT a.id, a.title, a.content, a.is_hazard, a.hazard_category, a.department_tag, a.created_at,
                         GROUP_CONCAT(ai.image_data SEPARATOR '|') as tum_resimler
                  FROM announcements a
                  LEFT JOIN announcement_images ai ON a.id = ai.announcement_id
                  " . ($secilenAy ? "WHERE DATE_FORMAT(a.created_at, '%Y-%m') = :ay" : "") . "
                  GROUP BY a.id
                  ORDER BY a.created_at DESC";
        $stmtL = $db->prepare($listQ);
        $stmtL->execute($params);
        $duyuruListesi = $stmtL->fetchAll(PDO::FETCH_ASSOC);

        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';
        $sayfaBasligi = ($secilenAy ? 'Aylık Performans Raporu' : 'Bitmiş İşlerin İstatistiği') . ' - MAISG';

        require_once 'app/views/istatistik.php';
    }

    public function departman_istatistik() {
        if (!isset($_SESSION['user_id'])) header("Location: /isg/index.php?url=login");
        $role = $_SESSION['role'] ?? 1;
        if (!in_array($role, [2, 4, 5])) {
            header("Location: /isg/index.php?url=duyurular");
            exit;
        }

        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;
        $db = (new Database())->getConnection();
        
        $where = "";
        $params = [];
        if ($secilenAy) {
            $where = " WHERE DATE_FORMAT(created_at, '%Y-%m') = :ay";
            $params[':ay'] = $secilenAy;
        }

        $q = "SELECT department_tag, is_hazard FROM announcements $where";
        $stmt = $db->prepare($q);
        $stmt->execute($params);
        $duyurular = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $depts = ["Kalite", "Üretim", "Lojistik", "IT", "OT", "Bakım", "İSG", "İdari İşler", "İK"];
        $stats = [];
        foreach($depts as $d) { $stats[$d] = ['cozulmus' => 0, 'cozulmemis' => 0, 'toplam' => 0]; }

        foreach($duyurular as $it) {
            $tags = explode(', ', $it['department_tag']);
            foreach($tags as $tag) {
                $tag = trim($tag);
                if(isset($stats[$tag])) {
                    $stats[$tag]['toplam']++;
                    if($it['is_hazard'] == 0) $stats[$tag]['cozulmus']++;
                    else $stats[$tag]['cozulmemis']++;
                }
            }
        }

        $role = $_SESSION['role'] ?? 1;
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';

        require_once 'app/views/departman_istatistik.php';
    }

    public function okundu_yap() {
        if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) exit;
        
        $db = (new Database())->getConnection();
        $aid = $_GET['id'];
        $uid = $_SESSION['user_id'];

        // Zaten okundu mu kontrol et
        $check = $db->prepare("SELECT id FROM announcement_reads WHERE announcement_id = :aid AND user_id = :uid");
        $check->execute(['aid' => $aid, 'uid' => $uid]);
        if ($check->rowCount() == 0) {
            $rid = $this->generateUUID();
            $ins = $db->prepare("INSERT INTO announcement_reads (id, announcement_id, user_id) VALUES (:id, :aid, :uid)");
            $ins->execute(['id' => $rid, 'aid' => $aid, 'uid' => $uid]);
        }
        header("Location: /isg/index.php?url=duyurular");
    }

    public function sil() {
        if (!isset($_GET['id']) || !in_array($_SESSION['role'], [2, 4, 5])) exit;

        $db = (new Database())->getConnection();
        $aid = $_GET['id'];

        // Resimleri ve okuma kayıtlarını da silebiliriz veya kalsın cascade varsa... 
        // Temiz temiz silelim.
        $stmt = $db->prepare("SELECT image_data FROM announcement_images WHERE announcement_id = :aid");
        $stmt->execute(['aid' => $aid]);
        $imgs = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach($imgs as $img) { if ($img && file_exists($img)) @unlink($img); }

        $db->prepare("DELETE FROM announcement_images WHERE announcement_id = :aid")->execute(['aid' => $aid]);
        $db->prepare("DELETE FROM announcement_reads WHERE announcement_id = :aid")->execute(['aid' => $aid]);
        $db->prepare("DELETE FROM announcements WHERE id = :aid")->execute(['aid' => $aid]);

        header("Location: /isg/index.php?url=duyurular");
    }

    public function sistemi_temizle() {
        if (!in_array($_SESSION['role'] ?? 1, [2, 4, 5])) exit("Yetkisiz işlem.");

        $db = (new Database())->getConnection();
        
        // 1. Duyuru Resimlerini Sil
        $q1 = $db->query("SELECT image_data FROM announcement_images");
        while($row = $q1->fetch()) { if($row['image_data'] && file_exists($row['image_data'])) @unlink($row['image_data']); }

        // 2. Mesaj Resimlerini Sil
        $q2 = $db->query("SELECT image_path FROM message_images");
        while($row = $q2->fetch()) { if($row['image_path'] && file_exists($row['image_path'])) @unlink($row['image_path']); }

        // 3. Veritabanını Temizle
        $db->exec("DELETE FROM announcement_reads");
        $db->exec("DELETE FROM announcement_images");
        $db->exec("DELETE FROM announcements");
        $db->exec("DELETE FROM message_deletions");
        $db->exec("DELETE FROM message_images");
        $db->exec("DELETE FROM messages");

        header("Location: /isg/index.php?url=duyurular");
    }

    public function ekle() {
        // Oturum kontrolü
        if (!isset($_SESSION['user_id'])) {
            header("Location: /isg/index.php?url=login");
            exit;
        }

        $role = $_SESSION['role'] ?? 1;
        
        // Sadece İSG Uzmanı (2), Yönetici (4) ve Admin (5) girebilir
        if (!in_array($role, [2, 4, 5])) {
            // Yetkisi olmayanlar göremez, ana sayfaya yolla
            header("Location: /isg/index.php?url=home");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';
        $sayfaBasligi = "Yeni Duyuru Ekle - İSG Yönetim Sistemi";

        require_once 'app/views/duyuru_ekle.php';
    }

    public function kontrol() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $baslik = $_POST['baslik'] ?? '';
            $icerik = $_POST['icerik'] ?? '';
            $kategori = $_POST['kategori'] ?? '';
            $departmanlar = $_POST['departmanlar'] ?? [];
            $userId = $_SESSION['user_id'];

            // UUID ÜRETELİM (Veritabanınız auto-increment değil, UUID bekliyor)
            $announcementId = $this->generateUUID();

            $dbClass = new Database();
            $db = $dbClass->getConnection();

            $deptString = is_array($departmanlar) ? implode(', ', $departmanlar) : $departmanlar;

            $danger = $_POST['danger_level'] ?? 'Düşük';

            // id alanını mutlaka doldurmalıyız
            $query = "INSERT INTO announcements (id, user_id, title, content, is_hazard, hazard_category, department_tag, danger_level) 
                      VALUES (:id, :user_id, :title, :content, 1, :hazard_category, :department_tag, :danger_level)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $announcementId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':title', $baslik);
            $stmt->bindParam(':content', $icerik);
            $stmt->bindParam(':hazard_category', $kategori);
            $stmt->bindParam(':department_tag', $deptString);
            $stmt->bindParam(':danger_level', $danger);

            if ($stmt->execute()) {
                // Çoklu Görsel Yükleme (announcement_images)
                if (isset($_FILES['duyuru_gorselleri'])) {
                    $targetDir = "assets/uploads/announcements/";
                    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                    foreach ($_FILES['duyuru_gorselleri']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['duyuru_gorselleri']['error'][$key] == 0) {
                            $fileName = time() . '_' . $key . '_' . basename($_FILES["duyuru_gorselleri"]["name"][$key]);
                            $targetFilePath = $targetDir . $fileName;
                            if (move_uploaded_file($tmp_name, $targetFilePath)) {
                                $imageId = $this->generateUUID();
                                $imgQuery = "INSERT INTO announcement_images (id, announcement_id, image_data) VALUES (:id, :aid, :url)";
                                $imgStmt = $db->prepare($imgQuery);
                                $imgStmt->execute(['id' => $imageId, 'aid' => $announcementId, 'url' => $targetFilePath]);
                            }
                        }
                    }
                }
                
                header("Location: /isg/index.php?url=duyurular");
            }
 else {
                echo "Bir hata oluştu.";
            }
        }
    }

    public function pdf_indir() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? 1, [2, 4, 5])) {
            header("Location: /isg/index.php?url=login");
            exit;
        }

        $getSolved = isset($_GET['tip']) && $_GET['tip'] === 'cozulmus';
        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;

        $db = (new Database())->getConnection();
        $isHazard = $getSolved ? 0 : 1;
        $where = "WHERE a.is_hazard = :is_hazard";
        $params = [':is_hazard' => $isHazard];

        if ($secilenAy) {
            $where .= " AND DATE_FORMAT(a.created_at, '%Y-%m') = :ay";
            $params[':ay'] = $secilenAy;
        }

        $q = "SELECT a.*, GROUP_CONCAT(ai.image_data SEPARATOR '|') as tum_resimler
              FROM announcements a
              LEFT JOIN announcement_images ai ON a.id = ai.announcement_id
              $where
              GROUP BY a.id
              ORDER BY a.created_at DESC";
        $stmt = $db->prepare($q);
        $stmt->execute($params);
        $duyurular = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ayAdi = "";
        if ($secilenAy) {
            $ts = strtotime($secilenAy."-01");
            $ayIsimleri  = ['January'=>'Ocak','February'=>'Şubat','March'=>'Mart','April'=>'Nisan',
                            'May'=>'Mayıs','June'=>'Haziran','July'=>'Temmuz','August'=>'Ağustos',
                            'September'=>'Eylül','October'=>'Ekim','November'=>'Kasım','December'=>'Aralık'];
            $enAy = date('F Y', $ts);
            $ayAdi = str_replace(array_keys($ayIsimleri), array_values($ayIsimleri), $enAy);
        }

        $pdfBaslik = ($ayAdi ? $ayAdi . " — " : "") . ($getSolved ? 'Çözülmüş Duyurular' : 'Çözülmemiş Duyurular');

        require_once 'app/views/pdf_print.php';
        exit;
    }

    public function pdf_istatistik() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? 1, [2, 4, 5])) {
            header("Location: /isg/index.php?url=login"); exit;
        }
        $db = (new Database())->getConnection();
        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;
        
        $where = "";
        $params = [];
        if ($secilenAy) {
            $where = "WHERE DATE_FORMAT(created_at, '%Y-%m') = :ay";
            $params[':ay'] = $secilenAy;
        }

        $statsQuery = "SELECT
            SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
            SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
            COUNT(*) as toplam
          FROM announcements $where";
        $stmtS = $db->prepare($statsQuery);
        $stmtS->execute($params);
        $stats = $stmtS->fetch(PDO::FETCH_ASSOC);

        $listQuery = "SELECT id, title, content, is_hazard, hazard_category, department_tag, created_at
            FROM announcements $where ORDER BY created_at DESC";
        $stmtL = $db->prepare($listQuery);
        $stmtL->execute($params);
        $duyuruListesi = $stmtL->fetchAll(PDO::FETCH_ASSOC);

        require_once 'app/views/pdf_istatistik_print.php';
        exit;
    }

    public function pdf_departman() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? 1, [2, 4, 5])) {
            header("Location: /isg/index.php?url=login"); exit;
        }
        $db = (new Database())->getConnection();
        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;

        $where = "";
        $params = [];
        if ($secilenAy) {
            $where = "WHERE DATE_FORMAT(created_at, '%Y-%m') = :ay";
            $params[':ay'] = $secilenAy;
        }

        $ozetQ = "SELECT
            SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
            SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
            COUNT(*) as toplam
          FROM announcements $where";
        $stmtO = $db->prepare($ozetQ);
        $stmtO->execute($params);
        $ozet = $stmtO->fetch(PDO::FETCH_ASSOC);

        $rowsQ = "SELECT department_tag, is_hazard FROM announcements $where";
        $stmtR = $db->prepare($rowsQ);
        $stmtR->execute($params);
        $rows = $stmtR->fetchAll(PDO::FETCH_ASSOC);

        $depts = ["Kalite", "Üretim", "Lojistik", "IT", "OT", "Bakım", "İSG", "İdari İşler", "İK"];
        $stats = [];
        foreach ($depts as $d) { $stats[$d] = ['cozulmus' => 0, 'cozulmemis' => 0, 'toplam' => 0]; }
        foreach ($rows as $it) {
            foreach (explode(', ', $it['department_tag']) as $tag) {
                $tag = trim($tag);
                if (isset($stats[$tag])) {
                    $stats[$tag]['toplam']++;
                    if ($it['is_hazard'] == 0) $stats[$tag]['cozulmus']++;
                    else $stats[$tag]['cozulmemis']++;
                }
            }
        }

        require_once 'app/views/pdf_departman_print.php';
        exit;
    }

    public function pdf_tehlike_analizi() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? 1, [2, 4, 5])) {
            header("Location: /isg/index.php?url=login"); exit;
        }
        $db = (new Database())->getConnection();
        
        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;
        $unvan = $_SESSION['title'] ?? 'GÖREVLİ';
        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $where = "";
        $params = [];
        if ($secilenAy) {
            $where = "WHERE DATE_FORMAT(created_at, '%Y-%m') = :ay";
            $params[':ay'] = $secilenAy;
        }

        // Tehlike seviyelerine göre çözülme durumu
        $q = "SELECT danger_level, 
                     SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
                     SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
                     COUNT(*) as toplam
              FROM announcements 
              $where
              GROUP BY danger_level";
        $stmt = $db->prepare($q);
        $stmt->execute($params);
        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Genel çözülme oranı
        $qGenel = "SELECT 
                    SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
                    SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
                    COUNT(*) as toplam
                   FROM announcements $where";
        $stmtGenel = $db->prepare($qGenel);
        $stmtGenel->execute($params);
        $genel = $stmtGenel->fetch(PDO::FETCH_ASSOC);

        require_once 'app/views/pdf_tehlike_analizi_print.php';
        exit;
    }

    public function tehlike_analizi() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /isg/index.php?url=login");
            exit;
        }
        $role = $_SESSION['role'] ?? 1;
        if (!in_array($role, [2, 4, 5])) {
            header("Location: /isg/index.php?url=duyurular");
            exit;
        }

        $db = (new Database())->getConnection();
        
        $secilenAy = isset($_GET['ay']) && preg_match('/^\d{4}-\d{2}$/', $_GET['ay']) ? $_GET['ay'] : null;
        $unvan = $_SESSION['title'] ?? 'GÖREVLİ';
        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $where = "";
        $params = [];
        if ($secilenAy) {
            $where = "WHERE DATE_FORMAT(created_at, '%Y-%m') = :ay";
            $params[':ay'] = $secilenAy;
        }

        // Tehlike seviyelerine göre çözülme durumu
        $q = "SELECT danger_level, 
                     SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
                     SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
                     COUNT(*) as toplam
              FROM announcements 
              $where
              GROUP BY danger_level";
        $stmt = $db->prepare($q);
        $stmt->execute($params);
        $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Genel çözülme oranı
        $qGenel = "SELECT 
                    SUM(CASE WHEN is_hazard = 0 THEN 1 ELSE 0 END) as cozulmus,
                    SUM(CASE WHEN is_hazard = 1 THEN 1 ELSE 0 END) as cozulmemis,
                    COUNT(*) as toplam
                   FROM announcements $where";
        $stmtGenel = $db->prepare($qGenel);
        $stmtGenel->execute($params);
        $genel = $stmtGenel->fetch(PDO::FETCH_ASSOC);

        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';
        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $sayfaBasligi = "Tehlike Analizi - MAISG";

        require_once 'app/views/tehlike_analizi.php';
    }

    // Basit UUIDv4 Üreteci (Veritabanı char(36) PK'ları için)
    private function generateUUID() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}
?>
