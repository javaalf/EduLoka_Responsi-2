<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../config/koneksi.php";
$BASE = (basename(dirname($_SERVER['SCRIPT_NAME'])) === 'admin') ? '../' : '';
function e($teks) { return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8'); }
function q($sql, $tipe = "", ...$param) {
    global $koneksi;
    $st = mysqli_prepare($koneksi, $sql);
    if ($tipe !== "") mysqli_stmt_bind_param($st, $tipe, ...$param);
    mysqli_stmt_execute($st);
    $hasil = mysqli_stmt_get_result($st);
    return $hasil ?: $st;
}
// semua() = jalankan SELECT dan kembalikan SEMUA baris sebagai array (kosong bila tidak ada).
function semua($sql, $tipe = "", ...$param) {
    $r = q($sql, $tipe, ...$param);
    return ($r instanceof mysqli_result) ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
}
// satu() = jalankan SELECT dan kembalikan SATU baris pertama saja, atau null bila tidak ada.
function satu($sql, $tipe = "", ...$param) {
    $a = semua($sql, $tipe, ...$param);
    return $a[0] ?? null;
}
function pengguna() { return $_SESSION['pengguna'] ?? null; }
function wajib_masuk($peran = null) {
    global $BASE;
    $p = pengguna();
    if (!$p) { flash("Silakan masuk dulu.", "err"); header("Location: {$BASE}login.php"); exit; }
    if ($peran && !in_array($p['peran'], (array)$peran)) {
        flash("Halaman itu bukan untuk peranmu.", "err");
        header("Location: {$BASE}index.php"); exit;
    }
}
// flash() = simpan pesan notifikasi di sesi. Pesan tampil SEKALI di halaman berikutnya (setelah redirect).
function flash($pesan, $jenis = "ok") { $_SESSION['flash'] = [$pesan, $jenis]; }
function tampil_flash() {
    if (!empty($_SESSION['flash'])) {
        [$m, $j] = $_SESSION['flash']; unset($_SESSION['flash']);
        echo "<div class='flash $j' role='status'>" . e($m) . "</div>";
    }
}
function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return "<input type='hidden' name='csrf' value='{$_SESSION['csrf']}'>";
}
// Bandingkan token dari form dengan token di sesi. Kalau tidak cocok, proses dihentikan.
function csrf_cek() {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        http_response_code(419);
        die("Sesi formulir tidak valid. Kembali ke halaman sebelumnya lalu coba lagi.");
    }
}
// Tulis satu baris ke tabel log_aktivitas (siapa melakukan apa). Dipakai untuk laporan dan riwayat.
function catat($aksi, $ket = "") {
    $p = pengguna();
    q("INSERT INTO log_aktivitas (id_pengguna, aksi, keterangan) VALUES (?,?,?)", "iss", $p['id'] ?? null, $aksi, $ket);
}

function url_berkas($p) { global $BASE; return preg_match('~^https?://~', $p) ? $p : $BASE . $p; }
// Hapus berkas upload dari folder uploads/ (abaikan kalau isinya tautan https atau kosong).
function hapus_berkas($p) {
    if ($p && !preg_match('~^https?://~', $p)) { $f = __DIR__ . "/../" . $p; if (is_file($f)) unlink($f); }
}

function unggah($field, $boleh, $maks, $folder, $wajib = false) {
    if (empty($_FILES[$field]['name'])) return [null, $wajib ? "Berkas wajib dipilih." : null];
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) return [null, "Upload gagal (kode {$f['error']}). Cek upload_max_filesize di php.ini."];
    if ($f['size'] > $maks) return [null, "Ukuran berkas melebihi " . round($maks / 1048576) . " MB."];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset($boleh[$ext]) || !in_array($mime, $boleh[$ext])) return [null, "Jenis berkas tidak diizinkan."];
    $dir = __DIR__ . "/../uploads/$folder/";
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $nama = bin2hex(random_bytes(8)) . "." . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . $nama)) return [null, "Gagal menyimpan berkas ke server."];
    return ["uploads/$folder/$nama", null];
}
// Daftar jenis berkas yang diizinkan: PDF.
const TIPE_PDF = ['pdf' => ['application/pdf']];
// Daftar jenis berkas yang diizinkan: gambar JPG, PNG, WebP.
const TIPE_GAMBAR = ['jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'], 'webp' => ['image/webp']];


