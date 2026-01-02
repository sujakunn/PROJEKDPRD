<?php
session_start();
include '../../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$id = intval($_GET['id'] ?? 0);
$pesanError = '';
$pesanSukses = '';

function buatSlug($judul) {
    $slug = strtolower($judul);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

// Ambil data lama
$queryData = mysqli_query($conn, "SELECT * FROM berita WHERE id='$id'");
$berita = mysqli_fetch_assoc($queryData);

if (!$berita) {
    header("Location: index.php");
    exit;
}

// Proses update
if (isset($_POST['update'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi   = mysqli_real_escape_string($conn, $_POST['isi']);
    $slug  = buatSlug($judul);

    $namaFileBaru = $berita['gambar'];

    // Jika upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $namaFile = $_FILES['gambar']['name'];
        $ukuran   = $_FILES['gambar']['size'];
        $tmp      = $_FILES['gambar']['tmp_name'];
        $ext      = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg','jpeg','png'])) {
            $pesanError = 'Format gambar harus JPG, JPEG, atau PNG';
        } elseif ($ukuran > 2097152) {
            $pesanError = 'Ukuran gambar maksimal 2MB';
        } else {
            // Hapus gambar lama jika ada
            if ($berita['gambar'] && file_exists("../../assets/img/berita/".$berita['gambar'])) {
                unlink("../../assets/img/berita/".$berita['gambar']);
            }
            $namaFileBaru = uniqid('berita_').'.'.$ext;
            move_uploaded_file($tmp, "../../assets/img/berita/".$namaFileBaru);
        }
    }

    if ($pesanError == '') {
        mysqli_query($conn, "
            UPDATE berita SET 
            judul='$judul', 
            slug='$slug', 
            isi='$isi', 
            gambar='$namaFileBaru' 
            WHERE id='$id'
        ");
        
        // Refresh data setelah update
        $berita['judul'] = $judul;
        $berita['isi'] = $isi;
        $berita['gambar'] = $namaFileBaru;
        
        $pesanSukses = 'Berita berhasil diperbarui secara sistem';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="max-w-4xl mx-auto px-4 py-10">
        
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="index.php" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    <i data-lucide="arrow-left" class="w-6 h-6 text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Edit Berita</h1>
                    <p class="text-sm text-gray-500">Sesuaikan informasi artikel Anda.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 md:p-10">

                <?php if ($pesanError): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 flex items-center rounded-r-lg text-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i> <?= $pesanError ?>
                    </div>
                <?php endif; ?>

                <?php if ($pesanSukses): ?>
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 flex items-center rounded-r-lg text-sm">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i> <?= $pesanSukses ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Artikel</label>
                        <input type="text" name="judul" value="<?= htmlspecialchars($berita['judul']) ?>" required 
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all"
                               placeholder="Contoh: Kunjungan Kerja Dapil 1">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Isi Berita</label>
                        <textarea name="isi" rows="10" required 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all"
                                  placeholder="Tuliskan isi berita di sini..."><?= htmlspecialchars($berita['isi']) ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Saat Ini</label>
                            <div class="relative w-full h-48 rounded-2xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50">
                                <?php if ($berita['gambar']): ?>
                                    <img src="../../assets/img/berita/<?= $berita['gambar'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="flex items-center justify-center h-full text-gray-300">
                                        <i data-lucide="image" class="w-12 h-12"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ganti Gambar (Opsional)</label>
                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-2"></i>
                                    <p class="text-xs text-gray-500">Pilih file atau drag & drop</p>
                                    <p class="text-[10px] text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                                </div>
                                <input type="file" name="gambar" class="hidden" />
                            </label>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" name="update" class="w-full md:w-auto px-8 py-3.5 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800 transition-all shadow-lg shadow-green-100 flex items-center justify-center">
                            <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    

    <script>
        lucide.createIcons();
    </script>
</body>
</html>