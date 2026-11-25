<?php
// DETAIL BUKU. Menampilkan info lengkap satu buku dan tombol unduh. Alamat: detail.php?id=NOMOR
require_once "includes/fungsi.php";
$id = (int)($_GET['id'] ?? 0);
$b = satu("SELECT * FROM kursus WHERE id_kursus=?", "i", $id);
if (!$b) { flash("Buku tidak ditemukan.", "err"); header("Location: katalog.php"); exit; }
$warna = ["#2f6a31", "#b5532f", "#7a5a1c", "#1f5f6b", "#5b4a8a"][$b['id_kursus'] % 5];
$judul = $b['judul'];
include "includes/header.php";
?>
<article class="detail">
  <div class="sampul" style="background:<?= $warna ?>">
    <?php if ($b['gambar_sampul']): ?><img src="<?= e($b['gambar_sampul']) ?>" alt="Sampul buku <?= e($b['judul']) ?>">
    <?php else: ?><small>SD/MI Kelas <?= e($b['kelas']) ?></small><strong><?= e($b['judul']) ?></strong><?php endif; ?>
  </div>
  <div>
    <h1><?= e($b['judul']) ?></h1>
    <dl>
      <dt>Penulis</dt><dd><?= e($b['penulis']) ?></dd>
      <dt>Mata pelajaran</dt><dd><?= e($b['mata_pelajaran']) ?></dd>
      <dt>Kelas</dt><dd>SD/MI Kelas <?= e($b['kelas']) ?></dd>
      <dt>Tahun terbit</dt><dd><?= e($b['tahun']) ?></dd>
      <dt>Diunduh</dt><dd><?= (int)$b['jumlah_unduh'] ?> kali</dd>
    </dl>
    <p><?= nl2br(e($b['deskripsi'])) ?></p>
    <p><a class="btn" href="unduh.php?id=<?= (int)$b['id_kursus'] ?>">Unduh PDF</a> <a class="btn alt" href="katalog.php">Kembali ke katalog</a></p>
  </div>
</article>
<?php include "includes/footer.php"; ?>
