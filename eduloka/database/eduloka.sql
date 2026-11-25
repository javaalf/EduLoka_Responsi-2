-- =====================================================
-- Database EduLoka (Portal Akses Belajar & Bimbingan Belajar Kolaboratif)
-- Cara impor: phpMyAdmin (XAMPP) > tab Impor > pilih berkas ini > Kirim
-- Catatan: mengimpor ulang akan MENGHAPUS data lama.
-- =====================================================

-- Buat database eduloka. utf8mb4 mendukung semua huruf dan emoji.
CREATE DATABASE IF NOT EXISTS eduloka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eduloka;

-- Matikan sementara pemeriksaan relasi supaya tabel bisa dihapus tanpa memikirkan urutan.
SET FOREIGN_KEY_CHECKS = 0;
-- Hapus tabel lama (bila ada) agar impor ulang selalu mulai dari kondisi bersih.
DROP TABLE IF EXISTS log_aktivitas, balasan_diskusi, diskusi, jadwal, pemesanan_bimbingan, kursus, profil, pengguna;
SET FOREIGN_KEY_CHECKS = 1;

-- Modul 1 Autentikasi: akun dan hak akses
-- PRIMARY KEY = penanda unik tiap baris; AUTO_INCREMENT = nomor otomatis; UNIQUE = tidak boleh kembar;
-- ENUM = nilai hanya boleh salah satu dari daftar; NOT NULL = wajib diisi; DEFAULT = nilai bawaan.
CREATE TABLE pengguna (
  id_pengguna  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email        VARCHAR(120) NOT NULL UNIQUE,
  username     VARCHAR(16)  NOT NULL UNIQUE,
  kata_sandi   VARCHAR(255) NOT NULL COMMENT 'hash dari password_hash(), bukan teks asli',
  peran        ENUM('siswa','mentor','admin') NOT NULL DEFAULT 'siswa',
  dibuat_pada  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Modul 2 Profil
-- FOREIGN KEY menghubungkan id_pengguna ke tabel pengguna. ON DELETE CASCADE = bila akun dihapus, profilnya ikut terhapus.
CREATE TABLE profil (
  id_profil     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pengguna   INT UNSIGNED NOT NULL UNIQUE,
  nama_lengkap  VARCHAR(100) NOT NULL,
  no_telepon    VARCHAR(20),
  foto          VARCHAR(255),
  bio           TEXT,
  CONSTRAINT fk_profil_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Modul 3 & 4 Katalog, detail, dan unduh materi
-- Tabel buku. ON DELETE SET NULL = bila admin pembuatnya dihapus, bukunya tetap ada (dibuat_oleh jadi kosong).
CREATE TABLE kursus (
  id_kursus       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul           VARCHAR(150) NOT NULL,
  mata_pelajaran  VARCHAR(60)  NOT NULL,
  kelas           ENUM('I','II','III','IV','V','VI') NOT NULL,
  penulis         VARCHAR(200),
  tahun           YEAR,
  deskripsi       TEXT,
  berkas_pdf      VARCHAR(255) NOT NULL COMMENT 'path PDF hasil upload (uploads/buku/...) atau tautan resmi SIBI',
  gambar_sampul   VARCHAR(255) NULL COMMENT 'path gambar halaman depan (uploads/sampul/...)',
  jumlah_unduh    INT UNSIGNED NOT NULL DEFAULT 0,
  dibuat_oleh     INT UNSIGNED NULL,
  dibuat_pada     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_kursus_pengguna FOREIGN KEY (dibuat_oleh) REFERENCES pengguna(id_pengguna) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Modul 5 Booking bimbingan
-- Dua foreign key ke tabel pengguna: id_siswa (pemesan) dan id_mentor (yang membimbing).
CREATE TABLE pemesanan_bimbingan (
  id_pemesanan  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_siswa      INT UNSIGNED NOT NULL,
  id_mentor     INT UNSIGNED NOT NULL,
  topik         VARCHAR(255) NOT NULL,
  tanggal       DATE NOT NULL,
  jam           TIME NOT NULL,
  status        ENUM('menunggu','dikonfirmasi','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  dibuat_pada   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pesan_siswa  FOREIGN KEY (id_siswa)  REFERENCES pengguna(id_pengguna) ON DELETE CASCADE,
  CONSTRAINT fk_pesan_mentor FOREIGN KEY (id_mentor) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Modul 6 Jadwal sesi (terbentuk saat pemesanan dikonfirmasi)
-- UNIQUE pada id_pemesanan: satu pemesanan hanya punya satu jadwal (hubungan satu-ke-satu).
CREATE TABLE jadwal (
  id_jadwal         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pemesanan      INT UNSIGNED NOT NULL UNIQUE,
  tautan_pertemuan  VARCHAR(255) COMMENT 'Google Meet / Zoom',
  lokasi            VARCHAR(150),
  catatan           TEXT,
  selesai_pada      DATETIME NULL,
  CONSTRAINT fk_jadwal_pemesanan FOREIGN KEY (id_pemesanan) REFERENCES pemesanan_bimbingan(id_pemesanan) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Modul 7 Forum diskusi
-- Pertanyaan forum, milik satu pengguna.
CREATE TABLE diskusi (
  id_diskusi   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pengguna  INT UNSIGNED NOT NULL,
  judul        VARCHAR(200) NOT NULL,
  isi          TEXT NOT NULL,
  dibuat_pada  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_diskusi_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Jawaban atas pertanyaan: satu diskusi punya banyak balasan (satu-ke-banyak).
CREATE TABLE balasan_diskusi (
  id_balasan   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_diskusi   INT UNSIGNED NOT NULL,
  id_pengguna  INT UNSIGNED NOT NULL,
  isi          TEXT NOT NULL,
  dibuat_pada  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_balasan_diskusi  FOREIGN KEY (id_diskusi)  REFERENCES diskusi(id_diskusi) ON DELETE CASCADE,
  CONSTRAINT fk_balasan_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Modul 8 & 9 Pemantauan admin, riwayat dan laporan aktivitas
-- Catatan aktivitas untuk riwayat dan laporan admin.
CREATE TABLE log_aktivitas (
  id_log       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pengguna  INT UNSIGNED NULL,
  aksi         VARCHAR(60) NOT NULL,
  keterangan   VARCHAR(255),
  dibuat_pada  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_log_pengguna FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE SET NULL
) ENGINE=InnoDB;

-- INDEX mempercepat pencarian pada kolom yang sering dipakai di WHERE.
CREATE INDEX idx_pemesanan_status ON pemesanan_bimbingan (status, tanggal);
CREATE INDEX idx_kursus_mapel ON kursus (mata_pelajaran, kelas);

-- =====================================================
-- DATA AWAL
-- Admin  : admin      / Admin#2026
-- Mentor : bu_ratna, kak_dimas, bu_sari / Mentor#2026
-- Siswa  : andi_siswa / Siswa#2026
-- Segera ganti password setelah demo.
-- =====================================================
-- Isi data awal. Kolom kata_sandi berisi HASH bcrypt, bukan password asli,
-- sehingga password_verify() di PHP bisa mencocokkannya dengan password yang diketik.
INSERT INTO pengguna (id_pengguna, email, username, kata_sandi, peran) VALUES
(1, 'admin@eduloka.test',  'admin',      '$2y$12$z1dB/NqR8xqnZlTcQC9FSeGQHTtyr4gMIkJ3hxWEwSEG7xjcOMPrG',  'admin'),
(2, 'ratna@eduloka.test',  'bu_ratna',   '$2y$12$7MkJky/37AKffBygdKH6qO7cEWL8jB0nUs1QipGms2jtUAO6OdV0e', 'mentor'),
(3, 'dimas@eduloka.test',  'kak_dimas',  '$2y$12$7MkJky/37AKffBygdKH6qO7cEWL8jB0nUs1QipGms2jtUAO6OdV0e', 'mentor'),
(4, 'sari@eduloka.test',   'bu_sari',    '$2y$12$7MkJky/37AKffBygdKH6qO7cEWL8jB0nUs1QipGms2jtUAO6OdV0e', 'mentor'),
(5, 'andi@eduloka.test',   'andi_siswa', '$2y$12$vQh/EFM2eNoQspaLCmsWLO5WH4nsfXqQefT1toqrxZ8x6CoPhO.Y2',  'siswa');

-- Profil untuk kelima akun di atas.
INSERT INTO profil (id_pengguna, nama_lengkap, bio) VALUES
(1, 'Administrator EduLoka', 'Pengelola portal EduLoka.'),
(2, 'Bu Ratna', 'Relawan pengajar Matematika SD.'),
(3, 'Kak Dimas', 'Relawan pengajar IPAS.'),
(4, 'Bu Sari', 'Relawan pengajar Bahasa Indonesia.'),
(5, 'Andi Pratama', 'Siswa kelas 5 SD.');

-- Empat buku teks utama SD/MI dari SIBI Kemendikdasmen (berkas_pdf = tautan resmi)
-- Empat buku awal. CURDATE() dan DATE_ADD pada data berikutnya membuat tanggal contoh selalu relatif terhadap hari impor.
INSERT INTO kursus (judul, mata_pelajaran, kelas, penulis, tahun, deskripsi, berkas_pdf, dibuat_oleh) VALUES
('Matematika', 'Matematika', 'I', 'Dara Retno Wulan, Rasfaniwaty', 2022,
 'Buku teks utama Matematika untuk SD/MI Kelas I.',
 'https://static.buku.kemdikbud.go.id/content/pdf/bukuteks/kurikulum21/Matematika-BS-KLS-I.pdf', 1),
('Bahasa Indonesia: Aku Bisa!', 'Bahasa Indonesia', 'I', 'Sofie Dewayani', 2023,
 'Buku teks utama Bahasa Indonesia untuk SD/MI Kelas I (Edisi Revisi).',
 'https://static.buku.kemdikbud.go.id/content/pdf/bukuteks/kurikulum21/Bahasa_Indonesia_BS_KLS_I_Rev.pdf', 1),
('Pendidikan Pancasila', 'Pendidikan Pancasila', 'I', 'Canny Ilmiati, Etika Indah Febriani, Elisa Seftriyana', 2022,
 'Buku teks utama Pendidikan Pancasila untuk SD/MI Kelas I.',
 'https://static.buku.kemdikbud.go.id/content/pdf/bukuteks/kurikulum21/Pendidikan-Pancasila-BS-KLS-I.pdf', 1),
('Ilmu Pengetahuan Alam dan Sosial', 'IPAS', 'V', 'Amalia Fitri Ghaniem, Ati Haviati Oktora, Maria Jeanindya Wahyudi', 2024,
 'Buku teks utama IPAS untuk SD/MI Kelas V (Edisi Revisi).',
 'https://static.buku.kemdikbud.go.id/content/pdf/bukuteks/kurikulum21/IPAS_BS_KLS_V_Rev.pdf', 1);

-- Contoh pemesanan: satu sudah selesai, satu sudah dikonfirmasi, satu menunggu
-- Tiga pemesanan contoh dengan status berbeda agar semua tampilan punya data saat didemokan.
INSERT INTO pemesanan_bimbingan (id_pemesanan, id_siswa, id_mentor, topik, tanggal, jam, status) VALUES
(1, 5, 2, 'Pecahan sederhana', DATE_SUB(CURDATE(), INTERVAL 3 DAY), '15:00:00', 'selesai'),
(2, 5, 3, 'Siklus air', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00:00', 'dikonfirmasi'),
(3, 5, 4, 'Membaca cepat dan memahami isi bacaan', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '14:00:00', 'menunggu');
-- Jadwal untuk dua pemesanan yang sudah dikonfirmasi atau selesai.
INSERT INTO jadwal (id_pemesanan, tautan_pertemuan, selesai_pada) VALUES
(1, 'https://meet.google.com/contoh-abcd-efg', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 'https://meet.google.com/contoh-hijk-lmn', NULL);

-- Dua pertanyaan forum contoh.
INSERT INTO diskusi (id_diskusi, id_pengguna, judul, isi) VALUES
(1, 5, 'Dari mana hujan berasal?', 'Aku belum paham kenapa air laut bisa jadi hujan. Bisa dijelaskan?'),
(2, 5, 'Kenapa 7 + 5 sama dengan 5 + 7?', 'Apakah urutan penjumlahan boleh ditukar?');
-- Satu jawaban dari mentor (kak_dimas).
INSERT INTO balasan_diskusi (id_diskusi, id_pengguna, isi) VALUES
(1, 3, 'Air laut menguap karena panas matahari, naik menjadi awan, lalu turun lagi sebagai hujan. Baca bab siklus air di buku IPAS ya.');

INSERT INTO log_aktivitas (id_pengguna, aksi, keterangan) VALUES
(1, 'impor_database', 'Database EduLoka dibuat dan data awal dimasukkan.');
