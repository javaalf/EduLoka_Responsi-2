<?php
// KATALOG BUKU. Menampilkan daftar buku dengan pencarian kata kunci, filter mata pelajaran dan kelas.
require_once "includes/fungsi.php";
$q = trim($_GET['q'] ?? ""); $kelas = $_GET['kelas'] ?? ""; $mapel = trim($_GET['mapel'] ?? "");
$syarat = ["1=1"]; $tipe = ""; $par = [];
if ($q !== "") { $syarat[] = "(judul LIKE ? OR mata_pelajaran LIKE ? OR penulis LIKE ?)"; $tipe .= "sss"; array_push($par, "%$q%", "%$q%", "%$q%"); }
if (in_array($kelas, ["I", "II", "III", "IV", "V", "VI"])) { $syarat[] = "kelas=?"; $tipe .= "s"; $par[] = $kelas; } else $kelas = "";
if ($mapel !== "") { $syarat[] = "mata_pelajaran=?"; $tipe .= "s"; $par[] = $mapel; }
$daftar = semua("SELECT * FROM kursus WHERE " . implode(" AND ", $syarat) . " ORDER BY kelas, judul", $tipe, ...$par);
$mapelAda = semua("SELECT DISTINCT mata_pelajaran FROM kursus ORDER BY mata_pelajaran");
$judul = "Buku SD/MI";
include "includes/header.php";
?>
<div class="kepala">
  <div><h1>Buku teks SD/MI</h1><p>Buku resmi dari SIBI Kemendikdasmen. Pilih buku untuk melihat detail dan mengunduh PDF-nya.</p></div>
</div>
<form class="filter" method="get">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari judul, mapel, atau penulis" aria-label="Cari buku">
  <select name="mapel" aria-label="Mata pelajaran"><option value="">Semua mapel</option>
    <?php foreach ($mapelAda as $m): ?><option<?= $mapel === $m['mata_pelajaran'] ? " selected" : "" ?>><?= e($m['mata_pelajaran']) ?></option><?php endforeach; ?></select>
  <select name="kelas" aria-label="Kelas"><option value="">Semua kelas</option>
    <?php foreach (["I", "II", "III", "IV", "V", "VI"] as $k): ?><option<?= $kelas === $k ? " selected" : "" ?>><?= $k ?></option><?php endforeach; ?></select>
  <button class="btn" type="submit">Terapkan</button>
</form>
<?php if (!$daftar): ?>
  <p class="kosong">Belum ada buku yang cocok. Coba kata kunci lain atau <a href="katalog.php">tampilkan semua buku</a>.</p>
<?php else: ?>
  <div class="rak"><?php foreach ($daftar as $b) kartu_buku($b); ?></div>
<?php endif; ?>
<?php include "includes/footer.php"; ?>
