<?php
// HALAMAN DAFTAR AKUN.
// Alur: isi email, username, password lalu klik Daftar. Data divalidasi, password di-hash, disimpan,
require_once "includes/fungsi.php";
if (pengguna()) { header("Location: index.php"); exit; }
$err = []; $email = $username = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $email = strtolower(trim($_POST['email'] ?? ""));
    $username = strtolower(trim($_POST['username'] ?? ""));
    $password = $_POST['password'] ?? "";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) $err['email'] = "Tulis email yang valid, contoh nama@email.com.";
    elseif (satu("SELECT 1 AS x FROM pengguna WHERE email=?", "s", $email)) $err['email'] = "Email ini sudah terdaftar.";
    if (!preg_match('/^[a-z0-9_]{4,16}$/', $username)) $err['username'] = "Username 4-16 karakter: huruf, angka, atau garis bawah.";
    elseif ($username === 'admin' || satu("SELECT 1 AS x FROM pengguna WHERE username=?", "s", $username)) $err['username'] = "Username sudah dipakai. Coba yang lain.";
    if (strlen($password) < 6) $err['password'] = "Password minimal 6 karakter.";
    if (!$err) {
        q("INSERT INTO pengguna (email, username, kata_sandi, peran) VALUES (?,?,?,'siswa')", "sss", $email, $username, password_hash($password, PASSWORD_DEFAULT));
        $id = mysqli_insert_id($koneksi);
        q("INSERT INTO profil (id_pengguna, nama_lengkap) VALUES (?,?)", "is", $id, $username);
        catat("daftar", $username);
        flash("Akun berhasil dibuat. Silakan masuk dengan username dan password tadi.");
        header("Location: login.php"); exit;
    }
}
$judul = "Daftar";
include "includes/header.php";
?>
<section class="auth">
  <?php include "includes/auth_samping.php"; ?>
  <div class="auth-box">
    <form method="post" onsubmit="return cekDaftar()" novalidate>
      <h2>Buat akun</h2>
      <?= csrf_field() ?>
      <div class="f"><label for="email">Email</label><input id="email" name="email" type="email" value="<?= e($email) ?>" autocomplete="email"><span class="err" id="err-email"><?= e($err['email'] ?? "") ?></span></div>
      <div class="f"><label for="username">Username</label><input id="username" name="username" value="<?= e($username) ?>" autocomplete="username"><span class="err" id="err-username"><?= e($err['username'] ?? "") ?></span></div>
      <div class="f"><label for="password">Buat password</label><input id="password" name="password" type="password" autocomplete="new-password"><span class="err" id="err-password"><?= e($err['password'] ?? "") ?></span></div>
      <button class="btn" type="submit">Daftar</button>
      <p class="petunjuk">Sudah punya akun? <a href="login.php">Masuk</a></p>
    </form>
  </div>
</section>
<?php include "includes/footer.php"; ?>
