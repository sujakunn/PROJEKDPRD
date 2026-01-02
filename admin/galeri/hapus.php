<?php
session_start();
include '../../config/koneksi.php';

/**
 * Keamanan: 
 * Pastikan hanya admin yang berhak yang bisa mengakses file ini
 */
// if (!isset($_SESSION['admin_logged_in'])) { header('Location: ../login.php'); exit; }

// 1. Ambil ID dan pastikan bertipe integer untuk keamanan (mencegah SQL Injection)
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // 2. Cari nama file gambar di database sebelum datanya dihapus
    $query = mysqli_query($conn, "SELECT gambar FROM galeri WHERE id='$id'");
    $galeri = mysqli_fetch_assoc($query);

    if ($galeri) {
        // 3. Hapus file fisik dari folder assets jika file tersebut ada
        $namaFile = $galeri['gambar'];
        $pathLengkap = "../../assets/img/galeri/" . $namaFile;

        if (!empty($namaFile) && file_exists($pathLengkap)) {
            // Menghapus file dari server
            unlink($pathLengkap);
        }

        // 4. Hapus record/data dari database
        $hapusData = mysqli_query($conn, "DELETE FROM galeri WHERE id='$id'");
        
        if ($hapusData) {
            // Opsional: Gunakan session untuk memberi notifikasi di halaman index
            $_SESSION['notifikasi'] = "Foto berhasil dihapus dari galeri.";
        }
    }
}

// 5. Kembalikan admin ke halaman utama manajemen galeri
header("Location: index.php");
exit;