-- ============================================
-- SISTEM REKOMENDASI BEASISWA - DATABASE
-- Import via phpMyAdmin atau MySQL CLI
-- ============================================

CREATE DATABASE IF NOT EXISTS db_beasiswa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_beasiswa;

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username_admin VARCHAR(50) NOT NULL UNIQUE,
    password_admin VARCHAR(255) NOT NULL,
    nama_admin VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id_mahasiswa INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100),
    angkatan YEAR,
    ipk DECIMAL(3,2) DEFAULT 0.00,
    penghasilan_orangtua DECIMAL(15,2) DEFAULT 0,
    jumlah_tanggungan INT DEFAULT 0,
    prestasi TEXT,
    aktif_organisasi ENUM('ya','tidak') DEFAULT 'tidak',
    skor_toefl INT DEFAULT 0,
    skor_ielts DECIMAL(3,1) DEFAULT 0.0,
    username_mhs VARCHAR(50) NOT NULL UNIQUE,
    password_mhs VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Beasiswa
CREATE TABLE IF NOT EXISTS beasiswa (
    id_beasiswa INT AUTO_INCREMENT PRIMARY KEY,
    nama_beasiswa VARCHAR(150) NOT NULL,
    penyelenggara VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    kuota INT DEFAULT 0,
    nominal DECIMAL(15,2) DEFAULT 0,
    id_admin INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin)
);

-- Tabel Kriteria (dengan kolom baru)
CREATE TABLE IF NOT EXISTS kriteria (
    id_kriteria INT AUTO_INCREMENT PRIMARY KEY,
    id_beasiswa INT NOT NULL,
    min_ipk DECIMAL(3,2) DEFAULT 0.00,
    max_penghasilan DECIMAL(15,2) DEFAULT 0,
    min_tanggungan INT DEFAULT 0,
    syarat_prestasi ENUM('ya','tidak') DEFAULT 'tidak',
    syarat_akademik TEXT,
    syarat_organisasi ENUM('ya','tidak') DEFAULT 'tidak',
    min_toefl INT DEFAULT 0,
    min_ielts DECIMAL(3,1) DEFAULT 0.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_beasiswa) REFERENCES beasiswa(id_beasiswa) ON DELETE CASCADE
);

-- Tabel Rekomendasi
CREATE TABLE IF NOT EXISTS rekomendasi_beasiswa (
    id_rekomendasi INT AUTO_INCREMENT PRIMARY KEY,
    id_mahasiswa INT NOT NULL,
    id_beasiswa INT NOT NULL,
    skor_kecocokan DECIMAL(5,2) DEFAULT 0,
    status ENUM('direkomendasikan','tidak_direkomendasikan') DEFAULT 'direkomendasikan',
    tanggal_rekomendasi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mahasiswa) REFERENCES mahasiswa(id_mahasiswa) ON DELETE CASCADE,
    FOREIGN KEY (id_beasiswa) REFERENCES beasiswa(id_beasiswa) ON DELETE CASCADE
);

-- ============================================
-- DATA AWAL
-- ============================================

-- Admin default (password: admin123)
INSERT INTO admin (username_admin, password_admin, nama_admin) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator'),
('superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin');

