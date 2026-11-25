<?php
// PESAN SESI BIMBINGAN. Hanya untuk siswa: pilih mentor, tulis topik, pilih tanggal dan jam.
require_once "includes/fungsi.php";
wajib_masuk('siswa');
$p = pengguna();
$err = []; $v = ['id_mentor' => '', 'topik' => '', 'tanggal' => '', 'jam' => ''];
$mentor = semua("SELECT u.id_pengguna, COALESCE(NULLIF(pr.nama_lengkap,''), u.username) AS nama
                 FROM pengguna u LEFT JOIN profil pr ON pr.id_pengguna = u.id_pengguna
                 WHERE u.peran='mentor' ORDER BY nama");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    foreach (array_keys($v) as $k) $v[$k] = trim($_POST[$k] ?? "");
    if (!in_array((int)$v['id_mentor'], array_map('intval', array_column($mentor, 'id_pengguna')))) $err['id_mentor'] = "Pilih salah satu mentor.";
    if (mb_strlen($v['topik']) < 5 || mb_strlen($v['topik']) > 255) $err['topik'] = "Tulis topik 5-255 karakter.";
    $d = DateTime::createFromFormat('Y-m-d', $v['tanggal']);
    if (!$d || $d->format('Y-m-d') !== $v['tanggal']) $err['tanggal'] = "Pilih tanggal sesi.";
    elseif ($v['tanggal'] < date('Y-m-d')) $err['tanggal'] = "Pilih tanggal hari ini atau sesudahnya.";
    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v['jam'])) $err['jam'] = "Pilih jam sesi.";
    if (!$err && satu("SELECT 1 AS x FROM pemesanan_bimbingan WHERE id_mentor=? AND tanggal=? AND jam=? AND status IN ('menunggu','dikonfirmasi')", "iss", (int)$v['id_mentor'], $v['tanggal'], $v['jam'] . ":00"))
        $err['jam'] = "Mentor sudah punya sesi di jam itu. Pilih jam lain.";
    if (!$err) {
        q("INSERT INTO pemesanan_bimbingan (id_siswa, id_mentor, topik, tanggal, jam) VALUES (?,?,?,?,?)", "iisss", $p['id'], (int)$v['id_mentor'], $v['topik'], $v['tanggal'], $v['jam'] . ":00");
        catat("booking", $v['topik']);
        flash("Permintaan sesi terkirim. Mentor akan mengonfirmasi dan menambahkan tautan meeting.");
        header("Location: jadwal.php"); exit;
    }
}
$judul = "Pesan bimbingan";
include "includes/header.php";
?>
<div class="kepala"><div><h1>Pesan sesi bimbingan</h1><p>Pilih mentor, tulis topik yang ingin dibahas, lalu tentukan waktunya.</p></div></div>
<div class="panel">
<?php if (!$mentor): ?>
  <p class="kosong">Belum ada mentor yang terdaftar. Admin dapat mengubah peran pengguna menjadi mentor di panel admin.</p>
<?php else: ?>
  <form method="post" onsubmit="return cekBooking()" novalidate>
    <?= csrf_field() ?>
    <div class="f"><label for="id_mentor">Mentor</label>
      <select id="id_mentor" name="id_mentor"><option value="">Pilih mentor</option>
        <?php foreach ($mentor as $m): ?><option value="<?= (int)$m['id_pengguna'] ?>"<?= $v['id_mentor'] == $m['id_pengguna'] ? " selected" : "" ?>><?= e($m['nama']) ?></option><?php endforeach; ?></select>
      <span class="err" id="err-id_mentor"><?= e($err['id_mentor'] ?? "") ?></span></div>
    <div class="f"><label for="topik">Topik yang ingin dibahas</label>
      <textarea id="topik" name="topik" rows="3" data-maks="255" data-hitung="sisa-topik" placeholder="Contoh: penjumlahan sampai 100"><?= e($v['topik']) ?></textarea>
      <span class="petunjuk" id="sisa-topik"></span><span class="err" id="err-topik"><?= e($err['topik'] ?? "") ?></span></div>
    <div class="grid2">
      <div class="f"><label for="tanggal">Tanggal</label><input id="tanggal" name="tanggal" type="date" min="<?= date('Y-m-d') ?>" value="<?= e($v['tanggal']) ?>"><span class="err" id="err-tanggal"><?= e($err['tanggal'] ?? "") ?></span></div>
      <div class="f"><label for="jam">Jam</label><input id="jam" name="jam" type="time" value="<?= e($v['jam']) ?>"><span class="err" id="err-jam"><?= e($err['jam'] ?? "") ?></span></div>
    </div>
    <button class="btn" type="submit">Kirim permintaan sesi</button>
  </form>
<?php endif; ?>
</div>
<?php include "includes/footer.php"; ?>
