<?php
include "data.php";
require_login();

// ambil parameter UI
$q = trim($_GET['q'] ?? '');
$sort = $_GET['sort'] ?? 'nama_asc'; // nama_asc, nama_desc, kota_asc, kloter_asc
$filter_gender = $_GET['gender'] ?? '';
$filter_lansia = isset($_GET['lansia']) ? true : false;
$filter_high = isset($_GET['high']) ? true : false;
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;

// filter & search
$all = $_SESSION['kontak'];
$filtered = array_filter($all, function($k) use($q, $filter_gender, $filter_lansia, $filter_high) {
    // ambil nilai dengan aman (jika key tidak ada, pakai string kosong / 0)
    $nama   = strtolower($k['nama']   ?? '');
    $email  = strtolower($k['email']  ?? '');
    $telp   = strtolower($k['telp']   ?? '');
    $kota   = strtolower($k['kota']   ?? '');
    $kloter = strtolower($k['kloter'] ?? '');

    // search
    if ($q) {
        $hay = $nama . ' ' . $email . ' ' . $telp . ' ' . $kota . ' ' . $kloter;
        if (strpos($hay, strtolower($q)) === false) return false;
    }

    if ($filter_gender && (($k['gender'] ?? '') !== $filter_gender)) return false;
    if ($filter_lansia && (int)($k['age'] ?? 0) < 60) return false; // lansia >=60
    if ($filter_high && empty($k['high_risk'])) return false;
    return true;
});

// sort
usort($filtered, function($a, $b) use($sort){
    $a_n = strtolower($a['nama']   ?? '');
    $b_n = strtolower($b['nama']   ?? '');
    $a_kota = strtolower($a['kota'] ?? '');
    $b_kota = strtolower($b['kota'] ?? '');
    $a_kl = strtolower($a['kloter'] ?? '');
    $b_kl = strtolower($b['kloter'] ?? '');

    if ($sort === 'nama_asc') return strcmp($a_n, $b_n);
    if ($sort === 'nama_desc') return strcmp($b_n, $a_n);
    if ($sort === 'kota_asc') return strcmp($a_kota, $b_kota);
    if ($sort === 'kloter_asc') return strcmp($a_kl, $b_kl);
    return 0;
});

// pagination
$total = count($filtered);
$pages = max(1, ceil($total / $perPage));
$start = ($page - 1) * $perPage;
$visible = array_slice($filtered, $start, $perPage);

include "header.php";
?>

<div class="flex justify-between items-center mb-4">
  <div class="flex gap-3">
    <form method="GET" class="flex gap-2">
      <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama/email/kota/telepon" class="p-2 rounded border bg-white">
      <button class="bg-emerald-700 text-white px-3 rounded">Cari</button>
    </form>
    <a href="daftar.php" class="px-3 py-2 bg-gray-100 rounded">Reset</a>
  </div>

  <div class="flex gap-2">
    <form method="GET" class="flex gap-2 items-center">
      <!-- keep other params when sorting -->
      <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
      <select name="sort" onchange="this.form.submit()" class="p-2 rounded border bg-white">
        <option value="nama_asc" <?= $sort==='nama_asc'?'selected':'' ?>>Nama A→Z</option>
        <option value="nama_desc" <?= $sort==='nama_desc'?'selected':'' ?>>Nama Z→A</option>
        <option value="kota_asc" <?= $sort==='kota_asc'?'selected':'' ?>>Kota A→Z</option>
        <option value="kloter_asc" <?= $sort==='kloter_asc'?'selected':'' ?>>Kloter A→Z</option>
      </select>
    </form>
  </div>
</div>

