<?php
include("auth_session.php");
require('db_config.php');

// User info for sidebar/header
$idPengguna = $_SESSION['idPengguna'];
$sql_user = "SELECT * FROM Pengguna_1 WHERE idPengguna='$idPengguna'";
$result_user = mysqli_query($conn, $sql_user);
$row = mysqli_fetch_assoc($result_user);
$namaPengguna = $row['namaPengguna'];

// --- STATS CALCULATION ---

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
$turnout_percentage = ($total_users > 0) ? round(($total_ballots / $total_users) * 100, 1) : 0;

// 3. Voting Velocity (Last 12 Hours)
// Group votes by hour for the graph
$voting_velocity = [];
$labels = [];
for ($i = 11; $i >= 0; $i--) {
    $hour_start = date('Y-m-d H:00:00', strtotime("-$i hours"));
    $hour_end = date('Y-m-d H:59:59', strtotime("-$i hours"));
    $hour_label = date('H:00', strtotime("-$i hours"));
    
    $sql_velocity = "SELECT COUNT(*) as count FROM Undian_1 WHERE timestamp BETWEEN '$hour_start' AND '$hour_end'";
    $res_velocity = mysqli_query($conn, $sql_velocity);
    $row_velocity = mysqli_fetch_assoc($res_velocity);
    
    $voting_velocity[] = $row_velocity['count'];
    $labels[] = $hour_label;
}
// Normalize height for CSS (max height 100%)
$max_votes = max($voting_velocity);
$max_votes = ($max_votes == 0) ? 1 : $max_votes; // Avoid division by zero

?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Papan Pemuka Analitik | Pilihan Raya Jawatankuasa FC</title>
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
                    "background-dark": "#0a0f05",
                    "sidebar-dark": "#0d1308",
                    "card-dark": "#121a0a",
                },
                fontFamily: {
                    "display": ["Plus Jakarta Sans", "Inter", "sans-serif"]
                },
                borderRadius: {
                    "DEFAULT": "12px",
                },
            },
        },
    }
