<?php
// Mendapatkan nama file saat ini untuk logika "isActive"
$current_page = basename($_SERVER['PHP_SELF']);

$navLinks = [
    ['path' => 'index.php', 'label' => 'Beranda'],
    ['path' => 'profil.php', 'label' => 'Profil'],
    ['path' => 'aspirasi.php', 'label' => 'Aspirasi'],
    ['path' => 'berita.php', 'label' => 'Berita'],
    ['path' => 'galeri.php', 'label' => 'Galeri'],
];
?>

<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <a href="index.php" class="flex items-center space-x-3 group">
                <div class="w-12 h-12 flex items-center justify-center overflow-hidden">
                    <img src="assets/img/AKBAR_INITIATIVE.png" alt="Logo AkbarInitiative" class="w-full h-full object-contain group-hover:scale-105 transition-transform">
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-gray-900 leading-none">Muhamad Rapiudin Akbar, Lc.</span>
                    <span class="text-[10px] text-gray-500 uppercase tracking-tighter">Fraksi PKB Kabupaten Tangerang</span>
                </div>
            </a>

            <nav class="hidden md:flex space-x-8">
                <?php foreach ($navLinks as $link): 
                    $activeClass = ($current_page == $link['path']) ? 'text-green-700 font-semibold' : 'text-gray-700 hover:text-green-700';
                ?>
                    <a href="<?php echo $link['path']; ?>" 
                       class="px-3 py-2 rounded-md transition-colors <?php echo $activeClass; ?>">
                        <?php echo $link['label']; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <button id="menu-btn" class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none">
                <i data-lucide="menu" id="menu-icon-open"></i>
                <i data-lucide="x" id="menu-icon-close" class="hidden"></i>
            </button>
        </div>

        <nav id="mobile-menu" class="hidden md:hidden py-4 border-t">
            <?php foreach ($navLinks as $link): 
                $activeClass = ($current_page == $link['path']) ? 'text-green-700 font-semibold bg-green-50' : 'text-gray-700 hover:bg-gray-50';
            ?>
                <a href="<?php echo $link['path']; ?>" 
                   class="block px-3 py-3 rounded-md transition-colors <?php echo $activeClass; ?>">
                    <?php echo $link['label']; ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<script>
    // Logika Pengganti useState untuk Mobile Menu
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('menu-icon-open');
    const iconClose = document.getElementById('menu-icon-close');

    menuBtn.addEventListener('click', () => {
        const isOpen = !mobileMenu.classList.contains('hidden');
        
        if (isOpen) {
            mobileMenu.classList.add('hidden');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
        } else {
            mobileMenu.classList.remove('hidden');
            iconOpen.classList.add('hidden');
            iconClose.classList.remove('hidden');
        }
    });
</script>