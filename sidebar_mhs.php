<?php
// sidebar_mhs.php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🎓</div>
    <h2>Sistem Rekomendasi Beasiswa</h2>
    <p>Portal Mahasiswa</p>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar"><?= strtoupper(substr($_SESSION['mahasiswa_nama'], 0, 1)) ?></div>
    <div class="user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['mahasiswa_nama']) ?></div>
      <div class="role"><?= $_SESSION['mahasiswa_nim'] ?></div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Menu Utama</div>
    <a href="dashboard.php" class="nav-item <?= $current==='dashboard.php'?'active':'' ?>">
      <span class="nav-icon">📊</span> <span>Dashboard</span>
    </a>
    <a href="profil.php" class="nav-item <?= $current==='profil.php'?'active':'' ?>">
      <span class="nav-icon">👤</span> <span>Profil Saya</span>
    </a>
    <a href="rekomendasi.php" class="nav-item <?= $current==='rekomendasi.php'?'active':'' ?>">
      <span class="nav-icon">⭐</span> <span>Rekomendasi Beasiswa</span>
    </a>
    <a href="beasiswa.php" class="nav-item <?= $current==='beasiswa.php'?'active':'' ?>">
      <span class="nav-icon">🏆</span> <span>Semua Beasiswa</span>
    </a>
  </nav>
  <div class="sidebar-bottom">
    <a href="logout.php" class="logout-btn">
      <span class="nav-icon">🚪</span> <span>Logout</span>
    </a>
  </div>
</aside>