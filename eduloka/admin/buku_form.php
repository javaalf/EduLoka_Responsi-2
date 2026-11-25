<?php
// FORM TAMBAH / UBAH BUKU dengan UPLOAD berkas PDF dan gambar sampul.
// Tanpa ?id di alamat = tambah buku baru. Dengan buku_form.php?id=NOMOR = ubah buku itu.
require_once "../includes/fungsi.php";
wajib_masuk('admin');
$id = (int)($_GET['id'] ?? 0);
$b = $id ? satu("SELECT * FROM kursus WHERE id_kursus=?", "i", $id) : null;
if ($id && !$b) { flash("Buku tidak ditemukan.", "err"); header("Location: buku.php"); exit; }
$v = $b ?: ['judul' => '', 'mata_pelajaran' => '', 'kelas' => 'I', 'penulis' => '', 'tahun' => date('Y'), 'deskripsi' => ''];
$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    foreach (['judul', 'mata_pelajaran', 'kelas', 'penulis', 'tahun', 'deskripsi'] as $k) $v[$k] = trim($_POST[$k] ?? "");
    if (mb_strlen($v['judul']) < 3 || mb_strlen($v['judul']) > 150) $err['judul'] = "Judul 3-150 karakter.";
    if ($v['mata_pelajaran'] === "" || mb_strlen($v['mata_pelajaran']) > 60) $err['mata_pelajaran'] = "Isi mata pelajaran (maks. 60 karakter).";
    if (!in_array($v['kelas'], ["I", "II", "III", "IV", "V", "VI"])) $err['kelas'] = "Pilih kelas.";
    if ($v['penulis'] === "" || mb_strlen($v['penulis']) > 200) $err['penulis'] = "Isi nama penulis (maks. 200 karakter).";
    if (!preg_match('/^\d{4}$/', $v['tahun']) || $v['tahun'] < 2000 || $v['tahun'] > date('Y') + 1) $err['tahun'] = "Tahun tidak valid.";
    $pdf = $img = null;
    if (!$err) {
        [$pdf, $e1] = unggah('berkas_pdf', TIPE_PDF, 25 * 1048576, 'buku', !$b);
        [$img, $e2] = unggah('gambar_sampul', TIPE_GAMBAR, 3 * 1048576, 'sampul');
        if ($e1) $err['berkas_pdf'] = $e1;
        if ($e2) $err['gambar_sampul'] = $e2;
        if ($err) { hapus_berkas($pdf); hapus_berkas($img); }
    }
    if (!$err) {
        if ($b) {
            if ($pdf) hapus_berkas($b['berkas_pdf']);
            if ($img) hapus_berkas($b['gambar_sampul']);
            q("UPDATE kursus SET judul=?, mata_pelajaran=?, kelas=?, penulis=?, tahun=?, deskripsi=?, berkas_pdf=?, gambar_sampul=? WHERE id_kursus=?",
              "ssssisssi", $v['judul'], $v['mata_pelajaran'], $v['kelas'], $v['penulis'], (int)$v['tahun'], $v['deskripsi'], $pdf ?: $b['berkas_pdf'], $img ?: $b['gambar_sampul'], $id);
            catat("ubah_buku", $v['judul']); flash("Buku diperbarui.");
        } else {
            q("INSERT INTO kursus (judul, mata_pelajaran, kelas, penulis, tahun, deskripsi, berkas_pdf, gambar_sampul, dibuat_oleh) VALUES (?,?,?,?,?,?,?,?,?)",
              "ssssisssi", $v['judul'], $v['mata_pelajaran'], $v['kelas'], $v['penulis'], (int)$v['tahun'], $v['deskripsi'], $pdf, $img, pengguna()['id']);
            catat("tambah_buku", $v['judul']); flash("Buku ditambahkan ke katalog.");
        }
        header("Location: buku.php"); exit;
    }
}
$judul = $b ? "Ubah buku" : "Tambah buku";
include "../includes/header.php";
?>
<div class="kepala"><div><h1><?= $judul ?></h1><p>Unggah berkas PDF buku dan gambar halaman depannya.</p></div></div>
<?php include "../includes/menu_admin.php"; ?>
<div class="panel">
  <form method="post" enctype="multipart/form-data" onsubmit="return cekBuku(<?= $b ? 'false' : 'true' ?>)" novalidate>
    <?= csrf_field() ?>
    <div class="f"><label for="judul">Judul</label><input id="judul" name="judul" value="<?= e($v['judul']) ?>"><span class="err" id="err-judul"><?= e($err['judul'] ?? "") ?></span></div>
    <div class="grid2">
      <div class="f"><label for="mata_pelajaran">Mata pelajaran</label><input id="mata_pelajaran" name="mata_pelajaran" value="<?= e($v['mata_pelajaran']) ?>"><span class="err" id="err-mata_pelajaran"><?= e($err['mata_pelajaran'] ?? "") ?></span></div>
      <div class="f"><label for="kelas">Kelas</label><select id="kelas" name="kelas"><?php foreach (["I", "II", "III", "IV", "V", "VI"] as $k): ?><option<?= $v['kelas'] === $k ? " selected" : "" ?>><?= $k ?></option><?php endforeach; ?></select><span class="err" id="err-kelas"><?= e($err['kelas'] ?? "") ?></span></div>
      <div class="f"><label for="penulis">Penulis</label><input id="penulis" name="penulis" value="<?= e($v['penulis']) ?>"><span class="err" id="err-penulis"><?= e($err['penulis'] ?? "") ?></span></div>
      <div class="f"><label for="tahun">Tahun terbit</label><input id="tahun" name="tahun" type="number" min="2000" max="<?= date('Y') + 1 ?>" value="<?= e($v['tahun']) ?>"><span class="err" id="err-tahun"><?= e($err['tahun'] ?? "") ?></span></div>
    </div>
    <div class="f"><label for="deskripsi">Deskripsi</label><textarea id="deskripsi" name="deskripsi" rows="3"><?= e($v['deskripsi']) ?></textarea></div>
    <div class="f"><label for="berkas_pdf">Berkas PDF buku (maks. 25 MB)<?= $b ? " - kosongkan jika tidak diganti" : "" ?></label><input id="berkas_pdf" name="berkas_pdf" type="file" accept="application/pdf"><span class="err" id="err-berkas_pdf"><?= e($err['berkas_pdf'] ?? "") ?></span></div>
    <div class="f"><label for="gambar_sampul">Gambar sampul / halaman depan (JPG, PNG, WebP, maks. 3 MB)</label><input id="gambar_sampul" name="gambar_sampul" type="file" accept="image/*"><span class="err" id="err-gambar_sampul"><?= e($err['gambar_sampul'] ?? "") ?></span></div>
    <?php if ($b && $b['gambar_sampul']): ?><p class="petunjuk">Sampul saat ini:<br><img class="thumb" style="width:80px;height:100px" src="../<?= e($b['gambar_sampul']) ?>" alt="Sampul saat ini"></p><?php endif; ?>
    <button class="btn" type="submit"><?= $b ? "Simpan perubahan" : "Tambah buku" ?></button>
    <a class="btn alt" href="buku.php">Batal</a>
  </form>
</div>
<?php include "../includes/footer.php"; ?>
