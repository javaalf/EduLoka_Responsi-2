<?php
// UNDUH PDF. Tidak menampilkan halaman: menambah hitungan unduhan lalu mengirim berkas PDF ke browser.
require_once "includes/fungsi.php";
$id = (int)($_GET['id'] ?? 0);
$b = satu("SELECT * FROM kursus WHERE id_kursus=?", "i", $id);
if (!$b) { http_response_code(404); die("Buku tidak ditemukan."); }
q("UPDATE kursus SET jumlah_unduh = jumlah_unduh + 1 WHERE id_kursus=?", "i", $id);
catat("unduh_buku", $b['judul']);
if (preg_match('~^https?://~', $b['berkas_pdf'])) { header("Location: " . $b['berkas_pdf']); exit; }
$f = __DIR__ . "/" . $b['berkas_pdf'];
if (!is_file($f)) die("Berkas PDF belum tersedia di server.");
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . preg_replace('/[^A-Za-z0-9_-]+/', '-', $b['judul']) . ".pdf\"");
header("Content-Length: " . filesize($f));
readfile($f);


