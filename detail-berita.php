<?php
include 'config/koneksi.php';
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('Asia/Jakarta');

// 1. Ambil slug dari URL & Sanitasi
$slug = mysqli_real_escape_string($conn, $_GET['slug'] ?? '');

// 2. Validasi awal
if (empty($slug)) {
    header("Location: berita.php");
    exit;
}

// 3. Ambil data berita berdasarkan slug
$query = mysqli_query($conn, "SELECT * FROM berita WHERE slug='$slug'");
$berita = mysqli_fetch_assoc($query);

// 4. Jika berita tidak ditemukan
if (!$berita) {
    include 'includes/header.php';
    echo "
    <div class='min-h-[70vh] flex items-center justify-center bg-gray-50 px-4'>
        <div class='text-center max-w-md bg-white p-10 rounded-[2.5rem] shadow-xl'>
            <div class='inline-flex items-center justify-center w-24 h-24 bg-red-50 text-red-500 rounded-full mb-6'>
                <i data-lucide='search-x' class='w-12 h-12'></i>
            </div>
            <h1 class='text-2xl font-black text-gray-900 mb-2 uppercase tracking-tight'>Konten Tidak Ditemukan</h1>
            <p class='text-gray-500 mb-8 font-medium'>Maaf, artikel yang Anda cari mungkin telah dipindahkan atau dihapus.</p>
            <a href='berita.php' class='inline-flex items-center justify-center w-full px-6 py-4 bg-green-700 text-white font-black rounded-2xl hover:bg-green-800 transition-all shadow-lg shadow-green-200 uppercase tracking-widest text-xs'>
                <i data-lucide='arrow-left' class='w-4 h-4 mr-2'></i> Kembali ke Berita
            </a>
        </div>
    </div>";
    include 'includes/footer.php';
    exit;
}

// --- LOGIKA PEMBACA (VIEW COUNTER) ---
$id_berita = $berita['id'];
// Update jumlah views di database (+1 setiap refresh)
mysqli_query($conn, "UPDATE berita SET views = views + 1 WHERE id = '$id_berita'");

// 5. Logika Share
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$share_url = urlencode($current_url);
$share_title = urlencode($berita['judul']);

// 6. Ambil data untuk Sidebar
$query_sidebar = mysqli_query($conn, "SELECT id, judul, tanggal, gambar, slug FROM berita WHERE slug != '$slug' ORDER BY tanggal DESC LIMIT 5");

