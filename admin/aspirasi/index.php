<?php 
session_start();
include '../auth.php'; // Proteksi halaman admin
include '../../config/koneksi.php';

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('Asia/Jakarta');

// --- LOGIKA FILTER ---
// Mengambil parameter dan membersihkan input untuk keamanan SQL
$search    = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$f_status  = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$f_camat   = isset($_GET['kecamatan']) ? mysqli_real_escape_string($conn, $_GET['kecamatan']) : '';
$f_bulan   = isset($_GET['bulan']) ? mysqli_real_escape_string($conn, $_GET['bulan']) : '';

$where_clauses = [];

// Logika: Hanya tambahkan ke query jika nilai TIDAK KOSONG (menangani opsi "Semua")
if ($search !== '') {
    $where_clauses[] = "(nama LIKE '%$search%' OR email LIKE '%$search%')";
}
if ($f_status !== '') {
    $where_clauses[] = "status = '$f_status'";
}
if ($f_camat !== '') {
    $where_clauses[] = "kecamatan = '$f_camat'";
}
if ($f_bulan !== '') {
    $where_clauses[] = "MONTH(tanggal) = '$f_bulan'";
}

// Gabungkan clause menjadi string WHERE
$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";

// Query utama dengan pengurutan cerdas (Status "Baru" selalu di atas)
$query_str = "SELECT id, nama, email, kecamatan, tanggal, status FROM aspirasi $where_sql 
              ORDER BY CASE WHEN status='baru' THEN 0 ELSE 1 END, tanggal DESC";
$data = mysqli_query($conn, $query_str);
$totalAspirasi = mysqli_num_rows($data);

// Daftar Wilayah Manual sesuai permintaan Anda
$kecamatan_list = ['Sukamulya', 'Kresek', 'Gunung Kaler', 'Mekar Baru', 'Kronjo', 'Kemiri', 'Mauk', 'Sukadiri'];

$daftar_bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April",
    "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus",
    "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Aspirasi - Panel Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .aspirasi-row-baru { position: relative; }
        /* Indikator bar oranye untuk data baru */
        .aspirasi-row-baru::after {
            content: ''; position: absolute; left: 0; top: 15%; height: 70%; width: 4px;
            background-color: #f59e0b; border-radius: 0 4px 4px 0;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen text-slate-900">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <nav class="flex mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                    <a href="../index.php" class="hover:text-green-600">Admin Panel</a>
                    <span class="mx-2 text-slate-200">/</span>
                    <span class="text-slate-900">Kotak Aspirasi</span>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center">
                    Suara Masyarakat
                    <span class="ml-4 px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black rounded-full uppercase tracking-widest"><?= $totalAspirasi ?> Hasil</span>
                </h1>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button onclick="cetakLaporan()" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold text-xs rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-900/20 group text-center uppercase tracking-widest">
                    <i data-lucide="printer" class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform text-center uppercase tracking-widest "></i>
                    Export PDF
                </button>
                <a href="../index.php" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold text-xs rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
                    <i data-lucide="layout-grid" class="w-4 h-4 mr-2 text-green-600"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
            <form method="GET" id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Cari nama/email..." 
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm font-medium">
                </div>

                <select name="status" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm font-medium cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="baru" <?= $f_status == 'baru' ? 'selected' : '' ?>>🚨 Baru</option>
                    <option value="dibaca" <?= $f_status == 'dibaca' ? 'selected' : '' ?>>✅ Selesai</option>
                </select>

                <select name="bulan" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm font-medium cursor-pointer">
                    <option value="">Semua Bulan</option>
                    <?php foreach ($daftar_bulan as $key => $val): ?>
                        <option value="<?= $key ?>" <?= $f_bulan == $key ? 'selected' : '' ?>><?= $val ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="kecamatan" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm font-medium cursor-pointer">
                    <option value="">Semua Wilayah</option>
                    <?php foreach($kecamatan_list as $kec): ?>
                        <option value="<?= htmlspecialchars($kec) ?>" <?= $f_camat == $kec ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kec) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-900 text-white font-black rounded-xl hover:bg-slate-800 transition-all text-[10px] uppercase tracking-widest">
                        Filter
                    </button>
                    <?php if($search || $f_status || $f_camat || $f_bulan): ?>
                        <a href="index.php" class="px-4 py-3 bg-slate-100 text-slate-500 rounded-xl hover:bg-red-50 hover:text-red-500 transition-all flex items-center justify-center shadow-sm" title="Reset Filter">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] uppercase font-black text-slate-400 tracking-[0.2em]">
                            <th class="px-8 py-5 w-16 text-center">No</th>
                            <th class="px-8 py-5">Identitas Pengirim</th>
                            <th class="px-8 py-5">Wilayah</th>
                            <th class="px-8 py-5 text-center">Tanggal</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm font-medium">
                        <?php 
                        $no = 1; 
                        if(mysqli_num_rows($data) > 0):
                            while($a = mysqli_fetch_assoc($data)): 
                                $isNew = ($a['status'] == 'baru');
                        ?>
                        <tr class="<?= $isNew ? 'bg-amber-50/30 aspirasi-row-baru' : '' ?> hover:bg-slate-50/80 transition-all group">
                            <td class="px-8 py-6 text-slate-300 font-bold text-center"><?= $no++ ?></td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-900 leading-tight"><?= htmlspecialchars($a['nama']) ?></div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"><?= htmlspecialchars($a['email']) ?></div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black bg-slate-100 text-slate-500 uppercase tracking-widest border border-slate-200">
                                    <?= htmlspecialchars($a['kecamatan']) ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="text-slate-800 text-xs font-bold"><?= date('d M Y', strtotime($a['tanggal'])) ?></div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <?php if($isNew): ?>
                                    <span class="inline-flex items-center px-4 py-1.5 text-[8px] font-black bg-amber-50 text-amber-600 uppercase tracking-[0.2em] rounded-full border border-amber-100 animate-pulse">Belum Dibaca</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-4 py-1.5 text-[8px] font-black bg-green-50 text-green-600 uppercase tracking-[0.2em] rounded-full border border-green-100">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="detail.php?id=<?= $a['id'] ?>" class="inline-flex items-center px-5 py-2.5 bg-slate-900 text-white hover:bg-green-700 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md active:scale-95 group">
                                    Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5 ml-2 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="6" class="px-8 py-32 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                                <p class="text-sm font-bold uppercase tracking-widest">Tidak menemukan data aspirasi</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function cetakLaporan() {
            // Mengambil elemen form berdasarkan ID
            const form = document.getElementById('filterForm');
            if (!form) return;

            // Mengambil nilai filter yang sedang aktif dari form
            const formData = new FormData(form);
            const search = formData.get('search') || '';
            const status = formData.get('status') || '';
            const bulan = formData.get('bulan') || '';
            const kecamatan = formData.get('kecamatan') || '';

            // Membangun URL dengan parameter yang aman untuk PDF
            const params = new URLSearchParams({
                search: search,
                status: status,
                bulan: bulan,
                kecamatan: kecamatan
            });

            // Membuka file cetak_laporan.php di tab baru dengan filter yang dipilih
            const url = `cetak_laporan.php?${params.toString()}`;
            window.open(url, '_blank');
        }
    </script>
</body>
</html>