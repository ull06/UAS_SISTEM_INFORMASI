<?php
require_once 'config.php';
if (isLoggedIn('mahasiswa')) redirect('dashboard.php');
if (isLoggedIn('admin')) redirect('dashboard_admin.php');

$error = '';
$success = '';
$tab = $_GET['tab'] ?? 'mahasiswa';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'mahasiswa';

    if ($role === 'admin') {
        $nama_admin = sanitize($conn, $_POST['nama_admin']);
        $username = sanitize($conn, $_POST['username']);
        $password = $_POST['password'];
        $kode = $_POST['kode_akses'] ?? '';

        // Kode akses khusus admin (ganti sesuai kebutuhan)
        if ($kode !== 'ADMINBEASISWA') {
            $error = 'Kode akses admin salah!';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter!';
        } else {
            $check = $conn->query("SELECT id_admin FROM admin WHERE username_admin='$username'");
            if ($check->num_rows > 0) {
                $error = 'Username admin sudah digunakan!';
            } else {
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO admin (username_admin, password_admin, nama_admin) VALUES (?,?,?)");
                $stmt->bind_param("sss", $username, $pass_hash, $nama_admin);
                if ($stmt->execute()) {
                    $success = 'Akun admin berhasil dibuat! Silakan login.';
                } else {
                    $error = 'Terjadi kesalahan, coba lagi.';
                }
            }
        }
    } else {
        $nim = sanitize($conn, $_POST['nim']);
        $nama = sanitize($conn, $_POST['nama']);
        $jurusan = sanitize($conn, $_POST['jurusan']);
        $angkatan = (int)$_POST['angkatan'];
        $username = sanitize($conn, $_POST['username']);
        $password = $_POST['password'];

        if (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter!';
        } else {
            $check = $conn->query("SELECT id_mahasiswa FROM mahasiswa WHERE nim='$nim' OR username_mhs='$username'");
            if ($check->num_rows > 0) {
                $error = 'NIM atau username sudah terdaftar!';
            } else {
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, username_mhs, password_mhs) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param("sssiss", $nim, $nama, $jurusan, $angkatan, $username, $pass_hash);
                if ($stmt->execute()) {
                    $success = 'Akun berhasil dibuat! Silakan login.';
                } else {
                    $error = 'Terjadi kesalahan, coba lagi.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun - Sistem Rekomendasi Beasiswa</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-bg">
  <div class="auth-card" style="max-width:480px">
    <div class="auth-logo">
      <div class="icon">🎓</div>
      <h1>Daftar Akun</h1>
      <p>Buat akun baru untuk menggunakan sistem</p>
    </div>

    <!-- Tab -->
    <div class="auth-tabs">
      <a href="?tab=mahasiswa" class="auth-tab <?= $tab==='mahasiswa'?'active':'' ?>">👤 Mahasiswa</a>
      <a href="?tab=admin" class="auth-tab <?= $tab==='admin'?'active':'' ?>">🔐 Admin</a>
    </div>

    <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?> <a href="index.php">Login →</a></div><?php endif; ?>

    <?php if ($tab === 'mahasiswa'): ?>
    <!-- FORM MAHASISWA -->
    <form method="POST">
      <input type="hidden" name="role" value="mahasiswa">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">NIM</label>
          <input type="text" name="nim" class="form-control" placeholder="Contoh: 2101001" required>
        </div>
        <div class="form-group">
          <label class="form-label">Angkatan</label>
          <input type="number" name="angkatan" class="form-control" placeholder="Contoh: 2021" min="2000" max="2030" required>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" placeholder="Nama lengkap Anda" required>
      </div>
      <div class="form-group">
        <label class="form-label">Jurusan</label>
        <select name="jurusan" class="form-control" required>
          <option value="">-- Pilih Jurusan --</option>
          <option>Teknik Informatika</option>
          <option>Sistem Informasi</option>
          <option>Teknik Elektro</option>
          <option>Teknik Sipil</option>
          <option>Manajemen</option>
          <option>Akuntansi</option>
          <option>Hukum</option>
          <option>Kedokteran</option>
          <option>Farmasi</option>
          <option>Ekonomi</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Username login" required>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" minlength="6" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        ✅ Buat Akun Mahasiswa
      </button>
    </form>

    <?php else: ?>
    <!-- FORM ADMIN -->
    <form method="POST">
      <input type="hidden" name="role" value="admin">
      <div class="form-group">
        <label class="form-label">Nama Admin</label>
        <input type="text" name="nama_admin" class="form-control" placeholder="Nama lengkap admin" required>
      </div>
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Username untuk login" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" minlength="6" required>
      </div>
      <div class="form-group">
        <label class="form-label">Kode Akses Admin</label>
        <input type="password" name="kode_akses" class="form-control" placeholder="Masukkan kode akses khusus" required>
        <div style="font-size:11px;color:var(--text-light);margin-top:4px">Kode akses diberikan oleh pengelola sistem.</div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        ✅ Buat Akun Admin
      </button>
    </form>
    <?php endif; ?>

    <div class="auth-footer" style="margin-top:16px">
      Sudah punya akun? <a href="index.php">Login Sekarang</a>
    </div>
  </div>
</div>
</body>
</html>