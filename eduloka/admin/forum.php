<?php
// MODERASI FORUM Admin dapat menghapus diskusi yang tidak pantas. Menghapus satu balasan dilakukan dari halaman detail diskusi.
require_once "../includes/fungsi.php";
wajib_masuk('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? "") === 'hapus') {
    csrf_cek();
    $d = satu("SELECT judul FROM diskusi WHERE id_diskusi=?", "i", (int)($_POST['id'] ?? 0));
    if ($d) {
        q("DELETE FROM diskusi WHERE id_diskusi=?", "i", (int)$_POST['id']);
        catat("moderasi_diskusi", $d['judul']);
        flash("Diskusi dihapus.");
    }
    header("Location: forum.php"); exit;
}
$utas = semua("SELECT d.*, u.username, (SELECT COUNT(*) FROM balasan_diskusi r WHERE r.id_diskusi = d.id_diskusi) AS jml
               FROM diskusi d JOIN pengguna u ON u.id_pengguna = d.id_pengguna ORDER BY d.dibuat_pada DESC");
$judul = "Moderasi forum";
include "../includes/header.php";
?>
<div class="kepala"><div><h1>Moderasi forum</h1><p>Hapus diskusi yang tidak pantas. Untuk menghapus satu balasan, buka diskusinya.</p></div></div>
<?php include "../includes/menu_admin.php"; ?>
<?php if (!$utas): ?><p class="kosong">Belum ada diskusi.</p><?php else: ?>
<div class="tw"><table><thead><tr><th>Pertanyaan</th><th>Penanya</th><th>Tanggal</th><th>Jawaban</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($utas as $t): ?>
<tr><td><?= e($t['judul']) ?></td><td><?= e($t['username']) ?></td><td><?= tgl_id($t['dibuat_pada']) ?></td><td><?= (int)$t['jml'] ?></td>
<td class="aksi"><a class="mini" href="../forum_detail.php?id=<?= (int)$t['id_diskusi'] ?>">Buka</a>
<?= tombol_aksi((int)$t['id_diskusi'], 'hapus', 'Hapus', 'del', 'Hapus diskusi ini beserta semua balasannya?') ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php include "../includes/footer.php"; ?>