-- ============================================
-- DATA BEASISWA (12 beasiswa)
-- ============================================
INSERT INTO beasiswa (nama_beasiswa, penyelenggara, deskripsi, kuota, nominal, id_admin) VALUES
('Beasiswa Bidikmisi / KIP-K', 'Kemdikbud', 'Beasiswa untuk mahasiswa dari keluarga kurang mampu yang berprestasi akademik', 50, 7500000, 1),
('Beasiswa Prestasi Akademik', 'Universitas', 'Beasiswa untuk mahasiswa dengan IPK tertinggi di jurusan', 20, 3000000, 1),
('Beasiswa Hafidz Quran', 'Lembaga Amil Zakat', 'Beasiswa khusus mahasiswa penghafal Al-Quran minimal 10 juz', 15, 2500000, 1),
('Beasiswa Bank BRI Peduli', 'Bank BRI', 'Beasiswa CSR Bank BRI untuk mahasiswa berprestasi dari keluarga tidak mampu', 30, 5000000, 1),
('Beasiswa Yayasan Pendidikan Aceh', 'Pemerintah Aceh', 'Beasiswa dari pemerintah daerah untuk putra-putri terbaik Aceh', 25, 4000000, 1),
('Beasiswa Djarum Foundation', 'Djarum Foundation', 'Beasiswa nasional untuk mahasiswa berprestasi dengan jiwa kepemimpinan tinggi', 10, 6000000, 1),
('Beasiswa LPDP Reguler', 'LPDP Kemenkeu', 'Beasiswa unggulan pemerintah Indonesia untuk studi lanjut dalam dan luar negeri', 200, 10000000, 1),
('Beasiswa Tanoto Foundation', 'Tanoto Foundation', 'Beasiswa untuk mahasiswa berprestasi dengan semangat pengabdian masyarakat', 40, 4500000, 1),
('Beasiswa Pertamina Sobat Bumi', 'PT Pertamina', 'Beasiswa CSR Pertamina untuk mahasiswa peduli lingkungan dan aktif organisasi', 35, 5500000, 1),
('Beasiswa Telkom Indonesia', 'PT Telkom Indonesia', 'Beasiswa untuk mahasiswa jurusan teknologi dan informatika berprestasi', 45, 4000000, 1),
('Beasiswa PPA (Peningkatan Prestasi Akademik)', 'Kemdikbud', 'Beasiswa bagi mahasiswa aktif yang menunjukkan peningkatan prestasi akademik', 60, 2400000, 1),
('Beasiswa Unggulan Masyarakat Berprestasi', 'Kemdikbud', 'Beasiswa untuk mahasiswa berprestasi internasional atau nasional', 30, 8000000, 1),
('Beasiswa Bank Mandiri', 'Bank Mandiri', 'Beasiswa CSR Bank Mandiri untuk mahasiswa berprestasi dari keluarga tidak mampu di seluruh Indonesia', 40, 4000000, 1),
('Beasiswa Supersemar', 'Yayasan Supersemar', 'Beasiswa tertua di Indonesia, diberikan kepada mahasiswa berprestasi dari keluarga kurang mampu', 100, 2000000, 1),
('Beasiswa Toyota Astra', 'PT Toyota Astra Motor', 'Beasiswa untuk mahasiswa jurusan teknik dengan prestasi akademik tinggi dan jiwa inovatif', 25, 5000000, 1),
('Beasiswa Sampoerna Foundation', 'Sampoerna Foundation', 'Beasiswa berbasis kebutuhan dan prestasi untuk mahasiswa S1 aktif di seluruh Indonesia', 50, 6000000, 1),
('Beasiswa Indonesia Bangkit', 'Kemenag RI', 'Beasiswa dari Kementerian Agama untuk mahasiswa di perguruan tinggi keagamaan Islam negeri', 80, 3500000, 1),
('Beasiswa Yayasan Orbit', 'Yayasan Orbit', 'Beasiswa untuk mahasiswa muda berbakat yang memiliki proyek sosial berdampak nyata', 20, 4500000, 1),
('Beasiswa BCA Finance', 'PT BCA Finance', 'Beasiswa untuk mahasiswa berprestasi jurusan ekonomi, manajemen, dan akuntansi', 30, 3000000, 1),
('Beasiswa Pelni Peduli', 'PT PELNI', 'Beasiswa untuk putra-putri karyawan PELNI dan masyarakat pesisir berprestasi', 15, 3500000, 1),
('Beasiswa Garuda Indonesia', 'PT Garuda Indonesia', 'Beasiswa untuk mahasiswa penerbangan, teknik, dan manajemen transportasi berprestasi', 20, 5500000, 1),
('Beasiswa Baznas Pusat', 'BAZNAS RI', 'Beasiswa berbasis zakat untuk mahasiswa dari keluarga tidak mampu dan yatim berprestasi', 60, 3000000, 1),
('Beasiswa Smartfren Kreatif', 'PT Smartfren Telecom', 'Beasiswa untuk mahasiswa kreatif di bidang digital, desain, dan teknologi komunikasi', 35, 3500000, 1),
('Beasiswa Unilever Indonesia', 'PT Unilever Indonesia', 'Beasiswa untuk mahasiswa jurusan kimia, farmasi, dan teknik industri dengan prestasi unggul', 20, 4000000, 1),
('Beasiswa Lazismu', 'Lembaga Amil Zakat Muhammadiyah', 'Beasiswa dari lembaga zakat Muhammadiyah untuk mahasiswa dhuafa berprestasi', 45, 2500000, 1),
('Beasiswa PLN Peduli', 'PT PLN (Persero)', 'Beasiswa CSR PLN untuk mahasiswa jurusan teknik elektro, mesin, dan energi terbarukan', 30, 4500000, 1),
('Beasiswa Medco Foundation', 'Medco Foundation', 'Beasiswa untuk mahasiswa putra daerah penghasil minyak dan gas yang berprestasi', 25, 5000000, 1),
('Beasiswa Indofood Riset Nugraha', 'PT Indofood Sukses Makmur', 'Beasiswa khusus untuk mahasiswa S1 yang sedang menyusun skripsi atau tugas akhir', 40, 6000000, 1),
('Beasiswa Pendidikan Terbaik Aceh', 'Dinas Pendidikan Aceh', 'Beasiswa khusus putra-putri Aceh berprestasi untuk melanjutkan pendidikan di perguruan tinggi unggulan', 30, 4000000, 1),
('Beasiswa Chevron Pacific Indonesia', 'PT Chevron Pacific Indonesia', 'Beasiswa untuk mahasiswa jurusan teknik dan sains dari daerah operasi Chevron yang berprestasi', 20, 6000000, 1);

