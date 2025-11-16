<?php
// header.php: include after include "data.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Sistem Kontak Jamaah Haji</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jsPDF & html2canvas untuk export PDF client-side -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
      /* small card photo fix */
      .photo-thumb { width:80px; height:80px; object-fit:cover; border-radius:8px; }
    </style>
</head>
<body class="bg-[#f4f9f4] text-gray-800 min-h-screen">
<nav class="bg-gradient-to-r from-emerald-700 to-green-800 text-white p-4 shadow-md border-b-4 border-yellow-400">
  <div class="max-w-5xl mx-auto flex justify-between items-center">
    <div class="flex items-center gap-3">
      <span class="text-2xl">📒</span>
      <h1 class="text-xl font-semibold">Data Kontak Jamaah Haji</h1>
    </div>
    <div class="flex items-center gap-3">
      <a href="daftar.php" class="hidden sm:inline-block px-3 py-1 bg-emerald-600 rounded">Daftar</a>
      <a href="tambah.php" class="px-3 py-1 bg-emerald-600 rounded">Tambah</a>
      <form method="POST" action="login.php" class="inline-block">
        <button name="logout" class="px-3 py-1 bg-red-600 rounded">Logout</button>
      </form>
    </div>
  </div>
</nav>
<div class="max-w-5xl mx-auto mt-8 px-4">