<?php
require_once 'config.php';
if (!isLoggedIn('admin')) redirect('index.php?tab=admin');

$search = sanitize($conn, $_GET['q'] ?? '');
$where = $search ? "WHERE nim LIKE '%$search%' OR nama LIKE '%$search%' OR jurusan LIKE '%$search%'" : '';

$mahasiswas = $conn->query("
    SELECT m.*,
           (SELECT COUNT(*) FROM rekomendasi_beasiswa WHERE id_mahasiswa=m.id_mahasiswa AND status='direkomendasikan') as total_rekomen
    FROM mahasiswa m $where
    ORDER BY m.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Mahasiswa - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>👥 Data Mahasiswa</h1>
        <p>Daftar seluruh mahasiswa terdaftar</p>
      </div>
      <div class="topbar-right">
        <form method="GET" style="display:flex;gap:8px">
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari NIM / nama / jurusan..." class="form-control" style="width:250px;padding:7px 12px">
          <button type="submit" class="btn btn-primary">🔍</button>
        </form>
      </div>
    </div>
    <div class="page-content">
      <div class="card">
        <div class="card-body" style="padding:0">
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Mahasiswa</th>
                  <th>Jurusan</th>
                  <th>IPK</th>
                  <th>Penghasilan Ortu</th>
                  <th>Tanggungan</th>
                  <th>Prestasi</th>
                  <th>Rekomendasi</th>
                  <th>Daftar</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; while ($m = $mahasiswas->fetch_assoc()): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px">
                      <div class="user-avatar" style="width:32px;height:32px;font-size:12px;border-radius:8px"><?= strtoupper(substr($m['nama'],0,1)) ?></div>
                      <div>
                        <div style="font-weight:600"><?= htmlspecialchars($m['nama']) ?></div>
                        <div style="font-size:11.5px;color:var(--text-light)"><?= $m['nim'] ?> · <?= $m['angkatan'] ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($m['jurusan'] ?: '-') ?></td>
                  <td>
                    <?php $ipk = $m['ipk']; $ipk_color = $ipk >= 3.5 ? 'var(--accent2)' : ($ipk >= 3.0 ? 'var(--primary)' : ($ipk > 0 ? 'var(--accent)' : 'var(--text-light)')); ?>
                    <strong style="color:<?= $ipk_color ?>"><?= $ipk > 0 ? number_format($ipk,2) : '-' ?></strong>
                  </td>
                  <td><?= $m['penghasilan_orangtua'] > 0 ? formatRupiah($m['penghasilan_orangtua']) : '-' ?></td>
                  <td><?= $m['jumlah_tanggungan'] > 0 ? $m['jumlah_tanggungan'].' orang' : '-' ?></td>
                  <td><?= !empty($m['prestasi']) ? '<span class="badge badge-green">✅ Ada</span>' : '<span class="badge badge-gray">-</span>' ?></td>
                  <td><span class="badge badge-blue"><?= $m['total_rekomen'] ?></span></td>
                  <td style="font-size:11.5px;color:var(--text-light)"><?= date('d/m/Y', strtotime($m['created_at'])) ?></td>
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