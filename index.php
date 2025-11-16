<?php
session_start();

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    if ($_POST['username'] === "admin" && $_POST['password'] === "123") {
        $_SESSION['login'] = true;
        $_SESSION['kontak'] = $_SESSION['kontak'] ?? [];
    } else {
        $error = "Login gagal";
    }
}

if (!isset($_SESSION['login'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <form method="POST" class="bg-gray-800 p-6 rounded-xl w-80 shadow-lg">
        <h2 class="text-xl font-semibold mb-4 text-center">Login</h2>
        <?php if (!empty($error)) echo "<p class='text-red-400 mb-2'>$error</p>"; ?>
        <input name="username" class="w-full p-2 rounded bg-gray-700 mb-2" placeholder="Username">
        <input type="password" name="password" class="w-full p-2 rounded bg-gray-700 mb-4" placeholder="Password">
        <button name="login" class="w-full bg-blue-600 hover:bg-blue-700 p-2 rounded">Masuk</button>
    </form>
</body>
</html>
<?php
exit;
}

if (isset($_POST['add'])) {
    $nama = trim($_POST['nama']);
    $telp = trim($_POST['telp']);
    if ($nama && $telp) {
        $_SESSION['kontak'][] = ["nama" => $nama, "telp" => $telp];
    }
}

if (isset($_POST['edit_index'])) {
    $i = $_POST['edit_index'];
    $_SESSION['kontak'][$i]["nama"] = $_POST['edit_nama'];
    $_SESSION['kontak'][$i]["telp"] = $_POST['edit_telp'];
}

if (isset($_GET['delete'])) {
    $d = $_GET['delete'];
    unset($_SESSION['kontak'][$d]);
    $_SESSION['kontak'] = array_values($_SESSION['kontak']);
}

$edit = isset($_GET['edit']) ? $_GET['edit'] : -1;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen">

    <div class="max-w-3xl mx-auto py-10">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Manajemen Kontak</h1>
            <form method="POST">
                <button name="logout" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700">Logout</button>
            </form>
        </div>

        <?php if ($edit >= 0): ?>
        <form method="POST" class="bg-gray-800 p-6 rounded-xl mb-6">
            <h2 class="text-xl mb-3">Edit Kontak</h2>
            <input type="hidden" name="edit_index" value="<?= $edit ?>">
            <input name="edit_nama" value="<?= $_SESSION['kontak'][$edit]['nama'] ?>" class="w-full p-2 mb-2 bg-gray-700 rounded" required>
            <input name="edit_telp" value="<?= $_SESSION['kontak'][$edit]['telp'] ?>" class="w-full p-2 mb-4 bg-gray-700 rounded" required>
            <button class="bg-yellow-600 px-4 py-2 rounded hover:bg-yellow-700">Simpan</button>
        </form>
        <?php else: ?>
        <form method="POST" class="bg-gray-800 p-6 rounded-xl mb-6">
            <h2 class="text-xl mb-3">Tambah Kontak</h2>
            <input name="nama" placeholder="Nama" class="w-full p-2 mb-2 bg-gray-700 rounded" required>
            <input name="telp" placeholder="No. Telepon" class="w-full p-2 mb-4 bg-gray-700 rounded" required>
            <button name="add" class="bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">Tambah</button>
        </form>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php foreach ($_SESSION['kontak'] as $i => $k): ?>
            <div class="bg-gray-800 p-4 rounded-xl shadow">
                <h3 class="text-lg font-semibold"><?= $k['nama'] ?></h3>
                <p class="text-gray-300 mb-3"><?= $k['telp'] ?></p>
                <div class="flex gap-2">
                    <a href="?edit=<?= $i ?>" class="px-3 py-1 bg-yellow-600 rounded hover:bg-yellow-700 text-sm">Edit</a>
                    <a href="?delete=<?= $i ?>" onclick="return confirm('Hapus kontak?')" class="px-3 py-1 bg-red-600 rounded hover:bg-red-700 text-sm">Hapus</a>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

    </div>

</body>
</html>