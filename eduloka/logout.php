<?php
// KELUAR (logout): menghapus sesi lalu kembali ke halaman login.
// Muat fungsi bantu.
require_once "includes/fungsi.php";
if (pengguna()) catat("logout", pengguna()['username']);
$_SESSION = [];
session_destroy();
session_start();
flash("Kamu sudah keluar. Sampai jumpa lagi.");
header("Location: login.php");
exit;
