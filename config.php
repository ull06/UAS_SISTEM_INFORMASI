<?php
// ============================================
// KONFIGURASI DATABASE
// File: includes/config.php
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_beasiswa');
define('SITE_NAME', 'Sistem Rekomendasi Beasiswa');
define('SITE_URL', 'http://localhost/beasiswa');

// Koneksi Database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;background:#fff5f5;color:#e53e3e;border:1px solid #fc8181;border-radius:8px;margin:40px auto;max-width:500px;">
        <h2>❌ Koneksi Database Gagal</h2>
        <p>Pastikan XAMPP MySQL sudah berjalan dan database <strong>db_beasiswa</strong> sudah diimport.</p>
        <p><small>Error: ' . $conn->connect_error . '</small></p>
    </div>');
}

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($conn, $data) {
    return $conn->real_escape_string(htmlspecialchars(strip_tags(trim($data))));
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function isLoggedIn($type = 'mahasiswa') {
    return isset($_SESSION[$type . '_id']);
}

session_start();
?>