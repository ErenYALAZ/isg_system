<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Performans Raporu — MAISG</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #060c18; color: #f1f5f9; }

        /* ── KAPAK SAYFASI ── */
        .kapak {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: linear-gradient(160deg, #0f172a 0%, #0b1120 50%, #0f1f3d 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            page-break-after: always;
            break-after: page;
            position: relative;
            overflow: hidden;
        }

        .kapak::before {
            content: '';
            position: absolute;
            top: -80px; left: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        .kapak::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(16,185,129,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }

        .kapak-logo { font-size: 42px; font-weight: 900; color: #fff; letter-spacing: -1px; margin-bottom: 20px; }
        .kapak-logo span { color: #3b82f6; font-weight: 300; }
        .kapak-baslik { font-size: 30px; font-weight: 800; color: #fff; text-align: center; margin-bottom: 10px; letter-spacing: -0.5px; }
        .kapak-alt { font-size: 14px; color: #64748b; font-weight: 600; text-align: center; margin-bottom: 60px; }
        .kapak-cizgi { width: 80px; height: 3px; background: linear-gradient(90deg, #3b82f6, #10b981); border-radius: 3px; margin: 20px auto; }
        .kapak-tarih { font-size: 13px; color: #475569; font-weight: 700; text-align: center; }
        .kapak-ozet { display: flex; gap: 25px; margin-top: 50px; }
        .kapak-ozet-item { text-align: center; padding: 20px 30px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 20px; }
        .kapak-ozet-num { font-size: 36px; font-weight: 900; }
        .kapak-ozet-lbl { font-size: 10px; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

        /* ── SAYFA ORTAK ── */
        .sayfa {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            padding: 0;
            position: relative;
            page-break-after: always;
            break-after: page;
            overflow: hidden;
        }
        .sayfa:last-child { page-break-after: avoid; break-after: avoid; }

        .page-header {
            height: 14mm;
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            border-bottom: 2px solid #3b82f6;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 15mm; flex-shrink: 0;
        }
        .brand { font-size: 17px; font-weight: 900; color: #fff; }
        .brand span { color: #3b82f6; font-weight: 300; }
        .page-info { font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        .page-footer {
            height: 10mm;
            background: #0a0f1e;
            border-top: 1px solid rgba(59,130,246,0.2);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 15mm; flex-shrink: 0; margin-top: auto;
        }
        .footer-text { font-size: 8px; color: #334155; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; }

        .page-body { flex: 1; padding: 10mm 15mm 6mm; overflow: hidden; display: flex; flex-direction: column; gap: 8mm; }

        /* ── İSTATİSTİK KARTLARI ── */
        .stat-row { display: flex; gap: 6mm; }
        .stat-box {
            flex: 1; background: rgba(30,41,59,0.6); border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px; padding: 6mm 7mm; display: flex; align-items: center; gap: 5mm;
        }
        .stat-icon { width: 13mm; height: 13mm; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .stat-lbl { font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 3px; }
        .stat-num { font-size: 28px; font-weight: 900; line-height: 1; }

        /* ── DONUT CHART ALANI ── */
        .chart-wrap {
            background: rgba(30,41,59,0.4); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px; padding: 5mm 8mm;
            display: flex; align-items: center; gap: 10mm;
        }
        .chart-container {
            position: relative; width: 130px; height: 130px; flex-shrink: 0;
        }
        .chart-center {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            text-align: center; pointer-events: none;
        }
        .chart-perc { font-size: 28px; font-weight: 900; color: #fff; line-height: 1; display: block; }
        .chart-lbl  { font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; display: block; }
        .chart-legend { display: flex; flex-direction: column; gap: 8px; }
        .legend-item { display: flex; align-items: center; gap: 6px; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .legend-label { font-size: 11px; font-weight: 700; color: #94a3b8; }
        .legend-val { font-size: 13px; font-weight: 900; margin-left: auto; }
        .chart-right-info { flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 6px; border-left: 1px solid rgba(255,255,255,0.06); padding-left: 8mm; }
        .info-label { font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px; }
        .info-value { font-size: 22px; font-weight: 900; line-height: 1; }
        .info-sub { font-size: 10px; color: #64748b; font-weight: 600; margin-top: 2px; }

        /* ── TABLO BAŞLIĞI ── */
        .tablo-baslik { font-size: 13px; font-weight: 900; color: #fff; letter-spacing: -0.3px; border-left: 3px solid #3b82f6; padding-left: 4mm; }
        .tablo-sub { font-size: 9px; color: #64748b; font-weight: 600; margin-top: 2px; padding-left: 4mm; }

        /* ── TABLO ── */
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: rgba(59,130,246,0.08); }
        th { padding: 3mm 4mm; font-size: 9px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); }
        td { padding: 3mm 4mm; font-size: 11px; border-bottom: 1px solid rgba(255,255,255,0.03); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .badge { padding: 3px 8px; border-radius: 6px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-ok { background: rgba(16,185,129,0.12); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
        .badge-err { background: rgba(239,68,68,0.12); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
        .td-baslik { font-weight: 800; color: #f8fafc; font-size: 11px; }
        .td-dept { font-size: 10px; color: #64748b; font-weight: 600; }
        .td-tarih { font-size: 10px; color: #475569; font-weight: 700; }

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
$bugunStr  = date('d.m.Y H:i');
$toplam    = (int)($stats['toplam'] ?? 0);
$cozulmus  = (int)($stats['cozulmus'] ?? 0);
$cozulmemis = (int)($stats['cozulmemis'] ?? 0);
$oran      = $toplam > 0 ? round(($cozulmus / $toplam) * 100) : 0;

// Tabloya sığan satır sayısı (yaklaşık)
$satirPerSayfa = 17;
$satirlar      = $duyuruListesi;
$toplamSatir   = count($satirlar);
$sayfaSayisi   = $toplamSatir > 0 ? ceil($toplamSatir / $satirPerSayfa) : 1;
?>

<!-- ══════════════ KAPAK SAYFASI ══════════════ -->
<div class="kapak">
    <div class="kapak-logo">MA<span>ISG</span></div>
    <div class="kapak-cizgi"></div>
    <div class="kapak-baslik">Performans Raporu</div>
    <?php if ($secilenAy): 
        $ts = strtotime($secilenAy."-01");
        $ayIsimleri  = ['January'=>'Ocak','February'=>'Şubat','March'=>'Mart','April'=>'Nisan',
                        'May'=>'Mayıs','June'=>'Haziran','July'=>'Temmuz','August'=>'Ağustos',
                        'September'=>'Eylül','October'=>'Ekim','November'=>'Kasım','December'=>'Aralık'];
        $ayEtiket = str_replace(array_keys($ayIsimleri), array_values($ayIsimleri), date('F Y', $ts));
    ?>
        <div style="background:rgba(59,130,246,0.1); color:#3b82f6; padding:6px 16px; border-radius:10px; font-size:13px; font-weight:800; margin-bottom:12px; border:1px solid rgba(59,130,246,0.2);">
            <?= $ayEtiket ?> DÖNEMİ
        </div>
    <?php endif; ?>
    <div class="kapak-alt">İş Sağlığı ve Güvenliği Yönetim Sistemi</div>
    <div class="kapak-tarih">Rapor Tarihi: <?= $bugunStr ?></div>
    <div class="kapak-ozet">
        <div class="kapak-ozet-item">
            <div class="kapak-ozet-num" style="color:#3b82f6;"><?= $toplam ?></div>
            <div class="kapak-ozet-lbl">Toplam</div>
        </div>
        <div class="kapak-ozet-item">
            <div class="kapak-ozet-num" style="color:#10b981;"><?= $cozulmus ?></div>
            <div class="kapak-ozet-lbl">Çözüldü</div>
        </div>
        <div class="kapak-ozet-item">
            <div class="kapak-ozet-num" style="color:#ef4444;"><?= $cozulmemis ?></div>
            <div class="kapak-ozet-lbl">Açık Risk</div>
        </div>
        <div class="kapak-ozet-item">
            <div class="kapak-ozet-num" style="color:#10b981;"><?= $oran ?>%</div>
            <div class="kapak-ozet-lbl">Başarı Oranı</div>
        </div>
    </div>
</div>

<!-- ══════════════ ÖZET SAYFA ══════════════ -->
<div class="sayfa">
    <div class="page-header">
        <div class="brand">MA<span>ISG</span></div>
        <div class="page-info"><?= $bugunStr ?> &nbsp;|&nbsp; Özet İstatistik</div>
    </div>

    <div class="page-body">

        <!-- Stat Kutucukları -->
        <div class="stat-row">
            <div class="stat-box">
                <div class="stat-icon" style="background:rgba(59,130,246,0.12);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <div>
                    <div class="stat-lbl">Toplam İş Gücü</div>
                    <div class="stat-num" style="color:#3b82f6;"><?= $toplam ?></div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:rgba(16,185,129,0.12);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div>
                    <div class="stat-lbl">Başarıyla Tamamlanan</div>
                    <div class="stat-num" style="color:#10b981;"><?= $cozulmus ?></div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background:rgba(239,68,68,0.12);">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div class="stat-lbl">Aktif Riskler</div>
                    <div class="stat-num" style="color:#ef4444;"><?= $cozulmemis ?></div>
                </div>
            </div>
        </div>

        <!-- Donut Grafik -->
        <div class="chart-wrap">
            <div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                <div class="chart-container">
                    <canvas id="perfChart"></canvas>
                    <div class="chart-center">
                        <span class="chart-perc" id="chartPercLabel"><?= $oran ?>%</span>
                        <span class="chart-lbl">ÇÖZÜM ORANI</span>
                    </div>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#10b981;"></span>
                        <span class="legend-label">Bitmiş İşler</span>
                        <span class="legend-val" style="color:#10b981;"><?= $cozulmus ?></span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#ef4444;"></span>
                        <span class="legend-label">Açık Riskler</span>
                        <span class="legend-val" style="color:#ef4444;"><?= $cozulmemis ?></span>
                    </div>
                </div>
            </div>
            <div class="chart-right-info">
                <div>
                    <div class="info-label">Toplam İş Gücü</div>
                    <div class="info-value" style="color:#3b82f6;"><?= $toplam ?> <span style="font-size:12px;font-weight:500;color:#475569;">Duyuru</span></div>
                </div>
                <div style="height:1px;background:rgba(255,255,255,0.05);"></div>
                <div>
                    <div class="info-label">Başarıyla Tamamlanan</div>
                    <div class="info-value" style="color:#10b981;"><?= $cozulmus ?> <span style="font-size:12px;font-weight:500;color:#475569;">İş</span></div>
                </div>
                <div style="height:1px;background:rgba(255,255,255,0.05);"></div>
                <div>
                    <div class="info-label">Aktif Riskler (Çözülmedi)</div>
                    <div class="info-value" style="color:#ef4444;"><?= $cozulmemis ?> <span style="font-size:12px;font-weight:500;color:#475569;">Adet</span></div>
                </div>
            </div>
        </div>

        <!-- Sayfa 1 tablo -->
        <?php $sayfa1Satirlar = array_slice($satirlar, 0, $satirPerSayfa); ?>
        <div>
            <div class="tablo-baslik">Duyuru Listesi</div>
            <div class="tablo-sub">Yeniden eskiye sıralı — Toplam <?= $toplamSatir ?> kayıt</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th width="90">Durum</th>
                    <th>Başlık / Konu</th>
                    <th>Birim</th>
                    <th width="80">Tarih</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sayfa1Satirlar as $it): ?>
                <tr>
                    <td>
                        <?php if ($it['is_hazard'] == 0): ?>
                            <span class="badge badge-ok">Çözüldü</span>
                        <?php else: ?>
                            <span class="badge badge-err">Açık</span>
                        <?php endif; ?>
                    </td>
                    <td class="td-baslik"><?= htmlspecialchars($it['title']) ?></td>
                    <td class="td-dept"><?= htmlspecialchars($it['department_tag']) ?></td>
                    <td class="td-tarih"><?= date('d.m.Y', strtotime($it['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <div class="page-footer">
        <div class="footer-text">MAISG — Performans Raporu</div>
        <div class="footer-text">Sayfa 1 / <?= $sayfaSayisi + 1 ?></div>
    </div>
</div>

<!-- ══════════════ EK TABLO SAYFALARI ══════════════ -->
<?php
$sayfaNo = 2;
for ($baslangic = $satirPerSayfa; $baslangic < $toplamSatir; $baslangic += $satirPerSayfa):
    $dilim = array_slice($satirlar, $baslangic, $satirPerSayfa);
?>
<div class="sayfa">
    <div class="page-header">
        <div class="brand">MA<span>ISG</span></div>
        <div class="page-info"><?= $bugunStr ?> &nbsp;|&nbsp; Duyuru Listesi (devam)</div>
    </div>

    <div class="page-body">
        <table>
            <thead>
                <tr>
                    <th width="90">Durum</th>
                    <th>Başlık / Konu</th>
                    <th>Birim</th>
                    <th width="80">Tarih</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dilim as $it): ?>
                <tr>
                    <td>
                        <?php if ($it['is_hazard'] == 0): ?>
                            <span class="badge badge-ok">Çözüldü</span>
                        <?php else: ?>
                            <span class="badge badge-err">Açık</span>
                        <?php endif; ?>
                    </td>
                    <td class="td-baslik"><?= htmlspecialchars($it['title']) ?></td>
                    <td class="td-dept"><?= htmlspecialchars($it['department_tag']) ?></td>
                    <td class="td-tarih"><?= date('d.m.Y', strtotime($it['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="page-footer">
        <div class="footer-text">MAISG — Performans Raporu</div>
        <div class="footer-text">Sayfa <?= $sayfaNo++ ?> / <?= $sayfaSayisi + 1 ?></div>
    </div>
</div>
<?php endfor; ?>
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
    var cozulmus  = <?= $cozulmus ?>;
    var cozulmemis = <?= $cozulmemis ?>;
    var toplam    = <?= $toplam ?>;

    var ctx = document.getElementById('perfChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Bitmiş İşler', 'Açık Riskler'],
                datasets: [{
                    data: [cozulmus, cozulmemis],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderColor: 'transparent',
                    borderWidth: 0,
                    hoverOffset: 10,
                    borderRadius: (toplam > 0 && cozulmemis > 0 && cozulmus > 0) ? 12 : 0
                }]
            },
            options: {
                cutout: '80%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                animation: { duration: 0 }
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

        pdf.save('MAISG-Performans-Raporu.pdf');

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
