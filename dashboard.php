<?php
include("auth_session.php");
require('db_config.php');

// Get current user info
$idPengguna = $_SESSION['idPengguna'];
$sql_user = "SELECT * FROM Pengguna_1 WHERE idPengguna='$idPengguna'";
$result_user = mysqli_query($conn, $sql_user);
$row = mysqli_fetch_assoc($result_user);
$namaPengguna = $row['namaPengguna'];

// Get current position to show, default to J1 (Pengerusi)
$current_pos_id = isset($_GET['pos']) ? mysqli_real_escape_string($conn, $_GET['pos']) : 'J1';

// Fetch Position Name
$sql_pos_name = "SELECT namaJawatan FROM Jawatan_1 WHERE idJawatan='$current_pos_id'";
$res_pos_name = mysqli_query($conn, $sql_pos_name);
$pos_row = mysqli_fetch_assoc($res_pos_name);
$current_pos_name = $pos_row['namaJawatan'];

// Check if user has already voted for this position
$sql_check_vote = "SELECT * FROM Undian_1 WHERE idPengguna='$idPengguna' AND idJawatan='$current_pos_id'";
$res_check_vote = mysqli_query($conn, $sql_check_vote);
$has_voted = mysqli_num_rows($res_check_vote) > 0;

?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Papan Undian - Kelab Bola Sepak</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#80f20d",
                    "accent-gold": "#FFD700",
                    "background-light": "#f7f8f5",
                    "background-dark": "#0a0f05",
                    "sidebar-dark": "#0d1308",
                    "card-dark": "#121a0a",
                },
                fontFamily: {
                    "display": ["Plus Jakarta Sans", "Inter", "sans-serif"]
                },
            },
        },
    }
