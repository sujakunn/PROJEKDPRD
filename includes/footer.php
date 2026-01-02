<?php
// Mendapatkan tahun saat ini secara otomatis
$currentYear = date('Y');

/** * Data diambil dari variabel $S yang sudah didefinisikan di header/index 
 * sebagai hasil query dari tabel 'pengaturan'
 */
?>

<footer class="bg-slate-950 text-slate-400 border-t border-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 mb-8">
            
            <div class="md:col-span-5 flex flex-col items-center md:items-start">
                <div class="flex items-center space-x-3 mb-4 group cursor-default">
                    <div class="w-10 h-10 bg-white rounded-xl p-1.5 shadow-xl transition-transform group-hover:scale-105">
                        <img src="assets/img/AKBAR_INITIATIVE.png" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="text-left border-l border-slate-800 pl-3"> 
                        <div class="font-black text-white text-sm leading-tight uppercase tracking-tighter">
                            <?= $S['nama_legislator'] ?? 'Muhamad Rapiudin Akbar, Lc.'; ?>
                        </div>
                        <div class="text-[9px] font-bold tracking-widest mt-0.5 uppercase text-green-600">
                            <?= $S['judul_web'] ?? 'DPRD Kab. Tangerang'; ?>
                        </div>
                    </div>
                </div>
                <p class="text-xs leading-relaxed max-w-xs text-center md:text-left opacity-70 italic">
                    "<?= $S['tagline_hero'] ?? 'Aspirasi publik untuk tata kelola pro-rakyat di Kabupaten Tangerang.'; ?>"
                </p>
            </div>

            <div class="md:col-span-3 text-center md:text-left">
                <h3 class="text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4">Navigasi</h3>
                <ul class="grid grid-cols-2 md:grid-cols-1 gap-2 text-xs font-medium">
                    <li><a href="profil.php" class="hover:text-green-500 transition-colors">Profil</a></li>
                    <li><a href="berita.php" class="hover:text-green-500 transition-colors">Berita</a></li>
                    <li><a href="aspirasi.php" class="hover:text-green-500 transition-colors">Aspirasi</a></li>
                    <li><a href="galeri.php" class="hover:text-green-500 transition-colors">Galeri</a></li>
                </ul>
            </div>

            <div class="md:col-span-4 flex flex-col items-center md:items-end">
                <h3 class="text-white text-[10px] font-black uppercase tracking-[0.2em] mb-4">Kontak Resmi</h3>
                <div class="space-y-2 mb-5 text-[11px] w-full text-center md:text-right">
                    <p class="opacity-70"><?= $S['alamat_kantor'] ?? 'Gedung DPRD Kab. Tangerang, Tigaraksa'; ?></p>
                    <p class="font-semibold text-slate-300"><?= $S['email_kantor'] ?? 'fraksi.pkb@dprd-tangerangkab.go.id'; ?></p>
                    <p class="font-bold text-green-600"><?= $S['telp_kantor'] ?? '(021) 5962-XXXX'; ?></p>
                </div>

                <div class="flex space-x-2">
                    <a href="<?= $S['link_facebook'] ?? '#'; ?>" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-md">
                        <i data-lucide="facebook" class="w-4 h-4"></i>
                    </a>
                    <a href="<?= $S['link_instagram'] ?? '#'; ?>" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all shadow-md">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                    <a href="<?= $S['link_twitter'] ?? '#'; ?>" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center hover:bg-slate-700 hover:text-white transition-all shadow-md">
                        <i data-lucide="twitter" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-900 flex flex-col items-center justify-center space-y-3">
            <p class="text-[9px] text-slate-500 uppercase tracking-widest font-bold text-center">
                &copy; <?= $currentYear; ?> <?= $S['nama_legislator'] ?? 'Syuja Rifka Khairyansyah'; ?>. All Rights Reserved.
            </p>
        </div>
    </div>
</footer>

<script>
    lucide.createIcons();
</script>