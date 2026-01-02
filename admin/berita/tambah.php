<?php
session_start();
include '../../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

$pesanError = '';
$pesanSukses = '';

function buatSlug($judul) {
    $judul = strtolower($judul);
    $judul = preg_replace('/[^a-z0-9\s-]/', '', $judul);
    $judul = preg_replace('/[\s-]+/', ' ', $judul);
    $judul = preg_replace('/\s/', '-', $judul);
    return trim($judul, '-');
}

// LOGIKA SIMPAN (BAIK PUBLISH MAUPUN DRAFT)
if (isset($_POST['simpan']) || isset($_POST['draft'])) {
    $judul  = mysqli_real_escape_string($conn, strip_tags($_POST['judul']));
    $isi    = mysqli_real_escape_string($conn, $_POST['isi']);
    $slug   = buatSlug($judul);
    $status = isset($_POST['draft']) ? 'draft' : 'publish'; // Tentukan status
    $namaFileBaru = NULL;

    if (empty($judul)) {
        $pesanError = 'Judul tidak boleh kosong.';
    }

    // VALIDASI GAMBAR (Hanya wajib jika status = publish)
    if ($pesanError == '' && !empty($_FILES['gambar']['name'])) {
        $namaFile = $_FILES['gambar']['name'];
        $ukuran   = $_FILES['gambar']['size'];
        $tmp      = $_FILES['gambar']['tmp_name'];
        $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

        if (!in_array($ekstensi, ['jpg', 'jpeg', 'png', 'webp'])) {
            $pesanError = 'Format gambar tidak didukung.';
        } elseif ($ukuran > 2 * 1024 * 1024) {
            $pesanError = 'Gambar maksimal 2MB.';
        } else {
            $namaFileBaru = uniqid('news_') . '.' . $ekstensi;
            move_uploaded_file($tmp, "../../assets/img/berita/" . $namaFileBaru);
        }
    } elseif ($pesanError == '' && $status == 'publish') {
        $pesanError = 'Gambar utama wajib diunggah untuk mempublikasikan berita.';
    }

    if ($pesanError == '') {
        $cekSlug = mysqli_query($conn, "SELECT id FROM berita WHERE slug = '$slug'");
        if (mysqli_num_rows($cekSlug) > 0) { $slug .= '-' . time(); }

        // Tambahkan kolom status pada query INSERT
        $query = "INSERT INTO berita (judul, slug, isi, gambar, tanggal, status) 
                  VALUES ('$judul', '$slug', '$isi', '$namaFileBaru', NOW(), '$status')";

        if (mysqli_query($conn, $query)) {
            $pesanSukses = ($status == 'draft') ? 'Berita disimpan sebagai draft.' : 'Berita berhasil diterbitkan.';
            $_POST = array();
        } else {
            $pesanError = 'Kesalahan database: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen">

    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center space-x-5">
                <a href="index.php" class="group p-3 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all">
                    <i data-lucide="arrow-left" class="w-6 h-6 text-slate-400 group-hover:text-green-700"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Editor Berita</h1>
                    <p class="text-slate-500 font-medium text-sm">Tulis aspirasi atau berita kegiatan Anda hari ini.</p>
                </div>
            </div>
        </div>

        <form id="newsForm" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 md:p-10 space-y-6">
                        <?php if ($pesanError): ?>
                            <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 flex items-center rounded-r-2xl">
                                <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                                <span class="text-sm font-bold"><?= $pesanError ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($pesanSukses): ?>
                            <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-800 flex items-center rounded-r-2xl">
                                <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i>
                                <p class="text-sm font-bold"><?= $pesanSukses ?> <a href="index.php" class="underline ml-2 text-xs">Lihat semua</a></p>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Judul Berita</label>
                            <input type="text" name="judul" value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>"
                                   class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-4 focus:ring-green-500/10 outline-none font-bold text-lg text-slate-800"
                                   placeholder="Ketik judul artikel...">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3">Isi Artikel</label>
                            <textarea name="isi" id="editor" rows="12"
                                      class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-none focus:ring-4 focus:ring-green-500/10 outline-none text-slate-700 leading-relaxed"
                                      placeholder="Tuliskan berita lengkap di sini..."><?= isset($_POST['isi']) ? htmlspecialchars($_POST['isi']) : '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 text-center">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-5">Foto Utama</label>
                        <label for="imgInput" class="block group cursor-pointer">
                            <div id="dropzone" class="relative w-full h-48 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] flex items-center justify-center overflow-hidden transition-all group-hover:border-green-400">
                                <div id="dropzoneContent" class="text-slate-400">
                                    <i data-lucide="image-plus" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                                    <p class="text-[10px] font-bold uppercase">Upload Foto</p>
                                </div>
                                <img id="imgPreview" class="hidden absolute inset-0 w-full h-full object-cover">
                            </div>
                            <input type="file" name="gambar" id="imgInput" class="hidden" accept="image/*">
                        </label>
                    </div>

                    <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl">
                        <div class="flex items-center space-x-3 mb-6 text-white">
                            <i data-lucide="send" class="w-5 h-5 text-green-400"></i>
                            <h3 class="font-bold uppercase text-xs tracking-widest">Manajemen</h3>
                        </div>
                        
                        <div class="space-y-3">
                            <button type="submit" name="simpan" class="w-full py-4 bg-green-600 hover:bg-green-500 text-white font-black rounded-2xl transition-all flex items-center justify-center shadow-lg shadow-green-900/40 text-sm">
                                <i data-lucide="globe" class="w-4 h-4 mr-2"></i> Terbitkan Sekarang
                            </button>
                            
                            <button type="submit" name="draft" class="w-full py-4 bg-slate-800 text-slate-300 hover:text-white font-bold rounded-2xl transition-all text-xs border border-slate-700/50">
                                <i data-lucide="save" class="w-4 h-4 mr-2 inline"></i> Simpan ke Draft
                            </button>
                            
                            <button type="button" onclick="window.history.back()" class="w-full py-2 text-slate-500 hover:text-red-400 font-bold transition-all text-[10px] uppercase">
                                Batalkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();
        const imgInput = document.getElementById('imgInput');
        const imgPreview = document.getElementById('imgPreview');
        const dropzoneContent = document.getElementById('dropzoneContent');

        imgInput.onchange = evt => {
            const [file] = imgInput.files;
            if (file) {
                imgPreview.src = URL.createObjectURL(file);
                imgPreview.classList.remove('hidden');
                dropzoneContent.classList.add('hidden');
            }
        }
    </script>
</body>
</html>