<!-- filters -->
<form method="GET" class="flex gap-3 items-center mb-4 flex-wrap">
  <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">
  <label class="flex items-center gap-2">
    <input type="checkbox" name="lansia" <?= $filter_lansia ? 'checked' : '' ?>> Lansia (>=60)
  </label>
  <label class="flex items-center gap-2">
    <input type="checkbox" name="high" <?= $filter_high ? 'checked' : '' ?>> High Risk
  </label>
  <label>
    Gender:
    <select name="gender" class="p-2 rounded border bg-white">
      <option value="">Semua</option>
      <option value="L" <?= $filter_gender==='L'?'selected':'' ?>>Laki-laki</option>
      <option value="P" <?= $filter_gender==='P'?'selected':'' ?>>Perempuan</option>
    </select>
  </label>
  <label>
    <button class="bg-emerald-700 text-white px-3 py-1 rounded">Filter</button>
  </label>

  <label class="ml-auto flex items-center gap-2">
    <button id="exportPdfBtn" type="button" class="bg-yellow-500 px-3 py-1 rounded">Export PDF</button>
  </label>
</form>

<?php if ($total==0): ?><p class="text-gray-600">Tidak ada data.</p><?php endif; ?>

<div id="contact-list" class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <?php foreach ($visible as $idx => $k): 
     // need original index to build edit/hapus link: find index in original session array
     $originalIndex = array_search($k, $_SESSION['kontak']);
  ?>
    <div class="bg-white p-5 rounded-xl shadow border-l-4 border-yellow-400">
      <div class="flex gap-4">
        <div>
          <?php if (!empty($k['photo']) && file_exists(__DIR__ . '/' . $k['photo'])): ?>
            <img src="<?= $k['photo'] ?>" class="photo-thumb" alt="foto">
          <?php else: ?>
            <div class="w-20 h-20 bg-gray-100 flex items-center justify-center rounded text-gray-400">No Photo</div>
          <?php endif; ?>
        </div>
        <div class="flex-1">
          <h3 class="text-xl font-bold text-emerald-800"><?= htmlspecialchars($k['nama']) ?></h3>
          <p class="text-gray-600">📧 <?= htmlspecialchars($k['email'] ?? '-') ?></p>
          <p class="text-gray-600">📞 <?= htmlspecialchars($k['telp'] ?? '-') ?></p>
          <p class="text-gray-600">🏙 <?= htmlspecialchars($k['kota'] ?? '-') ?> · Kloter: <?= htmlspecialchars($k['kloter'] ?? '-') ?></p>
          <p class="text-gray-600">Gender: <?= ($k['gender']??'') === 'L' ? 'Laki' : (($k['gender']??'')==='P'?'Perempuan':'-') ?> · Usia: <?= htmlspecialchars($k['age'] ?? '-') ?></p>
          <?php if (!empty($k['high_risk'])): ?><span class="inline-block mt-2 text-sm bg-red-100 text-red-700 px-2 py-1 rounded">High Risk</span><?php endif; ?>
        </div>
      </div>
      <div class="mt-4 flex gap-2">
        <a href="edit.php?id=<?= $originalIndex ?>" class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</a>
        <a href="hapus.php?id=<?= $originalIndex ?>" onclick="return confirm('Hapus data?')" class="px-3 py-1 bg-red-600 text-white rounded">Hapus</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- pagination -->
<div class="mt-6 flex justify-center items-center gap-3">
  <?php for($p=1;$p<=$pages;$p++): ?>
    <a href="?<?= http_build_query(array_merge($_GET, ['page'=>$p])) ?>" class="px-3 py-1 rounded <?= $p==$page ? 'bg-emerald-700 text-white':'bg-white' ?>"><?= $p ?></a>
  <?php endfor; ?>
</div>

<script>
  // Export visible contact-list element to PDF
  document.getElementById('exportPdfBtn').addEventListener('click', async () => {
    const el = document.getElementById('contact-list');
    // render to canvas
    const canvas = await html2canvas(el, {scale:2});
    const imgData = canvas.toDataURL('image/png');
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p','pt', 'a4');
    const pdfW = pdf.internal.pageSize.getWidth();
    const pdfH = (canvas.height * pdfW) / canvas.width;
    pdf.addImage(imgData, 'PNG', 20, 20, pdfW-40, pdfH);
    pdf.save('kontak.pdf');
  });
</script>

<?php include "footer.php"; ?>