-- ============================================
-- KRITERIA (syarat_organisasi, min_toefl, min_ielts)
-- ============================================
INSERT INTO kriteria (id_beasiswa, min_ipk, max_penghasilan, min_tanggungan, syarat_prestasi, syarat_akademik, syarat_organisasi, min_toefl, min_ielts) VALUES
-- 1. KIP-K
(1, 3.00, 4000000, 2, 'tidak', 'Tidak sedang menerima beasiswa lain, lampirkan SKTM', 'tidak', 0, 0.0),
-- 2. Prestasi Akademik
(2, 3.50, 0, 0, 'tidak', 'IPK minimal 3.50, tidak pernah mendapat nilai D atau E', 'tidak', 0, 0.0),
-- 3. Hafidz Quran
(3, 2.75, 5000000, 1, 'ya', 'Hafidz minimal 10 juz, lampirkan sertifikat tahfidz', 'tidak', 0, 0.0),
-- 4. BRI Peduli
(4, 3.00, 3500000, 2, 'tidak', 'KTP domisili Indonesia, surat keterangan tidak mampu', 'tidak', 0, 0.0),
-- 5. Yayasan Aceh
(5, 2.80, 4500000, 1, 'tidak', 'Warga asli Aceh, KTP Aceh, semester 2-7', 'tidak', 0, 0.0),
-- 6. Djarum Foundation (wajib organisasi)
(6, 3.20, 0, 0, 'ya', 'Aktif organisasi, leadership, semester 3-7', 'ya', 0, 0.0),
-- 7. LPDP (wajib TOEFL/IELTS + organisasi)
(7, 3.25, 0, 0, 'ya', 'Tidak sedang studi lanjut, usia max 35 tahun, rencana studi jelas', 'ya', 500, 6.0),
-- 8. Tanoto Foundation (wajib organisasi)
(8, 3.00, 6000000, 1, 'ya', 'Semester 3-6, berkomitmen membangun komunitas', 'ya', 0, 0.0),
-- 9. Pertamina Sobat Bumi (wajib organisasi)
(9, 3.00, 5000000, 0, 'tidak', 'Aktif kegiatan kepedulian lingkungan, proposal kegiatan', 'ya', 0, 0.0),
-- 10. Telkom (TOEFL diutamakan)
(10, 3.10, 0, 0, 'tidak', 'Jurusan TI/Informatika/Elektro, portofolio proyek teknologi', 'tidak', 450, 5.5),
-- 11. PPA
(11, 3.00, 4500000, 0, 'tidak', 'Mahasiswa aktif semester 2-7, tidak sedang cuti', 'tidak', 0, 0.0),
-- 12. Unggulan Masyarakat Berprestasi (wajib TOEFL + organisasi)
(12, 3.25, 0, 0, 'ya', 'Pernah juara kompetisi nasional/internasional, esai motivasi', 'ya', 475, 5.5),
-- 13. Bank Mandiri
(13, 3.00, 4000000, 2, 'tidak', 'KTP Indonesia, surat keterangan tidak mampu, semester 2-7', 'tidak', 0, 0.0),
-- 14. Supersemar
(14, 2.75, 3500000, 2, 'tidak', 'Mahasiswa aktif S1, tidak sedang menerima beasiswa lain', 'tidak', 0, 0.0),
-- 15. Toyota Astra
(15, 3.20, 0, 0, 'tidak', 'Jurusan teknik mesin/industri/elektro, portofolio inovasi', 'tidak', 0, 0.0),
-- 16. Sampoerna Foundation
(16, 3.00, 5000000, 1, 'ya', 'Esai motivasi, surat rekomendasi dosen, tidak sedang cuti', 'ya', 450, 5.5),
-- 17. Indonesia Bangkit (Kemenag)
(17, 2.80, 4000000, 1, 'tidak', 'Terdaftar di PTKIN/PTKIS, aktif semester 2-7', 'tidak', 0, 0.0),
-- 18. Yayasan Orbit
(18, 3.00, 0, 0, 'ya', 'Memiliki proposal proyek sosial, presentasi di hadapan panel', 'ya', 0, 0.0),
-- 19. BCA Finance
(19, 3.10, 0, 0, 'tidak', 'Jurusan ekonomi/manajemen/akuntansi, semester 3-6', 'tidak', 0, 0.0),
-- 20. Pelni Peduli
(20, 2.80, 4000000, 2, 'tidak', 'Diutamakan putra daerah kepulauan/pesisir, KTP valid', 'tidak', 0, 0.0),
-- 21. Garuda Indonesia
(21, 3.20, 0, 0, 'tidak', 'Jurusan penerbangan/teknik/manajemen transportasi, bukan semester akhir', 'tidak', 450, 5.5),
-- 22. Baznas Pusat
(22, 2.75, 3000000, 2, 'tidak', 'Surat keterangan tidak mampu, yatim/dhuafa diutamakan', 'tidak', 0, 0.0),
-- 23. Smartfren Kreatif
(23, 3.00, 0, 0, 'ya', 'Portofolio karya digital/desain, proposal kreatif', 'tidak', 0, 0.0),
-- 24. Unilever Indonesia
(24, 3.20, 0, 0, 'tidak', 'Jurusan kimia/farmasi/teknik industri, nilai mata kuliah inti minimal B', 'tidak', 0, 0.0),
-- 25. Lazismu
(25, 2.75, 3000000, 2, 'tidak', 'Mahasiswa dhuafa aktif, rekomendasi dari pengurus Muhammadiyah setempat', 'tidak', 0, 0.0),
-- 26. PLN Peduli
(26, 3.10, 0, 0, 'tidak', 'Jurusan teknik elektro/mesin/energi, bukan penerima beasiswa PLN sebelumnya', 'tidak', 0, 0.0),
-- 27. Medco Foundation
(27, 3.00, 5000000, 1, 'tidak', 'Berasal dari daerah operasi Medco, surat rekomendasi kepala daerah', 'tidak', 0, 0.0),
-- 28. Indofood Riset Nugraha
(28, 3.25, 0, 0, 'tidak', 'Sedang menyusun skripsi/TA bidang pangan/gizi/teknologi, proposal riset', 'tidak', 0, 0.0),
-- 29. Pendidikan Terbaik Aceh
(29, 3.00, 4500000, 1, 'tidak', 'Warga Aceh ber-KTP Aceh, semester 2-6, tidak sedang menerima beasiswa daerah lain', 'tidak', 0, 0.0),
-- 30. Chevron Pacific Indonesia
(30, 3.20, 0, 0, 'tidak', 'Jurusan teknik/sains, dari daerah operasi Chevron, CV dan transkrip nilai', 'tidak', 450, 5.5);

-- ============================================
-- MAHASISWA CONTOH (password: mhs123)
-- ============================================
INSERT INTO mahasiswa (nim, nama, jurusan, angkatan, ipk, penghasilan_orangtua, jumlah_tanggungan, prestasi, aktif_organisasi, skor_toefl, skor_ielts, username_mhs, password_mhs) VALUES
('2101001', 'Ahmad Rizki', 'Teknik Informatika', 2021, 3.65, 2500000, 3, 'Juara 1 Olimpiade Komputer Nasional 2023', 'ya', 520, 6.5, 'ahmad', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('2101002', 'Siti Aisyah', 'Sistem Informasi', 2021, 3.82, 1800000, 4, 'Hafidz 15 Juz, Juara 2 MTQ Provinsi', 'ya', 490, 6.0, 'siti', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('2201003', 'Budi Santoso', 'Teknik Elektro', 2022, 2.90, 3200000, 2, '', 'tidak', 0, 0.0, 'budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
