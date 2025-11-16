<?php
include "data.php";
require_login();

$id = $_GET['id'] ?? null;
if ($id === null || !isset($_SESSION['kontak'][$id])) {
    header("Location: daftar.php"); exit;
}
$k = $_SESSION['kontak'][$id];
$err = [];

if (isset($_POST['update'])) {
    $nama = trim($_POST['nama']); $email = trim($_POST['email']); $telp = trim($_POST['telp']);
    $kota = trim($_POST['kota']); $kloter = trim($_POST['kloter']);
    $gender = $_POST['gender'] ?? ''; $age = (int)($_POST['age'] ?? 0);
    $high_risk = isset($_POST['high_risk']) ? true : false;

    if ($nama=='' || $email=='' || $telp=='') $err[] = "Nama, email, telepon wajib.";
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $err[] = "Email tidak valid.";

    if (!empty($_FILES['photo']['name'])) {
        $allowed=['jpg','jpeg','png']; $ext=strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if(!in_array($ext,$allowed)) $err[]="Foto hanya JPG/PNG.";
        if($_FILES['photo']['size']>2*1024*1024) $err[]="Foto maksimal 2MB.";
        if(empty($err)){
            $fn=time().'_'.uniqid().'.'.$ext;
            $target=__DIR__.'/uploads/'.$fn;
            if(move_uploaded_file($_FILES['photo']['tmp_name'],$target)){
                // hapus file lama jika ada
                if (!empty($k['photo']) && file_exists(__DIR__ . '/' . $k['photo'])) unlink(__DIR__ . '/' . $k['photo']);
                $k['photo'] = 'uploads/'.$fn;
            }
        }
    }

    if (empty($err)) {
        $_SESSION['kontak'][$id] = [
            "nama"=>$nama,"email"=>$email,"telp"=>$telp,
            "kota"=>$kota,"kloter"=>$kloter,"gender"=>$gender,
            "age"=>$age,"high_risk"=>$high_risk,"photo"=>$k['photo'] ?? ''
        ];
        header("Location: daftar.php"); exit;
    }
}

include "header.php";
?>
<div class="bg-white p-6 rounded-xl shadow border-l-4 border-yellow-400">
  <h2 class="text-2xl font-bold text-emerald-800 mb-4">Edit Kontak</h2>
  <?php if ($err): ?><div class="bg-red-100 p-3 rounded mb-4"><?php foreach($err as $e) echo "<p class='text-red-700'>• $e</p>"; ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div><label>Nama</label><input name="nama" class="w-full p-2 rounded" value="<?= htmlspecialchars($k['nama']) ?>"></div>
    <div><label>Email</label><input name="email" class="w-full p-2 rounded" value="<?= htmlspecialchars($k['email']) ?>"></div>
    <div><label>Telepon</label><input name="telp" class="w-full p-2 rounded" value="<?= htmlspecialchars($k['telp']) ?>"></div>
    <div><label>Kota</label><input name="kota" class="w-full p-2 rounded" value="<?= htmlspecialchars($k['kota'] ?? '') ?>"></div>
    <div><label>Kloter</label><input name="kloter" class="w-full p-2 rounded" value="<?= htmlspecialchars($k['kloter'] ?? '') ?>"></div>
    <div>
      <label>Gender</label>
      <select name="gender" class="w-full p-2 rounded">
        <option value="">Pilih</option>
        <option value="L" <?= ($k['gender']??'')==='L'?'selected':'' ?>>Laki-laki</option>
        <option value="P" <?= ($k['gender']??'')==='P'?'selected':'' ?>>Perempuan</option>
      </select>
    </div>
    <div><label>Usia</label><input type="number" name="age" class="w-full p-2 rounded" value="<?= htmlspecialchars($k['age'] ?? '') ?>"></div>
    <div class="flex items-center gap-2"><input type="checkbox" id="hr" name="high_risk" <?= !empty($k['high_risk']) ? 'checked' : '' ?>><label for="hr">High Risk</label></div>
    <div>
      <label>Foto (kosongkan jika tidak ingin ganti)</label>
      <?php if (!empty($k['photo'])): ?><div class="mb-2"><img src="<?= $k['photo'] ?>" class="photo-thumb"></div><?php endif; ?>
      <input type="file" name="photo" accept=".jpg,.jpeg,.png">
    </div>
    <div class="md:col-span-2">
      <button name="update" class="bg-yellow-500 px-4 py-2 rounded">Update</button>
      <a href="daftar.php" class="ml-2 text-gray-600">Batal</a>
    </div>
  </form>
</div>
<?php include "footer.php"; ?>