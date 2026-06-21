<?php
require_once 'config.php';
if (!isLoggedIn('admin')) redirect('index.php?tab=admin');

$filter_beasiswa = (int)($_GET['beasiswa'] ?? 0);
$where = $filter_beasiswa ? "AND rb.id_beasiswa=$filter_beasiswa" : '';

$rekomendasis = $conn->query("
    SELECT rb.*, m.nama, m.nim, m.jurusan, m.ipk, b.nama_beasiswa, b.penyelenggara
    FROM rekomendasi_beasiswa rb
    JOIN mahasiswa m ON rb.id_mahasiswa = m.id_mahasiswa
    JOIN beasiswa b ON rb.id_beasiswa = b.id_beasiswa
    WHERE rb.status='direkomendasikan' $where
    ORDER BY rb.skor_kecocokan DESC
");

$all_beasiswa = $conn->query("SELECT id_beasiswa, nama_beasiswa FROM beasiswa ORDER BY nama_beasiswa");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rekap Rekomendasi - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>⭐ Rekap Rekomendasi</h1>
        <p>Daftar rekomendasi beasiswa untuk seluruh mahasiswa</p>
      </div>
      <div class="topbar-right">
        <form method="GET" style="display:flex;gap:8px">
          <select name="beasiswa" class="form-control" style="width:220px">
            <option value="">-- Semua Beasiswa --</option>
            <?php while ($ab = $all_beasiswa->fetch_assoc()): ?>
            <option value="<?= $ab['id_beasiswa'] ?>" <?= $filter_beasiswa==$ab['id_beasiswa']?'selected':'' ?>>
              <?= htmlspecialchars($ab['nama_beasiswa']) ?>
            </option>
            <?php endwhile; ?>
          </select>
          <button type="submit" class="btn btn-primary">Filter</button>
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
                  <th>Beasiswa</th>
                  <th>IPK</th>
                  <th>Skor Kecocokan</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; while ($r = $rekomendasis->fetch_assoc()):
                  $skor = $r['skor_kecocokan'];
                ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td>
                    <div style="font-weight:600"><?= htmlspecialchars($r['nama']) ?></div>
                    <div style="font-size:11.5px;color:var(--text-light)"><?= $r['nim'] ?> · <?= htmlspecialchars($r['jurusan']) ?></div>
                  </td>
                  <td>
                    <div style="font-weight:500;font-size:13px"><?= htmlspecialchars($r['nama_beasiswa']) ?></div>
                    <div style="font-size:11.5px;color:var(--text-light)"><?= htmlspecialchars($r['penyelenggara']) ?></div>
                  </td>
                  <td><strong><?= number_format($r['ipk'],2) ?></strong></td>
                  <td>
                    <div style="display:flex;align-items:center;gap:8px">
                      <span style="font-weight:700;font-size:14px;color:<?= $skor>=80?'var(--accent2)':($skor>=60?'var(--primary)':'var(--accent)') ?>"><?= round($skor) ?>%</span>
                      <span class="badge <?= $skor>=80?'badge-green':($skor>=60?'badge-blue':'badge-amber') ?>">
                        <?= $skor>=80?'Sangat Cocok':($skor>=60?'Cocok':'Cukup') ?>
                      </span>
                    </div>
                  </td>
                  <td style="font-size:12px;color:var(--text-light)"><?= date('d/m/Y', strtotime($r['tanggal_rekomendasi'])) ?></td>
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