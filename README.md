# Portal Undian Kelab Bola Sepak

Sistem pengundian moden yang direka untuk Jawatankuasa Kelab Bola Sepak dengan tema hijau limau yang dinamik dan antaramuka yang mesra pengguna.

## Ciri-ciri Utama
- **Tema Unified**: Skema warna hijau limau (`#80f20d`) yang konsisten di semua halaman.
- **Navigasi Kanan**: Bar sisi navigasi yang terletak di sebelah kanan untuk ergonomik yang lebih baik.
- **Pelokalan Penuh**: 100% Bahasa Melayu digunakan di seluruh platform.
- **Analitik Lanjut**: Paparan graf dan statistik masa nyata untuk keputusan undian.
- **Notifikasi Pintar**: Menggunakan SweetAlert2 untuk maklum balas log masuk, daftar, dan keluar yang elegan.
- **Pengurusan Admin**: Panel admin lengkap untuk mengurus calon, muat naik gambar (seret & lepas), dan import data pukal (CSV).

## Struktur Fail
- `login.php`: Halaman log masuk pengguna dan admin.
- `register.php`: Halaman pendaftaran keahlian baru.
- `dashboard.php`: Panel utama untuk pengguna mengundi.
- `results.php`: Paparan keputusan undian untuk pengguna umum.
- `admin.php`: Pengurusan calon dan data (Akses Terhad).
- `admin_results.php`: Analitik lanjut untuk kegunaan pentadbir.
- `db_config.php`: Konfigurasi sambungan pangkalan data MySQL.
- `logout.php`: Proses penamatan sesi pengguna.

## Pemasangan
1. Pastikan persekitaran XAMPP telah dipasang.
2. Salin folder projek ke dalam direktori `htdocs`.
3. Import pangkalan data menggunakan fail SQL yang disediakan (jika ada).
4. Pastikan folder `uploads/` wujud dan mempunyai kebenaran menulis untuk gambar calon.

---
*Pembangunan untuk Sesi 2024/2025*
