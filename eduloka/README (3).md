# EduLoka
Portal akses belajar dan bimbingan belajar kolaboratif, mendukung *SDGs ke-4: Pendidikan Berkualitas*.
Siswa SD/MI dapat mengunduh buku teks resmi Kemendikdasmen (SIBI), memesan sesi bimbingan dengan mentor relawan, dan bertanya di forum.
Proyek akhir *Responsi Pemrograman Web I*.

# Pembagian jobdesk
1. Java Alfanza Setiawan (H1D025006) : backend
2. Fahri Hafi (H1D025016) : UI/UX
3. Salsa Nur Fadhilah (H1D025045) : frontend

# Latar Belakang
Pemerataan akses bimbingan belajar dan materi pendukung masih terkendala faktor finansial serta geografis bagi pelajar, sementara banyak relawan dan pendidik belum memiliki wadah digital yang terpusat untuk berbagi ilmu. EduLoka hadir sebagai portal akses belajar dan bimbingan kolaboratif berbasis web untuk mempertemukan pelajar dengan mentor atau relawan secara terstruktur dan mudah diakses, sejalan dengan SDGs ke-4 (Pendidikan Berkualitas).

# Tujuan Proyek
1. *Membangun platform akses belajar inklusif.* Mengembangkan aplikasi web interaktif yang menyediakan sarana bagi pelajar SD/MI untuk mengakses dan mengunduh materi pendidikan berkualitas (buku SIBI) serta mendapatkan bimbingan belajar secara transparan.
2. *Menyediakan wadah kolaborasi relawan.* Memfasilitasi para mentor, mahasiswa, dan relawan pendidikan agar dapat membagikan bahan ajar, mengatur jadwal konfirmasi sesi bimbingan, serta menjawab pertanyaan siswa secara terorganisir.
3. *Menerapkan sistem terintegrasi.* Menerapkan sistem autentikasi multi-peran, manajemen profil, katalog buku, booking bimbingan, jadwal sesi, forum diskusi, dashboard admin, hingga laporan riwayat berbasis HTML5, CSS3, JavaScript, PHP Native, dan MySQL.

## Fitur (9 modul sesuai PRD)
| # | Modul | Berkas utama |
|---|-------|--------------|
| 1 | Autentikasi (daftar, masuk, keluar, peran siswa/mentor/admin) | `daftar.php`, `login.php`, `logout.php` |
| 2 | Manajemen profil (nama, telepon, bio, foto) | `profil.php` |
| 3 | Katalog buku SD/MI (cari dan filter) | `katalog.php`, `index.php` |
| 4 | Detail buku dan unduh PDF | `detail.php`, `unduh.php` |
| 5 | Booking bimbingan | `booking.php` |
| 6 | Jadwal sesi (konfirmasi mentor, tautan Meet/Zoom) | `jadwal.php` |
| 7 | Forum tanya jawab | `forum.php`, `forum_detail.php` |
| 8 | Dashboard admin (CRUD buku dengan upload PDF dan sampul, kelola pengguna, pemesanan, moderasi forum) | `admin/*` |
| 9 | Riwayat dan laporan aktivitas | `riwayat.php`, `admin/index.php` |

# Teknologi yang Digunakan
- **HTML5**: struktur halaman dan elemen semantik
- **CSS3**: tampilan, layout flex/grid, dan desain responsif
- **JavaScript**: validasi form di browser dan interaksi sederhana
- **PHP Native** (prosedural, `mysqli`): logika server dan CRUD
- **MySQL/MariaDB**: penyimpanan data
- **XAMPP** (Apache, PHP 8.x, MySQL): lingkungan pengembangan lokal

# Pemetaan ke modul praktikum
| Modul praktikum | Dipakai di proyek ini |
|-----------------|-----------------------|
| 1 HTML | struktur dokumen, heading, gambar `<img>`, daftar `<ul>`, atribut |
| 2 HTML Lanjut | elemen semantik (`header`, `nav`, `main`, `section`, `footer`), hyperlink, form (text, email, password, date, time, select, textarea, file), tabel |
| 3 CSS | stylesheet eksternal `assets/css/style.css` (selector tag, id, class, group), layout flex dan grid, responsif |
| 4 JavaScript | validasi form `onsubmit="return cek...()"`, penghitung karakter `keyup`, `confirm()` sebelum hapus (`assets/js/script.js`) |
| 5 PHP | `include`, variabel, array, `if/else`, `foreach`, function (`includes/fungsi.php`) |
| 6 CRUD | `mysqli_connect`, create/read/update/delete pada buku, pengguna, pemesanan, diskusi |

Perbedaan dari contoh modul, demi keamanan: query memakai *prepared statement* (bukan menyisipkan variabel langsung ke SQL), penghapusan lewat `POST` + token CSRF (bukan tautan `delete.php?id=`), dan semua keluaran dilewatkan `htmlspecialchars` (fungsi `e()`).

# Panduan Instalasi dan Konfigurasi (XAMPP)
1. Pasang *XAMPP*, lalu *Start* modul *Apache* dan *MySQL*.
2. Salin folder `eduloka` ke `C:\xampp\htdocs\` sehingga menjadi `C:\xampp\htdocs\eduloka`.
3. Buka `http://localhost/phpmyadmin`, pilih tab *Impor*, pilih `database/eduloka.sql`, klik *Kirim*.
   Database `eduloka` beserta semua tabel dan data contoh akan terbentuk.
