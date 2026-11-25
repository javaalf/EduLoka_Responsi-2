<?php
// KELOLA BUKU, bagian DAFTAR dan HAPUS
// Tambah dan ubah ada di buku_form.php.
require_once "../includes/fungsi.php";
wajib_masuk('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? "") === 'hapus') {
    csrf_cek();
    $id = (int)($_POST['id'] ?? 0);
    $b = satu("SELECT * FROM kursus WHERE id_kursus=?", "i", $id);
    if ($b) {
        hapus_berkas($b['berkas_pdf']); hapus_berkas($b['gambar_sampul']);
        q("DELETE FROM kursus WHERE id_kursus=?", "i", $id);
        catat("hapus_buku", $b['judul']);
        flash("Buku \"" . $b['judul'] . "\" dihapus.");
    }
    header("Location: buku.php"); exit;
}
$daftar = semua("SELECT * FROM kursus ORDER BY kelas, judul");
$judul = "Kelola buku";
include "../includes/header.php";
?>
<div class="kepala"><div><h1>Buku</h1><p>Tambah, ubah, atau hapus buku di katalog.</p></div><a class="btn" href="buku_form.php">Tambah buku</a></div>
<?php include "../includes/menu_admin.php"; ?>
<?php if (!$daftar): ?><p class="kosong">Belum ada buku.</p><?php else: ?>
<div class="tw"><table><thead><tr><th>Sampul</th><th>Judul</th><th>Mapel</th><th>Kelas</th><th>Unduhan</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($daftar as $b): ?>
<tr><td><?php if ($b['gambar_sampul']): ?><img class="thumb" src="../<?= e($b['gambar_sampul']) ?>" alt="Sampul <?= e($b['judul']) ?>"><?php else: ?><span class="thumb" style="display:inline-block"></span><?php endif; ?></td>
<td><?= e($b['judul']) ?></td><td><?= e($b['mata_pelajaran']) ?></td><td><?= e($b['kelas']) ?></td><td><?= (int)$b['jumlah_unduh'] ?></td>
<td class="aksi"><a class="mini" href="buku_form.php?id=<?= (int)$b['id_kursus'] ?>">Ubah</a>
<?= tombol_aksi((int)$b['id_kursus'], 'hapus', 'Hapus', 'del', 'Hapus buku ini beserta berkasnya?') ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php include "../includes/footer.php"; ?>
