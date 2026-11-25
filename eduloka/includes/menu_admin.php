<?php
// MENU TAB KHUSUS ADMIN. Di-include di semua halaman folder admin/.
// Nama berkas halaman yang sedang dibuka, dipakai untuk menandai tab yang aktif.
$hal = basename($_SERVER['SCRIPT_NAME']);
// Daftar tab: nama berkas => tulisan pada tab.
$menu = ["index.php" => "Ringkasan", "pengguna.php" => "Pengguna", "buku.php" => "Buku", "pemesanan.php" => "Pemesanan", "forum.php" => "Forum"];
?>
<nav class="tabs" aria-label="Menu admin">
<?php foreach ($menu as $f => $nama): ?>
  <a href="<?= $f ?>"<?= ($hal === $f || ($f === 'buku.php' && $hal === 'buku_form.php')) ? ' aria-current="page"' : '' ?>><?= $nama ?></a>
<?php endforeach; ?>
</nav>