4. Jika user/password MySQL Anda bukan bawaan (`root` tanpa password), ubah `config/koneksi.php`.

# Panduan Menjalankan Aplikasi
Buka **http://localhost/eduloka/** di browser.

# Akun demo
| Peran | Username | Password |
|-------|----------|----------|
| Admin | `admin` | `Admin#2026` |
| Mentor | `bu_ratna`, `kak_dimas`, `bu_sari` | `Mentor#2026` |
| Siswa | `andi_siswa` | `Siswa#2026` |

Pengguna baru: klik *Masuk* lalu *Daftar dulu*, isi email, username, dan password, klik *Daftar*, kemudian masuk dengan username dan password tadi. Akun baru berperan siswa. Admin dapat mengubah peran menjadi mentor di *Panel admin > Pengguna*.
*Ganti semua password demo sebelum dipakai di luar presentasi.*

# Alur demo yang disarankan
1. Daftar akun baru, masuk, lalu buka *Buku*, pilih buku, dan klik *Unduh PDF*.
2. Masuk sebagai siswa, buka *Bimbingan*, pesan sesi dengan salah satu mentor.
3. Masuk sebagai mentor (`bu_sari`), buka *Jadwal*, isi tautan meeting, klik *Konfirmasi*, lalu *Tandai selesai*.
4. Kembali sebagai siswa, lihat *Riwayat*. Buka *Forum*, ajukan pertanyaan, lalu balas sebagai mentor.
5. Masuk sebagai admin: tambah buku di *Panel admin > Buku* dengan upload PDF dan gambar sampul, lihat sampulnya muncul di halaman pengguna.

# Struktur Folder
```
eduloka/
index.php  login.php  daftar.php  logout.php  profil.php
katalog.php  detail.php  unduh.php
booking.php  jadwal.php  forum.php  forum_detail.php  riwayat.php
admin/            index, pengguna, buku, buku_form, pemesanan, forum
config/koneksi.php
includes/         fungsi.php (inti), header.php, footer.php, menu_admin.php
assets/           css/style.css, js/script.js, img/ (logo dan ilustrasi)
uploads/          buku/ (PDF), sampul/ (gambar), foto/ (foto profil)
database/eduloka.sql
```

# Database (8 tabel, nama berbahasa Indonesia)
`pengguna`, `profil`, `kursus`, `pemesanan_bimbingan`, `jadwal`, `diskusi`, `balasan_diskusi`, `log_aktivitas`.
Relasi memakai foreign key (`ON DELETE CASCADE` atau `SET NULL`). Password disimpan sebagai hash (`password_hash`).

# Catatan Keamanan
- Upload dibatasi jenis (PDF, JPG, PNG, WebP), ukuran, dan diperiksa isi file (bukan hanya ekstensi). Nama file diacak. `uploads/.htaccess` melarang eksekusi PHP di folder itu.
- Validasi dilakukan dua kali: di browser (JavaScript) untuk kenyamanan, dan di server (PHP) untuk keamanan.
- Belum ada pembatasan percobaan login (rate limiting), reset password, atau verifikasi email. Tambahkan bila akan dipakai publik.

# Panduan Membaca Kode
Semua berkas kode sudah diberi komentar berbahasa Indonesia. Urutan belajar yang disarankan:

1. `database/eduloka.sql`: pahami dulu tabel dan relasinya.
2. `config/koneksi.php` lalu `includes/fungsi.php`: inti proyek (sesi, query aman, upload, CSRF). Fungsi `q()`, `semua()`, `satu()`, `wajib_masuk()`, dan `unggah()` dipakai di hampir semua halaman.
3. `includes/header.php` dan `includes/footer.php`: kerangka halaman yang dipakai bersama.
4. Halaman sederhana dulu: `index.php`, `katalog.php`, `detail.php`.
5. Halaman berformulir: `daftar.php`, `login.php`, `booking.php`, `forum.php` (pola yang sama: validasi, simpan, redirect).
6. Halaman admin di folder `admin/`, lalu `assets/js/script.js` (validasi di browser) dan `assets/css/style.css`.

Jenis komentar: komentar di bagian atas tiap berkas menjelaskan tujuan berkas, komentar di atas baris menjelaskan fungsi baris itu.
Pada berkas PHP yang banyak HTML, komentar HTML ditulis sebagai `<?php // ... ?>` agar tidak ikut terkirim ke browser.

# Pemecahan Masalah
- *"Gagal koneksi ke database"*: pastikan MySQL di XAMPP sudah Start dan `eduloka.sql` sudah diimpor.
- *Upload PDF gagal untuk berkas besar*: naikkan `upload_max_filesize` dan `post_max_size` di `php.ini` XAMPP (bawaan 40 MB), lalu restart Apache.
- *Error `finfo` tidak ditemukan*: aktifkan `extension=fileinfo` di `php.ini`.
- *Font tidak tampil*: font dimuat dari Google Fonts (perlu internet); tanpa internet tampilan memakai font cadangan.
- *Tombol Unduh PDF buku awal*: mengarah ke berkas resmi di server `static.buku.kemdikbud.go.id` sehingga butuh internet. Buku yang diupload admin diunduh dari folder `uploads/`.

# Sumber Buku
Buku teks SD/MI diambil dari *SIBI (Sistem Informasi Perbukuan Indonesia), Pusat Perbukuan Kemendikdasmen*: https://buku.kemendikdasmen.go.id/katalog
