<?php
// Menampilkan satu pertanyaan, semua jawabannya, dan form menjawab.
// Admin juga bisa menghapus balasan (moderasi). Alamat: forum_detail.php?id=NOMOR
require_once "includes/fungsi.php";
$id = (int)($_GET['id'] ?? 0);
$d = satu("SELECT d.*, u.username, u.peran FROM diskusi d JOIN pengguna u ON u.id_pengguna = d.id_pengguna WHERE d.id_diskusi=?", "i", $id);
if (!$d) { flash("Pertanyaan tidak ditemukan.", "err"); header("Location: forum.php"); exit; }
$err = ""; $isi = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajib_masuk(); csrf_cek();
    $aksi = $_POST['aksi'] ?? "";
    if ($aksi === 'balas') {
        $isi = trim($_POST['isi'] ?? "");
        if (mb_strlen($isi) < 3 || mb_strlen($isi) > 1000) $err = "Tulis jawaban 3-1000 karakter.";
        else {
            q("INSERT INTO balasan_diskusi (id_diskusi, id_pengguna, isi) VALUES (?,?,?)", "iis", $id, pengguna()['id'], $isi);
            catat("balas_forum", $d['judul']);
            flash("Jawaban terkirim.");
            header("Location: forum_detail.php?id=$id"); exit;
        }
    } elseif ($aksi === 'hapus_balasan' && pengguna()['peran'] === 'admin') {
        q("DELETE FROM balasan_diskusi WHERE id_balasan=? AND id_diskusi=?", "ii", (int)($_POST['id_balasan'] ?? 0), $id);
        catat("moderasi_balasan", $d['judul']);
        flash("Balasan dihapus.");
        header("Location: forum_detail.php?id=$id"); exit;
    }
}
$balasan = semua("SELECT r.*, u.username, u.peran FROM balasan_diskusi r JOIN pengguna u ON u.id_pengguna = r.id_pengguna WHERE r.id_diskusi=? ORDER BY r.dibuat_pada", "i", $id);
$judul = $d['judul'];
include "includes/header.php";
?>
<p><a href="forum.php">&larr; Kembali ke forum</a></p>
<div class="panel">
  <h1 style="font-size:2rem"><?= e($d['judul']) ?></h1>
  <div class="meta"><?= e($d['username']) ?> · <?= tgl_id($d['dibuat_pada']) ?></div>
  <p><?= nl2br(e($d['isi'])) ?></p>
  <h3><?= count($balasan) ?> jawaban</h3>
  <?php foreach ($balasan as $r): ?>
    <div class="balasan <?= $r['peran'] === 'mentor' ? 'mentor' : '' ?>">
      <div class="meta"><?= e($r['username']) ?><?php if ($r['peran'] !== 'siswa'): ?><span class="lencana"><?= e($r['peran']) ?></span><?php endif; ?> · <?= tgl_id($r['dibuat_pada']) ?></div>
      <?= nl2br(e($r['isi'])) ?>
      <?php if (pengguna() && pengguna()['peran'] === 'admin'): ?>
        <form method="post" class="inline" onsubmit="return konfirmasi('Hapus balasan ini?')"><?= csrf_field() ?><input type="hidden" name="aksi" value="hapus_balasan"><input type="hidden" name="id_balasan" value="<?= (int)$r['id_balasan'] ?>"><button class="mini del">Hapus</button></form>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <?php if (pengguna()): ?>
  <form method="post" onsubmit="return cekDiskusi()" novalidate style="margin-top:22px">
    <?= csrf_field() ?><input type="hidden" name="aksi" value="balas">
    <div class="f"><label for="isi">Tulis jawaban</label><textarea id="isi" name="isi" rows="3" data-maks="1000" data-hitung="sisa-isi"><?= e($isi) ?></textarea><span class="petunjuk" id="sisa-isi"></span><span class="err" id="err-isi"><?= e($err) ?></span></div>
    <button class="btn" type="submit">Kirim jawaban</button>
  </form>
  <?php else: ?><p class="kosong"><a href="login.php">Masuk</a> untuk ikut menjawab.</p><?php endif; ?>
</div>
<?php include "includes/footer.php"; ?>
