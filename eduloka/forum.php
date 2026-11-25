<?php
// FORUM TANYA JAWAB. Daftar pertanyaan dan form untuk mengajukan pertanyaan baru. Siapa saja boleh membaca; bertanya butuh login.
require_once "includes/fungsi.php";
$err = []; $jf = $if = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajib_masuk(); csrf_cek();
    $jf = trim($_POST['judul'] ?? ""); $if = trim($_POST['isi'] ?? "");
    if (mb_strlen($jf) < 8 || mb_strlen($jf) > 200) $err['judul'] = "Tulis pertanyaan 8-200 karakter.";
    if (mb_strlen($if) < 10 || mb_strlen($if) > 1000) $err['isi'] = "Jelaskan dengan 10-1000 karakter.";
    if (!$err) {
        q("INSERT INTO diskusi (id_pengguna, judul, isi) VALUES (?,?,?)", "iss", pengguna()['id'], $jf, $if);
        $id = mysqli_insert_id($koneksi);
        catat("tanya_forum", $jf);
        flash("Pertanyaan terkirim. Mentor akan menjawab di sini.");
        header("Location: forum_detail.php?id=$id"); exit;
    }
}
$utas = semua("SELECT d.*, u.username, u.peran, (SELECT COUNT(*) FROM balasan_diskusi r WHERE r.id_diskusi = d.id_diskusi) AS jml
               FROM diskusi d JOIN pengguna u ON u.id_pengguna = d.id_pengguna ORDER BY d.dibuat_pada DESC");
$judul = "Forum tanya jawab";
include "includes/header.php";
?>
<div class="kepala"><div><h1>Tanya jawab</h1><p>Tanyakan apa saja soal pelajaran. Mentor menjawab di sini supaya bisa dibaca teman lain.</p></div></div>
<div class="dua" style="align-items:start">
  <div class="panel">
    <?php if (!$utas): ?><p class="kosong">Belum ada pertanyaan. Jadilah yang pertama bertanya.</p><?php endif; ?>
    <?php foreach ($utas as $t): ?>
      <div class="utas">
        <h3><a href="forum_detail.php?id=<?= (int)$t['id_diskusi'] ?>"><?= e($t['judul']) ?></a></h3>
        <div class="meta"><?= e($t['username']) ?> · <?= tgl_id($t['dibuat_pada']) ?> · <?= (int)$t['jml'] ?> jawaban</div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="panel">
    <h3>Ajukan pertanyaan</h3>
    <?php if (!pengguna()): ?>
      <p class="kosong"><a href="login.php">Masuk</a> atau <a href="daftar.php">daftar</a> dulu untuk bertanya.</p>
    <?php else: ?>
    <form method="post" onsubmit="return cekDiskusi()" novalidate>
      <?= csrf_field() ?>
      <div class="f"><label for="judul">Pertanyaan</label><input id="judul" name="judul" value="<?= e($jf) ?>"><span class="err" id="err-judul"><?= e($err['judul'] ?? "") ?></span></div>
      <div class="f"><label for="isi">Penjelasan singkat</label><textarea id="isi" name="isi" rows="4" data-maks="1000" data-hitung="sisa-isi"><?= e($if) ?></textarea><span class="petunjuk" id="sisa-isi"></span><span class="err" id="err-isi"><?= e($err['isi'] ?? "") ?></span></div>
      <button class="btn" type="submit">Kirim pertanyaan</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<?php include "includes/footer.php"; ?>
