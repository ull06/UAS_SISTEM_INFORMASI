# 🎓 Sistem Rekomendasi Beasiswa

Sistem Informasi berbasis web yang membantu mahasiswa menemukan beasiswa yang paling sesuai dengan profil dan kebutuhan mereka — cepat, mudah, dan objektif.

---

## 👥 Tim Pengembang

| Nama | NIM |
|------|-----|
| Fawwaz Ziyadi Ilmi | 2408107010021 |
| Rahmatul Uliya | 2408107010012 |
| Nayla Nabila Syahel | 2408107010005 |

---

## 🌐 Demo

🔗 **Website:** [https://rekomendasibeasiswa.freehosting.dev/](https://rekomendasibeasiswa.freehosting.dev/)

🎨 **Prototype Figma:** [Lihat Desain](https://www.figma.com/design/ce1HVTPbQ7a0u8yhyrgsne/Untitled?node-id=0-1&t=5G6iLCatkdfxy284-1)

---

## 📌 Latar Belakang

Banyak mahasiswa kesulitan memilih beasiswa yang tepat karena:

- Banyaknya jenis beasiswa dengan kriteria yang berbeda-beda
- Informasi beasiswa yang tersebar dan tidak terstruktur
- Proses pemilihan masih dilakukan secara coba-coba *(trial and error)*
- Tidak ada sistem yang menilai tingkat kecocokan berdasarkan data pengguna

---

## 💡 Solusi

Sistem ini dibangun berbasis **Decision Support System (DSS)** yang mampu:

1. Mengolah data mahasiswa secara sistematis
2. Membandingkan dengan berbagai alternatif beasiswa
3. Menganalisis tingkat kecocokan antara profil mahasiswa dan kriteria beasiswa
4. Memberikan rekomendasi yang tepat dan relevan
5. Menampilkan persentase kecocokan untuk setiap beasiswa

---

## ⚙️ Fitur Utama

### 👤 Mahasiswa
- Registrasi dan login akun
- Mengisi dan memperbarui profil diri
- Melihat rekomendasi beasiswa secara otomatis
- Melihat detail dan tingkat kecocokan tiap beasiswa

### 🛠️ Admin
- Menambah, mengedit, dan menghapus data beasiswa
- Mengelola kriteria penilaian
- Melihat statistik dan monitoring hasil rekomendasi sistem

---

## 🔄 Alur Sistem

1. **Akses & Autentikasi** — Pengguna registrasi dan login; sistem membagi akses berdasarkan role (Admin / Mahasiswa)
2. **Input Data** — Mahasiswa mengisi profil diri (IPK, penghasilan orang tua, prestasi, dll.)
3. **Auto-Matching** — Sistem mencocokkan profil mahasiswa dengan kriteria beasiswa secara real-time
4. **Output** — Menampilkan daftar beasiswa yang relevan beserta persentase kecocokan

---

## 🏗️ Desain Sistem

- **Use Case Diagram** — Menggambarkan interaksi aktor (Mahasiswa & Admin) dengan fitur sistem
- **ERD (Entity Relationship Diagram)** — Memodelkan relasi antar entitas data
- **DFD (Data Flow Diagram)** — Level 0 (Diagram Konteks) dan Level 1 (Diagram Overview) untuk alur data
- **Flowchart** — Alur lengkap proses rekomendasi dari login hingga logout

---

## 🛠️ Metode Pengembangan

Menggunakan **Traditional System Life Cycle** dengan alur berurutan:

```
Analisis → Desain → Implementasi → Testing → Maintenance
```

---

## 📂 Teknologi

- **Backend:** PHP + MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Hosting:** FreeHosting

---

## 📄 Lisensi

Proyek ini dikembangkan sebagai tugas mata kuliah Sistem Informasi — Universitas Syiah Kuala.