function tgl_id($t) {
    $b = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
    $x = strtotime($t);
    return date("j", $x) . " " . $b[(int)date("n", $x)] . " " . date("Y", $x);
}
// Cetak satu kartu buku (sampul, judul, penulis, tombol detail). Dipakai di beranda dan katalog.
function kartu_buku($b) {
    global $BASE;
    $warna = ["#2f6a31", "#b5532f", "#7a5a1c", "#1f5f6b", "#5b4a8a"][$b['id_kursus'] % 5];
    $id = (int)$b['id_kursus'];
    echo "<article class='buku'><a class='sampul' href='{$BASE}detail.php?id=$id' style='background:$warna' aria-label='Detail " . e($b['judul']) . "'>";
    if ($b['gambar_sampul']) echo "<img src='" . e($BASE . $b['gambar_sampul']) . "' alt='Sampul buku " . e($b['judul']) . "' loading='lazy'>";
    else echo "<small>SD/MI Kelas " . e($b['kelas']) . "</small><strong>" . e($b['judul']) . "</strong>";
    echo "</a><h3>" . e($b['judul']) . " <span class='kls'>Kelas " . e($b['kelas']) . "</span></h3><p>" . e($b['penulis']) . "</p>";
    echo "<a class='btn' href='{$BASE}detail.php?id=$id'>Lihat detail</a></article>";
}