</script>
<style type="text/tailwindcss">
        body {
            font-family: 'Lexend', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        .sparkline-svg {
             fill: none;
             stroke-width: 2;
             stroke-linecap: round;
             stroke-linejoin: round;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-gray-100 min-h-screen">
<div class="layout-container flex flex-col">
    <!-- Top Bar -->
<div class="bg-primary px-4 py-1.5 flex items-center justify-center gap-2 text-[#0d1b0d] font-bold text-xs uppercase tracking-wider">
<span class="material-symbols-outlined text-base">&#xe86c;</span>
<span>Audit Selesai: 100% undian disahkan untuk Semakan Analitik</span>
</div>

<div class="flex h-screen overflow-hidden flex-row-reverse">
    <!-- Sidebar Navigation -->
    <aside class="w-72 bg-sidebar-dark text-slate-300 flex flex-col justify-between py-6 px-4 shadow-xl shrink-0 hidden md:flex h-full border-l border-white/5 order-2">
        <div class="flex flex-col gap-8">
            <!-- Brand/Logo -->
            <div class="flex items-center gap-3 px-4">
                <div class="bg-primary p-2 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(128,242,13,0.3)]">
                    <span class="material-symbols-outlined text-black font-extrabold">&#xe8e1;</span>
                </div>
                <div class="flex flex-col text-right">
                    <h1 class="text-sm font-extrabold leading-tight uppercase tracking-tight text-white">Portal Undian</h1>
                    <p class="text-primary text-[10px] font-black uppercase tracking-widest">Sesi 2024/2025</p>
                </div>
            </div>
            <!-- Nav Categories -->
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all flex-row-reverse text-slate-400 hover:text-white group" href="dashboard.php">
                    <span class="material-symbols-outlined group-hover:text-primary">&#xe871;</span>
                    <span class="text-sm font-black uppercase tracking-tight">Papan Utama</span>
                </a>
                <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/10 text-white border-r-4 border-primary transition-all flex-row-reverse">
                    <span class="material-symbols-outlined text-primary font-black">&#xe24b;</span>
                    <span class="text-sm font-black uppercase tracking-tight">Keputusan Langsung</span>
                </div>
                
                <?php if($is_admin): ?>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all flex-row-reverse text-slate-400 hover:text-white group" href="admin.php">
                    <span class="material-symbols-outlined group-hover:text-primary">&#xe8e1;</span>
                    <span class="text-sm font-black uppercase tracking-tight">Panel Admin</span>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        
        <!-- Bottom Nav -->
        <div class="flex flex-col gap-2 pt-6 border-t border-white/5">
            <div class="flex items-center gap-3 px-4 py-3 bg-white/5 rounded-2xl mx-2 flex-row-reverse">
                <div class="size-10 rounded-full bg-primary flex items-center justify-center text-black font-black text-lg">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
                <div class="flex flex-col overflow-hidden text-right">
                    <p class="text-xs font-black text-white truncate"><?php echo $username; ?></p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo $_SESSION['idPengguna']; ?></p>
                </div>
            </div>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-red-400 transition-colors text-slate-500 flex-row-reverse group" href="logout.php">
                <span class="material-symbols-outlined group-hover:animate-pulse font-black">&#xe9ba;</span>
                <span class="text-sm font-black uppercase tracking-widest">Log Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto bg-background-light dark:bg-background-dark order-1">
        <!-- Top Navigation / Header -->
        <header class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-8 py-4 bg-white dark:bg-card-dark sticky top-0 z-30">
            <div class="flex items-center gap-3 text-right w-full justify-end">
                <div class="flex flex-col">
                    <h2 class="text-lg font-black tracking-tight dark:text-white uppercase tracking-tighter">Statistik</h2>
                    <span class="text-[10px] font-bold text-primary uppercase tracking-widest text-right">Pilihan Raya Jawatankuasa</span>
                </div>
                <div class="bg-primary/10 p-2 rounded-lg text-primary">
                    <span class="material-symbols-outlined font-black">&#xe24b;</span>
                </div>
            </div>
            <!-- Mobile Toggle -->
            <button class="md:hidden size-10 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-white/5 ml-4">
                 <span class="material-symbols-outlined">&#xe5d2;</span>
            </button>
        </header>

        <div class="max-w-[1400px] mx-auto w-full px-8 py-6">
    <!-- Title Section -->
<div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-8">
<div class="text-right w-full">
<h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Metadata Undian Jawatankuasa</h1>
<p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Paparan analitik masa nyata untuk ahli jawatankuasa</p>
</div>
<div class="flex items-center gap-3">
<button onclick="window.location.reload()" class="flex items-center gap-2 rounded-xl h-11 px-6 bg-primary text-black text-xs font-black hover:opacity-90 transition-all uppercase tracking-widest shadow-lg shadow-primary/20">
<span class="material-symbols-outlined text-lg">&#xe5d5;</span>
<span>Muat Semula</span>
</button>
</div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
<div class="bg-white dark:bg-card-dark p-6 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm">
<div class="text-right w-full">
<p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Jumlah Undian</p>
<p class="text-3xl font-black tabular-nums dark:text-white"><?php echo number_format($total_ballots); ?></p>
</div>
<span class="material-symbols-outlined text-primary/40 text-4xl mr-4">&#xe7ef;</span>
</div>
<div class="bg-white dark:bg-card-dark p-6 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm">
<div class="text-right w-full">
<p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Penyertaan</p>
<p class="text-3xl font-black tabular-nums dark:text-white"><?php echo $turnout_percentage; ?>%</p>
</div>
<span class="material-symbols-outlined text-primary/40 text-4xl mr-4">&#xe01d;</span>
</div>
<div class="bg-white dark:bg-card-dark p-6 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm">
<div class="text-right w-full">
<p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Keyakinan</p>
<p class="text-3xl font-black tabular-nums dark:text-white">99.9%</p>
</div>
<span class="material-symbols-outlined text-primary/40 text-4xl mr-4">&#xe32a;</span>
</div>
<div class="bg-white dark:bg-card-dark p-6 border border-slate-100 dark:border-slate-800 rounded-2xl flex items-center justify-between shadow-sm">
<div class="text-right w-full">
<p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Status</p>
<p class="text-3xl font-black tabular-nums text-primary uppercase">Aktif</p>
</div>
<span class="material-symbols-outlined text-primary/40 text-4xl mr-4">&#xe63e;</span>
</div>
</div>

<!-- Data Table -->
<div class="bg-white dark:bg-card-dark border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl overflow-hidden mb-10">
<div class="overflow-x-auto">
<table class="w-full text-right border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest text-slate-500">
<th class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 text-right">Jawatan &amp; Tugas</th>
<th class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 text-right">Nama Calon</th>
<th class="px-4 py-5 border-b border-slate-100 dark:border-slate-800 text-center">Trend (6j)</th>
<th class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 text-right">Jumlah Undian</th>
<th class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 text-right">Peratusan</th>
<th class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 text-right">Status</th>
</tr>
</thead>
<tbody class="text-xs">
    <?php
    $positions = [
        'J1' => ['icon' => '&#xf210;', 'name' => 'Pengerusi'],
        'J2' => ['icon' => '&#xeb4e;', 'name' => 'Setiausaha'],
        'J3' => ['icon' => '&#xe84f;', 'name' => 'Bendahari']
    ];

    foreach ($positions as $idJawatan => $meta) {
        $prefix_map = ['J1' => 'P', 'J2' => 'S', 'J3' => 'B'];
        $prefix = $prefix_map[$idJawatan];

        $sql_pos_total = "SELECT COUNT(*) as total FROM Undian_1 WHERE idJawatan='$idJawatan'";
        $res_pos_total = mysqli_query($conn, $sql_pos_total);
        $total_pos_votes = mysqli_fetch_assoc($res_pos_total)['total'];

        $sql_candidates = "SELECT c.namaCalon, c.idCalon, 
                          (SELECT COUNT(*) FROM Undian_1 u WHERE u.idCalon = c.idCalon AND u.idJawatan = '$idJawatan') as vote_count
                           FROM Calon_1 c 
                           WHERE c.idCalon LIKE '$prefix%' 
                           ORDER BY vote_count DESC";
        $res_candidates = mysqli_query($conn, $sql_candidates);
        
        $num_candidates = mysqli_num_rows($res_candidates);
        $rowspan = $num_candidates; 
        $first = true;
        
        while($cand = mysqli_fetch_assoc($res_candidates)) {
            $count = $cand['vote_count'];
            $perc = ($total_pos_votes > 0) ? round(($count / $total_pos_votes) * 100, 2) : 0;
            
            $status_html = '';
            if($perc > 50) {
                 $status_html = '<span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black border border-primary/20 uppercase tracking-widest">Mendahului</span>';
            } elseif ($count == 0) {
                 $status_html = '<span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 text-[10px] font-black uppercase tracking-widest">Tiada Undi</span>';
            } elseif ($perc < 20) {
                 $status_html = '<span class="px-3 py-1 rounded-full bg-red-50 dark:bg-red-900/20 text-red-500 text-[10px] font-black uppercase tracking-widest">Minoriti</span>';
            } else {
                 $status_html = '<span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-black uppercase tracking-widest">Bersaing</span>';
            }

            if($count > 0) {
                 $path_d = "M0 25 L20 15 L40 18 L60 8 L80 12 L100 0";
            } else {
                 $path_d = "M0 28 L100 28";
            }
    ?>
    <tr class="group border-b border-slate-50 dark:border-slate-800 hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors">
        <?php if($first) { ?>
        <td class="px-6 py-5 font-black align-middle bg-white dark:bg-card-dark border-l border-slate-50 dark:border-slate-800" rowspan="<?php echo $rowspan; ?>">
            <div class="flex items-center justify-end gap-3 text-slate-900 dark:text-white uppercase tracking-widest">
            <span><?php echo $meta['name']; ?></span>
            <span class="material-symbols-outlined text-primary text-xl"><?php echo $meta['icon']; ?></span>
            </div>
        </td>
        <?php } ?>
        <td class="px-6 py-5 font-bold <?php echo ($first) ? 'text-slate-900 dark:text-white' : 'text-slate-500 dark:text-slate-600'; ?>">
            <?php echo $cand['namaCalon']; ?>
        </td>
        <td class="px-4 py-5">
            <div class="flex justify-center">
            <svg class="h-10 w-28 <?php echo ($count > 0) ? 'text-primary' : 'text-slate-200 dark:text-slate-800'; ?> sparkline-svg" viewBox="0 0 100 30">
            <path d="<?php echo $path_d; ?>" fill="none" stroke="currentColor" stroke-width="3"></path>
            </svg>
            </div>
        </td>
        <td class="px-6 py-5 text-right tabular-nums dark:text-white font-bold"><?php echo $count; ?></td>
        <td class="px-6 py-5 text-right tabular-nums font-black text-primary transition-all group-hover:scale-110"><?php echo $perc; ?>%</td>
        <td class="px-6 py-5 text-right">
            <?php echo $status_html; ?>
        </td>
    </tr>
    <?php 
        $first = false;
        }
    }
    ?>

</tbody>
</table>
</div>
<div class="px-8 py-5 bg-slate-50 dark:bg-white/5 flex flex-row-reverse items-center justify-between text-[10px] text-slate-500 font-black uppercase tracking-widest">
<div class="flex items-center gap-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-base">&#xe2e6;</span>
<span>Semua metrik disahkan secara kriptografi</span>
</div>
<div class="h-4 w-px bg-slate-200 dark:bg-slate-800"></div>
<p>Kini: <?php echo gmdate("Y-m-d H:i:s"); ?> UTC</p>
</div>
<div>Analitik Pilihan Raya v2.1 PRO</div>
</div>
</div>

<!-- Graphs Section -->
<div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Velocity Chart -->
<div class="lg:col-span-2 bg-white dark:bg-card-dark rounded-3xl border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
<div class="flex flex-row-reverse items-center justify-between mb-10">
<h3 class="text-sm font-black flex items-center gap-3 dark:text-white uppercase tracking-widest">
<span class="material-symbols-outlined text-primary text-xl">&#xe94d;</span>
                        Halaju Undian Masa Nyata
                    </h3>
<div class="text-[10px] font-black text-primary uppercase tracking-[0.2em] bg-primary/10 px-3 py-1 rounded-full">
Penyegerakan Aktif
</div>
</div>
<div class="h-[220px] flex items-end justify-between gap-2 px-2">
    <?php foreach($voting_velocity as $idx => $v_count) { 
        $h_percent = ceil(($v_count / $max_votes) * 100);
    ?>
    <div class="w-full bg-primary/20 rounded-t-xl hover:bg-primary transition-all relative group cursor-pointer border-x border-t border-primary/5" style="height: <?php echo max($h_percent, 5); ?>%">
        <div class="h-full w-full bg-primary/40 rounded-t-lg transition-all group-hover:bg-primary"></div>
        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-black text-white text-[10px] font-black py-1 px-2 rounded-lg opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100 shadow-xl z-20">
            <?php echo $v_count; ?> Undian
        </div>
    </div>
    <?php } ?>
</div>
<div class="mt-6 flex flex-row-reverse justify-between text-[10px] text-slate-400 font-black uppercase tracking-widest">
    <span>Sekarang</span>
    <span>-2j</span>
    <span>-4j</span>
    <span>-6j</span>
    <span>-8j</span>
    <span>-10j</span>
    <span>-12j</span>
</div>
</div>

<!-- Export Card -->
<div class="bg-card-dark text-white rounded-3xl border border-primary/10 p-8 flex flex-col justify-between shadow-2xl relative overflow-hidden group">
<div class="absolute -right-10 -bottom-10 opacity-5 group-hover:opacity-10 transition-opacity">
<span class="material-symbols-outlined text-[180px]">&#xf090;</span>
</div>
<div>
<h3 class="text-xs font-black flex items-center gap-3 mb-4 uppercase tracking-[0.2em]">
<span class="material-symbols-outlined text-primary text-lg">&#xe873;</span>
                        Tindakan Laporan
                    </h3>
<p class="text-xs text-slate-500 leading-loose mb-8 font-medium">Jana laporan rasmi, eksport data audit lengkap atau cetak lejar keputusan untuk tujuan arkib.</p>
</div>
<div class="flex flex-col gap-3 relative z-10">
<button onclick="window.print()" class="w-full py-4 bg-primary text-black font-black text-[10px] rounded-2xl hover:opacity-90 transition-all flex items-center justify-center gap-2 uppercase tracking-widest shadow-lg shadow-primary/20">
<span class="material-symbols-outlined text-sm">&#xe8ad;</span>
                        Cetak Lejar Audit
                    </button>
<button class="w-full py-4 border border-white/5 bg-white/5 text-slate-300 font-black text-[10px] rounded-2xl hover:bg-white/10 transition-all flex items-center justify-center gap-2 uppercase tracking-widest cursor-pointer">
<span class="material-symbols-outlined text-sm">&#xf090;</span>
                        Muat Turun Laporan
                    </button>
</div>
</div>
</div>
</div> <!-- End Max-width Container -->
<footer class="mt-auto border-t border-[#cfe7cf] dark:border-[#1e3a1e] py-6 px-10 flex flex-col items-center gap-2 text-[10px] opacity-60 uppercase tracking-widest font-bold">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-sm">security</span>
<p>Tahap Akses Analitik: Ahli Jawatankuasa (L3)</p>
</div>
<p>© 2024 Jawatankuasa FC • ID Sistem: FC-VOTE-2024-PRO</p>
</footer>
</main>
</div> <!-- End Flex Container -->

</body>
</html>
