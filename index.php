<?php
include 'config/koneksi.php';

// 1. Ambil Global Settings
$query_set = mysqli_query($conn, "SELECT * FROM pengaturan");
$S = []; 
while($row = mysqli_fetch_assoc($query_set)) {
    $S[$row['meta_key']] = $row['meta_value'];
}

$officialInfo = [
    "name"    => $S['nama_legislator'] ?? "Muhamad Rapiudin Akbar, Lc.",
    "title"   => $S['judul_web'] ?? "Anggota DPRD Kabupaten Tangerang",
    "tagline" => $S['tagline_hero'] ?? "Bekerja Nyata untuk Rakyat, Membangun Tangerang Lebih Gemilang",
    "photo"   => "assets/img/profil/foto-pribadi1.jpeg"
];

// 2. Data Berita (Slider)
$queryNews = mysqli_query($conn, "SELECT id, judul, slug, isi, gambar, tanggal, views 
                                  FROM berita WHERE status = 'publish' 
                                  ORDER BY tanggal DESC LIMIT 6");

// 3. Data Galeri
$queryGallery = mysqli_query($conn, "SELECT judul, gambar FROM galeri ORDER BY tanggal DESC LIMIT 4");

// 4. Area Fokus Kerja (Definisi Variabel agar tidak error)
$focusAreas = [
    ["icon" => "heart-pulse", "title" => "Kesehatan", "desc" => "Layanan gratis & puskesmas desa."],
    ["icon" => "graduation-cap", "title" => "Pendidikan", "desc" => "Beasiswa & sarana sekolah."],
    ["icon" => "trending-up", "title" => "Ekonomi", "desc" => "Pemberdayaan UMKM digital."],
    ["icon" => "pickaxe", "title" => "Infrastruktur", "desc" => "Jalan desa & irigasi modern."]
];

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $officialInfo['name']; ?> - Official Site</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .hero-gradient { background: linear-gradient(135deg, #052c16 0%, #064e3b 100%); }
        
/* FIX TOTAL RUANG KOSONG */
        
        /* 1. Hilangkan padding besar pada section pembungkus */
        .warta-section {
            padding-top: 2rem !important;
            padding-bottom: 0.5rem !important; /* Mepet ke bawah */
            background-color: #F8FAFC;
        }

        /* 2. Paksa Swiper agar tingginya hanya mengikuti kartu saja */
        .newsSwiper {
            height: auto !important; /* Penting agar tidak memanjang */
            padding-bottom: 40px !important; /* Ruang sempit hanya untuk titik bulat */
            overflow: hidden;
        }

        /* 3. Tarik pagination naik tepat di bawah kartu */
        .newsSwiper .swiper-pagination {
            position: absolute !important;
            bottom: 0px !important; /* Menempel ke bawah kartu */
            left: 0;
            width: 100%;
        }

        /* 4. Atur bullet agar lebih modern */
        .newsSwiper .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background: #cbd5e1;
            opacity: 1;
            transition: all 0.3s ease;
        }
        .newsSwiper .swiper-pagination-bullet-active {
            background: #10b981 !important;
            width: 20px;
            border-radius: 5px;
        }

        /* Clamp teks judul agar tidak mendorong card jadi panjang */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <section class="relative hero-gradient text-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 py-16 lg:py-24 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 text-center lg:text-left">
                    <span class="inline-flex items-center px-4 py-1 bg-white/10 backdrop-blur-xl rounded-full text-green-400 text-[10px] font-black uppercase tracking-[0.2em] mb-4 border border-white/5">
                        Aspirasi Menjadi Aksi
                    </span>
                    <h1 class="text-4xl md:text-5xl font-black mb-6 leading-tight"><?= $officialInfo['name']; ?></h1>
                    <p class="text-base md:text-lg mb-8 text-green-100/80 italic font-medium">"<?= $officialInfo['tagline']; ?>"</p>
                    <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                        <a href="aspirasi.php" class="px-8 py-3.5 bg-green-600 text-white rounded-2xl font-black hover:bg-green-500 transition-all shadow-xl text-xs uppercase tracking-widest">Kirim Aspirasi</a>
                        <a href="profil.php" class="px-8 py-3.5 bg-white/5 border border-white/20 text-white rounded-2xl font-black hover:bg-white/10 transition-all text-xs uppercase tracking-widest">Profil</a>
                    </div>
                </div>
                <div class="lg:col-span-5 flex justify-center">
                    <img src="<?= $officialInfo['photo']; ?>" class="w-64 h-64 md:w-80 md:h-80 rounded-[3rem] object-cover border-4 border-white/10 shadow-2xl hover:scale-105 transition-all duration-700">
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($focusAreas as $area): ?>
                <div class="bg-slate-50 p-6 rounded-[2rem] border border-transparent hover:border-green-100 hover:bg-white hover:shadow-xl transition-all text-center group">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-4 mx-auto group-hover:bg-green-600 group-hover:text-white transition-all">
                        <i data-lucide="<?= $area['icon']; ?>" class="w-5 h-5"></i>
                    </div>
                    <h4 class="font-black text-slate-900 mb-2 uppercase text-xs tracking-widest"><?= $area['title']; ?></h4>
                    <p class="text-slate-500 text-[10px] font-medium"><?= $area['desc']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

   <section class="warta-section">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            
            <div class="flex justify-between items-center mb-6 border-b border-slate-200/60 pb-4">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">Berita Terbaru</h3>
                    <p class="text-slate-400 font-bold text-[9px] tracking-widest uppercase">Update Kegiatan Legislatif</p>
                </div>
                <div class="flex gap-2">
                    <button class="btn-prev p-2 bg-white border border-slate-200 rounded-full hover:bg-green-600 hover:text-white transition-all active:scale-90">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button class="btn-next p-2 bg-white border border-slate-200 rounded-full hover:bg-green-600 hover:text-white transition-all active:scale-90">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div class="swiper newsSwiper">
                <div class="swiper-wrapper">
                    <?php while ($news = mysqli_fetch_assoc($queryNews)): 
                        $slug = $news['slug'] ? $news['slug'].".html" : "detail.php?id=".$news['id'];
                    ?>
                    <div class="swiper-slide">
                        <a href="<?= $slug ?>" class="group block relative aspect-[3/4] rounded-2xl overflow-hidden bg-slate-200 shadow-md hover:shadow-xl transition-all duration-500">
                            <img src="assets/img/berita/<?= $news['gambar']; ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-4">
                                <span class="inline-block px-2 py-0.5 bg-green-600 text-white text-[7px] font-black uppercase rounded mb-2">
                                    <?= date("d M Y", strtotime($news['tanggal'])); ?>
                                </span>
                                <h4 class="text-xs font-bold text-white leading-tight line-clamp-2 group-hover:text-green-400 transition-colors">
                                    <?= $news['judul']; ?>
                                </h4>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>
    

    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Dokumentasi</h3>
                <p class="text-slate-400 font-bold text-[10px] tracking-widest uppercase">Kerja Nyata Legislatif</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php while ($photo = mysqli_fetch_assoc($queryGallery)): ?>
                <div class="relative aspect-square rounded-2xl overflow-hidden group border border-slate-100 shadow-sm">
                    <img src="assets/img/galeri/<?= $photo['gambar']; ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-green-950/70 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center p-4">
                        <p class="text-white text-center text-[9px] font-bold uppercase tracking-tighter"><?= htmlspecialchars($photo['judul']); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-10">
                <a href="galeri.php" class="text-[10px] font-black text-green-700 hover:text-green-900 uppercase tracking-widest border-b-2 border-green-100 pb-1 transition-all">Lihat Seluruh Galeri</a>
            </div>
        </div>
    </section>

    <section class="py-16 hero-gradient text-center text-white relative">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-2xl font-black mb-3 uppercase tracking-tight">Sampaikan Aspirasi</h2>
            <p class="text-green-100/70 mb-8 text-xs font-medium">Bantu kami membangun Tangerang lebih baik.</p>
            <a href="aspirasi.php" class="px-10 py-4 bg-white text-green-900 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl hover:scale-105 transition-all inline-block active:scale-95">Kirim Sekarang</a>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        lucide.createIcons();

        new Swiper('.newsSwiper', {
    slidesPerView: 2,
    spaceBetween: 10,
    loop: true, // Agar slide kembali ke awal setelah slide terakhir
    
    // AKTIFKAN GESER OTOMATIS DI SINI
    autoplay: {
        delay: 2000, // Bergeser setiap 2 detik
        disableOnInteraction: false, // Tetap bergeser meskipun user menyentuh slider
        pauseOnMouseEnter: true, // Opsional: berhenti sejenak saat mouse berada di atas berita
    },
                autoHeight: true, // INI KUNCINYA agar background tidak memanjang

    pagination: { 
        el: '.swiper-pagination', 
        clickable: true 
    },
    navigation: { 
        nextEl: '.btn-next', 
        prevEl: '.btn-prev' 
    },
    breakpoints: {
        640: { slidesPerView: 3, spaceBetween: 15 },
        1024: { slidesPerView: 5, spaceBetween: 15 } 
    }
});
 
    </script>
</body>
</html>