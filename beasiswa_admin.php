<?php
require_once 'config.php';
if (!isLoggedIn('admin')) redirect('index.php?tab=admin');

$admin_id = $_SESSION['admin_id'];
$error = ''; $success = '';
$action = $_GET['action'] ?? 'list';
$edit_id = (int)($_GET['id'] ?? 0);
$edit_data = null;

// HAPUS
if ($action === 'hapus' && $edit_id) {
    $conn->query("DELETE FROM beasiswa WHERE id_beasiswa=$edit_id");
    $success = 'Beasiswa berhasil dihapus.';
    $action = 'list';
}

// TAMBAH / EDIT PROSES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($conn, $_POST['nama_beasiswa']);
    $penyelenggara = sanitize($conn, $_POST['penyelenggara']);
    $deskripsi = sanitize($conn, $_POST['deskripsi']);
    $kuota = (int)$_POST['kuota'];
    $nominal = (int)str_replace(['.', ','], '', $_POST['nominal']);
    $min_ipk = (float)$_POST['min_ipk'];
    $max_penghasilan = (int)str_replace(['.', ','], '', $_POST['max_penghasilan']);
    $min_tanggungan = (int)$_POST['min_tanggungan'];
    $syarat_prestasi = sanitize($conn, $_POST['syarat_prestasi']);
    $syarat_akademik = sanitize($conn, $_POST['syarat_akademik']);
    $syarat_organisasi = in_array($_POST['syarat_organisasi'] ?? 'tidak', ['ya','tidak']) ? $_POST['syarat_organisasi'] : 'tidak';
    $min_toefl = (int)($_POST['min_toefl'] ?? 0);
    $min_ielts = (float)($_POST['min_ielts'] ?? 0);

    if ($_POST['beasiswa_id'] > 0) {
        // Update
        $bid = (int)$_POST['beasiswa_id'];
        $conn->query("UPDATE beasiswa SET nama_beasiswa='$nama', penyelenggara='$penyelenggara', deskripsi='$deskripsi', kuota=$kuota, nominal=$nominal WHERE id_beasiswa=$bid");
        $conn->query("UPDATE kriteria SET min_ipk=$min_ipk, max_penghasilan=$max_penghasilan, min_tanggungan=$min_tanggungan, syarat_prestasi='$syarat_prestasi', syarat_akademik='$syarat_akademik', syarat_organisasi='$syarat_organisasi', min_toefl=$min_toefl, min_ielts=$min_ielts WHERE id_beasiswa=$bid");
        $success = 'Beasiswa berhasil diperbarui!';
    } else {
        // Insert
        $conn->query("INSERT INTO beasiswa (nama_beasiswa, penyelenggara, deskripsi, kuota, nominal, id_admin) VALUES ('$nama','$penyelenggara','$deskripsi',$kuota,$nominal,$admin_id)");
        $new_id = $conn->insert_id;
        $conn->query("INSERT INTO kriteria (id_beasiswa, min_ipk, max_penghasilan, min_tanggungan, syarat_prestasi, syarat_akademik, syarat_organisasi, min_toefl, min_ielts) VALUES ($new_id, $min_ipk, $max_penghasilan, $min_tanggungan, '$syarat_prestasi', '$syarat_akademik', '$syarat_organisasi', $min_toefl, $min_ielts)");
        $success = 'Beasiswa berhasil ditambahkan!';
    }
    $action = 'list';
}

