<?php
require_once 'config.php';
if (!isLoggedIn('admin')) redirect('index.php?tab=admin');

// Stats
$total_mhs = $conn->query("SELECT COUNT(*) as c FROM mahasiswa")->fetch_assoc()['c'];
$total_beasiswa = $conn->query("SELECT COUNT(*) as c FROM beasiswa")->fetch_assoc()['c'];
$total_rekomen = $conn->query("SELECT COUNT(*) as c FROM rekomendasi_beasiswa WHERE status='direkomendasikan'")->fetch_assoc()['c'];
$total_kriteria = $conn->query("SELECT COUNT(*) as c FROM kriteria")->fetch_assoc()['c'];

// Top beasiswa by rekomendasi
$top_beasiswa = $conn->query("
    SELECT b.nama_beasiswa, COUNT(rb.id_rekomendasi) as total
    FROM beasiswa b
    LEFT JOIN rekomendasi_beasiswa rb ON b.id_beasiswa = rb.id_beasiswa AND rb.status='direkomendasikan'
    GROUP BY b.id_beasiswa
    ORDER BY total DESC
    LIMIT 5
");

// Recent mahasiswa
$recent_mhs = $conn->query("SELECT * FROM mahasiswa ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?>!</p>
      </div>
      <div class="topbar-right">
        <span class="score-badge">👤 Admin Panel</span>
      </div>
    </div>
    <div class="page-content">

      <!-- Stats -->
      <div class="stats-grid">

        <div class="stat-card">
          <div class="stat-icon blue">👥</div>
          <div class="stat-info">
            <div class="value"><?= $total_mhs ?></div>
            <div class="label">Total Mahasiswa</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon yellow">🏆</div>
          <div class="stat-info">
            <div class="value"><?= $total_beasiswa ?></div>
            <div class="label">Total Beasiswa</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon green">⭐</div>
          <div class="stat-info">
            <div class="value"><?= $total_rekomen ?></div>
            <div class="label">Total Rekomendasi</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon purple">📋</div>
          <div class="stat-info">
            <div class="value"><?= $total_kriteria ?></div>
            <div class="label">Kriteria Aktif</div>
          </div>
        </div>

    </div>
    

      <div class="grid-2" style="gap:20px">

        <!-- Top Beasiswa -->
        <div class="card">
          <div class="card-header">
            <h3>🏆 Top Beasiswa (Paling Banyak Direkomendasikan)</h3>
          </div>
          <div class="card-body" style="padding:0">
            <?php while ($tb = $top_beasiswa->fetch_assoc()): ?>
            <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
              <div style="font-size:13px;font-weight:500"><?= htmlspecialchars($tb['nama_beasiswa']) ?></div>
              <span class="badge badge-blue"><?= $tb['total'] ?> mahasiswa</span>
            </div>
            <?php endwhile; ?>
          </div>
        </div>

        <!-- Recent Mahasiswa -->
        <div class="card">
          <div class="card-header">
            <h3>👥 Mahasiswa Terbaru</h3>
            <a href="mahasiswa.php" class="btn btn-outline btn-sm">Lihat Semua</a>
          </div>
          <div class="card-body" style="padding:0">
            <?php while ($rm = $recent_mhs->fetch_assoc()): ?>
            <div style="padding:12px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px">
              <div class="user-avatar" style="width:34px;height:34px;font-size:13px;border-radius:8px">
                <?= strtoupper(substr($rm['nama'],0,1)) ?>
              </div>
              <div style="flex:1">
                <div style="font-size:13px;font-weight:600"><?= htmlspecialchars($rm['nama']) ?></div>
                <div style="font-size:11.5px;color:var(--text-light)"><?= $rm['nim'] ?> · <?= htmlspecialchars($rm['jurusan']) ?></div>
              </div>
              <span style="font-size:11.5px;color:var(--text-light)"><?= date('d/m/Y', strtotime($rm['created_at'])) ?></span>
            </div>
            <?php endwhile; ?>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div style="margin-top:20px">
        <div style="font-size:13px;font-weight:700;color:var(--text-mid);margin-bottom:12px;text-transform:uppercase;letter-spacing:0.5px">⚡ Aksi Cepat</div>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <a href="beasiswa_admin.php?action=tambah" class="btn btn-primary">➕ Tambah Beasiswa</a>
          <a href="mahasiswa.php" class="btn btn-outline">👥 Lihat Mahasiswa</a>
          <a href="statistik.php" class="btn btn-outline">📈 Lihat Statistik</a>
          <a href="rekomendasi_admin.php" class="btn btn-outline">⭐ Rekap Rekomendasi</a>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>