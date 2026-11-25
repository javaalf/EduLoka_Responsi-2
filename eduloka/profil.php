<?php
// PROFIL PENGGUNA. Mengubah nama lengkap, nomor telepon, bio, dan foto profil. Username dan email hanya ditampilkan.
require_once "includes/fungsi.php";
wajib_masuk();
$p = pengguna();
q("INSERT IGNORE INTO profil (id_pengguna, nama_lengkap) VALUES (?,?)", "is", $p['id'], $p['username']);
$err = [];
$data = satu("SELECT u.email, u.username, u.peran, pr.* FROM pengguna u LEFT JOIN profil pr ON pr.id_pengguna = u.id_pengguna WHERE u.id_pengguna=?", "i", $p['id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama = trim($_POST['nama_lengkap'] ?? ""); $tel = trim($_POST['no_telepon'] ?? ""); $bio = trim($_POST['bio'] ?? "");
    if (!preg_match("/^[\\p{L} .,'\\-]{3,100}$/u", $nama)) $err['nama_lengkap'] = "Nama 3-100 huruf, tanpa angka atau simbol.";
    if ($tel !== "" && !preg_match('/^[0-9+ ]{8,20}$/', $tel)) $err['no_telepon'] = "Nomor telepon 8-20 digit.";
    if (mb_strlen($bio) > 300) $err['bio'] = "Bio maksimal 300 karakter.";
    $foto = null;
    if (!$err) { [$foto, $e] = unggah('foto', TIPE_GAMBAR, 2 * 1048576, 'foto'); if ($e) $err['foto'] = $e; }
    if (!$err) {
        if ($foto) { hapus_berkas($data['foto']); } else $foto = $data['foto'];
        q("UPDATE profil SET nama_lengkap=?, no_telepon=?, foto=?, bio=? WHERE id_pengguna=?", "ssssi", $nama, $tel, $foto, $bio, $p['id']);
        catat("ubah_profil", $p['username']);
        flash("Profil diperbarui.");
        header("Location: profil.php"); exit;
    }
    $data['nama_lengkap'] = $nama; $data['no_telepon'] = $tel; $data['bio'] = $bio;
}
$judul = "Profil";
include "includes/header.php";
?>
<div class="kepala"><div><h1>Profil saya</h1><p>Perbarui data diri yang tampil di jadwal dan forum.</p></div></div>
<div class="panel">
  <form method="post" enctype="multipart/form-data" onsubmit="return cekProfil()" novalidate>
    <?= csrf_field() ?>
    <p><?php if ($data['foto']): ?><img class="avatar" src="<?= e($data['foto']) ?>" alt="Foto profil"><?php else: ?><img class="avatar" src="assets/img/logo.svg" alt="Foto profil bawaan"><?php endif; ?></p>
    <div class="f"><label for="foto">Foto profil (JPG, PNG, atau WebP, maks. 2 MB)</label><input id="foto" name="foto" type="file" accept="image/*"><span class="err" id="err-foto"><?= e($err['foto'] ?? "") ?></span></div>
    <div class="grid2">
      <div class="f"><label>Username</label><input value="<?= e($data['username']) ?>" disabled></div>
      <div class="f"><label>Email</label><input value="<?= e($data['email']) ?>" disabled></div>
    </div>
    <div class="f"><label for="nama_lengkap">Nama lengkap</label><input id="nama_lengkap" name="nama_lengkap" value="<?= e($data['nama_lengkap']) ?>"><span class="err" id="err-nama_lengkap"><?= e($err['nama_lengkap'] ?? "") ?></span></div>
    <div class="f"><label for="no_telepon">Nomor telepon</label><input id="no_telepon" name="no_telepon" value="<?= e($data['no_telepon']) ?>"><span class="err" id="err-no_telepon"><?= e($err['no_telepon'] ?? "") ?></span></div>
    <div class="f"><label for="bio">Bio singkat</label><textarea id="bio" name="bio" rows="3" data-maks="300" data-hitung="sisa-bio"><?= e($data['bio']) ?></textarea><span class="petunjuk" id="sisa-bio"></span><span class="err" id="err-bio"><?= e($err['bio'] ?? "") ?></span></div>
    <button class="btn" type="submit">Simpan profil</button>
  </form>
</div>
<?php include "includes/footer.php"; ?>
