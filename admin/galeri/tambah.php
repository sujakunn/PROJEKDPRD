<?php
include '../../config/koneksi.php';
session_start();

$pesanError = '';
$pesanSukses = '';

if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, htmlspecialchars($_POST['judul']));
    
    // Pastikan ada file yang diunggah
    if (!empty($_FILES['gambar']['name'][0])) {
        $files = $_FILES['gambar'];
        $jumlahFile = count($files['name']);
        $berhasil = 0;
        $gagal = 0;

        for ($i = 0; $i < $jumlahFile; $i++) {
            $namaFile = $files['name'][$i];
            $ukuran   = $files['size'][$i];
            $tmp      = $files['tmp_name'][$i];
            $error    = $files['error'][$i];
            $ext      = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

            // Validasi per file
            if ($error === 0) {
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    if ($ukuran <= 2097152) { // 2MB
                        $namaFileBaru = "galeri_" . uniqid() . "_" . $i . "." . $ext;
                        $targetDir = "../../assets/img/galeri/";

                        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

                        if (move_uploaded_file($tmp, $targetDir . $namaFileBaru)) {
                            $query = "INSERT INTO galeri (judul, gambar, tanggal) VALUES ('$judul', '$namaFileBaru', NOW())";
                            if (mysqli_query($conn, $query)) {
                                $berhasil++;
                            } else {
                                $gagal++;
                            }
                        } else {
                            $gagal++;
                        }
                    } else {
                        $pesanError = "Beberapa file terlalu besar (Maks 2MB).";
                    }
                } else {
                    $pesanError = "Beberapa format file tidak didukung.";
                }
            }
        }

        if ($berhasil > 0) {
            $pesanSukses = "$berhasil foto berhasil ditambahkan ke galeri." . ($gagal > 0 ? " ($gagal gagal)" : "");
        }
    } else {
        $pesanError = 'Silakan pilih minimal satu gambar.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Upload Galeri - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen">

    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <a href="index.php" class="text-xs font-black text-slate-400 hover:text-green-600 transition-colors uppercase tracking-[0.2em] flex items-center mb-2">
                    <i data-lucide="arrow-left" class="w-3 h-3 mr-2"></i> Kembali ke Galeri
                </a>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Multi-Upload Dokumentasi</h1>
            </div>
        </div>

        <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <form method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
                
                <?php if ($pesanError): ?>
                    <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 flex items-center rounded-r-2xl">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                        <span class="text-sm font-bold"><?= $pesanError ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($pesanSukses): ?>
                    <div class="mb-8 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 flex items-center rounded-r-2xl">
                        <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i>
                        <span class="text-sm font-bold"><?= $pesanSukses ?></span>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-8">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Judul Massal (Akan diterapkan ke semua foto)</label>
                        <input type="text" name="judul" required 
                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-semibold text-slate-800"
                            placeholder="Contoh: Dokumentasi Reses Masa Sidang I">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Pilih Beberapa Foto</label>
                        <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-slate-200 border-dashed rounded-[2.5rem] cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all group">
                            <div id="placeholder" class="flex flex-col items-center justify-center">
                                <i data-lucide="images" class="w-10 h-10 text-slate-300 mb-3 group-hover:text-green-500 transition-colors"></i>
                                <p class="text-sm text-slate-600 font-bold">Tarik foto ke sini atau klik untuk memilih</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-2">Bisa pilih lebih dari 1 foto sekaligus</p>
                            </div>
                            <div id="preview-grid" class="hidden absolute inset-0 p-6 grid grid-cols-4 sm:grid-cols-6 gap-3 overflow-y-auto bg-slate-50 rounded-[2.5rem]">
                                </div>
                            <input type="file" name="gambar[]" id="imgInput" class="hidden" multiple required accept="image/*" />
                        </label>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                        <p class="text-[10px] text-slate-400 font-bold italic">* Foto akan diproses secara otomatis satu per satu.</p>
                        <button type="submit" name="simpan" 
                            class="flex items-center px-10 py-4 bg-green-700 text-white font-black rounded-2xl hover:bg-green-800 transition-all shadow-lg shadow-green-900/20 active:scale-95">
                            <i data-lucide="upload" class="w-5 h-5 mr-3"></i>
                            UNGGAH SEKARANG
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const imgInput = document.getElementById('imgInput');
        const previewGrid = document.getElementById('preview-grid');
        const placeholder = document.getElementById('placeholder');

        imgInput.onchange = evt => {
            previewGrid.innerHTML = '';
            const files = imgInput.files;
            
            if (files.length > 0) {
                placeholder.classList.add('hidden');
                previewGrid.classList.remove('hidden');
                
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const div = document.createElement('div');
                        div.className = "aspect-square rounded-xl overflow-hidden border-2 border-white shadow-sm";
                        div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                        previewGrid.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</body>
</html>