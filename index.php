<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn('mahasiswa')) redirect('dashboard.php');
if (isLoggedIn('admin')) redirect('dashboard_admin.php');

$error = '';
$success = '';
$tab = $_GET['tab'] ?? 'mahasiswa'; // mahasiswa or admin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'mahasiswa';

    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username_admin = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password_admin'])) {
            $_SESSION['admin_id'] = $user['id_admin'];
            $_SESSION['admin_nama'] = $user['nama_admin'];
            $_SESSION['admin_username'] = $user['username_admin'];
            redirect('dashboard_admin.php');
        } else {
            $error = 'Username atau password admin salah!';
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE username_mhs = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password_mhs'])) {
            $_SESSION['mahasiswa_id'] = $user['id_mahasiswa'];
            $_SESSION['mahasiswa_nama'] = $user['nama'];
            $_SESSION['mahasiswa_nim'] = $user['nim'];
            redirect('dashboard.php');
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sistem Rekomendasi Beasiswa</title>
<link rel="stylesheet" href="style.css">
<style>
.divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; color: var(--text-light); font-size: 12px; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
.info-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px 16px; font-size: 12px; color: #1e40af; margin-top: 16px; }
.info-box strong { display: block; margin-bottom: 6px; }
.info-box ul { padding-left: 16px; }
.info-box li { margin-bottom: 2px; }
</style>
</head>
<body>
<div class="auth-bg">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="icon">🎓</div>
      <h1>Sistem Rekomendasi Beasiswa</h1>
      <p>Masuk untuk melihat rekomendasi beasiswa Anda</p>
    </div>

    <!-- Tab -->
    <div class="auth-tabs">
      <a href="?tab=mahasiswa" class="auth-tab <?= $tab==='mahasiswa'?'active':'' ?>">👤 Mahasiswa</a>
      <a href="?tab=admin" class="auth-tab <?= $tab==='admin'?'active':'' ?>">🔐 Admin</a>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="hidden" name="role" value="<?= $tab ?>">
      <div class="form-group">
        <label class="form-label">Username <?= $tab==='admin' ? 'Admin' : 'Mahasiswa' ?></label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        🚀 Masuk
      </button>
    </form>

    <?php if ($tab === 'mahasiswa'): ?>
    <div class="auth-footer">
      Belum punya akun? <a href="register.php">Daftar Sekarang</a>
    </div>
    <?php endif; ?>

    <!-- <div class="info-box">
      <strong>🔑 Akun Demo:</strong>
      <ul>
        <?php if ($tab === 'mahasiswa'): ?>
        <li><strong>Username:</strong> ahmad / siti / budi</li>
        <li><strong>Password:</strong> mhs123</li>
        <?php else: ?>
        <li><strong>Username:</strong> admin</li>
        <li><strong>Password:</strong> password</li>
        <?php endif; ?>
      </ul>
    </div> -->
  </div>
</div>
</body>
</html>