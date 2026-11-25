<?php
// RIWAYAT AKTIVITAS Untuk siswa: sesi belajar yang sudah selesai. Untuk mentor: sesi mengajar yang sudah selesai.
// Ditambah ringkasan angka dan 10 aktivitas terakhir.
require_once "includes/fungsi.php";
wajib_masuk(['siswa', 'mentor']);
$p = pengguna();
$kolom = $p['peran'] === 'siswa' ? "b.id_siswa=?" : "b.id_mentor=?";
$rows = daftar_pemesanan("$kolom AND b.status='selesai'", "i", $p['id']);
$ringkas = satu("SELECT COUNT(*) AS total, COUNT(DISTINCT " . ($p['peran'] === 'siswa' ? "id_mentor" : "id_siswa") . ") AS rekan FROM pemesanan_bimbingan WHERE " . str_replace("b.", "", $kolom) . " AND status='selesai'", "i", $p['id']);
$aktivitas = semua("SELECT * FROM log_aktivitas WHERE id_pengguna=? ORDER BY id_log DESC LIMIT 10", "i", $p['id']);
$judul = "Riwayat";
include "includes/header.php";
?>
<div class="kepala"><div><h1>Riwayat <?= $p['peran'] === 'siswa' ? "belajar" : "mengajar" ?></h1><p>Rekam jejak sesi yang sudah selesai dan aktivitasmu di EduLoka.</p></div></div>
<div class="stats">
  <div class="stat"><b><?= (int)$ringkas['total'] ?></b>Sesi selesai</div>
  <div class="stat"><b><?= (int)$ringkas['rekan'] ?></b><?= $p['peran'] === 'siswa' ? "Mentor yang pernah mendampingi" : "Siswa yang pernah dibimbing" ?></div>
</div>
<h2 style="font-size:1.5rem">Sesi selesai</h2>
<?php tabel_pemesanan($rows); ?>
<h2 style="font-size:1.5rem;margin-top:36px">Aktivitas terakhir</h2>
<?php if (!$aktivitas): ?><p class="kosong">Belum ada aktivitas.</p><?php else: ?>
<div class="tw"><table><thead><tr><th>Waktu</th><th>Aksi</th><th>Keterangan</th></tr></thead><tbody>
<?php foreach ($aktivitas as $a): ?><tr><td><?= e($a['dibuat_pada']) ?></td><td><?= e($a['aksi']) ?></td><td><?= e($a['keterangan']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php include "includes/footer.php"; ?>
