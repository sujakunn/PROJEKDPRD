<?php
session_start();
include '../config/koneksi.php';

// Jika sudah login, langsung lempar ke index
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");
    
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // GUNAKAN password_verify jika di database di-hash, 
        // atau gunakan == jika masih teks biasa (tapi segera ganti ke hash!)
        if (password_verify($password, $row['password']) || $password == $row['password']) { 
            
            // --- BARIS PALING PENTING YANG HILANG ---
            $_SESSION['login'] = true; 
            // ---------------------------------------
            
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['nama_lengkap'];
            
            header("Location: index.php");
            exit;
        }
    }
    $error = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - PKB Kab. Tangerang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-slate-200">
        <div class="text-center mb-8">
            <img src="../assets/img/logo-pkb.png" class="h-16 mx-auto mb-4" alt="Logo">
            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Panel Admin</h2>
            <p class="text-slate-500 text-sm italic">Silakan login untuk mengelola konten</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold text-center border border-red-100">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Username</label>
                <input type="text" name="username" required autocomplete="off"
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all font-semibold">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Password</label>
                <input type="password" name="password" required 
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-green-500 outline-none transition-all font-semibold">
            </div>
            <button type="submit" name="login" 
                class="w-full bg-green-700 hover:bg-green-800 text-white font-black py-4 rounded-2xl shadow-lg shadow-green-900/20 transition-all uppercase tracking-widest text-xs active:scale-95">
                Masuk Sistem
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <a href="../index.php" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-green-600 transition-colors">
                &larr; Kembali ke Website
            </a>
        </div>
    </div>
</body>
</html>