// Ambil daftar pemesanan bimbingan lengkap dengan nama siswa, nama mentor, dan tautan meeting.
// Parameter $where bisa diisi syarat tambahan (pakai ? untuk nilai), mis. b.id_siswa=?. Dipakai jadwal, riwayat, dan admin.
function daftar_pemesanan($where = "1=1", $tipe = "", ...$param) {
    return semua("SELECT b.*, j.tautan_pertemuan, j.lokasi, j.selesai_pada,
        COALESCE(NULLIF(ps.nama_lengkap,''), s.username) AS siswa,
        COALESCE(NULLIF(pm.nama_lengkap,''), m.username) AS mentor
        FROM pemesanan_bimbingan b
        JOIN pengguna s ON s.id_pengguna = b.id_siswa
        JOIN pengguna m ON m.id_pengguna = b.id_mentor
        LEFT JOIN profil ps ON ps.id_pengguna = s.id_pengguna
        LEFT JOIN profil pm ON pm.id_pengguna = m.id_pengguna
        LEFT JOIN jadwal j ON j.id_pemesanan = b.id_pemesanan
        WHERE $where ORDER BY b.tanggal DESC, b.jam DESC", $tipe, ...$param);
}
// Buat form kecil berisi satu tombol POST (misal Hapus). Bila $tanya diisi, muncul kotak konfirmasi dulu lewat JavaScript.
function tombol_aksi($id, $aksi, $label, $kelas = "", $tanya = "") {
    $on = $tanya ? " onsubmit=\"return konfirmasi('" . e($tanya) . "')\"" : "";
    return "<form method='post' class='inline'$on>" . csrf_field() .
        "<input type='hidden' name='id' value='$id'><input type='hidden' name='aksi' value='$aksi'><button class='mini $kelas'>$label</button></form>";
}
// Tombol yang tampil di tiap baris pemesanan menurut statusnya:
// menunggu = isi tautan lalu Konfirmasi, dikonfirmasi = Tandai selesai, keduanya bisa Batalkan.
function form_aksi($b) {
    $id = (int)$b['id_pemesanan']; $h = "";
    if ($b['status'] === 'menunggu') {
        $h .= "<form method='post' class='inline'>" . csrf_field() . "<input type='hidden' name='id' value='$id'><input type='hidden' name='aksi' value='konfirmasi'>" .
            "<input name='tautan' placeholder='Tautan Meet/Zoom' aria-label='Tautan meeting'><input name='lokasi' placeholder='atau lokasi' aria-label='Lokasi'><button class='mini'>Konfirmasi</button></form>";
    }
    if ($b['status'] === 'dikonfirmasi') $h .= tombol_aksi($id, 'selesai', 'Tandai selesai');
    if (in_array($b['status'], ['menunggu', 'dikonfirmasi'])) $h .= tombol_aksi($id, 'batal', 'Batalkan', 'del', 'Batalkan sesi ini?');
    return $h;
}
// Menangani aksi mentor/admin pada pemesanan: konfirmasi, selesai, batal. Dipakai di jadwal.php dan admin/pemesanan.php.
function proses_pemesanan() {
    csrf_cek();
    $p = pengguna(); $id = (int)($_POST['id'] ?? 0); $aksi = $_POST['aksi'] ?? "";
    $b = satu("SELECT * FROM pemesanan_bimbingan WHERE id_pemesanan=?", "i", $id);
    if (!$b || !in_array($p['peran'], ['mentor', 'admin']) || ($p['peran'] === 'mentor' && $b['id_mentor'] != $p['id'])) {
        flash("Pemesanan tidak ditemukan.", "err"); return;
    }
    if ($aksi === 'konfirmasi') {
        $tautan = trim($_POST['tautan'] ?? ""); $lokasi = trim($_POST['lokasi'] ?? "");
        if ($tautan !== "" && !preg_match('~^https?://\S+$~', $tautan)) { flash("Tautan meeting harus diawali http:// atau https://", "err"); return; }
        if ($tautan === "" && $lokasi === "") { flash("Isi tautan meeting atau lokasi sesi.", "err"); return; }
        q("UPDATE pemesanan_bimbingan SET status='dikonfirmasi' WHERE id_pemesanan=? AND status='menunggu'", "i", $id);
        q("INSERT INTO jadwal (id_pemesanan, tautan_pertemuan, lokasi) VALUES (?,?,?)
           ON DUPLICATE KEY UPDATE tautan_pertemuan=VALUES(tautan_pertemuan), lokasi=VALUES(lokasi)", "iss", $id, $tautan, $lokasi);
    } elseif ($aksi === 'selesai') {
        q("UPDATE pemesanan_bimbingan SET status='selesai' WHERE id_pemesanan=? AND status='dikonfirmasi'", "i", $id);
        q("UPDATE jadwal SET selesai_pada=NOW() WHERE id_pemesanan=?", "i", $id);
    } elseif ($aksi === 'batal') {
        q("UPDATE pemesanan_bimbingan SET status='dibatalkan' WHERE id_pemesanan=? AND status IN ('menunggu','dikonfirmasi')", "i", $id);
    } else return;
    catat("pemesanan_$aksi", "ID $id");
    flash("Pemesanan diperbarui.");
}
// Cetak tabel pemesanan. Bila $aksi = true, tampil kolom tombol aksi (untuk mentor dan admin).
function tabel_pemesanan($rows, $aksi = false) {
    if (!$rows) { echo "<p class='kosong'>Belum ada data di sini.</p>"; return; }
    echo "<div class='tw'><table><thead><tr><th>Tanggal</th><th>Jam</th><th>Siswa</th><th>Mentor</th><th>Topik</th><th>Status</th><th>Meeting</th>" . ($aksi ? "<th>Aksi</th>" : "") . "</tr></thead><tbody>";
    foreach ($rows as $r) {
        $mt = $r['tautan_pertemuan'] ? "<a href='" . e($r['tautan_pertemuan']) . "' target='_blank' rel='noopener'>Buka meeting</a>" : e($r['lokasi'] ?: "-");
        echo "<tr><td>" . tgl_id($r['tanggal']) . "</td><td>" . substr($r['jam'], 0, 5) . "</td><td>" . e($r['siswa']) . "</td><td>" . e($r['mentor']) .
            "</td><td>" . e($r['topik']) . "</td><td><span class='st {$r['status']}'>" . e($r['status']) . "</span></td><td>$mt</td>" .
            ($aksi ? "<td class='aksi'>" . form_aksi($r) . "</td>" : "") . "</tr>";
    }
    echo "</tbody></table></div>";
}
