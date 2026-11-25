<?php
// BAGIAN ATAS HALAMAN (header), di-include oleh semua halaman.
// Berisi pembuka HTML, tautan CSS, dan menu navigasi yang menyesuaikan status login dan peran pengguna.
require_once __DIR__ . "/fungsi.php";
// Data pengguna yang sedang login (null bila belum login), dipakai untuk menentukan isi menu.
$p = pengguna();
// Judul tab browser. Bila halaman tidak mengisi $judul, dipakai teks EduLoka.
$judul = $judul ?? "EduLoka";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> | EduLoka</title>
<link rel="icon" href="<?= $BASE ?>assets/img/logo.svg" type="image/svg+xml">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap">
<link rel="stylesheet" href="<?= $BASE ?>assets/css/style.css">
</head>
<body>
<header class="bar">
  <nav class="nav" aria-label="Navigasi utama">
    <a class="logo" href="<?= $BASE ?>index.php"><img src="<?= $BASE ?>assets/img/logo.svg" alt="" width="36" height="36">EduLoka</a>
    <ul>
      <li><a href="<?= $BASE ?>katalog.php">Buku</a></li>
      <li><a href="<?= $BASE ?>booking.php">Bimbingan</a></li>
      <li><a href="<?= $BASE ?>jadwal.php">Jadwal</a></li>
      <li><a href="<?= $BASE ?>forum.php">Forum</a></li>
      <?php if ($p && $p['peran'] !== 'admin'): ?><li><a href="<?= $BASE ?>riwayat.php">Riwayat</a></li><?php endif; ?>
      <?php if ($p && $p['peran'] === 'admin'): ?><li><a href="<?= $BASE ?>admin/index.php">Panel admin</a></li><?php endif; ?>
    </ul>
    <?php if ($p): ?>
      <a class="who" href="<?= $BASE ?>profil.php"><?= e($p['username']) ?></a>
      <a class="btn alt" href="<?= $BASE ?>logout.php">Keluar</a>
    <?php else: ?>
      <a class="btn" href="<?= $BASE ?>login.php">Masuk</a>
    <?php endif; ?>
  </nav>
</header>
<main class="isi">
<?php tampil_flash(); ?>
