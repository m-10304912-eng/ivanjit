<?php
include("auth_session.php");
require('db_config.php');

// User info for sidebar
$idPengguna = $_SESSION['idPengguna'];
$sql_user = "SELECT * FROM Pengguna_1 WHERE idPengguna='$idPengguna'";
$result_user = mysqli_query($conn, $sql_user);
$row = mysqli_fetch_assoc($result_user);
$namaPengguna = $row['namaPengguna'];

// Stats Calculation
// 1. Total Ballots Cast (Unique voters)
$sql_total_votes = "SELECT COUNT(DISTINCT idPengguna) as total FROM Undian_1";
$res_total_votes = mysqli_query($conn, $sql_total_votes);
$row_total_votes = mysqli_fetch_assoc($res_total_votes);
$total_ballots = $row_total_votes['total'];

// 2. Total Users for Turnout Calculation
$sql_total_users = "SELECT COUNT(*) as total FROM Pengguna_1";
$res_total_users = mysqli_query($conn, $sql_total_users);
$row_total_users = mysqli_fetch_assoc($res_total_users);
$total_users = $row_total_users['total'];
$turnout_percentage = ($total_users > 0) ? round(($total_ballots / $total_users) * 100) : 0;
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Keputusan Langsung | Undian FC</title>
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
                
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors" href="dashboard.php?pos=J1">
                    <span class="material-symbols-outlined">person</span>
                    <span class="text-sm font-semibold">Pengerusi</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors" href="dashboard.php?pos=J2">
                    <span class="material-symbols-outlined">edit_note</span>
                    <span class="text-sm font-medium">Setiausaha</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors" href="dashboard.php?pos=J3">
                    <span class="material-symbols-outlined">account_balance</span>
                    <span class="text-sm font-medium">Bendahari</span>
                </a>

                <p class="px-4 text-[10px] uppercase font-bold text-gray-500 tracking-[2px] mt-4 mb-2">Statistik Langsung</p>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors active-nav-item" href="results.php">
                    <span class="material-symbols-outlined text-primary">leaderboard</span>
                    <span class="text-sm font-medium">Keputusan</span>
                </a>
            </nav>
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
    <main class="flex-1 flex flex-col overflow-y-auto w-full">
         <!-- Top Navigation Bar (Mobile) -->
        <header class="sticky top-0 z-10 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-8 py-4 flex items-center justify-between md:hidden">
            <div class="flex items-center gap-4">
               <h1 class="text-lg font-bold">Undian FC</h1>
            </div>
            <a href="logout.php" class="text-red-500 font-bold text-sm">Log Keluar</a>
        </header>

         <div class="max-w-[1200px] mx-auto w-full px-6 py-8">
            <!-- Page Heading -->
            <div class="flex flex-wrap justify-between items-end gap-3 mb-8">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                        <p class="text-primary text-sm font-bold uppercase tracking-widest">Kemas Kini Langsung</p>
                    </div>
                    <h1 class="text-4xl font-black leading-tight tracking-tight">Keputusan Terkini</h1>
                    <p class="text-gray-500 text-base font-normal">Jejakan masa nyata untuk Pilihan Raya Jawatankuasa 2024</p>
                </div>
                <div class="flex gap-3 items-center">
                    <p class="text-xs text-gray-500 font-medium italic">Dikemaskini: <?php echo date("h:i A"); ?></p>
                    <button onclick="window.location.reload();" class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary/20 text-[#0d1b0d] dark:text-white text-sm font-bold border border-primary/30 cursor-pointer hover:bg-primary/30">
                        <span class="material-symbols-outlined text-sm">refresh</span>
                        <span>Muat Semula</span>
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#152a15] border border-[#cfe7cf] dark:border-[#1e3a1e] shadow-sm">
                    <p class="text-sm font-medium opacity-70">Jumlah Undian</p>
                    <p class="text-3xl font-black text-primary"><?php echo $total_ballots; ?></p>
                </div>
                <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#152a15] border border-[#cfe7cf] dark:border-[#1e3a1e] shadow-sm">
                    <p class="text-sm font-medium opacity-70">Peratusan Mengundi</p>
                    <p class="text-3xl font-black"><?php echo $turnout_percentage; ?>% <span class="text-lg font-normal opacity-50">(<?php echo $total_ballots; ?>/<?php echo $total_users; ?>)</span></p>
                    <div class="w-full bg-[#e7f3e7] dark:bg-[#1e3a1e] h-2 rounded-full mt-2">
                        <div class="bg-primary h-2 rounded-full" style="width: <?php echo $turnout_percentage; ?>%"></div>
                    </div>
                </div>
                 <div class="flex flex-col gap-2 rounded-xl p-6 bg-white dark:bg-[#152a15] border border-[#cfe7cf] dark:border-[#1e3a1e] shadow-sm">
                    <p class="text-sm font-medium opacity-70">Jawatan Aktif</p>
                    <p class="text-3xl font-black">3</p>
                </div>
            </div>

            <!-- Detailed Results Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <?php
                // Create array of positions
                $positions = [
                    'J1' => ['icon' => 'leaderboard', 'name' => 'Pengerusi'],
                    'J2' => ['icon' => 'assignment', 'name' => 'Setiausaha'],
                    'J3' => ['icon' => 'account_balance_wallet', 'name' => 'Bendahari']
                ];

                foreach($positions as $idJawatan => $meta) {
                    // Get total votes for this position
                    $sql_pos_total = "SELECT COUNT(*) as total FROM Undian_1 WHERE idJawatan='$idJawatan'";
                    $res_pos_total = mysqli_query($conn, $sql_pos_total);
                    $row_pos_total = mysqli_fetch_assoc($res_pos_total);
                    $total_pos_votes = $row_pos_total['total'];
                ?>
                <!-- Position Block -->
                <div class="bg-white dark:bg-[#152a15] rounded-xl border border-[#cfe7cf] dark:border-[#1e3a1e] overflow-hidden">
                    <div class="p-6 border-b border-[#cfe7cf] dark:border-[#1e3a1e] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-3xl"><?php echo $meta['icon']; ?></span>
                            <h2 class="text-xl font-bold"><?php echo $meta['name']; ?></h2>
                        </div>
                        <?php if($total_pos_votes > 0) { ?>
                            <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded border border-primary/20 uppercase">Aktif</span>
                        <?php } else { ?>
                             <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded border border-gray-200 uppercase">Tiada Undian</span>
                        <?php } ?>
                    </div>
                    <!-- Candidates List -->
                    <div class="p-6 flex flex-col gap-6">
                        <?php
                        // Fetch candidates structure similar to dashboard
                         $prefix_map = ['J1' => 'P', 'J2' => 'S', 'J3' => 'B'];
                         $prefix = isset($prefix_map[$idJawatan]) ? $prefix_map[$idJawatan] : '';
                
                        // Get candidates with vote count
                        $sql_candidates = "SELECT c.namaCalon, c.idCalon, c.gambar, 
                                            (SELECT COUNT(*) FROM Undian_1 u WHERE u.idCalon = c.idCalon AND u.idJawatan = '$idJawatan') as vote_count 
                                           FROM Calon_1 c 
                                           WHERE c.idCalon LIKE '$prefix%' 
                                           ORDER BY vote_count DESC";
                        $res_candidates = mysqli_query($conn, $sql_candidates);

                        if(mysqli_num_rows($res_candidates) == 0) {
                             $sql_candidates = "SELECT c.namaCalon, c.idCalon, c.gambar,
                                            (SELECT COUNT(*) FROM Undian_1 u WHERE u.idCalon = c.idCalon AND u.idJawatan = '$idJawatan') as vote_count 
                                           FROM Calon_1 c  
                                           ORDER BY vote_count DESC";
                             $res_candidates = mysqli_query($conn, $sql_candidates);
                        }

                        while($cand = mysqli_fetch_assoc($res_candidates)) {
                            $count = $cand['vote_count'];
                            $perc = ($total_pos_votes > 0) ? round(($count / $total_pos_votes) * 100) : 0;
                        ?>
                        <!-- Candidate Item -->
                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-end">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full overflow-hidden bg-gray-200 border border-gray-300">
                                        <?php if($cand['gambar']) { ?>
                                            <img src="<?php echo $cand['gambar']; ?>" class="w-full h-full object-cover">
                                        <?php } else { ?>
                                            <div class="w-full h-full flex items-center justify-center text-xs font-bold text-gray-500">N/A</div>
                                        <?php } ?>
                                    </div>
                                    <p class="font-bold text-lg"><?php echo $cand['namaCalon']; ?></p>
                                    <?php if($perc > 50) { ?> 
                                        <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                                    <?php } ?>
                                </div>
                                <p class="font-bold"><?php echo $count; ?> Undian <span class="text-sm font-normal opacity-50">(<?php echo $perc; ?>%)</span></p>
                            </div>
                            <div class="w-full bg-[#e7f3e7] dark:bg-[#1e3a1e] h-4 rounded-lg overflow-hidden">
                                <div class="bg-primary h-4 rounded-lg transition-all duration-1000" style="width: <?php echo $perc; ?>%"></div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
         </div>
    </main>
</div>
</body>
</html>
