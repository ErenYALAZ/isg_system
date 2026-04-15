<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Departman Performans Analizi — MAISG</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #060c18; color: #f1f5f9; }

        /* ── KAPAK ── */
        .kapak {
            width: 210mm; height: 297mm;
            margin: 0 auto;
            background: linear-gradient(160deg, #0f172a 0%, #0b1120 55%, #1a0f3d 100%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            page-break-after: always; break-after: page;
            position: relative; overflow: hidden;
        }
        .kapak::before {
            content: ''; position: absolute; top: -60px; right: -60px;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .kapak::after {
            content: ''; position: absolute; bottom: -80px; left: -60px;
            width: 320px; height: 320px;
            background: radial-gradient(circle, rgba(59,130,246,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .kapak-logo { font-size: 42px; font-weight: 900; color: #fff; letter-spacing: -1px; margin-bottom: 20px; }
        .kapak-logo span { color: #8b5cf6; font-weight: 300; }
        .kapak-cizgi { width: 80px; height: 3px; background: linear-gradient(90deg, #8b5cf6, #3b82f6); border-radius: 3px; margin: 0 auto 20px; }
        .kapak-baslik { font-size: 28px; font-weight: 800; color: #fff; text-align: center; margin-bottom: 8px; letter-spacing: -0.5px; }
        .kapak-alt { font-size: 13px; color: #64748b; font-weight: 600; text-align: center; margin-bottom: 8px; }
        .kapak-tarih { font-size: 12px; color: #475569; font-weight: 700; }

        .kapak-depts {
            display: flex; flex-wrap: wrap; gap: 10px;
            justify-content: center; margin-top: 50px; max-width: 160mm; padding: 0 10mm;
        }
        .kapak-dept-chip {
            padding: 6px 14px; border-radius: 10px;
            font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* ── SAYFA ── */
        .sayfa {
            width: 210mm; height: 297mm;
            margin: 0 auto; background: #0f172a;
            display: flex; flex-direction: column;
            position: relative; page-break-after: always; break-after: page; overflow: hidden;
        }
        .sayfa:last-child { page-break-after: avoid; break-after: avoid; }

        .page-header {
            height: 14mm; flex-shrink: 0;
            background: linear-gradient(135deg, #2d1b69 0%, #0f172a 100%);
            border-bottom: 2px solid #8b5cf6;
            display: flex; align-items: center; justify-content: space-between; padding: 0 15mm;
        }
        .brand { font-size: 17px; font-weight: 900; color: #fff; }
        .brand span { color: #8b5cf6; font-weight: 300; }
        .page-info { font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        .page-footer {
            height: 10mm; flex-shrink: 0; margin-top: auto;
            background: #0a0f1e; border-top: 1px solid rgba(139,92,246,0.2);
            display: flex; align-items: center; justify-content: space-between; padding: 0 15mm;
        }
        .footer-text { font-size: 8px; color: #334155; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; }

        .page-body { flex: 1; padding: 8mm 12mm 5mm; overflow: hidden; display: flex; flex-direction: column; gap: 5mm; }

        /* ── GENEL ÖZET ── */
        .ozet-banner {
            background: linear-gradient(135deg, rgba(139,92,246,0.1), rgba(59,130,246,0.1));
            border: 1px solid rgba(139,92,246,0.2); border-radius: 16px; padding: 5mm 8mm;
            display: flex; gap: 8mm; align-items: center;
        }
        .ozet-lbl { font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 3px; }
        .ozet-num { font-size: 28px; font-weight: 900; line-height: 1; }
        .ozet-divider { width: 1px; background: rgba(255,255,255,0.06); align-self: stretch; }

        /* ── DEPARTMAN GRID (2 sütun) ── */
        .dept-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5mm; flex: 1; }
        .dept-kart {
            background: rgba(30,41,59,0.5); border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px; padding: 5mm 6mm; display: flex; flex-direction: column; gap: 4mm;
        }
        .dept-kart-header { display: flex; align-items: center; justify-content: space-between; }
        .dept-kart-isim { display: flex; align-items: center; gap: 3mm; font-size: 14px; font-weight: 900; color: #fff; }
        .dept-dot { width: 5px; height: 16px; border-radius: 3px; flex-shrink: 0; }
        .dept-oran-label { font-size: 11px; font-weight: 900; }
        .dept-metrikler { display: flex; gap: 3mm; }
        .dept-m { flex: 1; background: rgba(255,255,255,0.02); border-radius: 10px; padding: 3mm 4mm; border: 1px solid rgba(255,255,255,0.04); }
        .dept-m-lbl { font-size: 8px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .dept-m-val { font-size: 20px; font-weight: 900; }
        .dept-progress { height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; }
        .dept-progress-fill { height: 100%; border-radius: 10px; }

        /* ── EKRAN vs YAZICI ── */
        @media screen {
            body { padding: 20px 0; }
            .kapak, .sayfa { margin: 0 auto 20px; box-shadow: 0 30px 80px rgba(0,0,0,0.7); border-radius: 4px; }
        }
        @page { size: A4 portrait; margin: 0; }
        @media print {
            body { background: #0f172a; padding: 0; }
            .kapak, .sayfa { margin: 0; box-shadow: none; border-radius: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .yazdir-btn { display: none !important; }
        }
    </style>
</head>
<body>
<div id="pdf-main">

<?php
$bugunStr = date('d.m.Y H:i');

$renkler = [
    'Kalite'      => '#0ea5e9',
    'Üretim'      => '#10b981',
    'Lojistik'    => '#f59e0b',
    'IT'          => '#3b82f6',
    'OT'          => '#6366f1',
    'Bakım'       => '#eab308',
    'İSG'         => '#ef4444',
    'İdari İşler' => '#8b5cf6',
    'İK'          => '#ec4899',
];

// Sadece verisi olanları filtrele
$aktifDepts = array_filter($stats, function($d) { return $d['toplam'] > 0; });

// Gerçek toplamlar (veritabanından gelen $ozet kullan — departman çiftlemesiz)
$genelToplam    = (int)($ozet['toplam'] ?? 0);
$genelCozulmus  = (int)($ozet['cozulmus'] ?? 0);
$genelCozulmemis = (int)($ozet['cozulmemis'] ?? 0);
$genelOran      = $genelToplam > 0 ? round(($genelCozulmus / $genelToplam) * 100) : 0;

$deptItems   = array_values($aktifDepts);
$deptNames   = array_keys($aktifDepts);
$kartsPerPage = 6; // 2 sütun × 3 satır
$toplamSayfa = max(1, ceil(count($deptItems) / $kartsPerPage));
?>

<!-- ══════════ KAPAK ══════════ -->
<div class="kapak">
    <div class="kapak-logo">MA<span>ISG</span></div>
    <div class="kapak-cizgi"></div>
    <div class="kapak-baslik">Departman Analizi</div>
    <?php if ($secilenAy): 
        $ts = strtotime($secilenAy."-01");
        $ayIsimleri  = ['January'=>'Ocak','February'=>'Şubat','March'=>'Mart','April'=>'Nisan',
                        'May'=>'Mayıs','June'=>'Haziran','July'=>'Temmuz','August'=>'Ağustos',
                        'September'=>'Eylül','October'=>'Ekim','November'=>'Kasım','December'=>'Aralık'];
        $ayEtiket = str_replace(array_keys($ayIsimleri), array_values($ayIsimleri), date('F Y', $ts));
    ?>
        <div style="background:rgba(139,92,246,0.1); color:#8b5cf6; padding:6px 16px; border-radius:10px; font-size:13px; font-weight:800; margin-bottom:12px; border:1px solid rgba(139,92,246,0.2);">
            <?= $ayEtiket ?> DÖNEMİ
        </div>
    <?php endif; ?>
    <div class="kapak-alt">İş Sağlığı ve Güvenliği Yönetim Sistemi</div>
    <div class="kapak-tarih">Rapor Tarihi: <?= $bugunStr ?></div>
    <div class="kapak-depts">
        <?php foreach ($deptNames as $idx => $name):
            $renk = $renkler[$name] ?? '#3b82f6';
            $d = $aktifDepts[$name];
            $oran = $d['toplam'] > 0 ? round(($d['cozulmus']/$d['toplam'])*100) : 0;
        ?>
        <div class="kapak-dept-chip" style="background:<?= $renk ?>18; color:<?= $renk ?>; border:1.5px solid <?= $renk ?>35;">
            <?= htmlspecialchars($name) ?> — %<?= $oran ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ══════════ GRAFİK SAYFASI ══════════ -->
<div class="sayfa" id="grafik-sayfa">
    <div class="page-header">
        <div class="brand">MA<span>ISG</span></div>
        <div class="page-info"><?= $bugunStr ?> &nbsp;|&nbsp; Département Performans Grafiği</div>
    </div>

    <div class="page-body" style="justify-content:center; gap:6mm;">

        <div style="border-left:3px solid #8b5cf6; padding-left:4mm; margin-bottom:2mm;">
            <div style="font-size:13px; font-weight:900; color:#fff; letter-spacing:-0.3px;">Département Bazlı Performans Grafiği</div>
            <div style="font-size:9px; color:#64748b; font-weight:600; margin-top:2px;">Çözülen İşler &amp; Açık Riskler — Karşılaştırmalı Görünüm</div>
        </div>

        <!-- Grafik alanı -->
        <div style="
            background:rgba(30,41,59,0.4); border:1px solid rgba(255,255,255,0.06);
            border-radius:16px; padding:8mm 8mm 6mm;
            display:flex; flex-direction:column; gap:6mm; flex:1;
        ">
            <!-- Legend -->
            <div style="display:flex; gap:20px; align-items:center; justify-content:center;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <div style="width:12px; height:12px; border-radius:4px; background:#10b981;"></div>
                    <span style="font-size:10px; font-weight:800; color:#94a3b8;">Çözülen İşler</span>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div style="width:12px; height:12px; border-radius:4px; background:#ef4444;"></div>
                    <span style="font-size:10px; font-weight:800; color:#94a3b8;">Açık Riskler</span>
                </div>
            </div>

            <!-- Canvas -->
            <div style="flex:1; position:relative; min-height:0;">
                <canvas id="pdfBarChart"></canvas>
            </div>
        </div>

        <!-- Özet bant -->
        <div style="display:flex; gap:4mm; flex-wrap:wrap;">
            <?php
            $aktifDepts2 = array_filter($stats, function($d){ return $d['toplam'] > 0; });
            foreach($aktifDepts2 as $name => $d):
                $renk2 = $renkler[$name] ?? '#3b82f6';
                $oran2 = $d['toplam'] > 0 ? round(($d['cozulmus']/$d['toplam'])*100) : 0;
            ?>
            <div style="
                background:<?= $renk2 ?>18; border:1.5px solid <?= $renk2 ?>35;
                border-radius:10px; padding:3mm 5mm; flex:1; min-width:0;
            ">
                <div style="font-size:8px; font-weight:800; color:<?= $renk2 ?>; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;"><?= htmlspecialchars($name) ?></div>
                <div style="font-size:16px; font-weight:900; color:#fff; line-height:1;">%<?= $oran2 ?></div>
                <div style="font-size:8px; color:#475569; font-weight:600; margin-top:2px;"><?= $d['cozulmus'] ?> / <?= $d['toplam'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

    <div class="page-footer">
        <div class="footer-text">MAISG — Departman Performans Analizi</div>
        <div class="footer-text">Grafik Sayfası</div>
    </div>
</div>

<!-- ══════════ DEPARTMAN SAYFALARI ══════════ -->
<?php for ($sayfa = 0; $sayfa < $toplamSayfa; $sayfa++):
    $baslangic = $sayfa * $kartsPerPage;
    $sayfaDepts = array_slice($deptNames, $baslangic, $kartsPerPage);
    $sayfaNo = $sayfa + 1;
?>
<div class="sayfa">
    <div class="page-header">
        <div class="brand">MA<span>ISG</span></div>
        <div class="page-info"><?= $bugunStr ?> &nbsp;|&nbsp; Sayfa <?= $sayfaNo ?> / <?= $toplamSayfa ?></div>
    </div>

    <div class="page-body">

        <?php if ($sayfa === 0): ?>
        <!-- Genel Özet (sadece ilk sayfada) -->
        <div class="ozet-banner">
            <div>
                <div class="ozet-lbl">Toplam</div>
                <div class="ozet-num" style="color:#8b5cf6;"><?= $genelToplam ?></div>
            </div>
            <div class="ozet-divider"></div>
            <div>
                <div class="ozet-lbl">Çözüldü</div>
                <div class="ozet-num" style="color:#10b981;"><?= $genelCozulmus ?></div>
            </div>
            <div class="ozet-divider"></div>
            <div>
                <div class="ozet-lbl">Açık Risk</div>
                <div class="ozet-num" style="color:#ef4444;"><?= $genelCozulmemis ?></div>
            </div>
            <div class="ozet-divider"></div>
            <div>
                <div class="ozet-lbl">Genel Başarı</div>
                <div class="ozet-num" style="color:#10b981;"><?= $genelOran ?>%</div>
            </div>
            <div style="flex:1; margin-left:8mm;">
                <div style="font-size:9px; color:#64748b; font-weight:700; margin-bottom:4px;">GENEL BAŞARI ORANI</div>
                <div style="height:8px; background:rgba(255,255,255,0.05); border-radius:10px; overflow:hidden;">
                    <div style="width:<?= $genelOran ?>%; height:100%; background:linear-gradient(90deg,#8b5cf6,#10b981); border-radius:10px;"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Departman Kartları Grid -->
        <div class="dept-grid">
            <?php foreach ($sayfaDepts as $name):
                $d    = $aktifDepts[$name];
                $renk = $renkler[$name] ?? '#3b82f6';
                $oran = $d['toplam'] > 0 ? round(($d['cozulmus'] / $d['toplam']) * 100) : 0;
                $hataOran = 100 - $oran;
            ?>
            <div class="dept-kart" style="border-left: 4px solid <?= $renk ?>;">
                <div class="dept-kart-header">
                    <div class="dept-kart-isim">
                        <div class="dept-dot" style="background:<?= $renk ?>;"></div>
                        <?= htmlspecialchars($name) ?>
                    </div>
                    <div class="dept-oran-label" style="color:<?= $renk ?>;">%<?= $oran ?> BAŞARI</div>
                </div>

                <div class="dept-metrikler">
                    <div class="dept-m">
                        <div class="dept-m-lbl">Çözülen</div>
                        <div class="dept-m-val" style="color:#10b981;"><?= $d['cozulmus'] ?></div>
                    </div>
                    <div class="dept-m">
                        <div class="dept-m-lbl">Açık Risk</div>
                        <div class="dept-m-val" style="color:#ef4444;"><?= $d['cozulmemis'] ?></div>
                    </div>
                    <div class="dept-m">
                        <div class="dept-m-lbl">Toplam</div>
                        <div class="dept-m-val" style="color:#fff;"><?= $d['toplam'] ?></div>
                    </div>
                </div>

                <div class="dept-progress">
                    <div class="dept-progress-fill" style="width:<?= $oran ?>%; background:<?= $renk ?>; box-shadow: 0 0 10px <?= $renk ?>55;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

    <div class="page-footer">
        <div class="footer-text">MAISG — Departman Performans Analizi</div>
        <div class="footer-text">Sayfa <?= $sayfaNo ?> / <?= $toplamSayfa ?></div>
    </div>
</div>
<?php endfor; ?>

<?php if (count($aktifDepts) === 0): ?>
<div class="sayfa" style="display:flex; align-items:center; justify-content:center;">
    <div class="page-header">
        <div class="brand">MA<span>ISG</span></div>
        <div class="page-info"><?= $bugunStr ?></div>
    </div>
    <p style="color:#334155; font-size:20px; font-weight:800;">Henüz kayıtlı departman verisi yok.</p>
    <div class="page-footer">
        <div class="footer-text">MAISG — Departman Performans Analizi</div>
    </div>
</div>
<?php endif; ?>
</div><!-- /#pdf-main -->

<!-- Direkt indirme butonu -->
<div class="yazdir-btn" id="yazdir-btn" style="
    position: fixed; bottom: 30px; right: 30px; z-index: 9999;
    display: flex; gap: 12px; flex-direction: column; align-items: flex-end;
">
    <button id="indirBtn" onclick="downloadPDF()" style="
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff; border: none; padding: 16px 28px;
        border-radius: 16px; font-family: 'Outfit', sans-serif;
        font-size: 14px; font-weight: 800; cursor: pointer;
        box-shadow: 0 15px 40px rgba(139,92,246,0.4);
        display: flex; align-items: center; gap: 10px; transition: 0.2s;
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
    // Bar Chart — Grafik Sayfası
    var deptLabels   = <?= json_encode(array_keys(array_filter($stats, function($d){ return $d['toplam'] > 0; }))) ?>;
    var deptCozulmus = <?= json_encode(array_column(array_filter($stats, function($d){ return $d['toplam'] > 0; }), 'cozulmus')) ?>;
    var deptAcik     = <?= json_encode(array_column(array_filter($stats, function($d){ return $d['toplam'] > 0; }), 'cozulmemis')) ?>;

    var barCtx = document.getElementById('pdfBarChart');
    if (barCtx) {
        new Chart(barCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [
                    { label: 'Çözülen İşler', data: deptCozulmus, backgroundColor: '#10b981', borderRadius: 6, barThickness: 22 },
                    { label: 'Açık Riskler', data: deptAcik, backgroundColor: '#ef4444', borderRadius: 6, barThickness: 22 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false, animation: { duration: 0 },
                scales: {
                    x: { stacked: true, grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'Outfit', weight: 'bold', size: 10 } } },
                    y: { stacked: true, grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#94a3b8', font: { size: 9 } } }
                },
                plugins: { legend: { display: false }, tooltip: { enabled: false } }
            }
        });
    }

    async function downloadPDF() {
        var btn = document.getElementById('indirBtn');
        var orig = btn.innerHTML;
        btn.innerHTML = '⏳ Hazırlanıyor...';
        btn.disabled = true;
        document.getElementById('yazdir-btn').style.display = 'none';

        // Ekran efektlerini geçici kaldır
        var sayfalar = document.querySelectorAll('#pdf-main .kapak, #pdf-main .sayfa');
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
                scale: 4, // Yüksek çözünürlük
                useCORS: true,
                backgroundColor: '#0f172a',
                logging: false
            });
            pdf.addImage(canvas.toDataURL('image/jpeg', 1.0), 'JPEG', 0, 0, 210, 297);
        }

        pdf.save('MAISG-Departman-Analizi.pdf');

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
