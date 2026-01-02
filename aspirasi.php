<?php
session_start();

// Inisialisasi angka captcha
if (!isset($_SESSION['angka1']) || !isset($_SESSION['angka2'])) {
    $_SESSION['angka1'] = rand(1, 9);
    $_SESSION['angka2'] = rand(1, 9);
    $_SESSION['captcha'] = $_SESSION['angka1'] + $_SESSION['angka2'];
}

include 'config/koneksi.php'; 

$pesanError  = '';
$pesanSukses = '';

if (isset($_POST['kirim'])) {
    if ($_POST['captcha'] != $_SESSION['captcha']) {
        $pesanError = 'Verifikasi keamanan salah.';
    } else {
        $nama      = mysqli_real_escape_string($conn, htmlspecialchars($_POST['nama']));
        $email     = mysqli_real_escape_string($conn, htmlspecialchars($_POST['email']));
        $kecamatan = mysqli_real_escape_string($conn, htmlspecialchars($_POST['kecamatan']));
        $pesan     = mysqli_real_escape_string($conn, htmlspecialchars($_POST['pesan']));

        $gambar = NULL;
        if (!empty($_FILES['gambar']['name'])) {
            $fileSize = $_FILES['gambar']['size'];
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, ['jpg','jpeg','png'])) {
                $pesanError = 'Gunakan format JPG/PNG.';
            } elseif ($fileSize > 2097152) {
                $pesanError = 'Maksimal ukuran 2MB.';
            } else {
                $gambar = uniqid('aspirasi_') . '.' . $ext;
                move_uploaded_file($_FILES['gambar']['tmp_name'], "assets/img/aspirasi/" . $gambar);
            }
        }

        if ($pesanError == '') {
            $query = "INSERT INTO aspirasi (nama, email, kecamatan, pesan, gambar, tanggal, status) 
                      VALUES ('$nama','$email','$kecamatan','$pesan','$gambar', NOW(), 'baru')";
            
            if (mysqli_query($conn, $query)) {
                $pesanSukses = 'Aspirasi Anda berhasil terkirim.';
                $_SESSION['angka1'] = rand(1, 9);
                $_SESSION['angka2'] = rand(1, 9);
                $_SESSION['captcha'] = $_SESSION['angka1'] + $_SESSION['angka2'];
            } else {
                $pesanError = 'Terjadi kesalahan sistem.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirasi - Fraksi PKB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] flex flex-col min-h-screen text-slate-900">

    <?php include 'includes/header.php'; ?>

    <main class="flex-grow">
        <section class="bg-gradient-to-br from-green-950 to-green-800 text-white py-10">
            <div class="max-w-7xl mx-auto px-4 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center md:space-x-4">
                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl mb-3 md:mb-0 shadow-lg">
                        <i data-lucide="message-square" class="w-6 h-6 text-green-300"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight uppercase leading-tight">Layanan Aspirasi</h1>
                        <p class="text-green-100 text-sm opacity-80">Sampaikan saran Anda untuk Tangerang yang lebih baik.</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 -mt-8 pb-12">
            <div class="bg-white rounded-3xl shadow-xl shadow-green-900/5 overflow-hidden border border-slate-100">
                <div class="p-6 md:p-10">
                    
                    <?php if ($pesanError): ?>
                        <div class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 text-red-800 text-xs font-bold rounded-r-xl flex items-center">
                            <i data-lucide="alert-circle" class="mr-2 w-4 h-4"></i> <?= $pesanError ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($pesanSukses): ?>
                        <div class="mb-6 p-3 bg-green-50 border-l-4 border-green-500 text-green-800 text-xs font-bold rounded-r-xl flex items-center">
                            <i data-lucide="check-circle" class="mr-2 w-4 h-4"></i> <?= $pesanSukses ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 ml-1">Nama Lengkap</label>
                                <input type="text" name="nama" required placeholder="Nama Anda"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-500/20 outline-none text-sm font-medium">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 ml-1">Alamat Email</label>
                                <input type="email" name="email" required placeholder="Email Anda"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-500/20 outline-none text-sm font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 ml-1">Kecamatan</label>
                            <select name="kecamatan" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-500/20 outline-none text-sm font-medium">
                                <option value="">Pilih Kecamatan</option>
                                <?php 
                                    $kecamatan_list = ['Sukamulya', 'Kresek', 'Gunung Kaler', 'Mekar Baru', 'Kronjo', 'Kemiri', 'Mauk', 'Sukadiri'];
                                    foreach($kecamatan_list as $kec) echo "<option value='$kec'>$kec</option>";
                                ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 ml-1">Pesan Aspirasi</label>
                            <textarea name="pesan" required rows="4" placeholder="Tuliskan aspirasi Anda..."
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-green-500/20 outline-none text-sm font-medium"></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 ml-1">Lampiran Foto (Opsional)</label>
                                <label class="flex items-center justify-center w-full h-24 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer bg-slate-50 hover:bg-green-50 transition-all overflow-hidden">
                                    <div id="upload_placeholder" class="text-center">
                                        <i data-lucide="camera" class="w-5 h-5 text-slate-400 mx-auto mb-1"></i>
                                        <p class="text-[10px] font-bold text-slate-500">Klik untuk Upload</p>
                                    </div>
                                    <img id="image_preview" class="hidden h-full w-full object-cover">
                                    <input type="file" name="gambar" class="hidden" accept="image/*" onchange="previewImage(event)">
                                </label>
                            </div>
                            <div class="bg-green-50 p-3 rounded-2xl border border-green-100">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5 ml-1">Keamanan (Captcha)</label>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-black text-green-700 ml-2">
                                        <?= $_SESSION['angka1'] ?> + <?= $_SESSION['angka2'] ?> =
                                    </span>
                                    <input type="number" name="captcha" required placeholder="?" 
                                        class="w-20 px-3 py-2 bg-white border border-green-200 rounded-xl focus:ring-2 focus:ring-green-500/20 outline-none text-center font-black">
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-[11px] text-slate-500 leading-relaxed italic">
                                Dengan mengirimkan form ini, Anda menyetujui bahwa informasi yang diberikan akan diproses 
                                sesuai dengan prosedur penyerapan aspirasi dan data pribadi Anda akan dijaga kerahasiaannya.
                            </p>
                        </div>

                        <button type="submit" name="kirim" 
                            class="w-full bg-green-700 hover:bg-green-800 text-white font-black py-4 rounded-2xl shadow-lg transition-all flex items-center justify-center text-xs uppercase tracking-widest active:scale-95">
                            <i data-lucide="send" class="mr-2 w-4 h-4"></i> Kirim Aspirasi
                        </button>
                    </form>
                </div>
            </div>

<div class="mt-12 max-w-4xl mx-auto px-4">
    
    <div class="bg-white p-3 rounded-2xl border border-slate-100 flex items-center justify-center space-x-3 shadow-sm max-w-2xl mx-auto mb-8">
        <div class="w-9 h-9 bg-green-50 text-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="help-circle" class="w-5 h-5"></i>
        </div>
        <div class="text-center md:text-left">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">Informasi Bantuan</p>
            <p class="text-xs md:text-sm font-bold text-slate-700 uppercase tracking-tight">Cara Lain Menyampaikan Aspirasi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <div class="group bg-white p-5 rounded-[1.5rem] border border-slate-100 flex items-center space-x-4 shadow-sm hover:border-blue-300 hover:shadow-md transition-all duration-300">
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <i data-lucide="mail" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider leading-none mb-1">Email Resmi</p>
                <p class="text-xs font-bold text-slate-700 truncate">syukha3@gmail.com</p>
            </div>
        </div>

        <div class="group bg-white p-5 rounded-[1.5rem] border border-slate-100 flex items-center space-x-4 shadow-sm hover:border-amber-300 hover:shadow-md transition-all duration-300">
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <i data-lucide="phone" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider leading-none mb-1">Hotline Pelayanan</p>
                <p class="text-xs font-bold text-slate-700 font-mono tracking-tighter">(021) 5962-XXXX</p>
            </div>
        </div>

        <div class="group bg-white p-5 rounded-[1.5rem] border border-slate-100 flex items-center space-x-4 shadow-sm hover:border-green-300 hover:shadow-md transition-all duration-300">
            <div class="w-11 h-11 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider leading-none mb-1">Lokasi Kantor</p>
                <p class="text-xs font-bold text-slate-700 leading-tight">DPRD Kab. Tangerang</p>
            </div>
        </div>
        
    </div>
</div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        lucide.createIcons();
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image_preview');
            const placeholder = document.getElementById('upload_placeholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>