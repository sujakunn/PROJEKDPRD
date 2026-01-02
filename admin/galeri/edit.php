<?php
session_start();
include '../../config/koneksi.php';

$id = intval($_GET['id'] ?? 0);
// Ambil data galeri berdasarkan ID
$data = mysqli_query($conn, "SELECT * FROM galeri WHERE id='$id'");
$galeri = mysqli_fetch_assoc($data);

if (!$galeri) {
    header("Location: index.php");
    exit;
}

$pesanError = '';
$pesanSukses = '';

if (isset($_POST['update'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $namaFileBaru = $galeri['gambar'];

    // Proses jika ada unggahan gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $namaFile = $_FILES['gambar']['name'];
        $ukuran   = $_FILES['gambar']['size'];
        $tmp      = $_FILES['gambar']['tmp_name'];
        $ext      = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $pesanError = 'Format gambar harus JPG, JPEG, atau PNG';
        } elseif ($ukuran > 2097152) {
            $pesanError = 'Ukuran gambar maksimal 2MB';
        } else {
            // Hapus gambar lama dari server
            if ($galeri['gambar'] && file_exists("../../assets/img/galeri/" . $galeri['gambar'])) {
                unlink("../../assets/img/galeri/" . $galeri['gambar']);
            }
            // Generate nama unik dan pindahkan file
            $namaFileBaru = uniqid('galeri_') . '.' . $ext;
            move_uploaded_file($tmp, "../../assets/img/galeri/" . $namaFileBaru);
        }
    }

    if ($pesanError == '') {
        mysqli_query($conn, "
            UPDATE galeri SET
            judul='$judul',
            gambar='$namaFileBaru'
            WHERE id='$id'
        ");
        
        // Perbarui variabel data lokal untuk tampilan setelah update
        $galeri['judul'] = $judul;
        $galeri['gambar'] = $namaFileBaru;
        $pesanSukses = 'Data galeri berhasil diperbarui';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Galeri - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <div class="max-w-4xl mx-auto px-4 py-12 w-full">
        <div class="mb-8">
            <a href="index.php" class="inline-flex items-center text-gray-500 hover:text-green-700 font-medium transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
                Kembali ke Daftar Galeri
            </a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="edit-3" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-900">Edit Foto Galeri</h1>
                        <p class="text-gray-500 text-sm">Perbarui informasi atau gambar dokumentasi.</p>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-10">
                <?php if ($pesanError): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 flex items-center rounded-r-xl">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                        <span class="text-sm font-medium"><?= $pesanError ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($pesanSukses): ?>
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 flex items-center rounded-r-xl">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i>
                        <span class="text-sm font-medium"><?= $pesanSukses ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Foto / Kegiatan</label>
                        <input type="text" name="judul" value="<?= htmlspecialchars($galeri['judul']) ?>" required 
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Saat Ini</label>
                            <div class="relative group rounded-2xl overflow-hidden border border-gray-200 shadow-sm aspect-video">
                                <img src="../../assets/img/galeri/<?= $galeri['gambar'] ?>" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold uppercase tracking-wider">Foto Aktif</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ganti Gambar (Opsional)</label>
                            <label class="flex flex-col items-center justify-center w-full h-full min-h-[160px] border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i data-lucide="upload-cloud" class="w-8 h-8 text-gray-400 mb-2 group-hover:text-green-500 transition-colors"></i>
                                    <p class="text-xs text-gray-500 font-semibold">Pilih file baru</p>
                                </div>
                                <input type="file" name="gambar" id="imgInput" class="hidden" accept="image/*" />
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" name="update" 
                            class="flex items-center px-10 py-4 bg-green-700 text-white font-bold rounded-2xl hover:bg-green-800 transition-all shadow-lg shadow-green-200 transform hover:-translate-y-1">
                            <i data-lucide="save" class="w-5 h-5 mr-2"></i>
                            Perbarui Galeri
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    <script>
        lucide.createIcons();

        // Logika pratinjau sederhana jika admin memilih file baru
        const imgInput = document.getElementById('imgInput');
        imgInput.onchange = evt => {
            const [file] = imgInput.files;
            if (file) {
                // Opsional: Anda bisa menambahkan elemen preview dinamis di sini menggunakan JS
            }
        }
    </script>
</body>
</html>