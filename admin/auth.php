<?php
// 1. Pastikan session_start() ada di paling atas, sebelum ada output HTML/spasi apapun.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Cek apakah session 'login' ada dan bernilai true
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    // 3. Arahkan ke halaman login jika belum masuk
    header("Location: login.php");
    exit; // 4. Selalu gunakan exit setelah header location agar kode di bawahnya tidak dieksekusi
}
?>