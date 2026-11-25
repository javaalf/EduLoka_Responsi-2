<?php
// DASHBOARD ADMIN. Ringkasan angka, buku terpopuler, dan aktivitas terbaru seluruh sistem.
require_once "../includes/fungsi.php";
wajib_masuk('admin');
$s = satu("SELECT
  (SELECT COUNT(*) FROM pengguna WHERE peran='siswa') AS siswa,
  (SELECT COUNT(*) FROM pengguna WHERE peran='mentor') AS mentor,
  (SELECT COUNT(*) FROM kursus) AS buku,
  (SELECT COALESCE(SUM(jumlah_unduh),0) FROM kursus) AS unduhan,
  (SELECT COUNT(*) FROM pemesanan_bimbingan WHERE status='menunggu') AS menunggu,
  (SELECT COUNT(*) FROM pemesanan_bimbingan WHERE status='selesai') AS selesai,
  (SELECT COUNT(*) FROM diskusi) AS diskusi");
$populer = semua("SELECT judul, kelas, jumlah_unduh FROM kursus ORDER BY jumlah_unduh DESC, judul LIMIT 5");
$log = semua("SELECT l.*, u.username FROM log_aktivitas l LEFT JOIN pengguna u ON u.id_pengguna = l.id_pengguna ORDER BY l.id_log DESC LIMIT 12");
$judul = "Panel admin";
include "../includes/header.php";
?>
<div class="kepala"><div><h1>Panel admin</h1><p>Pantau aktivitas sistem dan kelola data EduLoka.</p></div></div>
<?php include "../includes/menu_admin.php"; ?>
<div class="stats">
  <div class="stat"><b><?= (int)$s['siswa'] ?></b>Siswa</div>
  <div class="stat"><b><?= (int)$s['mentor'] ?></b>Mentor</div>
  <div class="stat"><b><?= (int)$s['buku'] ?></b>Buku</div>
  <div class="stat"><b><?= (int)$s['unduhan'] ?></b>Total unduhan</div>
  <div class="stat"><b><?= (int)$s['menunggu'] ?></b>Sesi menunggu</div>
  <div class="stat"><b><?= (int)$s['selesai'] ?></b>Sesi selesai</div>
  <div class="stat"><b><?= (int)$s['diskusi'] ?></b>Pertanyaan forum</div>
</div>
<div class="dua" style="align-items:start">
  <div><h2 style="font-size:1.4rem">Buku terpopuler</h2>
    <div class="tw"><table><thead><tr><th>Judul</th><th>Kelas</th><th>Unduhan</th></tr></thead><tbody>
    <?php foreach ($populer as $b): ?><tr><td><?= e($b['judul']) ?></td><td><?= e($b['kelas']) ?></td><td><?= (int)$b['jumlah_unduh'] ?></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
  <div><h2 style="font-size:1.4rem">Aktivitas terbaru</h2>
    <div class="tw"><table><thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th></tr></thead><tbody>
    <?php foreach ($log as $l): ?><tr><td><?= e($l['dibuat_pada']) ?></td><td><?= e($l['username'] ?? "-") ?></td><td><?= e($l['aksi']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>
<?php include "../includes/footer.php"; ?>
