<?php
require('db_config.php');

$error_msg = "";
$show_success = false;

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
            $error_msg = "ID Pengguna ini sudah wujud dalam sistem. Sila gunakan ID lain.";
        } else {
            $query = "INSERT INTO `Pengguna_1` (idPengguna, namaPengguna, kataLaluan) VALUES ('$idPengguna', '$namaPengguna', '$kataLaluan')";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $show_success = true;
            } else {
                $error_msg = "Pendaftaran gagal. Sila cuba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="ms">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Daftar Akaun - Jawatankuasa Kelab Bola Sepak</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
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
    .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; display: flex; align-items: center; justify-content: center; }
    .modal-card { background: white; border-radius: 1rem; padding: 2rem; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col">

<?php if ($show_success): ?>
<!-- Success Popup Modal -->
<div class="modal-backdrop" id="successModal">
    <div class="modal-card">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-green-500 text-4xl">check_circle</span>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Pendaftaran Berjaya!</h3>
        <p class="text-gray-600 text-sm mb-2">Akaun anda telah berjaya didaftarkan.</p>
        <p class="text-gray-500 text-xs mb-6">Anda akan diarahkan ke halaman log masuk...</p>
        <button onclick="window.location.href='login.php'" 
                class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg transition-all">
            Log Masuk Sekarang
        </button>
    </div>
</div>
<script>setTimeout(function(){ window.location.href='login.php'; }, 2500);</script>
<?php endif; ?>

<?php if ($error_msg): ?>
<!-- Error Popup Modal -->
<div class="modal-backdrop" id="errorModal">
    <div class="modal-card">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-red-500 text-4xl">error</span>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Pendaftaran Gagal</h3>
        <p class="text-gray-600 text-sm mb-6"><?php echo $error_msg; ?></p>
        <button onclick="document.getElementById('errorModal').style.display='none'" 
                class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg transition-all">
            Cuba Lagi
        </button>
    </div>
</div>
<?php endif; ?>

<div class="relative flex min-h-screen w-full flex-col stadium-overlay items-center justify-center p-4">
    <!-- Header -->
    <div class="mb-8 flex flex-col items-end w-full max-w-[440px] gap-2">
        <div class="flex items-center gap-3">
            <h1 class="text-white text-2xl font-bold tracking-tight text-right">Pendaftaran Keahlian Baru</h1>
            <div class="bg-primary p-3 rounded-full shadow-lg">
                <span class="material-symbols-outlined text-background-dark text-4xl">person_add</span>
            </div>
        </div>
    </div>

    <div class="w-full max-w-[440px] bg-white dark:bg-background-dark rounded-xl shadow-2xl overflow-hidden border border-white/10">
        <div class="px-8 pt-10 pb-6 text-right">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cipta Akaun</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Isi maklumat di bawah untuk mendaftar</p>
        </div>
        
        <?php if(!$show_success) { ?>
        <form class="px-8 pb-10 space-y-4" method="post">
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 ml-1 block text-right">ID Pengguna</label>
                <input class="block w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none" name="idPengguna" placeholder="cth. D6557" type="text" pattern="[A-Za-z]\d{4}" title="Format: 1 huruf + 4 nombor (contoh: D6557)" required />
                <p class="text-xs text-gray-500 dark:text-gray-400 ml-1 text-right">Format: 1 huruf diikuti 4 nombor (cth. D6557)</p>
            </div>
            
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 ml-1 block text-right">Nama Penuh</label>
                <input class="block w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none" name="namaPengguna" placeholder="Nama Penuh Anda" type="text" required />
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold uppercase text-gray-600 dark:text-gray-300 ml-1 block text-right">Kata Laluan</label>
                <input class="block w-full px-4 py-3 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary outline-none" name="kataLaluan" placeholder="Kata Laluan" type="password" required />
            </div>
            
            <button class="w-full mt-4 bg-primary text-background-dark font-bold py-3.5 px-4 rounded-lg shadow-lg hover:bg-primary/90 transition-all flex items-center justify-center gap-2" type="submit">
                <span class="material-symbols-outlined text-sm">how_to_reg</span>
                Daftar Akaun
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
