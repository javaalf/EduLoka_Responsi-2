<?php
// HALAMAN BERANDA: hero, kolom pencarian, penjelasan singkat, dan 4 buku terbaru.
require_once "includes/fungsi.php";
$judul = "Beranda";
$buku = semua("SELECT * FROM kursus ORDER BY id_kursus DESC LIMIT 4");
$n = satu("SELECT (SELECT COUNT(*) FROM kursus) AS buku, (SELECT COUNT(*) FROM pengguna WHERE peran='mentor') AS mentor, (SELECT COUNT(*) FROM diskusi) AS diskusi");
include "includes/header.php";
?>
<section class="hero">
  <img src="assets/img/hero.svg" alt="Sekolah kayu kecil di tepi hutan saat matahari terbit">
  <div class="txt">
    <h1>Belajar bareng mentor, tanpa jarak</h1>
    <p>EduLoka mempertemukan pelajar SD/MI dengan relawan pengajar, dan menyediakan buku teks resmi Kemendikdasmen yang bisa diunduh gratis.</p>
    <a class="btn" href="katalog.php">Lihat buku SD/MI</a>
  </div>
</section>

<form class="cari" action="katalog.php" method="get" role="search">
  <input type="search" name="q" placeholder="Cari buku, misalnya matematika" aria-label="Cari buku">
  <select name="kelas" aria-label="Kelas">
    <option value="">Semua kelas</option>
    <?php foreach (["I", "II", "III", "IV", "V", "VI"] as $k): ?><option><?= $k ?></option><?php endforeach; ?>
  </select>
  <button class="btn" type="submit">Cari buku</button>
</form>

<section class="bagian dua">
  <img src="assets/img/belajar.svg" alt="Ilustrasi pelajar membaca di bawah pohon">
  <div>
    <h2>Akses belajar yang tidak bergantung pada tempat tinggal</h2>
    <p>Banyak anak punya semangat belajar, tetapi sulit menemukan buku yang pas atau orang yang mau menjelaskan. EduLoka menyatukan keduanya: bahan bacaan resmi, lalu sesi bimbingan singkat dengan mentor yang dipilih sendiri.</p>
    <div class="fakta">
      <div><b><?= (int)$n['buku'] ?></b><span>buku teks siap unduh</span></div>
      <div><b><?= (int)$n['mentor'] ?></b><span>mentor relawan</span></div>
      <div><b><?= (int)$n['diskusi'] ?></b><span>pertanyaan di forum</span></div>
    </div>
    <?php if (pengguna()): ?><a class="btn alt" href="booking.php">Pesan sesi bimbingan</a>
    <?php else: ?><a class="btn alt" href="daftar.php">Daftar gratis</a><?php endif; ?>
  </div>
</section>

<section class="bagian">
  <div class="kepala">
    <div><h2>Buku terbaru</h2><p>Buku teks utama SD/MI dari SIBI Kemendikdasmen.</p></div>
    <a class="btn alt" href="katalog.php">Semua buku</a>
  </div>
  <div class="rak"><?php foreach ($buku as $b) kartu_buku($b); ?></div>
</section>
<?php include "includes/footer.php"; ?>
