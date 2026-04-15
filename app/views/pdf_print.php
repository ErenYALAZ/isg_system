<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pdfBaslik) ?> — MAISG</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        /* ── GENEL RESET ── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background: #0b1120;
            color: #f1f5f9;
        }

        /* ── HER DUYURU = 1 A4 SAYFASI ── */
        .sayfa {
            width: 210mm;
            min-height: 297mm;
            height: 297mm;
            margin: 0 auto;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 18mm 16mm;
            position: relative;
            page-break-after: always;
            break-after: page;
            overflow: hidden;
        }

        /* Son sayfada sayfa sonu ekleme */
        .sayfa:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }

        /* ── ÜST LOGO/BAŞLIK BANDI ── */
        .page-header {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 14mm;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            border-bottom: 2px solid #3b82f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16mm;
        }

        .brand-logo {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: -0.5px;
            color: #fff;
        }

        .brand-logo span { color: #3b82f6; font-weight: 300; }

        .page-meta {
            font-size: 9px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: right;
            line-height: 1.4;
        }

        /* ── ALT FOOTER BANDI ── */
        .page-footer {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 10mm;
            background: #0a0f1e;
            border-top: 1px solid rgba(59,130,246,0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16mm;
        }

        .footer-text {
            font-size: 8px;
            color: #334155;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* ── DUYURU KARTI (ortalanmış içerik) ── */
        .kart {
            width: 100%;
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        }

        /* Çözümlenme durumu renkli sol çizgi */
        .kart.cozulmemis { border-left: 5px solid #ef4444; }
        .kart.cozulmus   { border-left: 5px solid #10b981; }

        /* ── GÖRSEL ── */
        .kart-gorsel {
            width: 100%;
            height: 85mm;
            overflow: hidden;
            background: #020617;
            flex-shrink: 0;
        }

        .kart-gorsel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* ── İÇERİK ALANI ── */
        .kart-icerik {
            padding: 10mm 12mm 8mm;
        }

        /* Durum rozeti */
        .durum-rozet {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5mm;
        }

        .durum-rozet.cozulmus {
            background: rgba(16,185,129,0.12);
            color: #10b981;
            border: 1.5px solid rgba(16,185,129,0.25);
        }

        .durum-rozet.cozulmemis {
            background: rgba(239,68,68,0.12);
            color: #ef4444;
            border: 1.5px solid rgba(239,68,68,0.25);
        }

        /* Başlık */
        .baslik {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.3;
            color: #f8fafc;
            margin-bottom: 3mm;
            letter-spacing: -0.5px;
        }

        /* Meta: tarih & kategori */
        .meta {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4mm;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meta-dot {
            width: 4px; height: 4px;
            background: #334155;
            border-radius: 50%;
        }

        /* Departman etiketleri */
        .etiketler {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 5mm;
        }

        .etiket {
            font-size: 9px;
            font-weight: 800;
            padding: 3px 9px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Ayraç çizgisi */
        .ayrac {
            height: 1px;
            background: rgba(255,255,255,0.06);
            margin: 4mm 0;
        }

        /* İçerik metni */
        .icerik-metin {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.75;
            white-space: pre-line;
        }

        /* Görsel yoksa boş placeholder */
        .kart-no-gorsel {
            width: 100%;
            height: 30mm;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .kart-no-gorsel svg {
            opacity: 0.15;
        }

        /* Boş durum sayfası */
        .bos-sayfa {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* Ekran önizlemesi için arkaplan aralık */
        @media screen {
            body { padding: 20px 0; background: #060c18; }
            .sayfa {
                margin: 0 auto 20px;
                box-shadow: 0 30px 80px rgba(0,0,0,0.7);
                border-radius: 4px;
            }
        }

        /* ── YAZICI (PRINT) AYARLARI ── */
        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            body { background: #0f172a; padding: 0; }
            .sayfa {
                margin: 0;
                box-shadow: none;
                border-radius: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
            .yazdir-btn { display: none !important; }
        }
    </style>
</head>
<body>
<div id="pdf-main">

<?php
// Tarih-saat Türkçe format yardımcısı
function formatTarih($dt) {
    return date('d.m.Y H:i', strtotime($dt));
}

// Departman renk haritası
$deptRenkler = [
    'Kalite'      => '#0ea5e9',
    'Üretim'      => '#10b981',
    'Lojistik'    => '#f59e0b',
    'IT'          => '#3b82f6',
    'OT'          => '#6366f1',
    'Bakım'       => '#eab308',
    'İSG'         => '#ef4444',
    'İdari İşler' => '#8b5cf6',
    'İK'          => '#ec4899',
    'Genel'       => '#64748b',
];

$bugunStr = date('d.m.Y H:i');
$toplamSayfa = count($duyurular);
?>

<?php if ($toplamSayfa === 0): ?>
<div class="sayfa bos-sayfa">
    <div>
        <div class="page-header">
            <div class="brand-logo">MA<span>ISG</span></div>
            <div class="page-meta"><?= $bugunStr ?></div>
        </div>
        <p>Bu kategoride herhangi bir duyuru bulunamadı.</p>
        <div class="page-footer">
            <div class="footer-text">MAISG — İş Sağlığı ve Güvenliği Yönetim Sistemi</div>
            <div class="footer-text"><?= htmlspecialchars($pdfBaslik) ?></div>
        </div>
    </div>
</div>
<?php else: ?>

<?php foreach ($duyurular as $i => $d): 
    $isCozulmus  = $d['is_hazard'] == 0;
    $resimler    = !empty($d['tum_resimler']) ? explode('|', $d['tum_resimler']) : [];
    $ilkResim    = $resimler[0] ?? null;
    $kartSinif   = $isCozulmus ? 'cozulmus' : 'cozulmemis';
    $depts       = explode(', ', $d['department_tag'] ?? '');
    $sayfaNo     = $i + 1;
?>
<div class="sayfa">

    <!-- Üst başlık bandı -->
    <div class="page-header">
        <div class="brand-logo">MA<span>ISG</span></div>
        <div class="page-meta">
            <div><?= $bugunStr ?></div>
            <div style="color:#3b82f6; font-size:8px; opacity:0.8;"><?= htmlspecialchars($pdfBaslik) ?></div>
            <div style="font-size:7px; opacity:0.6; margin-top:1px;">Sayfa <?= $sayfaNo ?> / <?= $toplamSayfa ?></div>
        </div>
    </div>

    <!-- Duyuru kartı -->
    <div class="kart <?= $kartSinif ?>">

        <!-- Görsel -->
        <?php if ($ilkResim): ?>
            <div class="kart-gorsel">
                <img src="/isg/<?= htmlspecialchars($ilkResim) ?>" alt="Duyuru görseli">
            </div>
        <?php else: ?>
            <div class="kart-no-gorsel">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="3"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
        <?php endif; ?>

        <!-- İçerik -->
        <div class="kart-icerik">

            <!-- Durum rozeti -->
            <div class="durum-rozet <?= $kartSinif ?>">
                <?php if ($isCozulmus): ?>
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                    ÇÖZÜLDÜ
                <?php else: ?>
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    ÇÖZÜLMEMİŞ
                <?php endif; ?>
            </div>

            <!-- Başlık -->
            <div class="baslik"><?= htmlspecialchars($d['title'] ?? 'İsimsiz Duyuru') ?></div>

            <!-- Meta -->
            <div class="meta">
                <span><?= formatTarih($d['created_at']) ?></span>
                <span class="meta-dot"></span>
                <span><?= htmlspecialchars($d['hazard_category'] ?? 'Genel') ?></span>
            </div>

            <!-- Departman etiketleri -->
            <?php if (!empty($depts[0])): ?>
            <div class="etiketler">
                <?php foreach ($depts as $dept): 
                    $dept = trim($dept);
                    $renk = $deptRenkler[$dept] ?? '#3b82f6';
                ?>
                <span class="etiket" style="background:<?= $renk ?>18; color:<?= $renk ?>; border:1.5px solid <?= $renk ?>30;">
                    <?= htmlspecialchars($dept) ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="ayrac"></div>

            <!-- İçerik metni -->
            <div class="icerik-metin"><?= htmlspecialchars($d['content'] ?? '') ?></div>

        </div>
    </div>

    <!-- Alt footer bandı -->
    <div class="page-footer">
        <div class="footer-text">MAISG — İş Sağlığı ve Güvenliği Yönetim Sistemi</div>
        <div class="footer-text"><?= htmlspecialchars($pdfBaslik) ?></div>
    </div>

</div>
<?php endforeach; ?>

<?php endif; ?>
</div><!-- /#pdf-main -->

<!-- Direkt indirme butonu -->
<div class="yazdir-btn" id="yazdir-btn" style="
    position: fixed; bottom: 30px; right: 30px; z-index: 9999;
    display: flex; gap: 12px; flex-direction: column; align-items: flex-end;
">
    <button id="indirBtn" onclick="downloadPDF()" style="
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff; border: none; padding: 16px 28px;
        border-radius: 16px; font-family: 'Outfit', sans-serif;
        font-size: 14px; font-weight: 800; cursor: pointer;
        box-shadow: 0 15px 40px rgba(59,130,246,0.4);
        display: flex; align-items: center; gap: 10px;
        transition: 0.2s;
    " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        PDF İNDİR
    </button>
    <button onclick="window.close()" style="
        background: rgba(30,41,59,0.9); color: #94a3b8;
        border: 1px solid rgba(255,255,255,0.08); padding: 12px 22px;
        border-radius: 14px; font-family: 'Outfit', sans-serif;
        font-size: 13px; font-weight: 700; cursor: pointer;
    ">✕ Kapat</button>
</div>

<script>
    async function downloadPDF() {
        var btn = document.getElementById('indirBtn');
        var orig = btn.innerHTML;
        btn.innerHTML = '⏳ Hazırlanıyor...';
        btn.disabled = true;
        document.getElementById('yazdir-btn').style.display = 'none';

        // Ekran efektlerini geçici kaldır
        var sayfalar = document.querySelectorAll('#pdf-main .sayfa');
        sayfalar.forEach(function(s) {
            s.style.marginBottom = '0';
            s.style.borderRadius = '0';
            s.style.boxShadow = 'none';
        });
        document.body.style.padding = '0';

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

        for (let i = 0; i < sayfalar.length; i++) {
            if (i > 0) pdf.addPage();
            const canvas = await html2canvas(sayfalar[i], {
                scale: 4, // 2'den 4'e çıkarılarak çok daha yüksek kalite elde edildi
                useCORS: true,
                backgroundColor: '#0f172a',
                logging: false
            });
            // PNG de denenebilir ancak JPEG boyut-kalite olarak daha idealdir
            pdf.addImage(canvas.toDataURL('image/jpeg', 1.0), 'JPEG', 0, 0, 210, 297);
        }

        pdf.save('MAISG-<?= addslashes($pdfBaslik) ?>.pdf');

        // Ekran stillerini geri yükle
        sayfalar.forEach(function(s) {
            s.style.marginBottom = '';
            s.style.borderRadius = '';
            s.style.boxShadow = '';
        });
        document.body.style.padding = '';
        document.getElementById('yazdir-btn').style.display = 'flex';
        btn.innerHTML = orig;
        btn.disabled = false;
    }
</script>
</body>
</html>
