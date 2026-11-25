<?php
// FILE KONEKSI DATABASE.
// Di-include sekali oleh includes/fungsi.php, sehingga variabel $koneksi bisa dipakai di semua halaman.
// Koneksi database 
// Sesuaikan jika user/password MySQL Anda berbeda dari bawaan XAMPP.
// mysqli_connect(server, username MySQL, password MySQL, nama database).
// Bawaan XAMPP: server localhost, user root, password kosong. Kalau gagal, die() menghentikan program dan menampilkan pesan.
$koneksi = mysqli_connect("localhost", "root", "", "eduloka")
    or die("Gagal koneksi ke database. Pastikan MySQL di XAMPP sudah Start dan database/eduloka.sql sudah diimpor.");
// Pakai utf8mb4 supaya huruf, tanda baca, dan emoji tersimpan serta tampil dengan benar.
mysqli_set_charset($koneksi, "utf8mb4");
