<?php
require_once 'config.php';
if (!isLoggedIn('mahasiswa')) redirect('index.php');

$mhs_id = $_SESSION['mahasiswa_id'];
$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($conn, $_POST['nama']);
    $jurusan = sanitize($conn, $_POST['jurusan']);
    $angkatan = (int)$_POST['angkatan'];
    $ipk = (float)$_POST['ipk'];
    $penghasilan = (int)str_replace(['Rp', '.', ',', ' '], '', $_POST['penghasilan_orangtua']);
    $tanggungan = (int)$_POST['jumlah_tanggungan'];
    $prestasi = sanitize($conn, $_POST['prestasi']);
    $aktif_organisasi = in_array($_POST['aktif_organisasi'] ?? 'tidak', ['ya','tidak']) ? $_POST['aktif_organisasi'] : 'tidak';
    $skor_toefl = (int)($_POST['skor_toefl'] ?? 0);
    $skor_ielts = (float)($_POST['skor_ielts'] ?? 0);

    // Update password if provided
    $pass_sql = '';
    if (!empty($_POST['password_baru'])) {
        $pass_hash = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
        $pass_sql = ", password_mhs='$pass_hash'";
    }

    $sql = "UPDATE mahasiswa SET nama='$nama', jurusan='$jurusan', angkatan=$angkatan, ipk=$ipk, 
            penghasilan_orangtua=$penghasilan, jumlah_tanggungan=$tanggungan, prestasi='$prestasi', aktif_organisasi='$aktif_organisasi', skor_toefl=$skor_toefl, skor_ielts=$skor_ielts$pass_sql
            WHERE id_mahasiswa=$mhs_id";

    if ($conn->query($sql)) {
        $_SESSION['mahasiswa_nama'] = $nama;
        // Run rekomendasi engine
        include 'rekomendasi_engine.php';
        runRekomendasi($conn, $mhs_id);
        $success = 'Profil berhasil diperbarui! Rekomendasi beasiswa juga telah diperbarui.';
    } else {
        $error = 'Gagal memperbarui profil.';
    }
}

