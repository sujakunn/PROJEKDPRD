<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi login & CSRF Token (Keamanan tambahan)
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

$pesanSukses = '';
$pesanError = '';

// 2. Proses Update dengan Prepared Statements
if (isset($_POST['update_settings'])) {
    // Validasi CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $pesanError = "Keamanan tidak valid. Silakan coba lagi.";
    } else {
        $conn->begin_transaction();
        try {
            foreach ($_POST['settings'] as $key => $value) {
                $stmt = $conn->prepare("UPDATE pengaturan SET meta_value = ? WHERE meta_key = ?");
                $stmt->bind_param("ss", $value, $key);
                $stmt->execute();
            }
            $conn->commit();
            $pesanSukses = "Pengaturan berhasil diperbarui secara sistematis!";
        } catch (Exception $e) {
            $conn->rollback();
            $pesanError = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// 3. Ambil data dengan index yang aman
$query = mysqli_query($conn, "SELECT * FROM pengaturan");
$settings = [];
while($row = mysqli_fetch_assoc($query)) {
    $settings[$row['meta_key']] = $row['meta_value'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Settings - Premium Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .form-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .form-card:focus-within { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="p-4 md:p-12">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">System Configuration</h1>
                <p class="text-slate-500 font-medium">Kelola identitas sistem, SEO, dan integrasi API secara terpusat.</p>
            </div>
            <a href="index.php" class="p-3 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition-all shadow-sm group">
                <i data-lucide="arrow-left" class="w-6 h-6 text-slate-400 group-hover:text-slate-900"></i>
            </a>
        </div>

        <?php if($pesanSukses): ?>
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center rounded-2xl animate-in fade-in slide-in-from-top-4 duration-500">
                <i data-lucide="check-circle" class="w-5 h-5 mr-3"></i>
                <span class="font-bold"><?= $pesanSukses ?></span>
            </div>
        <?php endif; ?>

        <?php if($pesanError): ?>
            <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-700 flex items-center rounded-2xl">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                <span class="font-bold"><?= $pesanError ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm form-card">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-indigo-50 rounded-lg mr-4"><i data-lucide="settings" class="w-5 h-5 text-indigo-600"></i></div>
                    <h3 class="text-lg font-bold text-slate-800">Core Identity</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Nama Legislator</label>
                        <input type="text" name="settings[nama_legislator]" value="<?= htmlspecialchars($settings['nama_legislator'] ?? '') ?>" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-semibold text-slate-700">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Judul Web (SEO)</label>
                        <input type="text" name="settings[judul_web]" value="<?= htmlspecialchars($settings['judul_web'] ?? '') ?>" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-indigo-500 focus:bg-white transition-all outline-none font-semibold text-slate-700">
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm form-card">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-rose-50 rounded-lg mr-4"><i data-lucide="phone" class="w-5 h-5 text-rose-600"></i></div>
                    <h3 class="text-lg font-bold text-slate-800">Communication</h3>
                </div>
                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Alamat Fisik</label>
                        <textarea name="settings[alamat_kantor]" rows="3" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-rose-500 focus:bg-white transition-all outline-none font-semibold text-slate-700"><?= htmlspecialchars($settings['alamat_kantor'] ?? '') ?></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Email Official</label>
                            <input type="email" name="settings[email_kantor]" value="<?= htmlspecialchars($settings['email_kantor'] ?? '') ?>" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-rose-500 transition-all outline-none font-semibold text-slate-700">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Hotline</label>
                            <input type="text" name="settings[telp_kantor]" value="<?= htmlspecialchars($settings['telp_kantor'] ?? '') ?>" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-rose-500 transition-all outline-none font-semibold text-slate-700">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm form-card">
                <div class="flex items-center mb-6">
                    <div class="p-2 bg-violet-50 rounded-lg mr-4"><i data-lucide="zap" class="w-5 h-5 text-violet-600"></i></div>
                    <h3 class="text-lg font-bold text-slate-800">Social Integration</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-500 transition-colors"><i data-lucide="facebook" class="w-5 h-5"></i></div>
                        <input type="text" name="settings[link_facebook]" placeholder="Facebook URL" value="<?= htmlspecialchars($settings['link_facebook'] ?? '') ?>" class="w-full pl-12 pr-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-blue-500 outline-none font-semibold text-slate-700 transition-all">
                    </div>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-pink-500 transition-colors"><i data-lucide="instagram" class="w-5 h-5"></i></div>
                        <input type="text" name="settings[link_instagram]" placeholder="Instagram URL" value="<?= htmlspecialchars($settings['link_instagram'] ?? '') ?>" class="w-full pl-12 pr-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:border-pink-500 outline-none font-semibold text-slate-700 transition-all">
                    </div>
                </div>
            </div>

            <button type="submit" name="update_settings" class="group w-full bg-slate-900 hover:bg-black text-white font-bold py-5 rounded-[2rem] shadow-2xl transition-all flex items-center justify-center space-x-3">
                <i data-lucide="save" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                <span class="uppercase tracking-[0.2em] text-xs">Authorize & Sync Settings</span>
            </button>
        </form>
        
        <p class="mt-8 text-center text-slate-400 text-xs font-medium uppercase tracking-widest">Global Configuration Engine v2.0</p>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>