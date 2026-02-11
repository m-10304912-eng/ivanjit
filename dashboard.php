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
$current_pos_id = isset($_GET['pos']) ? $_GET['pos'] : 'J1';

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
<title>FC Committee Voting Dashboard</title>
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
                    "primary": "#11d411", // Pitch Green
                    "accent-gold": "#FFD700", // Gold Accent
                    "background-light": "#f6f8f6",
                    "background-dark": "#102210",
                    "deep-green": "#0d1b0d",
                },
                fontFamily: {
                    "display": ["Lexend"]
                },
            },
        },
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .active-nav-item { border-left: 4px solid #FFD700; background-color: rgba(17, 212, 17, 0.2); }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-deep-green dark:text-white transition-colors duration-300">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar Navigation -->
    <aside class="w-72 bg-deep-green text-white flex flex-col justify-between py-6 px-4 shadow-xl shrink-0 hidden md:flex">
        <div class="flex flex-col gap-8">
            <!-- Brand/Logo -->
            <div class="flex items-center gap-3 px-4">
                <div class="bg-primary p-2 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-deep-green font-bold">sports_soccer</span>
                </div>
                <div class="flex flex-col">
                    <h1 class="text-lg font-bold leading-tight uppercase tracking-wider">Undian FC</h1>
                    <p class="text-primary text-xs font-semibold">Portal Kelab 2024</p>
                </div>
            </div>
            <!-- Nav Categories -->
            <nav class="flex flex-col gap-2">
                <p class="px-4 text-[10px] uppercase font-bold text-gray-500 tracking-[2px] mb-2">Pilihan Raya</p>
                
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors <?php if($current_pos_id == 'J1') echo 'active-nav-item'; ?>" href="dashboard.php?pos=J1">
                    <span class="material-symbols-outlined <?php echo ($current_pos_id == 'J1') ? 'text-primary' : ''; ?>">person</span>
                    <span class="text-sm font-semibold">Pengerusi</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors <?php if($current_pos_id == 'J2') echo 'active-nav-item'; ?>" href="dashboard.php?pos=J2">
                    <span class="material-symbols-outlined <?php echo ($current_pos_id == 'J2') ? 'text-primary' : ''; ?>">edit_note</span>
                    <span class="text-sm font-medium">Setiausaha</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors <?php if($current_pos_id == 'J3') echo 'active-nav-item'; ?>" href="dashboard.php?pos=J3">
                    <span class="material-symbols-outlined <?php echo ($current_pos_id == 'J3') ? 'text-primary' : ''; ?>">account_balance</span>
                    <span class="text-sm font-medium">Bendahari</span>
                </a>

                <p class="px-4 text-[10px] uppercase font-bold text-gray-500 tracking-[2px] mt-4 mb-2">Statistik Langsung</p>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors" href="results.php">
                    <span class="material-symbols-outlined">leaderboard</span>
                    <span class="text-sm font-medium">Keputusan</span>
                </a>
            </nav>
            
            <?php if(in_array($idPengguna, ['D6290', 'admin'])) { ?>
            <div class="px-4 mt-6">
                <a href="admin.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors border border-red-500/20">
                    <span class="material-symbols-outlined">admin_panel_settings</span>
                    <span class="text-sm font-bold">Panel Admin</span>
                </a>
            </div>
            <?php } ?>
        </div>
        <!-- Bottom Nav -->
        <div class="flex flex-col gap-2 pt-6 border-t border-white/10">
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="size-10 rounded-full bg-primary flex items-center justify-center text-deep-green font-bold text-lg">
                    <?php echo strtoupper(substr($namaPengguna, 0, 1)); ?>
                </div>
                <div class="flex flex-col">
                    <p class="text-xs font-bold truncate w-32"><?php echo $namaPengguna; ?></p>
                    <p class="text-[10px] text-gray-400">ID: <?php echo $idPengguna; ?></p>
                </div>
            </div>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-500/10 text-red-400 transition-colors" href="logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span class="text-sm font-bold">Log Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto">
        <!-- Top Navigation Bar (Mobile) -->
        <header class="sticky top-0 z-10 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-8 py-4 flex items-center justify-between md:hidden">
            <div class="flex items-center gap-4">
               <h1 class="text-lg font-bold">Undian FC</h1>
            </div>
            <a href="logout.php" class="text-red-500 font-bold text-sm">Log Keluar</a>
        </header>

        <!-- Page Heading & Instructions -->
        <div class="px-8 pt-8 pb-4">
            <div class="bg-white dark:bg-white/5 border border-gray-100 dark:border-gray-800 rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm">
                <div class="flex-1">
                    <h3 class="text-2xl font-black mb-2 flex items-center gap-2">
                        <?php echo $current_pos_name; ?> <span class="text-accent-gold material-symbols-outlined">military_tech</span>
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-2xl">
                        Sila pilih calon kegemaran anda. 
                        <span class="font-bold text-deep-green dark:text-white underline decoration-primary decoration-2">Satu undi bagi setiap jawatan sahaja.</span>
                    </p>
                </div>
                <div class="shrink-0 flex gap-2">
                    <div class="flex flex-col items-center justify-center bg-primary/10 px-6 py-2 rounded-lg border border-primary/20">
                        <span class="text-xs font-bold text-primary uppercase tracking-widest flex items-center gap-1">
                             Status <span class="material-symbols-outlined text-[10px]">info</span>
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-primary"><?php echo $has_voted ? 'DIUNDI' : 'BUKA'; ?></span>
                            <?php if($has_voted) { ?>
                                <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                            <?php } else { ?>
                                <span class="material-symbols-outlined text-primary text-sm">radio_button_unchecked</span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(isset($_GET['success'])): ?>
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berjaya!</strong>
                <span class="block sm:inline">Undian anda telah direkodkan.</span>
            </div>
            <?php endif; ?>
             <?php if(isset($_GET['error'])): ?>
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
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
            
            $sql_calon = "SELECT * FROM Calon_1 WHERE idCalon LIKE '$prefix%'"; 
            $result_calon = mysqli_query($conn, $sql_calon);
            if(mysqli_num_rows($result_calon) == 0) {
                 $sql_calon = "SELECT * FROM Calon_1";
                 $result_calon = mysqli_query($conn, $sql_calon);
            }

            while($row_calon = mysqli_fetch_assoc($result_calon)) {
            ?>
            <!-- Candidate Card -->
            <div class="group bg-white dark:bg-white/5 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 hover:border-primary/50 transition-all shadow-sm hover:shadow-xl hover:-translate-y-1">
                <div class="relative aspect-[4/5] overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-t from-deep-green/90 to-transparent z-10"></div>
                    <?php if($row_calon['gambar']) { ?>
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?php echo $row_calon['gambar']; ?>" alt="<?php echo $row_calon['namaCalon']; ?>"/>
                    <?php } else { ?>
                    <div class="w-full h-full flex items-center justify-center bg-gray-300">Tiada Gambar</div>
                    <?php } ?>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    <div>
                        <h4 class="text-lg font-extrabold"><?php echo $row_calon['namaCalon']; ?></h4>
                        <p class="text-primary text-xs font-bold uppercase tracking-widest"><?php echo $row_calon['kelas']; ?></p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <?php if(!$has_voted) { ?>
                        <form action="vote.php" method="POST">
                            <input type="hidden" name="idJawatan" value="<?php echo $current_pos_id; ?>">
                            <input type="hidden" name="idCalon" value="<?php echo $row_calon['idCalon']; ?>">
                            <button type="submit" class="w-full py-3 bg-primary text-deep-green font-black rounded-lg text-sm hover:bg-primary/90 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined text-sm">how_to_reg</span>
                                UNDI CALON
                            </button>
                        </form>
                        <?php } else { ?>
                             <button disabled class="w-full py-3 bg-gray-300 text-gray-500 font-bold rounded-lg text-sm flex items-center justify-center gap-2 cursor-not-allowed">
                                Telah Mengundi
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        
    </main>
</div>
</body>
</html>