// Load edit data
if ($action === 'edit' && $edit_id) {
    $edit_data = $conn->query("
        SELECT b.*, k.min_ipk, k.max_penghasilan, k.min_tanggungan, k.syarat_prestasi, k.syarat_akademik, k.syarat_organisasi, k.min_toefl, k.min_ielts
        FROM beasiswa b LEFT JOIN kriteria k ON b.id_beasiswa=k.id_beasiswa
        WHERE b.id_beasiswa=$edit_id
    ")->fetch_assoc();
}

// List beasiswa
$beasiswas = $conn->query("
    SELECT b.*, k.min_ipk, k.max_penghasilan,
           (SELECT COUNT(*) FROM rekomendasi_beasiswa WHERE id_beasiswa=b.id_beasiswa AND status='direkomendasikan') as total_rekomen
    FROM beasiswa b LEFT JOIN kriteria k ON b.id_beasiswa=k.id_beasiswa
    ORDER BY b.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Beasiswa - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_admin.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>🏆 Kelola Beasiswa</h1>
        <p>Tambah, edit, dan hapus data beasiswa</p>
      </div>
      <div class="topbar-right">
        <a href="?action=tambah" class="btn btn-primary">➕ Tambah Beasiswa</a>
      </div>
    </div>
    <div class="page-content">

      <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= $error ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>

      <?php if ($action === 'tambah' || $action === 'edit'): ?>
      <!-- FORM TAMBAH/EDIT -->
      <div class="card" style="max-width:700px">
        <div class="card-header">
          <h3><?= $action==='edit' ? '✏️ Edit Beasiswa' : '➕ Tambah Beasiswa Baru' ?></h3>
          <a href="beasiswa_admin.php" class="btn btn-outline btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="beasiswa_id" value="<?= $edit_data['id_beasiswa'] ?? 0 ?>">
            <div class="form-group">
              <label class="form-label">Nama Beasiswa *</label>
              <input type="text" name="nama_beasiswa" class="form-control" value="<?= htmlspecialchars($edit_data['nama_beasiswa'] ?? '') ?>" required placeholder="Contoh: Beasiswa Prestasi Akademik">
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Penyelenggara *</label>
                <input type="text" name="penyelenggara" class="form-control" value="<?= htmlspecialchars($edit_data['penyelenggara'] ?? '') ?>" required placeholder="Contoh: Universitas">
              </div>
              <div class="form-group">
                <label class="form-label">Kuota Penerima *</label>
                <input type="number" name="kuota" class="form-control" value="<?= $edit_data['kuota'] ?? 0 ?>" min="0" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Nominal (Rp/bulan) *</label>
              <input type="number" name="nominal" class="form-control" value="<?= $edit_data['nominal'] ?? 0 ?>" min="0" required>
            </div>
            <div class="form-group">
              <label class="form-label">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan tentang beasiswa ini..."><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:8px">
              <div style="font-weight:700;font-size:13px;color:var(--text-mid);margin-bottom:14px">📋 KRITERIA SELEKSI</div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">IPK Minimal (0 = tidak ada syarat)</label>
                  <input type="number" name="min_ipk" class="form-control" value="<?= $edit_data['min_ipk'] ?? 0 ?>" min="0" max="4" step="0.01">
                </div>
                <div class="form-group">
                  <label class="form-label">Max Penghasilan Ortu (0 = tidak ada syarat)</label>
                  <input type="number" name="max_penghasilan" class="form-control" value="<?= $edit_data['max_penghasilan'] ?? 0 ?>" min="0">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Min Tanggungan (0 = tidak ada syarat)</label>
                  <input type="number" name="min_tanggungan" class="form-control" value="<?= $edit_data['min_tanggungan'] ?? 0 ?>" min="0">
                </div>
                <div class="form-group">
                  <label class="form-label">Syarat Prestasi</label>
                  <select name="syarat_prestasi" class="form-control">
                    <option value="tidak" <?= ($edit_data['syarat_prestasi']??'tidak')==='tidak'?'selected':'' ?>>Tidak Wajib</option>
                    <option value="ya" <?= ($edit_data['syarat_prestasi']??'')==='ya'?'selected':'' ?>>Wajib Ada Prestasi</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Syarat Akademik / Tambahan</label>
                <textarea name="syarat_akademik" class="form-control" rows="2" placeholder="Syarat tambahan lainnya..."><?= htmlspecialchars($edit_data['syarat_akademik'] ?? '') ?></textarea>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Wajib Aktif Organisasi</label>
                  <select name="syarat_organisasi" class="form-control">
                    <option value="tidak" <?= ($edit_data['syarat_organisasi']??'tidak')==='tidak'?'selected':'' ?>>Tidak Wajib</option>
                    <option value="ya" <?= ($edit_data['syarat_organisasi']??'')==='ya'?'selected':'' ?>>Wajib Aktif Organisasi</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Min Skor TOEFL (0 = tidak ada)</label>
                  <input type="number" name="min_toefl" class="form-control" value="<?= $edit_data['min_toefl'] ?? 0 ?>" min="0" max="677" placeholder="Contoh: 500">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Min Skor IELTS (0 = tidak ada)</label>
                  <input type="number" name="min_ielts" class="form-control" value="<?= $edit_data['min_ielts'] ?? 0 ?>" min="0" max="9" step="0.5" placeholder="Contoh: 6.0">
                  <div style="font-size:11px;color:var(--text-light);margin-top:4px">Jika ada syarat TOEFL atau IELTS, mahasiswa cukup memenuhi salah satu.</div>
                </div>
                <div class="form-group"></div>
              </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px">
              <button type="submit" class="btn btn-primary">💾 <?= $action==='edit' ? 'Simpan Perubahan' : 'Tambah Beasiswa' ?></button>
              <a href="beasiswa_admin.php" class="btn btn-outline">Batal</a>
            </div>
          </form>
        </div>
      </div>

      <?php else: ?>
      <!-- LIST BEASISWA -->
      <div class="card">
        <div class="card-body" style="padding:0">
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama Beasiswa</th>
                  <th>Penyelenggara</th>
                  <th>Nominal</th>
                  <th>Kuota</th>
                  <th>Min IPK</th>
                  <th>Penerima</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; while ($b = $beasiswas->fetch_assoc()): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><strong><?= htmlspecialchars($b['nama_beasiswa']) ?></strong></td>
                  <td><?= htmlspecialchars($b['penyelenggara']) ?></td>
                  <td style="color:var(--accent2);font-weight:600"><?= formatRupiah($b['nominal']) ?></td>
                  <td><?= $b['kuota'] ?></td>
                  <td><?= $b['min_ipk'] > 0 ? number_format($b['min_ipk'],2) : '<span class="badge badge-gray">-</span>' ?></td>
                  <td><span class="badge badge-blue"><?= $b['total_rekomen'] ?> mhs</span></td>
                  <td>
                    <div style="display:flex;gap:6px">
                      <a href="?action=edit&id=<?= $b['id_beasiswa'] ?>" class="btn btn-outline btn-xs">✏️ Edit</a>
                      <a href="?action=hapus&id=<?= $b['id_beasiswa'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Yakin hapus beasiswa ini?')">🗑️</a>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>