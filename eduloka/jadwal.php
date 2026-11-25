<?php
// JADWAL SESI BELAJAR. Menampilkan sesi yang menunggu dan yang sudah dikonfirmasi.
// Siswa melihat sesinya, mentor melihat sesi yang ia ampu (plus tombol konfirmasi), admin melihat semuanya.
require_once "includes/fungsi.php";
wajib_masuk();
$p = pengguna();
$bisaKelola = in_array($p['peran'], ['mentor', 'admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bisaKelola) { proses_pemesanan(); header("Location: jadwal.php"); exit; }
if ($p['peran'] === 'siswa') $rows = daftar_pemesanan("b.id_siswa=? AND b.status IN ('menunggu','dikonfirmasi')", "i", $p['id']);
elseif ($p['peran'] === 'mentor') $rows = daftar_pemesanan("b.id_mentor=? AND b.status IN ('menunggu','dikonfirmasi')", "i", $p['id']);
else $rows = daftar_pemesanan("b.status IN ('menunggu','dikonfirmasi')");
$rows = array_reverse($rows); // jadwal terdekat di atas
$judul = "Jadwal sesi";
include "includes/header.php";
?>
<div class="kepala">
  <div><h1>Jadwal sesi belajar</h1><p>Sesi yang menunggu konfirmasi dan yang sudah dikonfirmasi, lengkap dengan tautan meeting.</p></div>
  <?php if ($p['peran'] === 'siswa'): ?><a class="btn" href="booking.php">Pesan sesi baru</a><?php endif; ?>
</div>
<?php tabel_pemesanan($rows, $bisaKelola); ?>
<?php include "includes/footer.php"; ?>
