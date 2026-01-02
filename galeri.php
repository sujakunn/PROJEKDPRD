<?php
include 'config/koneksi.php';
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('Asia/Jakarta');

// --- LOGIKA PAGINATION ---
$batas = 8; // Diubah menjadi 8 agar pas dengan baris berisi 4 foto (4 x 2 baris)
$halaman = isset($_GET['hal']) ? max(1, (int)$_GET['hal']) : 1;
$awal = ($halaman - 1) * $batas;

// Ambil data galeri
$data = mysqli_query($conn, "SELECT * FROM galeri ORDER BY tanggal DESC LIMIT $awal, $batas");

// HITUNG TOTAL DATA UNTUK PAGINATION
$totalDataQuery = mysqli_query($conn, "SELECT COUNT(id) as total FROM galeri");
$totalDataRow = mysqli_fetch_assoc($totalDataQuery);
$totalData = $totalDataRow['total'];
$totalHalaman = max(1, ceil($totalData / $batas));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kegiatan - Fraksi PKB Kab. Tangerang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        #lightbox { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); backdrop-filter: blur(12px); }
        .gallery-card:hover img { transform: scale(1.08); }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col text-slate-900">

    <?php include 'includes/header.php'; ?>

    <main class="flex-grow">
        <section class="relative bg-gradient-to-br from-green-900 via-green-800 to-green-700 text-white py-12 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <img src="https://www.transparenttextures.com/patterns/carbon-fibre.png" class="w-full h-full object-cover">
            </div>
            <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
                <h1 class="text-3xl md:text-4xl font-black mb-2 tracking-tight uppercase">Galeri Kegiatan</h1>
                <p class="text-green-100 text-sm max-w-2xl mx-auto opacity-90 font-medium tracking-wide italic">"Rekam jejak aktivitas dan kerja nyata untuk masyarakat."</p>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-20">
            <?php if(mysqli_num_rows($data) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    <?php while($g = mysqli_fetch_assoc($data)): ?>
                        <div class="gallery-card group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-500 border border-slate-100 cursor-pointer"
                             onclick="openLightbox('assets/img/galeri/<?= $g['gambar'] ?>','<?= addslashes(htmlspecialchars($g['judul'])) ?>', '<?= date('d M Y', strtotime($g['tanggal'])) ?>')">
                            
                            <div class="aspect-[4/3] overflow-hidden relative">
                                <img src="assets/img/galeri/<?= $g['gambar'] ?>" 
                                     alt="<?= htmlspecialchars($g['judul']) ?>"
                                     class="w-full h-full object-cover transition-transform duration-700">
                                
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                     <i data-lucide="zoom-in" class="text-white w-8 h-8"></i>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <p class="text-slate-400 text-[9px] font-bold uppercase tracking-widest mb-1 flex items-center">
                                    <i data-lucide="calendar" class="w-3 h-3 mr-1.5 text-green-600"></i>
                                    <?= date('d F Y', strtotime($g['tanggal'])) ?>
                                </p>
                                <h3 class="text-sm font-extrabold text-slate-800 leading-snug group-hover:text-green-700 transition-colors line-clamp-2">
                                    <?= htmlspecialchars($g['judul']) ?>
                                </h3>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php if($totalHalaman > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="flex items-center space-x-1 bg-white p-2 rounded-xl shadow-sm border border-slate-100">
                        <?php if($halaman > 1): ?>
                            <a href="?hal=<?= $halaman - 1 ?>" class="w-10 h-10 flex items-center justify-center hover:bg-green-50 rounded-lg text-green-700 transition-colors">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </a>
                        <?php endif; ?>

                        <?php for($i=1; $i<=$totalHalaman; $i++): ?>
                            <a href="?hal=<?= $i ?>" 
                               class="w-10 h-10 flex items-center justify-center rounded-lg font-black text-xs transition-all
                               <?= $i == $halaman 
                                   ? 'bg-green-700 text-white shadow-md shadow-green-900/20' 
                                   : 'text-slate-400 hover:bg-slate-50 hover:text-slate-600' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if($halaman < $totalHalaman): ?>
                            <a href="?hal=<?= $halaman + 1 ?>" class="w-10 h-10 flex items-center justify-center hover:bg-green-50 rounded-lg text-green-700 transition-colors">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-slate-100">
                    <i data-lucide="image-off" class="w-10 h-10 mx-auto text-slate-200 mb-4"></i>
                    <h3 class="text-xl font-black text-slate-400">Belum Ada Dokumentasi</h3>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <div id="lightbox" class="hidden fixed inset-0 z-[9999] bg-slate-950/95 flex items-center justify-center p-4 md:p-10" onclick="closeLightbox()">
        <button onclick="closeLightbox()" class="absolute top-8 right-8 text-white/50 hover:text-white transition-all hover:rotate-90">
            <i data-lucide="x" class="w-10 h-10"></i>
        </button>

        <div class="max-w-5xl w-full flex flex-col items-center" onclick="event.stopPropagation()">
            <div class="relative w-full overflow-hidden rounded-2xl shadow-2xl border border-white/5">
                <img id="lightbox-img" src="" class="w-full h-auto max-h-[70vh] object-contain mx-auto">
            </div>
            <div class="mt-6 text-center bg-white/5 backdrop-blur-md p-5 rounded-2xl border border-white/10 w-fit max-w-xl">
                <h3 id="lightbox-caption" class="text-white text-lg font-black leading-tight"></h3>
                <div class="flex items-center justify-center mt-2 text-green-400 font-bold text-[10px] uppercase tracking-widest">
                    <i data-lucide="calendar" class="w-3 h-3 mr-2"></i>
                    <span id="lightbox-date"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function openLightbox(src, caption, date) {
            const lb = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            const cap = document.getElementById('lightbox-caption');
            const dt = document.getElementById('lightbox-date');

            img.src = src;
            cap.innerText = caption;
            dt.innerText = date;
            
            lb.classList.remove('hidden');
            lb.classList.add('flex');
            document.body.style.overflow = 'hidden'; 
            
            lb.style.opacity = '0';
            setTimeout(() => lb.style.opacity = '1', 10);
        }

        function closeLightbox() {
            const lb = document.getElementById('lightbox');
            lb.style.opacity = '0';
            setTimeout(() => {
                lb.classList.add('hidden');
                lb.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }, 300);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") closeLightbox();
        });
    </script>
</body>
</html>