<?php
include '../../config/koneksi.php';
session_start();

// Proteksi akses (Pastikan auth.php sudah benar)
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('Asia/Jakarta');

// Ambil data galeri terbaru
$data = mysqli_query($conn, "SELECT * FROM galeri ORDER BY tanggal DESC");
$totalFoto = mysqli_num_rows($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Galeri - Panel Syuja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-row-hover:hover { background-color: #f8fafc; transition: all 0.2s; }
        input[type="checkbox"]:checked { background-color: #16a34a; border-color: #16a34a; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen text-slate-900">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <nav class="flex mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                    <a href="../index.php" class="hover:text-green-600 transition-colors">Admin Panel</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-900">Dokumentasi</span>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center">
                    Galeri Foto
                    <span class="ml-4 px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase tracking-widest"><?= $totalFoto ?> Foto</span>
                </h1>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="../index.php" class="inline-flex items-center px-5 py-3 bg-white border border-slate-200 text-slate-600 font-bold text-xs rounded-2xl hover:bg-slate-50 transition-all shadow-sm group">
                    <i data-lucide="layout-grid" class="w-4 h-4 mr-2 text-slate-400 group-hover:text-green-600"></i>
                    Dashboard
                </a>

                <button id="btnHapusMassal" onclick="bulkDelete()" class="hidden items-center px-5 py-3 bg-red-50 text-red-600 font-bold text-xs rounded-2xl hover:bg-red-600 hover:text-white transition-all border border-red-100 group shadow-sm">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2 group-hover:animate-pulse"></i>
                    Hapus Terpilih (<span id="countSelected">0</span>)
                </button>

                <a href="tambah.php" class="inline-flex items-center px-6 py-3 bg-green-700 text-white font-bold text-xs rounded-2xl hover:bg-green-800 transition-all shadow-lg shadow-green-900/20 group">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform"></i>
                    Unggah Baru
                </a>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <form id="formBulkDelete" action="hapus_massal.php" method="POST">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] uppercase font-black text-slate-400 tracking-[0.2em]">
                                <th class="px-8 py-5 w-16 text-center">
                                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 cursor-pointer">
                                </th>
                                <th class="px-8 py-5">Preview</th>
                                <th class="px-8 py-5">Judul Dokumentasi</th>
                                <th class="px-8 py-5">Tanggal</th>
                                <th class="px-8 py-5 text-right">Manajemen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm font-medium">
                            <?php if($totalFoto > 0): while($g = mysqli_fetch_assoc($data)): ?>
                            <tr class="table-row-hover group">
                                <td class="px-8 py-6 text-center">
                                    <input type="checkbox" name="id_foto[]" value="<?= $g['id'] ?>" class="checkItem w-4 h-4 rounded border-slate-300 cursor-pointer">
                                </td>
                                <td class="px-8 py-6">
                                    <div class="w-20 h-14 rounded-xl overflow-hidden shadow-inner bg-slate-100 border border-slate-100">
                                        <img src="../../assets/img/galeri/<?= $g['gambar'] ?>" class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-500">
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-slate-900 font-bold text-sm leading-tight mb-1"><?= htmlspecialchars($g['judul']) ?></div>
                                    <div class="text-[9px] text-green-600 font-black uppercase tracking-widest italic">Dokumentasi Fraksi</div>
                                </td>
                                <td class="px-8 py-6 text-slate-400 text-xs font-bold uppercase">
                                    <?= date('d M, Y', strtotime($g['tanggal'])) ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button type="button" onclick="confirmDelete('hapus.php?id=<?= $g['id'] ?>')" class="w-9 h-9 inline-flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-32 text-center text-slate-300 uppercase font-black tracking-widest text-xs">
                                    <i data-lucide="image-off" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                                    Belum ada dokumentasi.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    

    <script>
        lucide.createIcons();

        const selectAll = document.getElementById('selectAll');
        const checkItems = document.querySelectorAll('.checkItem');
        const btnHapusMassal = document.getElementById('btnHapusMassal');
        const countSpan = document.getElementById('countSelected');

        // Logic Select All
        selectAll.addEventListener('change', function() {
            checkItems.forEach(item => item.checked = this.checked);
            updateUI();
        });

        // Logic Individual Checkbox
        checkItems.forEach(item => {
            item.addEventListener('change', updateUI);
        });

        function updateUI() {
            const checkedCount = document.querySelectorAll('.checkItem:checked').length;
            countSpan.innerText = checkedCount;
            if (checkedCount > 0) {
                btnHapusMassal.classList.remove('hidden');
                btnHapusMassal.classList.add('inline-flex');
            } else {
                btnHapusMassal.classList.add('hidden');
                btnHapusMassal.classList.remove('inline-flex');
            }
        }

        function bulkDelete() {
            Swal.fire({
                title: 'Hapus Massal?',
                text: "Semua foto yang dicentang akan dihapus selamanya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl font-bold' }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('formBulkDelete').submit();
            });
        }

        function confirmDelete(url) {
            Swal.fire({
                title: 'Hapus Foto Ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Hapus',
                customClass: { popup: 'rounded-[2rem]', confirmButton: 'rounded-xl font-bold' }
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        }
    </script>
</body>
</html>