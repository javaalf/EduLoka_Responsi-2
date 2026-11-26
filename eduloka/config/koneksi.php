<?php

$koneksi = mysqli_connect("localhost", "root", "", "eduloka")
    or die("Gagal koneksi ke database. Pastikan MySQL di XAMPP sudah Start dan database/eduloka.sql sudah diimpor.");

mysqli_set_charset($koneksi, "utf8mb4");
