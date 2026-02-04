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
    $idPengguna = stripslashes($_REQUEST['idPengguna']);    // removes backslashes
    $idPengguna = mysqli_real_escape_string($conn, $idPengguna);
    $kataLaluan = stripslashes($_REQUEST['kataLaluan']);
    $kataLaluan = mysqli_real_escape_string($conn, $kataLaluan);
    // Check user is exist in the database
    $query    = "SELECT * FROM `Pengguna_1` WHERE idPengguna='$idPengguna' AND kataLaluan='$kataLaluan'";
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $rows = mysqli_num_rows($result);
    if ($rows == 1) {
        $_SESSION['idPengguna'] = $idPengguna;
        // Redirect to user dashboard page
        echo "<script>
            alert('Log Masuk Berjaya!');
            window.location.href='dashboard.php';
        </script>";
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
<title>Login - Football Club Voting System</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;family=Noto+Sans:wght@100..900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#11d411",
                    "background-light": "#f6f8f6",
                    "background-dark": "#102210",
                },
                fontFamily: {
                    "display": ["Lexend", "sans-serif"],
                    "sans": ["Lexend", "Noto Sans", "sans-serif"]
                },
            },
        },
    }
</script>
<style>
    body { font-family: 'Lexend', sans-serif; }
    .stadium-overlay {
        background: linear-gradient(rgba(16, 34, 16, 0.85), rgba(16, 34, 16, 0.95)), url('https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=2070&auto=format&fit=crop');
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
        <div class="bg-primary p-3 rounded-full shadow-lg">
            <span class="material-symbols-outlined text-background-dark text-4xl">sports_soccer</span>
        </div>
        <h1 class="text-white text-2xl font-bold tracking-tight text-center">Jawatankuasa Kelab Bola Sepak</h1>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-[440px] bg-white dark:bg-background-dark rounded-xl shadow-2xl overflow-hidden border border-white/10">
        <div class="px-8 pt-10 pb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Portal Undian</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm font-normal">Sila masukkan maklumat anda untuk mengundi</p>
            <?php if($error_msg) echo '<p class="text-red-500 mt-4 text-sm font-bold">'.$error_msg.'</p>'; ?>
        </div>
        
        <!-- Login Form -->
        <form class="px-8 pb-10 space-y-6" method="post" name="login">
            <!-- User ID Field -->
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 ml-1" for="idPengguna">ID Pengguna</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-400 text-xl">badge</span>
                    </div>
                    <input class="block w-full pl-10 pr-3 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-gray-400" id="idPengguna" name="idPengguna" placeholder="Masukkan ID (cth. D6290)" type="text" required />
                </div>
            </div>
            
            <!-- Password Field -->
            <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 ml-1" for="kataLaluan">Kata Laluan</label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-gray-400 text-xl">lock</span>
                    </div>
                    <input class="block w-full pl-10 pr-3 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-gray-400" id="kataLaluan" name="kataLaluan" placeholder="••••••••" type="password" required />
                </div>
            </div>
            
            <!-- Login Button -->
            <button class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 text-background-dark font-bold py-3.5 px-4 rounded-lg shadow-lg shadow-primary/20 transition-all active:scale-[0.98]" type="submit">
                <span class="material-symbols-outlined">login</span>
                <span>Log Masuk</span>
            </button>
        </form>
        
        <!-- Card Footer -->
        <div class="bg-gray-50 dark:bg-white/5 px-8 py-4 border-t border-gray-100 dark:border-white/10">
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">person_add</span>
                    <a class="hover:text-primary font-bold" href="register.php">Daftar Akaun Baru</a>
                </div>
                <span>Sistem v1.2.0</span>
            </div>
        </div>
    </div>
    
    <!-- System Footer -->
    <div class="mt-8 text-white/50 text-xs font-light text-center">
        <p>© 2024 Jawatankuasa Kelab Bola Sepak. Hak Cipta Terpelihara.</p>
    </div>
</div>
</body>
</html>
