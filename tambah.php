<?php
include "data.php";
require_login();

$err = [];
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $telp = trim($_POST['telp']);
    $kota = trim($_POST['kota']);
    $kloter = trim($_POST['kloter']);
    $gender = $_POST['gender'] ?? '';
    $age = (int)($_POST['age'] ?? 0);
    $high_risk = isset($_POST['high_risk']) ? true : false;

    if ($nama === '' || $email === '' || $telp === '') $err[] = "Nama, email, dan telepon wajib diisi.";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err[] = "Email tidak valid.";

    // handle photo upload
    $photoPath = '';
    if (!empty($_FILES['photo']['name'])) {
        $allowed = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) $err[] = "Foto hanya JPG/PNG.";
        if ($_FILES['photo']['size'] > 2*1024*1024) $err[] = "Foto maksimal 2MB.";
        if (empty($err)) {
            $fn = time() . '_' . uniqid() . '.' . $ext;
            $target = __DIR__ . '/uploads/' . $fn;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $photoPath = 'uploads/' . $fn;
            }
        }
    }

    if (empty($err)) {
        $_SESSION['kontak'][] = [
            "nama"=>$nama, "email"=>$email, "telp"=>$telp,
            "kota"=>$kota, "kloter"=>$kloter, "gender"=>$gender,
            "age"=>$age, "high_risk"=>$high_risk, "photo"=>$photoPath
        ];
        header("Location: daftar.php");
        exit;
    }
}
include "header.php";
?>
<div class="bg-white p-6 rounded-xl shadow border-l-4 border-emerald-600">
  <h2 class="text-2xl font-bold text-emerald-800 mb-4">Tambah Kontak Jamaah</h2>
  <?php if ($err): ?>
    <div class="bg-red-100 p-3 rounded mb-4 text-red-700"><?php foreach($err as $e) echo "<p>• $e</p>"; ?></div>
  <?php endif; ?>
  <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label>Nama</label>
      <input name="nama" class="w-full p-2 rounded border bg-gray-100" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
    </div>
    <div>
      <label>Email</label>
      <input name="email" class="w-full p-2 rounded border bg-gray-100" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>
    <div>
      <label>Telepon</label>
      <input name="telp" class="w-full p-2 rounded border bg-gray-100" value="<?= htmlspecialchars($_POST['telp'] ?? '') ?>" required>
    </div>
    <div>
      <label>Kota</label>
      <input name="kota" class="w-full p-2 rounded border bg-gray-100" value="<?= htmlspecialchars($_POST['kota'] ?? '') ?>">
    </div>
    <div>
      <label>Kloter</label>
      <input name="kloter" class="w-full p-2 rounded border bg-gray-100" value="<?= htmlspecialchars($_POST['kloter'] ?? '') ?>">
    </div>
    <div>
      <label>Gender</label>
      <select name="gender" class="w-full p-2 rounded bg-gray-100">
        <option value="">Pilih</option>
        <option value="L">Laki-laki</option>
        <option value="P">Perempuan</option>
      </select>
    </div>
    <div>
      <label>Usia</label>
      <input type="number" name="age" min="0" class="w-full p-2 rounded border bg-gray-100" value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
    </div>
    <div class="flex items-center gap-2">
      <input type="checkbox" id="high_risk" name="high_risk" <?= isset($_POST['high_risk']) ? 'checked' : '' ?>>
      <label for="high_risk">High Risk</label>
    </div>
    <div>
      <label>Foto (opsional, max 2MB)</label>
      <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="w-full">
    </div>

    <div class="md:col-span-2">
      <button name="simpan" class="bg-emerald-700 text-white px-4 py-2 rounded">Simpan</button>
      <a href="daftar.php" class="ml-2 text-sm text-gray-600">Kembali</a>
    </div>
  </form>
</div>
<?php include "footer.php"; ?>