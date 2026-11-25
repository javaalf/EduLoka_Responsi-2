<?php
// KELOLA PENGGUNA. Admin dapat mengubah peran (siswa, mentor, admin) atau menghapus akun.
// Inilah cara membuat mentor: siswa yang sudah mendaftar dijadikan mentor oleh admin.
require_once "../includes/fungsi.php";
wajib_masuk('admin');
$me = pengguna();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $id = (int)($_POST['id'] ?? 0); $aksi = $_POST['aksi'] ?? "";
    $u = satu("SELECT * FROM pengguna WHERE id_pengguna=?", "i", $id);
    if (!$u || $id === $me['id']) flash("Aksi ini tidak bisa dilakukan pada akunmu sendiri.", "err");
    elseif ($aksi === 'peran' && in_array($_POST['peran'] ?? "", ['siswa', 'mentor', 'admin'])) {
        q("UPDATE pengguna SET peran=? WHERE id_pengguna=?", "si", $_POST['peran'], $id);
        catat("ubah_peran", $u['username'] . " -> " . $_POST['peran']);
        flash("Peran " . $u['username'] . " diperbarui.");
    } elseif ($aksi === 'hapus') {
        hapus_berkas(satu("SELECT foto FROM profil WHERE id_pengguna=?", "i", $id)['foto'] ?? null);
        q("DELETE FROM pengguna WHERE id_pengguna=?", "i", $id);
        catat("hapus_pengguna", $u['username']);
        flash("Pengguna " . $u['username'] . " dihapus.");
    }
    header("Location: pengguna.php"); exit;
}
$daftar = semua("SELECT u.*, pr.nama_lengkap FROM pengguna u LEFT JOIN profil pr ON pr.id_pengguna = u.id_pengguna ORDER BY u.peran, u.username");
$judul = "Kelola pengguna";
include "../includes/header.php";
?>
<div class="kepala"><div><h1>Pengguna</h1><p>Ubah peran siswa menjadi mentor, atau hapus akun yang tidak diperlukan.</p></div></div>
<?php include "../includes/menu_admin.php"; ?>
<div class="tw"><table><thead><tr><th>Username</th><th>Nama</th><th>Email</th><th>Peran</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($daftar as $u): ?>
<tr><td><?= e($u['username']) ?></td><td><?= e($u['nama_lengkap']) ?></td><td><?= e($u['email']) ?></td><td><span class="st"><?= e($u['peran']) ?></span></td>
<td class="aksi">
<?php if ((int)$u['id_pengguna'] === $me['id']): ?>(akunmu)
<?php else: ?>
  <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$u['id_pengguna'] ?>"><input type="hidden" name="aksi" value="peran">
    <select name="peran" aria-label="Peran <?= e($u['username']) ?>"><?php foreach (['siswa', 'mentor', 'admin'] as $r): ?><option<?= $u['peran'] === $r ? " selected" : "" ?>><?= $r ?></option><?php endforeach; ?></select>
    <button class="mini">Simpan</button></form>
  <?= tombol_aksi((int)$u['id_pengguna'], 'hapus', 'Hapus', 'del', 'Hapus pengguna ' . $u['username'] . ' beserta datanya?') ?>
<?php endif; ?>
</td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php include "../includes/footer.php"; ?>
