<?php
session_start();
include '../../config/koneksi.php';
// Load file PHPMailer / Fungsi Email Anda

// Pastikan ID adalah angka untuk mencegah SQL Injection
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

// Ambil data aspirasi
$query = mysqli_prepare($conn, "SELECT * FROM aspirasi WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$a = mysqli_fetch_assoc($result);

if (!$a) {
    echo "<div class='min-h-screen flex items-center justify-center bg-gray-50'>
            <div class='text-center p-8 bg-white rounded-3xl shadow-xl border border-gray-100'>
                <h2 class='text-2xl font-bold text-gray-800 mb-4'>Data tidak ditemukan</h2>
                <a href='index.php' class='text-green-600 font-bold hover:underline'>Kembali ke Daftar</a>
            </div>
          </div>";
    exit;
}

// Tandai sebagai 'dibaca' secara otomatis
mysqli_query($conn, "UPDATE aspirasi SET status='dibaca' WHERE id='$id'");

// --- LOGIKA PENGIRIMAN EMAIL ---
$notifEmail = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_balasan'])) {
    $pesanBalasan = mysqli_real_escape_string($conn, $_POST['pesan_balasan']);
    $emailWarga   = $_POST['email_warga'];
    $namaWarga    = $_POST['nama_warga'];

    if (function_exists('kirimBalasanOtomatis')) {
        if (kirimBalasanOtomatis($emailWarga, $namaWarga, $pesanBalasan)) {
            $notifEmail = ['type' => 'success', 'msg' => 'Balasan berhasil terkirim ke ' . $emailWarga];
        } else {
            $notifEmail = ['type' => 'error', 'msg' => 'Gagal mengirim email. Cek konfigurasi SMTP.'];
        }
    } else {
        $notifEmail = ['type' => 'error', 'msg' => 'Sistem email tidak ditemukan.'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aspirasi - <?= htmlspecialchars($a['nama']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print { .no-print { display: none !important; } .print-only { display: block !important; } }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen text-slate-900">

    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="mb-8 flex items-center justify-between no-print">
            <a href="index.php" class="group flex items-center text-slate-500 hover:text-green-700 font-bold text-sm transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform"></i>
                KEMBALI KE DAFTAR
            </a>
            <div class="flex items-center space-x-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <span>Aspirasi</span>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="text-slate-900">Detail Laporan</span>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            
            <div class="p-8 md:p-10 border-b border-slate-50 bg-slate-50/30">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center space-x-5">
                        <div class="w-20 h-20 bg-green-600 text-white rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-green-200">
                            <i data-lucide="user" class="w-10 h-10"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-green-600 uppercase tracking-[0.2em] mb-1">Identitas Warga</p>
                            <h1 class="text-3xl font-extrabold text-slate-900 leading-none mb-2"><?= htmlspecialchars($a['nama']) ?></h1>
                            <div class="flex items-center text-slate-500 font-medium">
                                <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                                <?= htmlspecialchars($a['email']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col md:items-end">
                        <span class="inline-flex items-center px-5 py-2 bg-green-50 text-green-700 rounded-2xl text-xs font-black uppercase tracking-widest border border-green-100">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                            Telah Terbaca
                        </span>
                        <p class="text-[11px] text-slate-400 font-bold mt-3 flex items-center">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1.5"></i>
                            <?= date('d M Y • H:i', strtotime($a['tanggal'])) ?> WIB
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    
                    <div class="md:col-span-2 space-y-10">
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-5 flex items-center">
                                <i data-lucide="message-circle" class="w-4 h-4 mr-2 text-green-600"></i>
                                Isi Aspirasi & Keluhan
                            </h4>
                            <div class="relative">
                                <i data-lucide="quote" class="absolute -top-4 -left-4 w-10 h-10 text-slate-100 -z-10 rotate-12"></i>
                                <div class="bg-slate-50 rounded-[2rem] p-8 text-slate-700 leading-relaxed text-lg font-medium border border-slate-100 italic">
                                    "<?= nl2br(htmlspecialchars($a['pesan'])) ?>"
                                </div>
                            </div>
                        </div>

                        <?php if ($a['gambar']): ?>
                        <div>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-5">Bukti Lampiran</h4>
                            <div class="relative group inline-block">
                                <img src="../../assets/img/aspirasi/<?= $a['gambar'] ?>" 
                                     class="rounded-[2rem] shadow-2xl border-4 border-white max-w-full h-auto transition-transform group-hover:scale-[1.01]"
                                     alt="Lampiran">
                                <a href="../../assets/img/aspirasi/<?= $a['gambar'] ?>" target="_blank" 
                                   class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-[2rem] text-white font-bold backdrop-blur-sm">
                                   <i data-lucide="maximize" class="mr-2"></i> Perbesar Foto
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-8">
                        <div class="p-6 bg-white border border-slate-100 rounded-3xl shadow-sm">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Wilayah Terkait</h4>
                            <div class="flex items-center p-3 bg-blue-50 rounded-2xl text-blue-700">
                                <i data-lucide="map-pin" class="w-6 h-6 mr-3"></i>
                                <span class="font-black text-sm uppercase"><?= htmlspecialchars($a['kecamatan']) ?></span>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-900 rounded-3xl shadow-xl no-print">
                            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Opsi Manajemen</h4>
                            <button onclick="window.print()" class="w-full flex items-center justify-center px-6 py-4 bg-white/10 text-white rounded-2xl font-bold hover:bg-white/20 transition-all text-sm mb-3">
                                <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                                Cetak Laporan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-16 no-print">
                    <div class="p-10 bg-green-700 rounded-[3rem] shadow-2xl shadow-green-200 relative overflow-hidden text-white">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                        
                        <h3 class="text-2xl font-black mb-2 flex items-center">
                            <i data-lucide="send" class="mr-3"></i> Respon Cepat
                        </h3>
                        <p class="text-green-100 text-sm mb-8 opacity-80 font-medium uppercase tracking-widest">Kirim jawaban resmi melalui email warga</p>
                        
                        <?php if ($notifEmail): ?>
                            <div class="mb-6 p-5 rounded-2xl text-sm font-bold flex items-center <?= $notifEmail['type'] == 'success' ? 'bg-white text-green-800' : 'bg-red-500 text-white' ?>">
                                <i data-lucide="<?= $notifEmail['type'] == 'success' ? 'check-circle' : 'alert-octagon' ?>" class="w-5 h-5 mr-3"></i>
                                <?= $notifEmail['msg'] ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="email_warga" value="<?= $a['email'] ?>">
                            <input type="hidden" name="nama_warga" value="<?= $a['nama'] ?>">
                            
                            <textarea name="pesan_balasan" rows="5" required
                                      class="w-full p-6 bg-white/10 border border-white/20 rounded-[2rem] focus:bg-white focus:text-slate-900 focus:ring-0 outline-none transition-all placeholder:text-green-200" 
                                      placeholder="Tuliskan pesan tanggapan Anda di sini..."></textarea>
                            
                            <div class="flex justify-end">
                                <button type="submit" name="kirim_balasan" class="px-10 py-4 bg-yellow-400 text-green-900 rounded-2xl font-black hover:bg-white transition-all flex items-center shadow-xl shadow-green-900/20">
                                    KIRIM BALASAN SEKARANG
                                    <i data-lucide="chevron-right" class="w-5 h-5 ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>