<?php 
session_start();
include 'auth.php'; // Proteksi halaman
include '../config/koneksi.php';

// 1. Ambil Statistik Utama
$jmlBerita   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM berita"))['total'];
$jmlGaleri   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM galeri"))['total'];
$jmlAspirasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM aspirasi"))['total'];

// 2. Logika Notifikasi Aspirasi Baru
$queryNotif  = mysqli_query($conn, "SELECT id FROM aspirasi WHERE status = 'baru'");
$totalNotif  = mysqli_num_rows($queryNotif);

// 3. Logika Grafik Tren (6 Bulan Terakhir)
$labelTren = []; $dataTren  = [];
for ($i = 5; $i >= 0; $i--) {
    $bulanTahunQuery = date('Y-m', strtotime("-$i month"));
    $namaBulanLabel  = date('M', strtotime("-$i month"));
    $resChart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM aspirasi WHERE tanggal LIKE '$bulanTahunQuery%'"));
    $labelTren[] = $namaBulanLabel;
    $dataTren[]  = (int)$resChart['total'];
}

// 4. Logika Grafik Kecamatan (Top 5)
$labelKecamatan = []; $dataKecamatan  = [];
$queryKec = mysqli_query($conn, "SELECT kecamatan, COUNT(*) as total FROM aspirasi GROUP BY kecamatan ORDER BY total DESC LIMIT 5");
while($rowKec = mysqli_fetch_assoc($queryKec)) {
    $labelKecamatan[] = $rowKec['kecamatan'] ?: 'Lainnya';
    $dataKecamatan[]  = (int)$rowKec['total'];
}

