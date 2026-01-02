<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya admin yang bisa backup
if (!isset($_SESSION['login'])) {
    exit("Akses ditolak.");
}

// Konfigurasi Nama File
$nama_file = "backup_db_" . date('Y-m-d_H-i-s') . ".sql";

// Inisialisasi konten SQL
$isi_sql = "-- Backup Database: " . date('Y-m-d H:i:s') . "\n";
$isi_sql .= "-- Panel Syuja - Fraksi PKB Kab. Tangerang\n\n";

// Ambil semua daftar tabel
$tables = array();
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

// Proses setiap tabel
foreach ($tables as $table) {
    // 1. Buat Struktur Tabel (CREATE TABLE)
    $row2 = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE $table"));
    $isi_sql .= "\n\n" . $row2[1] . ";\n\n";

    // 2. Ambil Data (INSERT INTO)
    $result = mysqli_query($conn, "SELECT * FROM $table");
    $num_fields = mysqli_num_fields($result);

    while ($row = mysqli_fetch_row($result)) {
        $isi_sql .= "INSERT INTO $table VALUES(";
        for ($j = 0; $j < $num_fields; $j++) {
            $row[$j] = addslashes($row[$j]);
            // Ganti line breaks dengan teks agar tidak error saat import nanti
            $row[$j] = str_replace("\n", "\\n", $row[$j]);
            if (isset($row[$j])) {
                $isi_sql .= '"' . $row[$j] . '"';
            } else {
                $isi_sql .= 'NULL';
            }
            if ($j < ($num_fields - 1)) {
                $isi_sql .= ',';
            }
        }
        $isi_sql .= ");\n";
    }
    $isi_sql .= "\n\n\n";
}

// HEADER UNTUK DOWNLOAD FILE
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . $nama_file . "\"");

echo $isi_sql;
exit;