<?php
// KELOLA PEMESANAN BIMBINGAN. Admin melihat semua pemesanan dan dapat mengonfirmasi, menandai selesai, atau membatalkan.
require_once "../includes/fungsi.php";
wajib_masuk('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') { proses_pemesanan(); header("Location: pemesanan.php"); exit; }
$rows = daftar_pemesanan();
$judul = "Kelola pemesanan";
include "../includes/header.php";
?>
<div class="kepala"><div><h1>Pemesanan bimbingan</h1><p>Konfirmasi permintaan sesi, isi tautan meeting, atau batalkan sesi.</p></div></div>
<?php include "../includes/menu_admin.php"; ?>
<?php tabel_pemesanan($rows, true); ?>
<?php include "../includes/footer.php"; ?>
