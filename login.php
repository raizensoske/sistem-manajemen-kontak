<?php
include "data.php";

// logout jika tombol logout dari header
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$err = "";
if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = trim($_POST['password']);
    if ($u === "admin" && $p === "123456") {
        $_SESSION['logged_in'] = true;
        header("Location: daftar.php");
        exit;
    } else $err = "Username atau password salah";
}
include "header.php";
?>
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8 mt-8 border-t-4 border-emerald-700">
  <h2 class="text-center text-2xl font-bold text-emerald-800 mb-4">Login Admin</h2>
  <?php if ($err): ?><div class="bg-red-100 text-red-700 p-2 rounded mb-3"><?= $err ?></div><?php endif; ?>
  <form method="POST" class="space-y-3">
    <input name="username" placeholder="Username" class="w-full p-3 rounded border bg-gray-100" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    <input name="password" type="password" placeholder="Password" class="w-full p-3 rounded border bg-gray-100">
    <button name="login" class="w-full bg-emerald-700 text-white p-3 rounded">Masuk</button>
  </form>
</div>
<?php include "footer.php"; ?>