// 5. Ambil 5 aspirasi terbaru
$aspirasiTerbaru = mysqli_query($conn, "SELECT * FROM aspirasi ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Panel Syuja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-header { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] flex min-h-screen text-slate-800">

    <aside class="w-64 bg-slate-900 text-slate-400 hidden lg:flex flex-col sticky top-0 h-screen z-50">
        <div class="p-6 flex-grow overflow-y-auto custom-scrollbar">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-9 h-9 bg-white rounded-xl p-1.5 shadow-xl">
                    <img src="../assets/img/AKBAR_INITIATIVE.png" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-white font-black text-xs tracking-tight uppercase">Syuja Panel</h1>
                    <p class="text-[9px] text-green-500 font-bold uppercase tracking-widest leading-none">Administrator</p>
                </div>
            </div>

 <nav class="space-y-1">
    <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-3 px-3">Utama</p>
    
    <a href="index.php" class="flex items-center space-x-3 px-3 py-2.5 bg-green-600 text-white rounded-xl shadow-lg shadow-green-900/20 transition-all">
        <i data-lucide="layout-grid" class="w-4 h-4"></i>
        <span class="font-bold text-sm">Dashboard</span>
    </a>
    
    <a href="berita/" class="flex items-center space-x-3 px-3 py-2.5 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
        <i data-lucide="newspaper" class="w-4 h-4"></i>
        <span class="text-sm font-medium">Kelola Berita</span>
    </a>
    
    <a href="galeri/" class="flex items-center space-x-3 px-3 py-2.5 hover:bg-slate-800 hover:text-white rounded-xl transition-all">
        <i data-lucide="image" class="w-4 h-4"></i>
        <span class="text-sm font-medium">Kelola Galeri</span>
    </a>
    
    <a href="aspirasi/" class="flex items-center justify-between px-3 py-2.5 hover:bg-slate-800 hover:text-white rounded-xl transition-all group">
        <div class="flex items-center space-x-3">
            <i data-lucide="message-square" class="w-4 h-4"></i>
            <span class="text-sm font-medium">Aspirasi</span>
        </div>
        <?php if($totalNotif > 0): ?>
            <span class="h-5 w-5 flex items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white animate-pulse"><?= $totalNotif ?></span>
        <?php endif; ?>
    </a>

    <a href="pengaturan.php" class="flex items-center space-x-3 px-3 py-2.5 hover:bg-slate-800 hover:text-white rounded-xl transition-all group">
        <i data-lucide="settings" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-500"></i>
        <span class="text-sm font-medium">Pengaturan Web</span>
    </a>

    <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest pt-6 mb-3 px-3">System</p>
    <div class="bg-slate-800/40 p-4 rounded-2xl border border-slate-700/30">
        <div class="flex items-center space-x-2 mb-3">
            <i data-lucide="database" class="w-3 h-3 text-green-500"></i>
            <h4 class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Backup Data</h4>
        </div>
        <a href="backup_db.php" class="w-full py-2 bg-slate-700 hover:bg-green-600 text-white rounded-lg text-[10px] font-black transition-all flex items-center justify-center group">
            <i data-lucide="download-cloud" class="w-3 h-3 mr-2 group-hover:animate-bounce"></i>
            DOWNLOAD .SQL
        </a>
    </div>

    <div class="pt-4">
        <a href="../index.php" target="_blank" class="flex items-center justify-between px-3 py-2.5 hover:bg-slate-800 text-slate-400 hover:text-white rounded-xl transition-all group text-sm">
            <div class="flex items-center space-x-3">
                <i data-lucide="external-link" class="w-4 h-4 text-green-500"></i>
                <span class="font-medium">Halaman Publik</span>
            </div>
        </a>
    </div>
</nav>
        </div>
        
        <div class="mt-auto p-4 border-t border-slate-800/50">
            <a href="logout.php" class="flex items-center space-x-3 px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all text-xs font-bold">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                <span>Keluar Sistem</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0">
        <header class="h-16 glass-header border-b border-slate-200/60 flex items-center justify-between px-8 sticky top-0 z-40">
            <h2 class="font-bold text-slate-700 text-sm">Enterprise Dashboard</h2>
            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-black text-slate-400 uppercase leading-none mb-1"><?= date('l, d M Y') ?></p>
                    <p id="clock" class="text-xs font-bold text-green-600 leading-none tracking-tighter">00:00:00 WIB</p>
                </div>
                <div class="h-8 w-px bg-slate-200"></div>
                <div class="flex items-center space-x-3">
                    <span class="text-xs font-bold text-slate-600"><?= $_SESSION['admin_name'] ?></span>
                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center text-white font-black text-xs shadow-lg shadow-green-200">S</div>
                </div>
            </div>
        </header>

        <div class="p-6 lg:p-10 max-w-7xl w-full mx-auto">
            <div class="mb-8">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang, <?= explode(' ', $_SESSION['admin_name'])[0] ?>! 👋</h1>
                <p class="text-xs text-slate-500 font-medium mt-1 italic">Pantau sistem aspirasi Kabupaten Tangerang secara real-time.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm transition-transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center"><i data-lucide="newspaper" class="w-5 h-5"></i></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Konten Berita</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900"><?= number_format($jmlBerita) ?></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm transition-transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center"><i data-lucide="image" class="w-5 h-5"></i></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Media Visual</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900"><?= number_format($jmlGaleri) ?></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm transition-transform hover:-translate-y-1 transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center"><i data-lucide="message-circle" class="w-5 h-5"></i></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Aspirasi</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900"><?= number_format($jmlAspirasi) ?></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="font-bold text-slate-800 text-xs uppercase tracking-widest mb-6 border-l-4 border-green-500 pl-3">Aktivitas 6 Bulan</h3>
                    <div class="h-48 w-full"><canvas id="chartAspirasi"></canvas></div>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="font-bold text-slate-800 text-xs uppercase tracking-widest mb-6 border-l-4 border-blue-500 pl-3">Sebaran Wilayah</h3>
                    <div class="h-48 w-full"><canvas id="chartKecamatan"></canvas></div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-black text-slate-800 text-xs uppercase tracking-widest">Antrian Aspirasi Terbaru</h3>
                    <a href="aspirasi/" class="text-[9px] font-black bg-slate-900 text-white px-4 py-2 rounded-xl uppercase tracking-tighter hover:bg-green-600 transition-all">Lihat Arsip</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white text-slate-400 text-[10px] uppercase font-black tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Pengirim</th>
                                <th class="px-6 py-4 text-center">Status Verifikasi</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($row = mysqli_fetch_assoc($aspirasiTerbaru)): 
                                $isNew = ($row['status'] == 'baru');
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-6 py-4">
                                    <p class="text-xs font-black text-slate-800"><?= htmlspecialchars($row['nama']) ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic"><?= htmlspecialchars($row['kecamatan']) ?></p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest <?= $isNew ? 'bg-red-50 text-red-500 border border-red-100 animate-pulse' : 'bg-green-50 text-green-500 border border-green-100' ?>">
                                        <?= $isNew ? 'New Entry' : 'Processed' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="aspirasi/detail.php?id=<?= $row['id'] ?>" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-green-600 hover:text-white rounded-xl inline-block transition-all shadow-sm">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        lucide.createIcons();

        // Chart Config: Line Trend
        const ctxTren = document.getElementById('chartAspirasi').getContext('2d');
        const grad = ctxTren.createLinearGradient(0, 0, 0, 200);
        grad.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        grad.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: <?= json_encode($labelTren) ?>,
                datasets: [{
                    data: <?= json_encode($dataTren) ?>,
                    borderColor: '#10b981',
                    backgroundColor: grad,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff'
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } }, grid: { borderDash: [5,5] } },
                    x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                }
            }
        });

        // Chart Config: Kecamatan Bar
        new Chart(document.getElementById('chartKecamatan'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($labelKecamatan) ?>,
                datasets: [{
                    data: <?= json_encode($dataKecamatan) ?>,
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barThickness: 12
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
                }
            }
        });

        // Live Clock
        setInterval(() => {
            const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('clock').textContent = time + ' WIB';
        }, 1000);
    </script>
</body>
</html>