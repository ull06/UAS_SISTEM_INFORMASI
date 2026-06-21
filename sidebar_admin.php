<?php
// sidebar_admin.php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🎓</div>
    <h2>Sistem Rekomendasi Beasiswa</h2>
    <p>Panel Admin</p>
  </div>
  <div class="sidebar-user">
    <div class="user-avatar" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)"><?= strtoupper(substr($_SESSION['admin_nama'], 0, 1)) ?></div>
    <div class="user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['admin_nama']) ?></div>
      <div class="role">Administrator</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Menu Admin</div>
    <a href="dashboard_admin.php" class="nav-item <?= $current==='dashboard_admin.php'?'active':'' ?>">
      <span class="nav-icon">📊</span> <span>Dashboard</span>
    </a>
    <div class="nav-label">Kelola Data</div>
    <a href="beasiswa_admin.php" class="nav-item <?= $current==='beasiswa_admin.php'?'active':'' ?>">
      <span class="nav-icon">🏆</span> <span>Kelola Beasiswa</span>
    </a>
    <a href="mahasiswa.php" class="nav-item <?= $current==='mahasiswa.php'?'active':'' ?>">
      <span class="nav-icon">👥</span> <span>Data Mahasiswa</span>
    </a>
    <div class="nav-label">Laporan</div>
    <a href="statistik.php" class="nav-item <?= $current==='statistik.php'?'active':'' ?>">
      <span class="nav-icon">📈</span> <span>Lihat Statistik</span>
    </a>
    <a href="rekomendasi_admin.php" class="nav-item <?= $current==='rekomendasi_admin.php'?'active':'' ?>">
      <span class="nav-icon">⭐</span> <span>Rekap Rekomendasi</span>
    </a>
  </nav>
  <div class="sidebar-bottom">
    <a href="logout.php" class="logout-btn">
      <span class="nav-icon">🚪</span> <span>Logout</span>
    </a>
  </div>
</aside>