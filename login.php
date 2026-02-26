<?php
require('db_config.php');
session_start();

$error_msg = "";

if (isset($_SESSION['idPengguna'])) {
    header("Location: dashboard.php");
    exit();
}

// When form submitted, check and create user session.
if (isset($_POST['idPengguna'])) {
    $idPengguna = stripslashes($_REQUEST['idPengguna']);    // buang backslash
    $idPengguna = mysqli_real_escape_string($conn, $idPengguna);
    $kataLaluan = stripslashes($_REQUEST['kataLaluan']);
    $kataLaluan = mysqli_real_escape_string($conn, $kataLaluan);
    // Semak pengguna wujud dalam pangkalan data
    $query    = "SELECT * FROM `Pengguna_1` WHERE idPengguna='$idPengguna' AND kataLaluan='$kataLaluan'";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
    $rows = mysqli_num_rows($result);
    if ($rows == 1) {
        $_SESSION['idPengguna'] = $idPengguna;
        // Log masuk berjaya
        if (in_array($idPengguna, ['D6290', 'admin'])) {
            echo "<script>alert('Log Masuk Admin Berjaya!'); window.location.href='admin.php';</script>";
        } else {
            echo "<script>alert('Log Masuk Berjaya!'); window.location.href='dashboard.php';</script>";
        }
    } else {
        $error_msg = "ID Pengguna atau Kata Laluan salah.";
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Log Masuk - Portal Undian Kelab Bola Sepak</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;family=Noto+Sans:wght@100..900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: { 
                    "primary": "#80f20d", 
                    "background-light": "#f7f8f5", 
                    "background-dark": "#0a0f05" 
                },
                fontFamily: { 
                    "display": ["Plus Jakarta Sans", "Inter", "sans-serif"], 
                    "sans": ["Plus Jakarta Sans", "Inter", "sans-serif"] 
                },
            },
        },
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .stadium-overlay {
        background: linear-gradient(rgba(10, 15, 5, 0.8), rgba(10, 15, 5, 0.95)), url('https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
    }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col">
<!-- Main Background Wrapper -->
<div class="relative flex min-h-screen w-full flex-col stadium-overlay items-center justify-center p-4">
    <!-- Header/Logo Section -->
    <div class="mb-8 flex flex-col items-center gap-2">
        <div class="bg-primary p-3 rounded-full shadow-[0_0_20px_rgba(30,215,0,0.3)]">
            <span class="material-symbols-outlined text-black text-4xl font-black">&#xe52e;</span>
        </div>
        <h1 class="text-white text-3xl font-black tracking-tight text-center">Jawatankuasa Kelab Bola Sepak</h1>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-[440px] bg-white dark:bg-card-dark rounded-3xl shadow-2xl overflow-hidden border border-white/5">
        <div class="px-8 pt-10 pb-6 text-center">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Log Masuk</h2>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 font-bold uppercase tracking-widest">Sistem Undian Kelab Bola Sepak</p>
        </div>
        
        <!-- Login Form -->
        <form class="px-8 pb-10 space-y-4" method="post" name="login">
            <!-- User ID Field -->
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1 tracking-widest">ID Pengguna</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">&#xe853;</span>
                    <input class="block w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none transition-all font-bold" name="idPengguna" placeholder="Masukkan ID anda" required type="text"/>
                </div>
            </div>
            
            <!-- Password Field -->
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1 tracking-widest">Kata Laluan</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">&#xef64;</span>
                    <input class="block w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none transition-all font-bold" name="kataLaluan" placeholder="••••••••" required type="password"/>
                </div>
            </div>
            
            <!-- Login Button -->
            <button class="w-full flex items-center justify-center gap-3 bg-primary hover:bg-primary/90 text-black font-black py-4.5 rounded-2xl shadow-xl shadow-primary/20 transition-all active:scale-[0.98] uppercase tracking-widest text-xs" type="submit">
                <span class="material-symbols-outlined">&#xea77;</span>
                <span>Log Masuk</span>
            </button>
        </form>
        
        <!-- Card Footer -->
        <div class="bg-slate-50 dark:bg-white/5 px-10 py-6 border-t border-slate-100 dark:border-white/5">
            <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">&#xe7fe;</span>
                    <a class="hover:text-primary transition-colors" href="register.php">Daftar Akaun Baru</a>
                </div>
                <span class="opacity-50">Sistem v2.1.0</span>
            </div>
        </div>
    </div>
    
    <!-- System Footer -->
    <div class="mt-10 text-white/30 text-[10px] font-black uppercase tracking-[0.3em] text-center">
        <p>© 2024 Jawatankuasa Kelab Bola Sepak • Hak Cipta Terpelihara</p>
    </div>
</div>
</body>
</html>
