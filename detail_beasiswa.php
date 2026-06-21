<?php
require_once 'config.php';
if (!isLoggedIn('mahasiswa')) redirect('index.php');

$id = (int)($_GET['id'] ?? 0);
$mhs_id = $_SESSION['mahasiswa_id'];

$b = $conn->query("
    SELECT b.*, k.min_ipk, k.max_penghasilan, k.min_tanggungan, k.syarat_prestasi, k.syarat_akademik, k.syarat_organisasi, k.min_toefl, k.min_ielts
    FROM beasiswa b LEFT JOIN kriteria k ON b.id_beasiswa = k.id_beasiswa
    WHERE b.id_beasiswa = $id
")->fetch_assoc();

if (!$b) { echo "Beasiswa tidak ditemukan."; exit; }

$mhs = $conn->query("SELECT * FROM mahasiswa WHERE id_mahasiswa=$mhs_id")->fetch_assoc();

// Cek rekomendasi
$rekomen = $conn->query("SELECT * FROM rekomendasi_beasiswa WHERE id_mahasiswa=$mhs_id AND id_beasiswa=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($b['nama_beasiswa']) ?> - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_mhs.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Detail Beasiswa</h1>
        <p><a href="beasiswa.php" style="color:var(--primary)">← Kembali ke Daftar Beasiswa</a></p>
      </div>
    </div>
    <div class="page-content">
      <div class="grid-2" style="gap:20px;align-items:start">

        <div>
          <!-- Hero card -->
          <div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary-light));border-radius:var(--radius);padding:28px;color:white;margin-bottom:16px">
            <div style="font-size:40px;margin-bottom:12px">🎓</div>
            <h2 style="font-size:20px;font-family:'Sora',sans-serif;margin-bottom:6px"><?= htmlspecialchars($b['nama_beasiswa']) ?></h2>
            <div style="opacity:0.8;font-size:14px">🏢 <?= htmlspecialchars($b['penyelenggara']) ?></div>
            <?php if ($rekomen): ?>
            <div style="margin-top:16px;background:rgba(255,255,255,0.15);border-radius:8px;padding:12px 16px;display:inline-flex;align-items:center;gap:10px">
              <span style="font-size:20px">🎯</span>
              <div>
                <div style="font-size:11px;opacity:0.8">Skor Kecocokan</div>
                <div style="font-size:22px;font-weight:800;font-family:'Sora',sans-serif"><?= round($rekomen['skor_kecocokan']) ?>%</div>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <div class="card">
            <div class="card-header"><h3>📖 Deskripsi Beasiswa</h3></div>
            <div class="card-body" style="font-size:13.5px;line-height:1.8;color:var(--text-mid)">
              <?= nl2br(htmlspecialchars($b['deskripsi'])) ?>
            </div>
          </div>
        </div>

        <div>
          <div class="card" style="margin-bottom:16px">
            <div class="card-header"><h3>📋 Syarat & Kriteria</h3></div>
            <div class="card-body">
              <div style="display:grid;gap:14px">
                <div style="background:var(--content-bg);border-radius:8px;padding:14px">
                  <div style="font-size:11px;color:var(--text-light);margin-bottom:6px">💰 NOMINAL BEASISWA</div>
                  <div style="font-size:20px;font-weight:800;color:var(--accent2);font-family:'Sora',sans-serif"><?= formatRupiah($b['nominal']) ?><span style="font-size:12px;font-weight:400;color:var(--text-light)">/smt</span></div>
                </div>
                <div style="background:var(--content-bg);border-radius:8px;padding:14px">
                  <div style="font-size:11px;color:var(--text-light);margin-bottom:6px">👥 KUOTA PENERIMA</div>
                  <div style="font-size:20px;font-weight:700;font-family:'Sora',sans-serif"><?= $b['kuota'] ?> <span style="font-size:13px;font-weight:400;color:var(--text-light)">mahasiswa</span></div>
                </div>
              </div>

              <div style="margin-top:16px">
                <div style="font-size:12px;font-weight:700;color:var(--text-mid);margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px">Persyaratan</div>

                <?php
                $checks = [
                    ['label' => 'IPK Minimal', 'req' => $b['min_ipk'] > 0 ? number_format($b['min_ipk'],2) : 'Tidak ada syarat', 'val' => number_format($mhs['ipk'],2), 'ok' => $b['min_ipk']==0 || $mhs['ipk'] >= $b['min_ipk']],
                    ['label' => 'Max Penghasilan Ortu', 'req' => $b['max_penghasilan'] > 0 ? formatRupiah($b['max_penghasilan']) : 'Tidak ada syarat', 'val' => formatRupiah($mhs['penghasilan_orangtua']), 'ok' => $b['max_penghasilan']==0 || $mhs['penghasilan_orangtua'] <= $b['max_penghasilan']],
                    ['label' => 'Min Jumlah Tanggungan', 'req' => $b['min_tanggungan'] > 0 ? $b['min_tanggungan'].' orang' : 'Tidak ada syarat', 'val' => $mhs['jumlah_tanggungan'].' orang', 'ok' => $b['min_tanggungan']==0 || $mhs['jumlah_tanggungan'] >= $b['min_tanggungan']],
                    ['label' => 'Syarat Prestasi', 'req' => $b['syarat_prestasi']==='ya' ? 'Wajib ada prestasi' : 'Tidak ada syarat', 'val' => !empty($mhs['prestasi']) ? 'Ada' : 'Tidak ada', 'ok' => $b['syarat_prestasi']==='tidak' || !empty($mhs['prestasi'])],
                    ['label' => 'Aktif Organisasi', 'req' => $b['syarat_organisasi']==='ya' ? 'Wajib aktif organisasi' : 'Tidak ada syarat', 'val' => ($mhs['aktif_organisasi']??'tidak')==='ya' ? 'Aktif' : 'Tidak aktif', 'ok' => $b['syarat_organisasi']==='tidak' || ($mhs['aktif_organisasi']??'tidak')==='ya'],
                    ['label' => 'TOEFL/IELTS', 'req' => ($b['min_toefl']>0||$b['min_ielts']>0) ? 'TOEFL min '.$b['min_toefl'].' / IELTS min '.$b['min_ielts'] : 'Tidak ada syarat', 'val' => ($mhs['skor_toefl']??0)>0 ? 'TOEFL: '.($mhs['skor_toefl']??0) : (($mhs['skor_ielts']??0)>0 ? 'IELTS: '.($mhs['skor_ielts']??0) : 'Tidak ada'), 'ok' => ($b['min_toefl']==0 && $b['min_ielts']==0) || ($mhs['skor_toefl']??0)>=$b['min_toefl'] || ($mhs['skor_ielts']??0)>=$b['min_ielts']],
                ];
                foreach ($checks as $c): ?>
                <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">
                  <span style="font-size:18px"><?= $c['ok'] ? '✅' : '❌' ?></span>
                  <div style="flex:1">
                    <div style="font-size:12px;font-weight:600"><?= $c['label'] ?></div>
                    <div style="font-size:11.5px;color:var(--text-light)">Syarat: <?= $c['req'] ?> · Anda: <strong><?= $c['val'] ?></strong></div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>

              <?php if (!empty($b['syarat_akademik'])): ?>
              <div style="margin-top:14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px">
                <div style="font-size:11px;font-weight:700;color:#1e40af;margin-bottom:4px">📌 Syarat Tambahan</div>
                <div style="font-size:12.5px;color:#1e40af"><?= nl2br(htmlspecialchars($b['syarat_akademik'])) ?></div>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</body>
</html>