</script>
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .active-nav-item { border-right: 4px solid #80f20d; background: linear-gradient(270deg, rgba(128, 242, 13, 0.15) 0%, rgba(128, 242, 13, 0.05) 100%); }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-deep-green dark:text-white transition-colors duration-300">
<div class="flex h-screen overflow-hidden flex-row-reverse">
    <!-- Sidebar Navigation -->
    <aside class="w-72 bg-sidebar-dark text-slate-300 flex flex-col justify-between py-6 px-4 shadow-xl shrink-0 hidden md:flex border-l border-white/5 order-2">
        <div class="flex flex-col gap-8">
            <!-- Brand/Logo -->
            <div class="flex items-center gap-3 px-4">
                <div class="bg-primary p-2 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(128,242,13,0.3)]">
                    <span class="material-symbols-outlined text-black font-extrabold">&#xe8e1;</span>
                </div>
                <div class="flex flex-col text-right">
                    <h1 class="text-sm font-extrabold leading-tight uppercase tracking-tight text-white">Kelab Bola Sepak</h1>
                    <p class="text-primary text-[10px] font-black uppercase tracking-widest">Portal Undian 2024</p>
                </div>
            </div>
            <!-- Nav Categories -->
            <nav class="flex flex-col gap-2">
                <p class="px-4 text-[10px] uppercase font-black text-slate-500 tracking-[2px] mb-2 text-right">Pilihan Raya</p>
                
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all flex-row-reverse <?php if($current_pos_id == 'J1') echo 'active-nav-item text-white'; else echo 'text-slate-400 hover:text-white'; ?>" href="dashboard.php?pos=J1">
                    <span class="material-symbols-outlined <?php echo ($current_pos_id == 'J1') ? 'text-primary' : ''; ?>">&#xe7fd;</span>
                    <span class="text-sm font-black uppercase">Pengerusi</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all flex-row-reverse <?php if($current_pos_id == 'J2') echo 'active-nav-item text-white'; else echo 'text-slate-400 hover:text-white'; ?>" href="dashboard.php?pos=J2">
                    <span class="material-symbols-outlined <?php echo ($current_pos_id == 'J2') ? 'text-primary' : ''; ?>">&#xef4d;</span>
                    <span class="text-sm font-black uppercase">Setiausaha</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all flex-row-reverse <?php if($current_pos_id == 'J3') echo 'active-nav-item text-white'; else echo 'text-slate-400 hover:text-white'; ?>" href="dashboard.php?pos=J3">
                    <span class="material-symbols-outlined <?php echo ($current_pos_id == 'J3') ? 'text-primary' : ''; ?>">&#xe84f;</span>
                    <span class="text-sm font-black uppercase">Bendahari</span>
                </a>

                <p class="px-4 text-[10px] uppercase font-black text-slate-500 tracking-[2px] mt-6 mb-2 text-right">Statistik</p>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all flex-row-reverse text-slate-400 hover:text-white" href="results.php">
                    <span class="material-symbols-outlined">&#xf210;</span>
                    <span class="text-sm font-black uppercase">Keputusan</span>
                </a>
            </nav>
            
            <?php if(in_array($idPengguna, ['D6290', 'admin'])) { ?>
            <div class="px-4 mt-6">
                <a href="admin.php" class="flex flex-row-reverse items-center gap-3 px-4 py-3 rounded-xl bg-primary/5 text-primary hover:bg-primary/10 transition-all border border-primary/20 shadow-[0_0_10px_rgba(128,242,13,0.1)]">
                    <span class="material-symbols-outlined font-black">&#xe8e1;</span>
                    <span class="text-[10px] font-black uppercase tracking-widest">Panel Pentadbir</span>
                </a>
            </div>
            <?php } ?>
        </div>
        <!-- Bottom Nav -->
        <div class="flex flex-col gap-2 pt-6 border-t border-white/5">
            <div class="flex items-center gap-3 px-4 py-3 bg-white/5 rounded-2xl mx-2 flex-row-reverse">
                <div class="size-10 rounded-full bg-primary flex items-center justify-center text-black font-black text-lg">
                    <?php echo strtoupper(substr($namaPengguna, 0, 1)); ?>
                </div>
                <div class="flex flex-col overflow-hidden text-right">
                    <p class="text-xs font-black text-white truncate"><?php echo $namaPengguna; ?></p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo $idPengguna; ?></p>
                </div>
            </div>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-red-400 transition-colors text-slate-500 flex-row-reverse group" href="logout.php">
                <span class="material-symbols-outlined group-hover:animate-pulse font-black">&#xe9ba;</span>
                <span class="text-xs font-black uppercase tracking-widest">Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto order-1">
        <!-- Top Navigation Bar (Mobile) -->
        <header class="sticky top-0 z-10 bg-white dark:bg-card-dark border-b border-slate-200 dark:border-slate-800 px-8 py-4 flex items-center justify-between md:hidden">
            <div class="flex items-center gap-4">
               <h1 class="text-lg font-extrabold text-right">Kelab Bola Sepak</h1>
            </div>
            <a href="logout.php" class="text-red-500 font-bold text-sm">Log Keluar</a>
        </header>

        <!-- Page Heading & Instructions -->
        <div class="px-8 pt-8 pb-4">
            <div class="bg-white dark:bg-card-dark border border-slate-100 dark:border-slate-800 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm">
                <div class="flex-1">
                    <h3 class="text-2xl font-black mb-2 flex items-center gap-2 justify-end text-slate-900 dark:text-white">
                        <?php echo $current_pos_name; ?> <span class="text-primary material-symbols-outlined">&#xea3f;</span>
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm max-w-2xl text-right ml-auto">
                        Sila pilih calon kegemaran anda. 
                        <span class="font-bold text-primary underline underline-offset-4 decoration-2">Satu undi bagi setiap jawatan sahaja.</span>
                    </p>
                </div>
                <div class="shrink-0 flex gap-2">
                    <div class="flex flex-col items-center justify-center bg-primary/10 px-6 py-2 rounded-xl border border-primary/20">
                        <span class="text-[10px] font-black text-primary uppercase tracking-widest flex items-center gap-1">
                             Status <span class="material-symbols-outlined text-[12px]">&#xe88e;</span>
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-primary"><?php echo $has_voted ? 'DIUNDI' : 'BUKA'; ?></span>
                            <?php if($has_voted) { ?>
                                <span class="material-symbols-outlined text-primary text-sm">&#xe86c;</span>
                            <?php } else { ?>
                                <span class="material-symbols-outlined text-primary text-sm">&#xf042;</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(isset($_GET['success'])): ?>
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-right" role="alert">
                <strong class="font-bold">Berjaya!</strong>
                <span class="block sm:inline">Undian anda telah direkodkan dengan jayanya.</span>
            </div>
            <?php endif; ?>
             <?php if(isset($_GET['error'])): ?>
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-right" role="alert">
                <strong class="font-bold">Ralat!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Candidate Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-8">
            <?php
            // Fetch candidates
            $prefix_map = ['J1' => 'P', 'J2' => 'S', 'J3' => 'B'];
            $prefix = isset($prefix_map[$current_pos_id]) ? $prefix_map[$current_pos_id] : '';
            
            $sql_calon = "SELECT * FROM Calon_1 WHERE idCalon LIKE '$prefix%' ORDER BY idCalon ASC"; 
            $result_calon = mysqli_query($conn, $sql_calon);
            
            while($row_calon = mysqli_fetch_assoc($result_calon)) {
            ?>
            <!-- Candidate Card -->
            <div class="group bg-white dark:bg-card-dark rounded-3xl overflow-hidden border border-slate-100 dark:border-white/5 transition-all shadow-sm hover:shadow-[0_20px_40px_rgba(0,0,0,0.3)] hover:-translate-y-2">
                <div class="relative aspect-[3/4] overflow-hidden bg-slate-200 dark:bg-slate-900">
                    <?php if($row_calon['gambar']) { ?>
                    <img class="w-full h-full object-cover grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700" src="<?php echo $row_calon['gambar']; ?>" alt="<?php echo $row_calon['namaCalon']; ?>"/>
                    <?php } else { ?>
                    <div class="w-full h-full flex items-center justify-center text-slate-500 font-black text-3xl uppercase tracking-tighter bg-gradient-to-br from-slate-100 to-slate-200 dark:from-white/5 dark:to-white/10">Tiada Imej</div>
                    <?php } ?>
                    <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black to-transparent flex flex-col justify-end">
                        <span class="text-primary text-[10px] font-black bg-black/60 backdrop-blur-md px-2 py-1 rounded w-fit mb-2 uppercase tracking-widest border border-primary/20">Calon #<?php echo substr($row_calon['idCalon'], 1); ?></span>
                        <h4 class="text-xl font-black text-white leading-tight uppercase tracking-tighter"><?php echo $row_calon['namaCalon']; ?></h4>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                             <span class="material-symbols-outlined text-sm font-black">&#xe7ef;</span>
                             <span class="text-[10px] font-black uppercase tracking-widest"><?php echo $row_calon['kelas']; ?></span>
                        </div>
                    </div>
                    <?php if(!$has_voted) { ?>
                    <form action="vote.php" method="POST">
                        <input type="hidden" name="idJawatan" value="<?php echo $current_pos_id; ?>">
                        <input type="hidden" name="idCalon" value="<?php echo $row_calon['idCalon']; ?>">
                        <button type="submit" class="w-full py-4 bg-primary text-black font-black rounded-2xl text-[10px] uppercase tracking-widest hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 shadow-[0_10px_20px_rgba(128,242,13,0.2)]">
                            <span class="material-symbols-outlined text-lg font-black">&#xe176;</span>
                            Hantar Undi
                        </button>
                    </form>
                    <?php } else { ?>
                         <button disabled class="w-full py-4 bg-slate-100 dark:bg-white/5 text-slate-400 dark:text-slate-600 font-black rounded-2xl text-[10px] uppercase tracking-widest flex items-center justify-center gap-2 cursor-not-allowed border border-slate-200 dark:border-white/5">
                            Telah Mengundi
                        </button>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
        
    </main>
</div>
</body>
</html>
