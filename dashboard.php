<?php
require_once 'config.php';
if (!isLoggedIn('mahasiswa')) redirect('index.php');

$mhs_id = $_SESSION['mahasiswa_id'];

// Get mahasiswa data
$mhs = $conn->query("SELECT * FROM mahasiswa WHERE id_mahasiswa=$mhs_id")->fetch_assoc();

// Count rekomendasi
$total_rekomen = $conn->query("SELECT COUNT(*) as c FROM rekomendasi_beasiswa WHERE id_mahasiswa=$mhs_id")->fetch_assoc()['c'];

// Total beasiswa tersedia
$total_beasiswa = $conn->query("SELECT COUNT(*) as c FROM beasiswa")->fetch_assoc()['c'];

// Get top 3 rekomendasi
$rekomen_list = $conn->query("
    SELECT rb.*, b.nama_beasiswa, b.penyelenggara, b.nominal, b.kuota
    FROM rekomendasi_beasiswa rb
    JOIN beasiswa b ON rb.id_beasiswa = b.id_beasiswa
    WHERE rb.id_mahasiswa = $mhs_id
    ORDER BY rb.skor_kecocokan DESC
    LIMIT 3
");

// Profil completeness
$filled = 0;
$fields = ['nim','nama','jurusan','angkatan','ipk','penghasilan_orangtua','jumlah_tanggungan'];
foreach ($fields as $f) { if (!empty($mhs[$f])) $filled++; }
$completeness = round(($filled / count($fields)) * 100);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_mhs.php'; ?>

  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Dashboard</h1>
        <p>Selamat datang, <?= htmlspecialchars($mhs['nama']) ?>!</p>
      </div>
      <div class="topbar-right">
        <div class="topbar-search">
          <span>🔍</span>
          <input type="text" placeholder="Cari beasiswa...">
        </div>
        <a href="beasiswa.php" class="notif-btn" title="Semua Beasiswa">🏆</a>
        <a href="rekomendasi.php" class="notif-btn" title="Rekomendasi">⭐</a>
      </div>
    </div>

    <div class="page-content">

      <?php if ($completeness < 100): ?>
      <div class="alert alert-warning">
        ⚠️ <strong>Profil belum lengkap (<?= $completeness ?>%).</strong>
        <a href="profil.php" style="color:inherit;font-weight:700;margin-left:6px">Lengkapi sekarang →</a>
      </div>
      <?php endif; ?>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">🏆</div>
          <div class="stat-info">
            <div class="value"><?= $total_beasiswa ?></div>
            <div class="label">Total Beasiswa</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon amber">⭐</div>
          <div class="stat-info">
            <div class="value"><?= $total_rekomen ?></div>
            <div class="label">Rekomendasi Saya</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">📊</div>
          <div class="stat-info">
            <div class="value"><?= number_format($mhs['ipk'], 2) ?></div>
            <div class="label">IPK Saya</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple">✅</div>
          <div class="stat-info">
            <div class="value"><?= $completeness ?>%</div>
            <div class="label">Kelengkapan Profil</div>
          </div>
        </div>
      </div>

      <div class="grid-2" style="gap:20px">
        <!-- Profil Singkat -->
        <div class="card">
          <div class="card-header">
            <h3>👤 Profil Saya</h3>
            <a href="profil.php" class="btn btn-outline btn-sm">Edit Profil</a>
          </div>
          <div class="card-body">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
              <div class="user-avatar" style="width:56px;height:56px;font-size:20px;border-radius:14px;background:linear-gradient(135deg,#1a4d7c,#2563ab)">
                <?= strtoupper(substr($mhs['nama'], 0, 1)) ?>
              </div>
              <div>
                <div style="font-weight:700;font-size:15px"><?= htmlspecialchars($mhs['nama']) ?></div>
                <div style="color:var(--text-light);font-size:12px">NIM: <?= $mhs['nim'] ?></div>
                <div style="color:var(--text-light);font-size:12px"><?= htmlspecialchars($mhs['jurusan']) ?> · <?= $mhs['angkatan'] ?></div>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div>
                <div style="font-size:11px;color:var(--text-light);margin-bottom:2px">IPK</div>
                <div style="font-weight:700;font-size:15px;color:var(--primary)"><?= number_format($mhs['ipk'],2) ?></div>
              </div>
              <div>
                <div style="font-size:11px;color:var(--text-light);margin-bottom:2px">Penghasilan Ortu</div>
                <div style="font-weight:700;font-size:13px"><?= formatRupiah($mhs['penghasilan_orangtua']) ?></div>
              </div>
              <div>
                <div style="font-size:11px;color:var(--text-light);margin-bottom:2px">Jumlah Tanggungan</div>
                <div style="font-weight:700;font-size:15px"><?= $mhs['jumlah_tanggungan'] ?> orang</div>
              </div>
              <div>
                <div style="font-size:11px;color:var(--text-light);margin-bottom:2px">Prestasi</div>
                <div style="font-weight:600;font-size:12px;color:var(--text-mid)"><?= !empty($mhs['prestasi']) ? '✅ Ada' : '❌ Belum ada' ?></div>
              </div>
            </div>
            <div style="margin-top:16px">
              <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
                <span>Kelengkapan Profil</span>
                <span style="font-weight:700"><?= $completeness ?>%</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $completeness ?>%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Rekomendasi -->
        <div class="card">
          <div class="card-header">
            <h3>⭐ Rekomendasi Terbaik</h3>
            <a href="rekomendasi.php" class="btn btn-outline btn-sm">Lihat Semua</a>
          </div>
          <div class="card-body" style="padding:0">
            <?php if ($rekomen_list->num_rows > 0): ?>
            <?php while ($r = $rekomen_list->fetch_assoc()): ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px">
              <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,var(--primary-dark),var(--primary));display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:13px;flex-shrink:0">
                <?= round($r['skor_kecocokan']) ?>%
              </div>
              <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?= htmlspecialchars($r['nama_beasiswa']) ?>
                </div>
                <div style="font-size:11.5px;color:var(--text-light)"><?= htmlspecialchars($r['penyelenggara']) ?></div>
                <div style="font-size:11.5px;color:var(--accent2);font-weight:600;margin-top:2px"><?= formatRupiah($r['nominal']) ?>/bln</div>
              </div>
              <span class="badge <?= $r['skor_kecocokan'] >= 80 ? 'badge-green' : ($r['skor_kecocokan'] >= 60 ? 'badge-blue' : 'badge-amber') ?>">
                <?= $r['skor_kecocokan'] >= 80 ? '🎯 Cocok' : ($r['skor_kecocokan'] >= 60 ? '👍 Sesuai' : '🔍 Cek') ?>
              </span>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="empty-state" style="padding:32px">
              <div class="icon">⭐</div>
              <h3>Belum Ada Rekomendasi</h3>
              <p>Lengkapi profil Anda terlebih dahulu</p>
              <a href="profil.php" class="btn btn-primary btn-sm" style="margin-top:12px">Lengkapi Profil</a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Info Tips -->
      <div style="margin-top:20px;background:linear-gradient(135deg,var(--primary-dark),var(--primary));border-radius:var(--radius);padding:24px;color:white;display:flex;align-items:center;gap:20px">
        <div style="font-size:40px">💡</div>
        <div>
          <div style="font-family:'Sora',sans-serif;font-weight:700;font-size:15px;margin-bottom:6px">Tips Mendapatkan Beasiswa</div>
          <div style="font-size:13px;opacity:0.85;line-height:1.7">
            Lengkapi data profil Anda (IPK, penghasilan orang tua, prestasi) agar sistem dapat menemukan beasiswa yang paling cocok untuk Anda. Semakin lengkap data, semakin akurat rekomendasinya!
          </div>
        </div>
        <a href="profil.php" class="btn btn-accent" style="white-space:nowrap;margin-left:auto">Update Profil →</a>
      </div>

    </div>
  </div>
</div>
</body>
</html>