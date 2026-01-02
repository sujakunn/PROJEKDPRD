<?php
include '../../config/koneksi.php';

// Nama file yang akan dihasilkan
$filename = "Data_Aspirasi_" . date('Ymd_His') . ".xls";

// Header agar browser mendownload file sebagai Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil semua data aspirasi
$data = mysqli_query($conn, "SELECT * FROM aspirasi ORDER BY tanggal DESC");
?>

<table border="1">
    <thead>
        <tr>
            <th style="background-color: #166534; color: white;">No</th>
            <th style="background-color: #166534; color: white;">Nama Pengirim</th>
            <th style="background-color: #166534; color: white;">Email</th>
            <th style="background-color: #166534; color: white;">Kecamatan</th>
            <th style="background-color: #166534; color: white;">Isi Aspirasi</th>
            <th style="background-color: #166534; color: white;">Tanggal</th>
            <th style="background-color: #166534; color: white;">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while($a = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($a['nama']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= htmlspecialchars($a['kecamatan']) ?></td>
            <td><?= htmlspecialchars($a['pesan']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($a['tanggal'])) ?></td>
            <td><?= $a['status'] == 'baru' ? 'Belum Dibaca' : 'Sudah Dibaca' ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>