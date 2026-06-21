<?php
require_once 'config.php';
if (!isLoggedIn('mahasiswa')) redirect('index.php');

$mhs_id = $_SESSION['mahasiswa_id'];
$search = sanitize($conn, $_GET['q'] ?? '');

$where = $search ? "WHERE b.nama_beasiswa LIKE '%$search%' OR b.penyelenggara LIKE '%$search%'" : '';

$beasiswas = $conn->query("
    SELECT b.*, k.min_ipk, k.max_penghasilan, k.min_tanggungan, k.syarat_prestasi
    FROM beasiswa b
    LEFT JOIN kriteria k ON b.id_beasiswa = k.id_beasiswa
    $where
    ORDER BY b.nama_beasiswa
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Semua Beasiswa - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_mhs.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>🏆 Semua Beasiswa</h1>
        <p>Daftar beasiswa yang tersedia</p>
      </div>
      <div class="topbar-right">
        <form method="GET" style="display:flex;gap:8px">
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari beasiswa..." class="form-control" style="width:220px;padding:7px 12px">
          <button type="submit" class="btn btn-primary">🔍</button>
        </form>
      </div>
    </div>
    <div class="page-content">
      <div class="grid-auto">
        <?php while ($b = $beasiswas->fetch_assoc()): ?>
        <div class="beasiswa-card">
          <div class="bc-header">
            <div class="bc-icon">🎓</div>
            <div>
              <h3><?= htmlspecialchars($b['nama_beasiswa']) ?></h3>
              <div class="penyelenggara">🏢 <?= htmlspecialchars($b['penyelenggara']) ?></div>
            </div>
          </div>
          <div style="font-size:12.5px;color:var(--text-mid);margin:10px 0;line-height:1.6">
            <?= htmlspecialchars(substr($b['deskripsi'], 0, 110)) ?>...
          </div>
          <div class="bc-detail">
            <div class="bc-detail-item">
              <div class="label">💰 Nominal</div>
              <div class="val" style="color:var(--accent2)"><?= formatRupiah($b['nominal']) ?></div>
            </div>
            <div class="bc-detail-item">
              <div class="label">👥 Kuota</div>
              <div class="val"><?= $b['kuota'] ?> orang</div>
            </div>
            <div class="bc-detail-item">
              <div class="label">📊 Min IPK</div>
              <div class="val"><?= $b['min_ipk'] > 0 ? number_format($b['min_ipk'],2) : 'Tidak ada' ?></div>
            </div>
            <div class="bc-detail-item">
              <div class="label">💵 Max Penghasilan</div>
              <div class="val" style="font-size:11px"><?= $b['max_penghasilan'] > 0 ? formatRupiah($b['max_penghasilan']) : 'Tidak ada' ?></div>
            </div>
          </div>
          <div class="bc-footer">
            <?= $b['syarat_prestasi']==='ya' ? '<span class="badge badge-purple">⭐ Prestasi Wajib</span>' : '<span class="badge badge-gray">Tanpa Syarat Prestasi</span>' ?>
            <a href="detail_beasiswa.php?id=<?= $b['id_beasiswa'] ?>" class="btn btn-primary btn-sm">Detail →</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>