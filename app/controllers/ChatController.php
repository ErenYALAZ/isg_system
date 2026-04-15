<?php

class ChatController {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /isg/index.php?url=login");
            exit;
        }
        $kullaniciAdi = $_SESSION['user_full_name'] ?? $_SESSION['username'];
        $role = (int)($_SESSION['role'] ?? 1);
        $isHigh = in_array($role, [2, 4, 5]);
        $unvanMap = [1 => 'Personel', 2 => 'İSG Uzmanı', 3 => 'Operatör', 4 => 'Yönetici', 5 => 'Admin'];
        $unvan = $unvanMap[$role] ?? 'Personel';
        $sayfaBasligi = "Op1 - İSG Mesajlaşma Paneli";
        $db = (new Database())->getConnection();
        
        // Chat Listesi Mantığı:
        if ($role == 3) {
            // Operatör sadece İSG Uzmanlarını görür
            $stmt = $db->prepare("SELECT * FROM uye WHERE role = 2 ORDER BY kuladsoyad ASC");
            $stmt->execute();
        } else if ($role == 2) {
            // İSG Uzmanı: Operatörleri, Yöneticileri ve Adminleri görür
            $stmt = $db->prepare("SELECT * FROM uye WHERE role IN (3, 4, 5) ORDER BY kuladsoyad ASC");
            $stmt->execute();
        } else {
            // Yönetici ve Admin: İSG Uzmanlarını ve Operatörleri görür (Herkesi görebilirler istersen)
            $stmt = $db->prepare("SELECT * FROM uye WHERE role IN (2, 3) ORDER BY kuladsoyad ASC");
            $stmt->execute();
        }
        $kullanicilar = $stmt->fetchAll();
        require_once 'app/views/chat.php';
    }

    public function get_messages() {
        $myId = $_SESSION['user_id'];
        $otherId = $_GET['id'] ?? 0;
        $db = (new Database())->getConnection();
        $query = "SELECT m.*, u.kuladsoyad as sender_name, a.title as ann_title FROM messages m 
                  JOIN uye u ON m.sender_id = u.id
                  LEFT JOIN announcements a ON m.announcement_id = a.id
                  LEFT JOIN message_deletions md ON m.id = md.message_id AND md.user_id = :me
                  WHERE ((sender_id = :me AND receiver_id = :other) 
                  OR (sender_id = :other AND receiver_id = :me))
                  AND md.id IS NULL
                  ORDER BY created_at ASC";
        $stmt = $db->prepare($query);
        $stmt->execute(['me' => $myId, 'other' => $otherId]);
        $messages = $stmt->fetchAll();

        // Mesajları okundu olarak işaretle (Alıcısı ben olanlar)
        $upRead = $db->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = :me AND sender_id = :other AND is_read = 0");
        $upRead->execute(['me' => $myId, 'other' => $otherId]);

        foreach($messages as &$m) {
            // Kesinlik için 1 mi kontrolü yapıyoruz. (Bazı db driverlarında 0 truthy dönebilir)
            if (isset($m['is_deleted_everyone']) && $m['is_deleted_everyone'] == 1) {
                $m['message'] = "Bu mesaj silindi.";
                $m['images'] = [];
            } else {
                $stmtImg = $db->prepare("SELECT image_path FROM message_images WHERE message_id = :mid");
                $stmtImg->execute(['mid' => $m['id']]);
                $m['images'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
            }
        }
        echo json_encode($messages);
    }

    public function send() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') return;
        $myId = $_SESSION['user_id'];
        $otherId = $_POST['receiver_id'];
        $msgStr = $_POST['message'] ?? '';
        $db = (new Database())->getConnection();
        $db->beginTransaction();
        try {
            $annId = !empty($_POST['announcement_id']) ? $_POST['announcement_id'] : null;
            // Ekleme yaparken is_deleted_everyone'ı açıkça 0 yapıyoruz.
            $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message, announcement_id, is_deleted_everyone) VALUES (:me, :other, :msg, :aid, 0)");
            $stmt->execute(['me' => $myId, 'other' => $otherId, 'msg' => $msgStr, 'aid' => $annId]);
            $mid = $db->lastInsertId();
            if (!empty($_FILES['msg_images']['name'][0])) {
                $uploadDir = 'uploads/chat/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                foreach ($_FILES['msg_images']['tmp_name'] as $key => $tmpName) {
                    $fileName = time() . '_' . $_FILES['msg_images']['name'][$key];
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $db->prepare("INSERT INTO message_images (message_id, image_path) VALUES (:mid, :path)")->execute(['mid' => $mid, 'path' => $targetPath]);
                    }
                }
            }
            $db->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function delete_everyone() {
        $mid = $_POST['id'] ?? 0;
        $uid = $_SESSION['user_id'];
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("UPDATE messages SET is_deleted_everyone = 1 WHERE id = :id AND sender_id = :uid");
        $stmt->execute(['id' => $mid, 'uid' => $uid]);
        echo json_encode(['success' => true]);
    }

    public function delete_me() {
        $mid = $_POST['id'] ?? 0;
        $uid = $_SESSION['user_id'];
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT IGNORE INTO message_deletions (message_id, user_id) VALUES (:mid, :uid)");
        $stmt->execute(['mid' => $mid, 'uid' => $uid]);
        echo json_encode(['success' => true]);
    }

    public function get_unread_counts() {
        if (!isset($_SESSION['user_id'])) return;
        $myId = $_SESSION['user_id'];
        $db = (new Database())->getConnection();
        $query = "SELECT sender_id, COUNT(*) as unread_count FROM messages 
                  WHERE receiver_id = :me AND is_read = 0 
                  GROUP BY sender_id";
        $stmt = $db->prepare($query);
        $stmt->execute(['me' => $myId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function get_announcement_info() {
        if (!isset($_GET['id'])) return;
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT id, title FROM announcements WHERE id = :id");
        $stmt->execute(['id' => $_GET['id']]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }
}
?>
