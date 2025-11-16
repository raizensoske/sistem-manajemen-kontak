<?php
include "data.php";
require_login();

$id = $_GET['id'] ?? null;
if ($id !== null && isset($_SESSION['kontak'][$id])) {
    $p = $_SESSION['kontak'][$id]['photo'] ?? '';
    if ($p && file_exists(__DIR__ . '/' . $p)) unlink(__DIR__ . '/' . $p);
    unset($_SESSION['kontak'][$id]);
    $_SESSION['kontak'] = array_values($_SESSION['kontak']);
}
header("Location: daftar.php");
exit;