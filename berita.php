<?php
include 'config/koneksi.php';
include 'includes/header.php';

// --- LOGIKA PENCARIAN & PAGINATION ---
$limit = 8; // Diubah ke 8 agar pas dengan baris berisi 4 berita (4x2)
$halaman = isset($_GET['halaman']) ? max(1, intval($_GET['halaman'])) : 1;
$halaman_awal = ($halaman - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = "";
if (!empty($search)) {
    $where_clause = "WHERE judul LIKE '%$search%' OR isi LIKE '%$search%'";
}

$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM berita $where_clause");
$row_total = mysqli_fetch_assoc($query_total);
$total_data = $row_total['total'];
$total_halaman = max(1, ceil($total_data / $limit));

$query_berita = "SELECT id, judul, slug, isi, gambar, tanggal, IFNULL(views, 0) as views 
                 FROM berita $where_clause 
                 ORDER BY tanggal DESC 
                 LIMIT $halaman_awal, $limit";
$data = mysqli_query($conn, $query_berita);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Kegiatan - Fraksi PKB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .news-card:hover .news-image { transform: scale(1.05); }
        /* Pembatasan baris agar kartu seragam */
        .line-clamp-judul { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 3rem; }
        .line-clamp-isi { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col text-slate-900">

    <main class="flex-grow">
        <section class="relative bg-gradient-to-br from-green-950 to-green-800 text-white py-12 overflow-hidden border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
                <h1 class="text-3xl md:text-4xl font-black mb-2 tracking-tight uppercase">Berita & Kegiatan</h1>
                <p class="text-green-100 text-sm opacity-80 font-medium">Update aktivitas resmi Fraksi PKB Kabupaten Tangerang.</p>
            </div>
        </section>

        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-10">
            
            <div class="max-w-4xl mx-auto bg-white p-3 rounded-2xl shadow-xl shadow-green-900/5 mb-12 border border-slate-100">
                <form action="" method="GET" class="flex flex-col md:flex-row gap-2">
                    <div class="relative flex-grow">
                        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                            placeholder="Cari berita..." 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500/20 outline-none transition-all text-sm">
                    </div>
                    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-8 py-3 rounded-xl font-bold transition-all text-sm">CARI</button>
                    <?php if(!empty($search)): ?>
                        <a href="berita.php" class="bg-slate-100 text-slate-500 px-6 py-3 rounded-xl font-bold text-center text-sm">RESET</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if(mysqli_num_rows($data) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                    <?php while ($b = mysqli_fetch_assoc($data)): 
                        $link = !empty($b['slug']) ? $b['slug'].".html" : "detail-berita.php?id=".$b['id'];
                    ?>
                        <article class="news-card group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-200 flex flex-col h-full">
                            <div class="aspect-[16/10] overflow-hidden relative bg-slate-200">
                                <?php if (!empty($b['gambar'])): ?>
                                    <img src="assets/img/berita/<?= htmlspecialchars($b['gambar']) ?>" class="news-image w-full h-full object-cover transition-transform duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center"><i data-lucide="image" class="text-slate-400 w-10 h-10"></i></div>
                                <?php endif; ?>
                                 </div>
                            
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="flex items-center justify-between mb-3 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                    <span class="flex items-center"><i data-lucide="calendar" class="w-3 h-3 mr-1 text-green-600"></i><?= date('d M Y', strtotime($b['tanggal'])) ?></span>
                                    <span class="flex items-center"><i data-lucide="eye" class="w-3 h-3 mr-1"></i><?= number_format($b['views']) ?></span>
                                </div>
                                
                                <h3 class="text-sm font-extrabold text-slate-900 mb-2 leading-tight group-hover:text-green-700 transition-colors line-clamp-judul">
                                    <a href="<?= $link ?>"><?= htmlspecialchars($b['judul']) ?></a>
                                </h3>
                                
                                <p class="text-slate-500 text-xs mb-4 line-clamp-isi leading-relaxed opacity-80">
                                    <?= substr(strip_tags($b['isi']), 0, 100) ?>...
                                </p>
                                
                                <div class="mt-auto pt-4 border-t border-slate-50">
                                    <a href="<?= $link ?>" class="text-green-700 text-[10px] font-black uppercase tracking-widest hover:text-green-900 flex items-center">
                                        Selengkapnya <i data-lucide="arrow-right" class="ml-1 w-3 h-3"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <nav class="mt-16 flex justify-center items-center space-x-2">
                    <?php for($x = max(1, $halaman - 2); $x <= min($total_halaman, $halaman + 2); $x++): ?>
                        <a href="?halaman=<?= $x ?>&search=<?= urlencode($search) ?>" 
                           class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-xs transition-all <?= ($halaman == $x) ? 'bg-green-700 text-white shadow-lg shadow-green-900/20' : 'bg-white border border-slate-200 text-slate-400 hover:border-green-600' ?>">
                            <?= $x ?>
                        </a>
                    <?php endfor; ?>
                </nav>

            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-3xl border border-slate-100">
                    <i data-lucide="search-x" class="w-10 h-10 text-slate-300 mx-auto mb-4"></i>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-widest">Kabar tidak ditemukan</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script>lucide.createIcons();</script>
</body>
</html>