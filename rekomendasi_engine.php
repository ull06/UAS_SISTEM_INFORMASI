<?php
// rekomendasi_engine.php
// Mesin pencocok mahasiswa dengan kriteria beasiswa

function runRekomendasi($conn, $mhs_id) {
    $mhs = $conn->query("SELECT * FROM mahasiswa WHERE id_mahasiswa=$mhs_id")->fetch_assoc();
    if (!$mhs) return;

    // Hapus rekomendasi lama
    $conn->query("DELETE FROM rekomendasi_beasiswa WHERE id_mahasiswa=$mhs_id");

    // Get all beasiswa + kriteria (termasuk kolom baru)
    $beasiswas = $conn->query("
        SELECT b.*, k.min_ipk, k.max_penghasilan, k.min_tanggungan,
               k.syarat_prestasi, k.syarat_akademik,
               k.syarat_organisasi, k.min_toefl, k.min_ielts
        FROM beasiswa b
        LEFT JOIN kriteria k ON b.id_beasiswa = k.id_beasiswa
    ");

    while ($b = $beasiswas->fetch_assoc()) {
        $skor = 0;
        $max_skor = 0;
        $lolos = true; // flag untuk syarat wajib

        // === KRITERIA IPK (bobot 35) ===
        $max_skor += 35;
        if ($b['min_ipk'] > 0) {
            if ($mhs['ipk'] >= $b['min_ipk']) {
                $selisih = min(($mhs['ipk'] - $b['min_ipk']) / (4.0 - $b['min_ipk']), 1);
                $skor += 25 + ($selisih * 10);
            } else {
                $lolos = false; // IPK tidak memenuhi = gugur
            }
        } else {
            $skor += 30;
        }

        if (!$lolos) continue; // skip jika IPK tidak memenuhi

        // === KRITERIA PENGHASILAN (bobot 25) ===
        $max_skor += 25;
        if ($b['max_penghasilan'] > 0) {
            if ($mhs['penghasilan_orangtua'] <= $b['max_penghasilan']) {
                $ratio = 1 - ($mhs['penghasilan_orangtua'] / $b['max_penghasilan']);
                $skor += 15 + ($ratio * 10);
            }
        } else {
            $skor += 20;
        }

        // === KRITERIA TANGGUNGAN (bobot 10) ===
        $max_skor += 10;
        if ($b['min_tanggungan'] > 0) {
            if ($mhs['jumlah_tanggungan'] >= $b['min_tanggungan']) {
                $skor += 10;
            }
        } else {
            $skor += 8;
        }

        // === KRITERIA PRESTASI (bobot 10) ===
        $max_skor += 10;
        if ($b['syarat_prestasi'] === 'ya') {
            if (!empty($mhs['prestasi'])) {
                $skor += 10;
            }
        } else {
            $skor += 7;
        }

        // === KRITERIA ORGANISASI (bobot 10) ===
        $max_skor += 10;
        if ($b['syarat_organisasi'] === 'ya') {
            if ($mhs['aktif_organisasi'] === 'ya') {
                $skor += 10;
            }
            // jika tidak aktif organisasi tapi wajib, kurangi skor signifikan
        } else {
            if ($mhs['aktif_organisasi'] === 'ya') {
                $skor += 8; // bonus kecil jika aktif walau tidak wajib
            } else {
                $skor += 6;
            }
        }

        // === KRITERIA TOEFL / IELTS (bobot 10) ===
        $max_skor += 10;
        $punya_toefl = $mhs['skor_toefl'] > 0;
        $punya_ielts = $mhs['skor_ielts'] > 0;

        if ($b['min_toefl'] > 0 || $b['min_ielts'] > 0) {
            $lolos_toefl = $punya_toefl && $mhs['skor_toefl'] >= $b['min_toefl'];
            $lolos_ielts = $punya_ielts && $b['min_ielts'] > 0 && $mhs['skor_ielts'] >= $b['min_ielts'];

            if ($lolos_toefl || $lolos_ielts) {
                $skor += 10;
            } else {
                // Tidak punya sertifikat bahasa yang disyaratkan
                $skor += 0;
            }
        } else {
            // Tidak ada syarat TOEFL/IELTS, tapi jika punya = nilai plus
            if ($punya_toefl || $punya_ielts) {
                $skor += 9;
            } else {
                $skor += 7;
            }
        }

        // Hitung persentase
        $persentase = ($max_skor > 0) ? round(($skor / $max_skor) * 100, 2) : 0;

        // Simpan jika skor >= 40%
        if ($persentase >= 40) {
            $status = $persentase >= 60 ? 'direkomendasikan' : 'tidak_direkomendasikan';
            $conn->query("INSERT INTO rekomendasi_beasiswa (id_mahasiswa, id_beasiswa, skor_kecocokan, status)
                          VALUES ($mhs_id, {$b['id_beasiswa']}, $persentase, '$status')");
        }
    }
}
?>
