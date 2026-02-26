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

    // Validasi format ID: 1 huruf + 4 nombor (cth. D6557)
    if (!preg_match('/^[A-Za-z]\d{4}$/', $idPengguna)) {
        $error_msg = "Format ID Pengguna tidak sah. Mesti bermula dengan 1 huruf diikuti 4 nombor (contoh: D6557).";
    } else {
        // Semak ID wujud
        $check_query = "SELECT * FROM `Pengguna_1` WHERE idPengguna='$idPengguna'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error_msg = "ID Pengguna sudah wujud.";
        } else {
            $query = "INSERT INTO `Pengguna_1` (idPengguna, namaPengguna, kataLaluan) VALUES ('$idPengguna', '$namaPengguna', '$kataLaluan')";
            $result = mysqli_query($conn, $query);
            if ($result) {
                echo "<script>window.location.href='login.php?registered=1';</script>";
                exit();
            } else {
                $error_msg = "Pendaftaran gagal. Sila cuba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Daftar Akaun - Portal Undian Kelab Bola Sepak</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
<div class="relative flex min-h-screen w-full flex-col stadium-overlay items-center justify-center p-4">
    <div class="mb-8 flex flex-col items-center gap-2">
        <div class="bg-primary p-3 rounded-full shadow-lg">
            <span class="material-symbols-outlined text-background-dark text-4xl">person_add</span>
        </div>
        <h1 class="text-white text-2xl font-bold tracking-tight text-right w-full">Pendaftaran Keahlian Baru</h1>
    </div>

    <div class="w-full max-w-[440px] bg-white dark:bg-background-dark rounded-xl shadow-2xl overflow-hidden border border-white/10">
        <div class="px-8 pt-10 pb-6 text-center">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Daftar Pengguna</h2>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-2 font-bold uppercase tracking-widest">Sertai Kelab Bola Sepak Kami</p>
        </div>
        
        <form class="px-8 pb-10 space-y-4" method="post">
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1 tracking-widest">ID Pengguna</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">&#xe853;</span>
                    <input class="block w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none transition-all font-bold" name="idPengguna" placeholder="cth. D6557" type="text" pattern="[A-Za-z]\d{4}" title="Format: 1 huruf + 4 nombor (contoh: D6557)" required />
                </div>
            </div>
            
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1 tracking-widest">Nama Penuh</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">&#xe85e;</span>
                    <input class="block w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none transition-all font-bold" name="namaPengguna" placeholder="Nama Penuh" type="text" required />
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-500 ml-1 tracking-widest">Kata Laluan</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">&#xef64;</span>
                    <input class="block w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-white/10 rounded-xl bg-slate-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none transition-all font-bold" name="kataLaluan" placeholder="Kata Laluan" type="password" required />
                </div>
            </div>
            
            <button class="w-full mt-6 bg-primary text-black font-black py-4 px-4 rounded-xl shadow-[0_0_20px_rgba(128,242,13,0.3)] hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-sm" type="submit">
                Daftar Sekarang
            </button>
        </form>
        
        <div class="bg-slate-50 dark:bg-white/5 px-8 py-6 border-t border-slate-100 dark:border-white/10 text-center flex flex-col gap-2">
            <a href="login.php" class="text-xs font-black text-slate-500 hover:text-primary transition-colors uppercase tracking-widest">Sudah ada akaun? Log Masuk</a>
        </div>
    </div>
</div>

<script>
    // Handle Registration Errors
    <?php if(isset($error_msg) && $error_msg): ?>
    Swal.fire({
        title: 'Ralat!',
        text: '<?php echo $error_msg; ?>',
        icon: 'error',
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Cuba Lagi'
    });
    <?php endif; ?>
</script>
</body>
</html>