$mhs = $conn->query("SELECT * FROM mahasiswa WHERE id_mahasiswa=$mhs_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrapper">
  <?php include 'sidebar_mhs.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h1>👤 Profil Saya</h1>
        <p>Isi & perbarui data diri untuk mendapatkan rekomendasi terbaik</p>
      </div>
    </div>
    <div class="page-content">

      <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= $error ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>

      <div class="grid-2" style="gap:20px;align-items:start">

        <div class="card">
          <div class="card-header"><h3>📋 Data Akademik & Pribadi</h3></div>
          <div class="card-body">
            <form method="POST">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">NIM</label>
                  <input type="text" class="form-control" value="<?= htmlspecialchars($mhs['nim']) ?>" disabled style="background:#f8fafc">
                </div>
                <div class="form-group">
                  <label class="form-label">Angkatan</label>
                  <input type="number" name="angkatan" class="form-control" value="<?= $mhs['angkatan'] ?>" min="2000" max="2030" required>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($mhs['nama']) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label">Jurusan</label>
                <select name="jurusan" class="form-control" required>
                  <?php
                  $jurusans = ['Teknik Informatika','Sistem Informasi','Teknik Elektro','Teknik Sipil','Manajemen','Akuntansi','Hukum','Kedokteran','Farmasi','Ekonomi'];
                  foreach ($jurusans as $j): ?>
                  <option <?= $mhs['jurusan']===$j?'selected':'' ?>><?= $j ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">IPK (0.00 - 4.00)</label>
                  <input type="number" name="ipk" class="form-control" value="<?= $mhs['ipk'] ?>" min="0" max="4" step="0.01" required>
                </div>
                <div class="form-group">
                  <label class="form-label">Jumlah Tanggungan Keluarga</label>
                  <input type="number" name="jumlah_tanggungan" class="form-control" value="<?= $mhs['jumlah_tanggungan'] ?>" min="0" max="20" required>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Penghasilan Orang Tua (Rp/bulan)</label>
                <input type="number" name="penghasilan_orangtua" class="form-control" value="<?= $mhs['penghasilan_orangtua'] ?>" min="0" placeholder="Contoh: 3000000" required>
                <div style="font-size:11px;color:var(--text-light);margin-top:4px">Isi dengan angka saja, contoh: 3000000 (untuk Rp 3.000.000)</div>
              </div>
              <div class="form-group">
                <label class="form-label">Prestasi / Penghargaan</label>
                <textarea name="prestasi" class="form-control" rows="3" placeholder="Contoh: Juara 1 olimpiade komputer, Hafidz 10 juz, dll. Kosongkan jika tidak ada."><?= htmlspecialchars($mhs['prestasi']) ?></textarea>
              </div>

              <!-- Field Baru: Organisasi + TOEFL/IELTS -->
              <div class="form-group">
                <label class="form-label">Aktif Organisasi</label>
                <select name="aktif_organisasi" class="form-control">
                  <option value="tidak" <?= ($mhs['aktif_organisasi']??'tidak')==='tidak'?'selected':'' ?>>Tidak Aktif</option>
                  <option value="ya" <?= ($mhs['aktif_organisasi']??'')=== 'ya'?'selected':'' ?>>Aktif Organisasi</option>
                </select>
                <div style="font-size:11px;color:var(--text-light);margin-top:4px">Beberapa beasiswa mewajibkan aktif di organisasi kampus atau kemasyarakatan.</div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Skor TOEFL (0 jika tidak ada)</label>
                  <input type="number" name="skor_toefl" class="form-control" value="<?= $mhs['skor_toefl'] ?? 0 ?>" min="0" max="677" placeholder="Contoh: 500">
                  <div style="font-size:11px;color:var(--text-light);margin-top:4px">Skor TOEFL ITP/iBT. Isi 0 jika belum punya sertifikat.</div>
                </div>
                <div class="form-group">
                  <label class="form-label">Skor IELTS (0 jika tidak ada)</label>
                  <input type="number" name="skor_ielts" class="form-control" value="<?= $mhs['skor_ielts'] ?? 0 ?>" min="0" max="9" step="0.5" placeholder="Contoh: 6.0">
                  <div style="font-size:11px;color:var(--text-light);margin-top:4px">Skor IELTS 0.0 - 9.0. Isi 0 jika belum punya sertifikat.</div>
                </div>
              </div>

              <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:4px">
                <div style="font-size:13px;font-weight:600;color:var(--text-mid);margin-bottom:12px">🔑 Ganti Password (Opsional)</div>
                <div class="form-row">
                  <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak ganti">
                  </div>
                </div>
              </div>

              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:16px;padding:11px">
                💾 Simpan & Perbarui Rekomendasi
              </button>
            </form>
          </div>
        </div>

        <!-- Panduan -->
        <div>
          <div class="card" style="margin-bottom:16px">
            <div class="card-header"><h3>📌 Panduan Pengisian</h3></div>
            <div class="card-body" style="font-size:13px;line-height:1.8;color:var(--text-mid)">
              <div style="margin-bottom:10px">
                <strong style="color:var(--text-dark)">IPK</strong><br>
                Masukkan IPK terakhir Anda. Semakin tinggi IPK, semakin banyak beasiswa yang bisa direkomendasikan.
              </div>
              <div style="margin-bottom:10px">
                <strong style="color:var(--text-dark)">Penghasilan Orang Tua</strong><br>
                Penghasilan total kedua orang tua per bulan. Data ini digunakan untuk beasiswa berbasis ekonomi.
              </div>
              <div style="margin-bottom:10px">
                <strong style="color:var(--text-dark)">Jumlah Tanggungan</strong><br>
                Jumlah anggota keluarga yang ditanggung (termasuk Anda sendiri).
              </div>
              <div style="margin-bottom:10px">
                <strong style="color:var(--text-dark)">Prestasi</strong><br>
                Isi dengan prestasi akademik atau non-akademik. Beberapa beasiswa mensyaratkan prestasi tertentu.
              </div>
              <div style="margin-bottom:10px">
                <strong style="color:var(--text-dark)">Aktif Organisasi</strong><br>
                Pilih "Aktif" jika kamu tergabung dalam organisasi kampus (BEM, UKM, Himpunan, dll). Beberapa beasiswa seperti Djarum, LPDP, dan Pertamina mewajibkan ini.
              </div>
              <div>
                <strong style="color:var(--text-dark)">TOEFL / IELTS</strong><br>
                Isi skor jika kamu memiliki sertifikat. Beasiswa LPDP, Telkom, dan Unggulan mensyaratkan skor tertentu. Isi 0 jika belum punya.
              </div>
            </div>
          </div>

          <div style="background:linear-gradient(135deg,#065f46,#059669);border-radius:var(--radius);padding:20px;color:white">
            <div style="font-size:18px;margin-bottom:8px">🤖 Mesin Rekomendasi</div>
            <div style="font-size:13px;opacity:0.9;line-height:1.7">
              Setelah Anda menyimpan profil, sistem akan otomatis mencocokkan data Anda dengan kriteria setiap beasiswa dan menghasilkan daftar rekomendasi yang dipersonalisasi.
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
</body>
</html>