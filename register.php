<?php
require('db_config.php');

$error_msg = "";
$success_msg = "";

if (isset($_POST['idPengguna'])) {
    $idPengguna = stripslashes($_REQUEST['idPengguna']);
    $idPengguna = mysqli_real_escape_string($conn, $idPengguna);
    $namaPengguna = stripslashes($_REQUEST['namaPengguna']);
    $namaPengguna = mysqli_real_escape_string($conn, $namaPengguna);
    $kataLaluan = stripslashes($_REQUEST['kataLaluan']);
    $kataLaluan = mysqli_real_escape_string($conn, $kataLaluan);

    // Check if ID exists
    $check_query = "SELECT * FROM `Pengguna_1` WHERE idPengguna='$idPengguna'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error_msg = "ID Pengguna sudah wujud.";
    } else {
        $query = "INSERT INTO `Pengguna_1` (idPengguna, namaPengguna, kataLaluan) VALUES ('$idPengguna', '$namaPengguna', '$kataLaluan')";
        $result = mysqli_query($conn, $query);
        if ($result) {
            echo "<script>
                alert('Pendaftaran Berjaya! Sila log masuk.');
                window.location.href='login.php';
            </script>";
        } else {
            $error_msg = "Pendaftaran gagal. Sila cuba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Register - Football Club Voting System</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: { "primary": "#11d411", "background-light": "#f6f8f6", "background-dark": "#102210" },
                fontFamily: { "display": ["Lexend", "sans-serif"], "sans": ["Lexend", "sans-serif"] },
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
<div class="relative flex min-h-screen w-full flex-col stadium-overlay items-center justify-center p-4">
    <div class="mb-8 flex flex-col items-center gap-2">
        <div class="bg-primary p-3 rounded-full shadow-lg">
            <span class="material-symbols-outlined text-background-dark text-4xl">person_add</span>
        </div>
        <h1 class="text-white text-2xl font-bold tracking-tight text-center">Pendaftaran Keahlian Baru</h1>
    </div>

    <div class="w-full max-w-[440px] bg-white dark:bg-background-dark rounded-xl shadow-2xl overflow-hidden border border-white/10">
        <div class="px-8 pt-10 pb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cipta Akaun</h2>
            <?php if($error_msg) echo '<p class="text-red-500 mt-4 text-sm font-bold">'.$error_msg.'</p>'; ?>
            <?php if($success_msg) echo '<p class="text-green-500 mt-4 text-sm font-bold">'.$success_msg.'</p>'; ?>
        </div>
        
        <?php if(!$success_msg) { ?>
        <form class="px-8 pb-10 space-y-4" method="post">
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 ml-1">ID Pengguna</label>
                <input class="block w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none" name="idPengguna" placeholder="cth. D1234" type="text" required />
            </div>
            
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 ml-1">Nama Penuh</label>
                <input class="block w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none" name="namaPengguna" placeholder="Nama Penuh" type="text" required />
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 ml-1">Kata Laluan</label>
                <input class="block w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none" name="kataLaluan" placeholder="Kata Laluan" type="password" required />
            </div>
            
            <button class="w-full mt-4 bg-primary text-background-dark font-bold py-3.5 px-4 rounded-lg shadow-lg hover:bg-primary/90 transition-all" type="submit">
                Daftar
            </button>
        </form>
        <?php } ?>
        
        <div class="bg-gray-50 dark:bg-white/5 px-8 py-4 border-t border-gray-100 dark:border-white/10 text-center">
            <a href="login.php" class="text-sm font-bold text-gray-500 hover:text-primary">Sudah ada akaun? Log Masuk</a>
        </div>
    </div>
</div>
</body>
</html>
