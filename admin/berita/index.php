<?php 
session_start();
include '../auth.php'; // Proteksi halaman admin
include '../../config/koneksi.php';
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
date_default_timezone_set('Asia/Jakarta');

// Ambil data berita terbaru
// Query disesuaikan untuk mengambil kolom 'status' dan 'views'
$data = mysqli_query($conn, "SELECT id, judul, gambar, tanggal, views, IFNULL(status, 'publish') as status FROM berita ORDER BY tanggal DESC");
$totalBerita = mysqli_num_rows($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Berita - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .table-row-hover:hover { background-color: #f8fafc; transition: all 0.2s; }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen text-slate-900">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <nav class="flex mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                    <a href="../index.php" class="hover:text-green-600 transition-colors">Admin</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-900">Berita</span>
                </nav>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center">
                    Konten Warta
                    <span class="ml-4 px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full uppercase tracking-widest"><?= $totalBerita ?> Artikel</span>
                </h1>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="../index.php" class="inline-flex items-center px-5 py-3 bg-white border border-slate-200 text-slate-600 font-bold text-xs rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
                    <i data-lucide="layout-grid" class="w-4 h-4 mr-2"></i>
                    Dashboard
                </a>
                <a href="tambah.php" class="inline-flex items-center px-6 py-3 bg-green-700 text-white font-bold text-xs rounded-2xl hover:bg-green-800 transition-all shadow-lg shadow-green-900/20 group">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform"></i>
                    Buat Artikel
                </a>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] uppercase font-black text-slate-400 tracking-[0.2em]">
                            <th class="px-8 py-5 w-20">No.</th>
                            <th class="px-8 py-5">Judul & Gambar</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5 text-center">Statistik</th>
                            <th class="px-8 py-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm font-medium">
                        <?php 
                        $no = 1; 
                        if(mysqli_num_rows($data) > 0):
                            while($row = mysqli_fetch_assoc($data)): 
                                $status = $row['status'];
                                $isDraft = ($status == 'draft');
                        ?>
                        <tr class="table-row-hover group">
                            <td class="px-8 py-6 text-slate-300 font-bold"><?= $no++ ?></td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden shadow-inner flex-shrink-0 bg-slate-100 border border-slate-100">
                                        <?php if(!empty($row['gambar'])): ?>
                                            <img src="../../assets/img/berita/<?= $row['gambar'] ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i data-lucide="image" class="w-4 h-4 text-slate-300"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="max-w-md">
                                        <div class="text-slate-900 font-bold text-sm leading-tight mb-1 group-hover:text-green-700 transition-colors line-clamp-1">
                                            <?= htmlspecialchars($row['judul']) ?>
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-bold flex items-center gap-1">
                                            <i data-lucide="calendar" class="w-3 h-3"></i>
                                            <?= date('d M, Y', strtotime($row['tanggal'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <?php if($isDraft): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[9px] font-black bg-amber-50 text-amber-600 uppercase tracking-widest border border-amber-100">
                                        <i data-lucide="file-edit" class="w-3 h-3 mr-1.5"></i> DRAFT
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[9px] font-black bg-green-50 text-green-600 uppercase tracking-widest border border-green-100">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1.5"></i> PUBLISHED
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex items-center justify-center gap-1 text-slate-400 text-xs">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    <span class="font-bold"><?= number_format($row['views'] ?? 0) ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-green-600 hover:text-white transition-all shadow-sm" title="Ubah">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <button onclick="confirmDelete('hapus.php?id=<?= $row['id'] ?>')" class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
                                <p class="text-sm font-bold uppercase tracking-widest">Belum ada berita</p>
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

        // SweetAlert2 Konfirmasi Hapus
        function confirmDelete(url) {
            Swal.fire({
                title: 'Hapus Artikel?',
                text: "Berita yang dihapus tidak dapat dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#f1f5f9',
                confirmButtonText: 'Ya, Hapus Sekarang',
                cancelButtonText: 'Batalkan',
                customClass: {
                    popup: 'rounded-[1.5rem]',
                    confirmButton: 'rounded-xl font-bold py-3 px-6',
                    cancelButton: 'rounded-xl font-bold py-3 px-6 text-slate-600'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        }
    </script>
</body>
</html>