<?php
require_once '../../assets/libs/dompdf/autoload.inc.php';
include '../../config/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil parameter filter dari URL
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Ambil Data dari Database
$query = mysqli_query($conn, "SELECT * FROM aspirasi WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' ORDER BY tanggal ASC");

// Mulai Output Buffering untuk menyusun HTML
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #999; }
        th { background-color: #f2f2f2; padding: 10px; text-align: center; text-transform: uppercase; font-size: 10px; }
        td { padding: 8px; vertical-align: top; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
        .status-baru { color: #b45309; font-weight: bold; }
        .status-dibaca { color: #15803d; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Aspirasi Masyarakat</h2>
        <p>Fraksi PKB DPRD Kabupaten Tangerang</p>
        <p>Periode: <?php echo $nama_bulan[$bulan] . " " . $tahun; ?></p>
    </div>

    <div class="info">
        <p>Total Aspirasi Masuk: <strong><?php echo mysqli_num_rows($query); ?> Pesan</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama / Email</th>
                <th width="15%">Kecamatan</th>
                <th width="15%">Tanggal</th>
                <th width="35%">Isi Aspirasi</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($d = mysqli_fetch_assoc($query)): 
            ?>
            <tr>
                <td style="text-align: center;"><?php echo $no++; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($d['nama']); ?></strong><br>
                    <?php echo htmlspecialchars($d['email']); ?>
                </td>
                <td><?php echo htmlspecialchars($d['kecamatan']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($d['tanggal'])); ?></td>
                <td><?php echo nl2br(htmlspecialchars($d['pesan'])); ?></td>
                <td style="text-align: center;">
                    <span class="<?php echo ($d['status'] == 'baru') ? 'status-baru' : 'status-dibaca'; ?>">
                        <?php echo strtoupper($d['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?php echo date('d/m/Y H:i'); ?> WIB</p>
        <br><br><br>
        <p>(..................................................)</p>
        <p>Administrator Panel Syuja</p>
    </div>

</body>
</html>

<?php
$html = ob_get_clean();

// Konfigurasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Penting agar gambar logo bisa muncul

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Set Ukuran Kertas (A4 Portrait)
$dompdf->setPaper('A4', 'portrait');

// Render HTML ke PDF
$dompdf->render();

// Output PDF ke Browser (Attachment = false artinya langsung buka, bukan langsung download)
$dompdf->stream("Laporan_Aspirasi_" . $nama_bulan[$bulan] . "_$tahun.pdf", ["Attachment" => false]);
?>