// 7. Hitung Estimasi Waktu Baca
$wordCount = str_word_count(strip_tags($berita['isi']));
$readingTime = ceil($wordCount / 200); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($berita['judul']) ?> - Website Resmi DPRD</title>
    
    <meta name="description" content="<?= substr(strip_tags($berita['isi']), 0, 160) ?>">
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= htmlspecialchars($berita['judul']) ?>">
    <meta property="og:image" content="assets/img/berita/<?= $berita['gambar'] ?>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .content-area { font-size: 1.125rem; line-height: 1.9; color: #374151; }
        .content-area p { margin-bottom: 1.75rem; }
        .content-area img { border-radius: 1.5rem; margin: 2.5rem 0; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .sticky-sidebar { top: 6rem; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col">

    <?php include 'includes/header.php'; ?>

    <main class="flex-grow py-8 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <nav class="flex mb-10 text-xs font-bold uppercase tracking-[0.2em] text-slate-400 items-center space-x-3">
                <a href="index.php" class="hover:text-green-700 transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <a href="berita.php" class="hover:text-green-700 transition-colors">Berita</a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
                <span class="text-green-700 truncate max-w-[200px]">Detail Artikel</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <div class="lg:col-span-8">
                    <article class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-slate-100">
                        
                        <div class="p-6 md:p-12 pb-0">
                            <div class="flex flex-wrap items-center gap-4 mb-8">
                                <span class="bg-green-600 text-white text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full shadow-lg shadow-green-100">
                                    Informasi Publik
                                </span>
                                <div class="flex items-center text-xs font-bold text-slate-400">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2 text-green-600"></i>
                                    <?= date('d M Y', strtotime($berita['tanggal'])) ?>
                                </div>
                                <div class="flex items-center text-xs font-bold text-slate-400 border-l border-slate-200 pl-4">
                                    <i data-lucide="eye" class="w-4 h-4 mr-2 text-green-600"></i>
                                    <?= number_format($berita['views'] + 1) ?> Pembaca
                                </div>
                            </div>

                            <h1 class="text-3xl md:text-5xl font-black text-slate-900 mb-10 leading-[1.2] tracking-tight">
                                <?= htmlspecialchars($berita['judul']) ?>
                            </h1>
                        </div>

                        <?php if (!empty($berita['gambar'])): ?>
                        <div class="px-6 md:px-12">
                            <div class="w-full aspect-video md:aspect-[21/9] overflow-hidden rounded-[2rem] shadow-2xl">
                                <img src="assets/img/berita/<?= htmlspecialchars($berita['gambar']) ?>" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="p-6 md:p-12 pt-10">
                            <div class="content-area">
                                <?= nl2br($berita['isi']) ?>
                            </div>

                            <div class="mt-16 pt-10 border-t border-slate-50">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                                    <div class="flex items-center space-x-6">
                                        <span class="text-xs font-black uppercase tracking-widest text-slate-400">Bagikan:</span>
                                        <div class="flex space-x-3">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share_url ?>" target="_blank" class="w-12 h-12 flex items-center justify-center bg-blue-600 text-white rounded-2xl hover:-translate-y-1 transition-all shadow-lg shadow-blue-100"><i data-lucide="facebook" class="w-5 h-5"></i></a>
                                            <a href="https://api.whatsapp.com/send?text=<?= $share_title ?>%0A<?= $share_url ?>" target="_blank" class="w-12 h-12 flex items-center justify-center bg-emerald-500 text-white rounded-2xl hover:-translate-y-1 transition-all shadow-lg shadow-emerald-100"><i data-lucide="message-circle" class="w-5 h-5"></i></a>
                                            <button onclick="copyLink()" class="w-12 h-12 flex items-center justify-center bg-slate-800 text-white rounded-2xl hover:-translate-y-1 transition-all shadow-lg shadow-slate-100"><i data-lucide="link" class="w-5 h-5"></i></button>
                                        </div>
                                    </div>
                                    
                                    <a href="berita.php" class="group inline-flex items-center text-green-700 font-black text-xs uppercase tracking-widest bg-green-50 px-8 py-4 rounded-2xl hover:bg-green-700 hover:text-white transition-all">
                                        <i data-lucide="arrow-left" class="mr-2 w-4 h-4 transition-transform group-hover:-translate-x-1"></i> Kembali ke List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="lg:col-span-4">
                    <aside class="sticky-sidebar space-y-10">
                        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-900 mb-6">Cari Kabar</h3>
                            <form action="berita.php" method="GET" class="relative">
                                <input type="text" name="search" placeholder="Kata kunci..." class="w-full pl-6 pr-12 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all">
                                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 bg-green-700 text-white rounded-xl"><i data-lucide="search" class="w-4 h-4"></i></button>
                            </form>
                        </div>

                        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-900 mb-10">Terkini</h3>
                            <div class="space-y-10">
                                <?php while($row = mysqli_fetch_assoc($query_sidebar)): 
                                    $side_link = !empty($row['slug']) ? $row['slug'].".html" : "detail-berita.php?id=".$row['id'];
                                ?>
                                <a href="<?= $side_link ?>" class="group flex items-start gap-5">
                                    <div class="w-20 h-20 flex-shrink-0 rounded-[1.25rem] overflow-hidden bg-slate-100 shadow-sm">
                                        <img src="assets/img/berita/<?= $row['gambar'] ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-extrabold text-slate-900 group-hover:text-green-700 transition-colors leading-snug line-clamp-2"><?= htmlspecialchars($row['judul']) ?></h4>
                                        <p class="text-[10px] uppercase font-black text-slate-400 mt-2"><?= date('d M Y', strtotime($row['tanggal'])) ?></p>
                                    </div>
                                </a>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>

    

    <?php include 'includes/footer.php'; ?>

    <script>
    lucide.createIcons();
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert("✅ Link berita berhasil disalin!");
        });
    }
    </script>
</body>
</html>