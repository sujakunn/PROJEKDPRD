<?php
session_start();
include '../../config/koneksi.php';

/**
 * Proteksi Tambahan: 
 * Pastikan hanya admin yang login yang bisa mengakses file ini
 */
// if (!isset($_SESSION['admin_logged_in'])) { header('Location: ../login.php'); exit; }

// 1. Ambil dan bersihkan ID
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // 2. Ambil informasi gambar sebelum data dihapus dari database
    $query = mysqli_query($conn, "SELECT gambar FROM berita WHERE id='$id'");
    $berita = mysqli_fetch_assoc($query);

    if ($berita) {
        // 3. Hapus file fisik gambar jika ada di folder assets
        $pathGambar = "../../assets/img/berita/" . $berita['gambar'];
        if (!empty($berita['gambar']) && file_exists($pathGambar)) {
            unlink($pathGambar);
        }

        // 4. Hapus data dari database
        $delete = mysqli_query($conn, "DELETE FROM berita WHERE id='$id'");
        
        if ($delete) {
            // Opsional: Set pesan sukses menggunakan session flash
            $_SESSION['pesan_sukses'] = "Berita telah berhasil dihapus.";
        }
    }
}

// 5. Kembali ke halaman utama manajemen berita
header("Location: index.php");
exit;