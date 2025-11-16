<?php
session_start();

// Jika belum ada data kontak, isi data dummy otomatis
if (!isset($_SESSION['kontak']) || empty($_SESSION['kontak'])) {
    $_SESSION['kontak'] = [];

    $dummyNames = [
        "Abdullah Rahman", "Siti Aminah", "Muhammad Yusuf", "Nur Aisyah",
        "Zainab Fatimah", "Ali Mustofa", "Hisyam Zubair", "Rahmad Hidayat",
        "Farhan Fauzi", "Latifah Zahra", "Imam Syafi'i", "Budi Santoso",
        "Halimah Sa'diyah", "Junaidi Arif", "Ahmad Fauzan", "Rizky Maulana",
        "Sulastri Widya", "Dewi Anggraini", "Fauzi Abdullah", "Mahmud Hasan"
    ];

    $dummyTelp = [
        "081234567890", "082233445566", "083112233445", "081355667788",
        "082144556677", "083899776655", "081277889900"
    ];

    foreach ($dummyNames as $i => $name) {
        $_SESSION['kontak'][] = [
            "nama"  => $name,
            "email" => strtolower(str_replace(" ", ".", $name)) . "@mail.com",
            "telp"  => $dummyTelp[array_rand($dummyTelp)]
        ];
    }
}

// redirect jika belum login (dipakai oleh pages selain login.php)
function require_login() {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header("Location: login.php");
        exit;
    }
}

// buat uploads folder
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// inisialisasi data kontak (array)
if (!isset($_SESSION['kontak'])) {
    $_SESSION['kontak'] = [
        // contoh data awal (opsional)
        // [
        //   "nama"=>"Ahmad",
        //   "email"=>"ahmad@mail.com",
        //   "telp"=>"08123456789",
        //   "kota"=>"Bandung",
        //   "kloter"=>"1",
        //   "gender"=>"L",
        //   "age"=>67,
        //   "high_risk"=>true,
        //   "photo"=>"uploads/example.jpg"
        // ]
    ];
}