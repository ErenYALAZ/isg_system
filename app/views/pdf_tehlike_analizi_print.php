<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Tehlike Analiz Raporu — MAISG</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #060c18; color: #f1f5f9; }

        .kapak {
            width: 210mm; height: 297mm; margin: 0 auto;
            background: linear-gradient(160deg, #090e1a 0%, #0b1120 50%, #0d1a33 100%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            page-break-after: always; break-after: page; position: relative; overflow: hidden;
        }
        .kapak::before { content: ''; position: absolute; top: -80px; left: -80px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(239,68,68,0.1) 0%, transparent 70%); border-radius: 50%; }
        
        .kapak-logo { font-size: 48px; font-weight: 900; color: #fff; letter-spacing: -1.5px; margin-bottom: 30px; }
        .kapak-logo span { color: #ef4444; font-weight: 300; }
        .kapak-baslik { font-size: 34px; font-weight: 800; color: #fff; text-align: center; margin-bottom: 10px; letter-spacing: -1px; }
        .kapak-alt { font-size: 14px; color: #64748b; font-weight: 600; text-align: center; margin-bottom: 60px; text-transform: uppercase; letter-spacing: 2px; }
        .kapak-tarih { font-size: 13px; color: #475569; font-weight: 700; text-align: center; }

        .sayfa {
            width: 210mm; height: 297mm; margin: 0 auto; background: #0f172a;
            display: flex; flex-direction: column; position: relative;
            page-break-after: always; break-after: page; overflow: hidden;
        }
        .page-header {
            height: 16mm; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-bottom: 2px solid #ef4444; display: flex; align-items: center; justify-content: space-between;
            padding: 0 15mm; flex-shrink: 0;
        }
        .brand { font-size: 18px; font-weight: 900; color: #fff; }
        .brand span { color: #ef4444; font-weight: 300; }
        .page-body { flex: 1; padding: 12mm 15mm; display: flex; flex-direction: column; gap: 10mm; }

        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8mm; }
        .stat-card { background: rgba(30,41,59,0.5); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 20px; }
        .card-title { font-size: 13px; font-weight: 800; color: #94a3b8; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; }

        .chart-box { height: 220px; position: relative; width: 100%; }
        
        .level-row { display: flex; flex-direction: column; gap: 12px; }
        .level-item { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 12px 18px; }
        .level-top { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .level-name { font-size: 12px; font-weight: 800; }
        .level-perc { font-size: 12px; font-weight: 900; color: #fff; }
        .level-bar-bg { height: 6px; background: rgba(255,255,255,0.05); border-radius: 3px; overflow: hidden; }
        .level-bar { height: 100%; border-radius: 3px; }
        .level-stats { display: flex; justify-content: space-between; margin-top: 6px; font-size: 10px; color: #64748b; font-weight: 700; }

        .page-footer { height: 10mm; background: #0a0f1e; border-top: 1px solid rgba(239,68,68,0.2); display: flex; align-items: center; justify-content: space-between; padding: 0 15mm; margin-top: auto; }
        .footer-text { font-size: 8px; color: #334155; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        @media screen {
            body { padding: 40px 0; }
            .kapak, .sayfa { margin: 0 auto 30px; box-shadow: 0 40px 100px rgba(0,0,0,0.8); border-radius: 8px; }
        }
        @page { size: A4 portrait; margin: 0; }
    </style>
</head>
<body>
<div id="pdf-content">
    <div class="kapak">
        <?php
        $ayIsimleri = [
            '01' => 'Ocak', '02' => 'Şubat', '03' => 'Mart', '04' => 'Nisan',
            '05' => 'Mayıs', '06' => 'Haziran', '07' => 'Temmuz', '08' => 'Ağustos',
            '09' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık'
        ];
        $aktifAyAdi = $secilenAy ? ($ayIsimleri[substr($secilenAy, 5)] ?? '') . ' ' . substr($secilenAy, 0, 4) : 'Tüm Zamanlar';
        ?>
        <div class="kapak-logo">MA<span>ISG</span></div>
        <div class="kapak-baslik"><?= $secilenAy ? $aktifAyAdi . ' Tehlike Analiz Raporu' : 'Genel Tehlike Analiz Raporu' ?></div>
        <div class="kapak-alt">Risk Seviyeleri ve Çözüm Performansı</div>
        <div class="kapak-tarih">Oluşturulma: <?= date('d.m.Y H:i') ?></div>
        
        <div style="display:flex; gap:30px; margin-top:60px;">
            <div style="text-align:center; padding:25px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); border-radius:24px;">
                <div style="font-size:32px; font-weight:900; color:#ef4444;"><?= $genel['toplam'] ?></div>
                <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; margin-top:5px;">Toplam Vaka</div>
            </div>
            <div style="text-align:center; padding:25px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); border-radius:24px;">
                <div style="font-size:32px; font-weight:900; color:#10b981;"><?= ($genel['toplam'] > 0) ? round(($genel['cozulmus'] / $genel['toplam']) * 100, 1) : 0 ?>%</div>
                <div style="font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; margin-top:5px;">Çözüm Oranı</div>
            </div>
        </div>
    </div>

    <div class="sayfa">
        <div class="page-header">
            <div class="brand">MA<span>ISG</span></div>
            <div style="font-size:10px; color:#64748b; font-weight:800;"><?= date('d.m.Y') ?> &bull; Analiz Detayları</div>
        </div>

        <div class="page-body">
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="card-title" style="color:#10b981;">Çözülmüş Tehlike Dağılımı</div>
                    <div class="chart-box"><canvas id="solvedPdfChart"></canvas></div>
                </div>
                <div class="stat-card">
                    <div class="card-title" style="color:#ef4444;">Çözülmeyen Tehlike Dağılımı</div>
                    <div class="chart-box"><canvas id="unsolvedPdfChart"></canvas></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="card-title">Tehlike Seviyesi Bazlı Teknik Analiz</div>
                <div class="level-row">
                    <?php 
                    $order = ['Yüksek', 'Orta', 'Düşük'];
                    $sorted = [];
                    foreach($order as $o) { foreach($stats as $s) { if($s['danger_level']==$o) { $sorted[]=$s; break; } } }
                    foreach($sorted as $s): 
                        $p = ($s['toplam']>0)?round(($s['cozulmus']/$s['toplam'])*100,1):0;
                        $c = $s['danger_level']=='Yüksek'?'#ef4444':($s['danger_level']=='Orta'?'#f59e0b':'#3b82f6');
                    ?>
                    <div class="level-item">
                        <div class="level-top">
                            <div class="level-name" style="color:<?= $c ?>;"><?= mb_strtoupper($s['danger_level']) ?> RİSKLİ TEHLİKELER</div>
                            <div class="level-perc">%<?= $p ?> Başarı</div>
                        </div>
                        <div class="level-bar-bg"><div class="level-bar" style="width:<?= $p ?>%; background:<?= $c ?>;"></div></div>
                        <div class="level-stats">
                            <span><?= $s['cozulmus'] ?> Çözülen Vaka</span>
                            <span><?= $s['cozulmemis'] ?> Çözülmeyen Vaka</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="page-footer">
            <div class="footer-text">MAISG Raporlama Sistemi</div>
            <div class="footer-text">Sayfa 1 / 1</div>
        </div>
    </div>
</div>

<div style="position:fixed; bottom:30px; right:30px; display:flex; gap:12px; z-index:9999;">
    <button id="pdfBtn" onclick="downloadPDF()" style="background:#ef4444; color:#fff; border:none; padding:15px 30px; border-radius:15px; font-weight:800; cursor:pointer; font-family:'Outfit'; box-shadow:0 10px 30px rgba(239,68,68,0.4);">PDF İNDİR</button>
    <button onclick="window.close()" style="background:#1e293b; color:#fff; border:none; padding:15px 25px; border-radius:15px; font-weight:600; cursor:pointer; font-family:'Outfit';">KAPAT</button>
</div>

<script>
    <?php
    $labels = []; $sd = []; $ud = []; $clrs = [];
    foreach($sorted as $s) {
        $labels[] = $s['danger_level']; $sd[] = $s['cozulmus']; $ud[] = $s['cozulmemis'];
        $clrs[] = $s['danger_level']=='Yüksek'?'#ef4444':($s['danger_level']=='Orta'?'#f59e0b':'#3b82f6');
    }
    ?>
    const chOpt = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{ color:'#94a3b8', font:{ weight:'700', size:10 } } } }, cutout:'70%', animation:false };
    new Chart(document.getElementById('solvedPdfChart'), { type:'doughnut', data:{ labels:<?= json_encode($labels) ?>, datasets:[{ data:<?= json_encode($sd) ?>, backgroundColor:<?= json_encode($clrs) ?>, borderWidth:0 }] }, options:chOpt });
    new Chart(document.getElementById('unsolvedPdfChart'), { type:'doughnut', data:{ labels:<?= json_encode($labels) ?>, datasets:[{ data:<?= json_encode($ud) ?>, backgroundColor:<?= json_encode($clrs) ?>, borderWidth:0 }] }, options:chOpt });

    async function downloadPDF() {
        const btn = document.getElementById('pdfBtn');
        btn.innerHTML = 'hazırlanıyor...'; btn.disabled = true;
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pages = document.querySelectorAll('.kapak, .sayfa');
        
        for (let i = 0; i < pages.length; i++) {
            if (i > 0) pdf.addPage();
            const canvas = await html2canvas(pages[i], { scale:3, useCORS:true, backgroundColor:'#0f172a' });
            pdf.addImage(canvas.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 210, 297);
        }
        pdf.save('Tehlike-Analizi-<?= $secilenAy ? $secilenAy : date('d-m-Y') ?>.pdf');
        btn.innerHTML = 'PDF İNDİR'; btn.disabled = false;
    }
</script>
</body>
</html>
