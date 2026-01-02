<?php
include 'includes/header.php';

// Mock Data (Biasanya data ini dari database)
$officialInfo = [
    "name" => "Muhamad Rapiudin Akbar, Lc.",
    "title" => "Anggota DPRD Kabupaten Tangerang",
    "tagline" => "Bekerja Nyata untuk Rakyat, Membangun Tangerang Lebih Gemilang",
    "photo" => "assets/img/profil/foto-pribadi1.jpeg",
    "party_logo" => "assets/img/logo-pkb.png"
];

$profile = [
    "biography" => "Muhamad Rapiudin Akbar, Lc. adalah putra daerah yang lahir dan besar di Tangerang. Pendidikan sarjananya diselesaikan di Al-Azhar University, Cairo, Mesir, yang memberikan landasan keilmuan dan diplomasi yang kuat.\n\nMemulai karier politik dengan semangat pengabdian, beliau dikenal vokal dalam memperjuangkan hak pekerja lokal, peningkatan mutu pesantren, serta digitalisasi layanan publik di pelosok Kabupaten Tangerang.",
    "stats" => [
        ["label" => "Tahun Mengabdi", "value" => "8+"],
        ["label" => "Aspirasi", "value" => "120+"],
        ["label" => "Dapil", "value" => "II"]
    ],
    "education" => [
        ["degree" => "Sarjana (Lc)", "institution" => "Al-Azhar University, Cairo", "year" => "2016 - 2021"],
        ["degree" => "Madrasah Aliyah", "institution" => "Pondok Pesantren Daar El-Qolam", "year" => "2013 - 2016"]
    ],
    "experience" => [
        ["position" => "Sekretaris DPC PKB Kabupaten Tangerang", "period" => "2015 - 2020"],
        ["position" => "Ketua Karang Taruna Kabupaten Tangerang", "period" => "2010 - 2015"],
        ["position" => "Pendiri Akbar Initiative", "period" => "2012 - Sekarang"]
    ],
    "currentPositions" => [
        "Ketua Fraksi PKB DPRD Kabupaten Tangerang",
        "Anggota Komisi I (Hukum & Pemerintahan)",
        "Wakil Ketua Badan Musyawarah (Bamus)",
        "Penasehat Himpunan Pengusaha Muda Daerah"
    ],
    "visionMission" => [
        "vision" => "Terwujudnya Kabupaten Tangerang yang mandiri, sejahtera, dan berdaya saing melalui tata kelola pemerintahan yang transparan dan pro-rakyat.",
        "mission" => [
            "Mendorong percepatan infrastruktur pedesaan yang terintegrasi.",
            "Transformasi digital sistem kesehatan tingkat desa.",
            "Beasiswa pendidikan tinggi bagi santri dan putra daerah berprestasi.",
            "Pemberdayaan UMKM berbasis ekonomi kreatif lokal."
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo $officialInfo['name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .timeline-line::before { content: ''; position: absolute; left: 0.75rem; top: 0; height: 100%; width: 2px; background: #e2e8f0; }
    </style>
</head>
<body class="bg-[#F8FAFC]">

    <main>
    <section class="relative bg-green-900 pt-16 pb-28 md:pt-20 md:pb-32 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <img src="assets/img/pattern.png" class="w-full h-full object-cover" alt="pattern">
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
            <div class="inline-flex items-center space-x-4 bg-white/10 p-2.5 rounded-2xl border border-white/20 mb-8">
                <img src="assets/img/logo-pkb.png" class="h-8 md:h-10 w-auto object-contain" alt="PKB">
                
                <div class="h-6 w-px bg-white/20"></div>
                
                <img src="assets/img/logo-kabta.png" class="h-20 md:h-14 w-auto object-contain" alt="Kab Tangerang">
                
                <div class="h-6 w-px bg-white/20"></div>
                
                <img src="assets/img/logo-dprd.png" class="h-8 md:h-10 w-auto object-contain" alt="DPRD">
            </div>

            <h1 class="text-white text-3xl md:text-6xl font-extrabold mb-4 tracking-tight">Profil Legislator</h1>
            <p class="text-green-100 text-base md:text-xl max-w-2xl mx-auto font-medium">Membangun Jembatan Aspirasi, Mewujudkan Perubahan Nyata untuk Kabupaten Tangerang.</p>
        </div>
    </section>

        <div class="max-w-7xl mx-auto px-4 -mt-16 md:-mt-20 relative z-20 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden sticky top-24">
                        <div class="h-28 bg-green-600"></div>
                        <div class="px-6 pb-8">
                            <div class="relative -mt-14 mb-6">
                                <img src="<?php echo $officialInfo['photo']; ?>" class="w-32 h-32 md:w-40 md:h-40 rounded-3xl object-cover border-4 border-white shadow-lg mx-auto" alt="Photo Profile">
                            </div>
                            <div class="text-center">
                                <h2 class="text-xl md:text-2xl font-bold text-gray-900"><?php echo $officialInfo['name']; ?></h2>
                                <p class="text-green-700 font-semibold mb-4 text-sm md:text-base"><?php echo $officialInfo['title']; ?></p>
                                <div class="p-4 bg-gray-50 rounded-2xl italic text-gray-600 text-xs md:text-sm mb-6 leading-relaxed">
                                    "<?php echo $officialInfo['tagline']; ?>"
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-2 border-t pt-6">
                                <?php foreach ($profile['stats'] as $stat): ?>
                                <div class="text-center border-r last:border-r-0 border-gray-100">
                                    <p class="text-lg md:text-xl font-bold text-green-700"><?php echo $stat['value']; ?></p>
                                    <p class="text-[9px] md:text-[10px] uppercase tracking-wider font-bold text-gray-400"><?php echo $stat['label']; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-8 space-y-8">
                    
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-10 transition-all hover:shadow-md">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 text-green-700 rounded-2xl flex items-center justify-center mr-4">
                                <i data-lucide="user"></i>
                            </div>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-900">Biografi Singkat</h3>
                        </div>
                        <div class="text-gray-600 leading-relaxed text-sm md:text-lg space-y-4">
                            <?php 
                            $paragraphs = explode("\n\n", $profile['biography']);
                            foreach ($paragraphs as $paragraph): ?>
                                <p class="font-medium opacity-90"><?php echo nl2br($paragraph); ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8 transition-all hover:shadow-md">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-2xl flex items-center justify-center mr-4">
                                    <i data-lucide="graduation-cap"></i>
                                </div>
                                <h3 class="text-lg md:text-xl font-bold text-gray-900">Pendidikan</h3>
                            </div>
                            <div class="relative space-y-8 timeline-line pl-8">
                                <?php foreach ($profile['education'] as $edu): ?>
                                <div class="relative">
                                    <div class="absolute -left-[1.65rem] top-1.5 w-3 h-3 bg-blue-600 rounded-full border-2 border-white shadow-sm"></div>
                                    <h4 class="font-bold text-gray-900 text-sm md:text-base"><?php echo $edu['degree']; ?></h4>
                                    <p class="text-xs md:text-sm text-gray-600"><?php echo $edu['institution']; ?></p>
                                    <p class="text-[10px] md:text-xs font-bold text-blue-600 mt-1 uppercase"><?php echo $edu['year']; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8 transition-all hover:shadow-md">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-purple-100 text-purple-700 rounded-2xl flex items-center justify-center mr-4">
                                    <i data-lucide="award"></i>
                                </div>
                                <h3 class="text-lg md:text-xl font-bold text-gray-900">Organisasi</h3>
                            </div>
                            <div class="space-y-6">
                                <?php foreach ($profile['experience'] as $exp): ?>
                                <div class="group border-l-2 border-transparent hover:border-purple-500 pl-2 transition-all">
                                    <h4 class="font-bold text-gray-900 group-hover:text-purple-700 transition-colors text-sm md:text-base leading-tight"><?php echo $exp['position']; ?></h4>
                                    <p class="text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-widest mt-1"><?php echo $exp['period']; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-800 to-green-950 rounded-3xl shadow-2xl p-8 md:p-12 text-white">
                        <div class="flex items-center mb-10">
                            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mr-4">
                                <i data-lucide="target" class="text-white"></i>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold">Visi & Misi</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 md:gap-12">
                            <div class="lg:col-span-5 border-b lg:border-b-0 lg:border-r border-white/10 pb-6 lg:pb-0 lg:pr-6">
                                <h4 class="text-green-400 font-bold uppercase tracking-widest text-xs mb-4">Visi Utama</h4>
                                <p class="text-lg md:text-xl font-medium leading-relaxed italic">
                                    "<?php echo $profile['visionMission']['vision']; ?>"
                                </p>
                            </div>
                            <div class="lg:col-span-7">
                                <h4 class="text-green-400 font-bold uppercase tracking-widest text-xs mb-6">Misi Strategis</h4>
                                <div class="space-y-4">
                                    <?php foreach ($profile['visionMission']['mission'] as $index => $item): ?>
                                    <div class="flex items-start bg-white/5 p-3 md:p-4 rounded-2xl hover:bg-white/10 transition-all group">
                                        <span class="text-green-400 font-black mr-4 group-hover:scale-110 transition-transform"><?php echo sprintf("%02d", $index + 1); ?>.</span>
                                        <span class="text-xs md:text-sm font-medium leading-relaxed"><?php echo $item; ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>