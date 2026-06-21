<?php
require_once 'config.php';
if (!isLoggedIn('admin')) redirect('index.php?tab=admin');

$total_mhs = $conn->query("SELECT COUNT(*) as c FROM mahasiswa")->fetch_assoc()['c'];
$total_beasiswa = $conn->query("SELECT COUNT(*) as c FROM beasiswa")->fetch_assoc()['c'];
$total_rekomen = $conn->query("SELECT COUNT(*) as c FROM rekomendasi_beasiswa WHERE status='direkomendasikan'")->fetch_assoc()['c'];
$mhs_lengkap = $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE ipk > 0 AND penghasilan_orangtua > 0")->fetch_assoc()['c'];

// IPK distribution
$ipk_ranges = [
    '3.50 - 4.00' => $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE ipk >= 3.50")->fetch_assoc()['c'],
    '3.00 - 3.49' => $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE ipk >= 3.00 AND ipk < 3.50")->fetch_assoc()['c'],
    '2.50 - 2.99' => $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE ipk >= 2.50 AND ipk < 3.00")->fetch_assoc()['c'],
    'Di bawah 2.50' => $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE ipk > 0 AND ipk < 2.50")->fetch_assoc()['c'],
    'Belum diisi' => $conn->query("SELECT COUNT(*) as c FROM mahasiswa WHERE ipk = 0")->fetch_assoc()['c'],
];

// Beasiswa stats
$beasiswa_stats = $conn->query("
    SELECT b.nama_beasiswa, COUNT(rb.id_rekomendasi) as total,
           ROUND(AVG(rb.skor_kecocokan),1) as avg_skor
    FROM beasiswa b
    LEFT JOIN rekomendasi_beasiswa rb ON b.id_beasiswa = rb.id_beasiswa AND rb.status='direkomendasikan'
    GROUP BY b.id_beasiswa
    ORDER BY total DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Statistik - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>📈 Statistik Sistem</h1>
        <p>Laporan dan rekap data sistem rekomendasi beasiswa</p>
      </div>
    </div>
    <div class="page-content">

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon blue">👥</div>
          <div class="stat-info">
            <div class="value"><?= $total_mhs ?></div>
            <div class="label">Total Mahasiswa</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon green">✅</div>
          <div class="stat-info">
            <div class="value"><?= $mhs_lengkap ?></div>
            <div class="label">Profil Lengkap</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon amber">🏆</div>
          <div class="stat-info">
            <div class="value"><?= $total_beasiswa ?></div>
            <div class="label">Total Beasiswa</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon purple">⭐</div>
          <div class="stat-info">
            <div class="value"><?= $total_rekomen ?></div>
            <div class="label">Total Rekomendasi</div>
          </div>
        </div>
      </div>

      <div class="grid-2" style="gap:20px">

        <!-- IPK Distribution -->
        <div class="card">
          <div class="card-header"><h3>📊 Distribusi IPK Mahasiswa</h3></div>
          <div class="card-body">
            <?php foreach ($ipk_ranges as $range => $count):
              $pct = $total_mhs > 0 ? round(($count / $total_mhs) * 100) : 0;
              $colors = ['3.50 - 4.00' => '#10b981', '3.00 - 3.49' => '#3b82f6', '2.50 - 2.99' => '#f59e0b', 'Di bawah 2.50' => '#ef4444', 'Belum diisi' => '#94a3b8'];
            ?>
            <div style="margin-bottom:14px">
              <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:5px">
                <span><?= $range ?></span>
                <span style="font-weight:700"><?= $count ?> (<?= $pct ?>%)</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $colors[$range] ?>"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Beasiswa Stats -->
        <div class="card">
          <div class="card-header"><h3>🏆 Statistik Beasiswa</h3></div>
          <div class="card-body" style="padding:0">
            <table>
              <thead>
                <tr>
                  <th>Beasiswa</th>
                  <th>Penerima Rekomen</th>
                  <th>Avg Skor</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($bs = $beasiswa_stats->fetch_assoc()): ?>
                <tr>
                  <td style="font-size:12px"><?= htmlspecialchars($bs['nama_beasiswa']) ?></td>
                  <td><span class="badge badge-blue"><?= $bs['total'] ?> mhs</span></td>
                  <td>
                    <?php $avg = $bs['avg_skor'] ?? 0; ?>
                    <span style="font-weight:700;color:<?= $avg >= 80 ? 'var(--accent2)' : ($avg >= 60 ? 'var(--primary)' : 'var(--accent)') ?>">
                      <?= $avg > 0 ? $avg.'%' : '-' ?>
                    </span>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>
</body>
</html>