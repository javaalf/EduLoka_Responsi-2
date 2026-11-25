<?php
// HALAMAN LOGIN.
// Memeriksa username dan password, lalu menyimpan identitas pengguna di sesi. Admin dialihkan ke panel admin.
require_once "includes/fungsi.php";
if (pengguna()) { header("Location: index.php"); exit; }
$galat = ""; $username = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $username = strtolower(trim($_POST['username'] ?? ""));
    $password = $_POST['password'] ?? "";
    $u = satu("SELECT * FROM pengguna WHERE username=?", "s", $username);
    if ($u && password_verify($password, $u['kata_sandi'])) {
        session_regenerate_id(true);
        $_SESSION['pengguna'] = ['id' => (int)$u['id_pengguna'], 'username' => $u['username'], 'peran' => $u['peran']];
        catat("login", $u['username']);
        header("Location: " . ($u['peran'] === 'admin' ? "admin/index.php" : "index.php")); exit;
    }
    $galat = "Username atau password salah. Belum punya akun? Daftar dulu.";
}
$judul = "Masuk";
include "includes/header.php";
?>
<section class="auth">
  <?php include "includes/auth_samping.php"; ?>
  <div class="auth-box">
    <form method="post" onsubmit="return cekLogin()" novalidate>
      <h2>Masuk</h2>
      <?= csrf_field() ?>
      <?php if ($galat): ?><div class="flash err"><?= e($galat) ?></div><?php endif; ?>
      <div class="f"><label for="username">Username</label><input id="username" name="username" value="<?= e($username) ?>" autocomplete="username"><span class="err" id="err-username"></span></div>
      <div class="f"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password"><span class="err" id="err-password"></span></div>
      <button class="btn" type="submit">Masuk</button>
      <p class="petunjuk">Belum punya akun? <a href="daftar.php">Daftar dulu</a></p>
    </form>
  </div>
</section>
<?php include "includes/footer.php"; ?>
