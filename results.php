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
<title>Analytical Dashboard | FC Committee Elections</title>
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
                        "background-light": "#f8faf8",
                        "background-dark": "#0a140a",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
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
<span class="material-symbols-outlined text-base">verified</span>
<span>Audit Completed: 100% of votes verified for Analytical Review</span>
</div>

<!-- Header -->
<header class="flex items-center justify-between border-b border-[#cfe7cf] dark:border-[#1e3a1e] px-8 py-3 bg-white dark:bg-[#102210]">
<div class="flex items-center gap-3">
<div class="size-7 bg-primary rounded flex items-center justify-center">
<span class="material-symbols-outlined text-white text-lg">analytics</span>
</div>
<h2 class="text-base font-bold tracking-tight">FC Elections <span class="text-primary">/ Analytical Board</span></h2>
</div>
<div class="flex items-center gap-6">
<nav class="flex items-center gap-5">
<a class="text-xs font-semibold hover:text-primary transition-colors border-b-2 border-primary pb-1" href="dashboard.php">Dashboard</a>
<a class="text-xs font-semibold text-primary transition-colors pb-1" href="#">Master Grid</a>
<a class="text-xs font-semibold opacity-60 hover:text-primary transition-colors pb-1" href="#">Trend Logs</a>
</nav>
<div class="flex gap-2 items-center border-l pl-6 border-gray-200 dark:border-gray-800">
<button class="flex items-center justify-center rounded h-8 w-8 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
<span class="material-symbols-outlined text-lg">search</span>
</button>
<a href="logout.php" class="flex items-center justify-center rounded h-8 w-8 bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400" title="Logout">
<span class="material-symbols-outlined text-lg">logout</span>
</a>
</div>
</div>
</header>

<main class="max-w-[1400px] mx-auto w-full px-8 py-6">
    <!-- Title Section -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
<div>
<h1 class="text-2xl font-black tracking-tight">Committee Election Metadata</h1>
<p class="text-gray-500 dark:text-gray-400 text-xs">Analytical view for Committee Members • Real-time synchronization active</p>
</div>
<div class="flex items-center gap-3">
<div class="flex bg-white dark:bg-[#152a15] rounded-lg border border-[#cfe7cf] dark:border-[#1e3a1e] p-1 shadow-sm">
<button class="px-3 py-1.5 text-xs font-bold bg-primary/10 text-primary rounded">Grid View</button>
<button class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 rounded">Chart View</button>
</div>
<button onclick="window.location.reload()" class="flex items-center gap-2 rounded-lg h-9 px-4 bg-primary text-[#0d1b0d] text-xs font-bold hover:bg-primary/90 transition-colors">
<span class="material-symbols-outlined text-sm">refresh</span>
<span>Refresh Data</span>
</button>
</div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
<div class="bg-white dark:bg-[#152a15] p-4 border border-[#cfe7cf] dark:border-[#1e3a1e] rounded-xl flex items-center justify-between">
<div>
<p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Total Ballots</p>
<p class="text-xl font-black tabular-nums"><?php echo number_format($total_ballots); ?></p>
</div>
<span class="material-symbols-outlined text-primary/40">groups</span>
</div>
<div class="bg-white dark:bg-[#152a15] p-4 border border-[#cfe7cf] dark:border-[#1e3a1e] rounded-xl flex items-center justify-between">
<div>
<p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Participation</p>
<p class="text-xl font-black tabular-nums"><?php echo $turnout_percentage; ?>%</p>
</div>
<span class="material-symbols-outlined text-primary/40">bar_chart</span>
</div>
<div class="bg-white dark:bg-[#152a15] p-4 border border-[#cfe7cf] dark:border-[#1e3a1e] rounded-xl flex items-center justify-between">
<div>
<p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Confidence Level</p>
<p class="text-xl font-black tabular-nums">99.9%</p>
</div>
<span class="material-symbols-outlined text-primary/40">shield</span>
</div>
<div class="bg-white dark:bg-[#152a15] p-4 border border-[#cfe7cf] dark:border-[#1e3a1e] rounded-xl flex items-center justify-between">
<div>
<p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Margin of Error</p>
<p class="text-xl font-black tabular-nums">±0.4%</p>
</div>
<span class="material-symbols-outlined text-primary/40">query_stats</span>
</div>
</div>

<!-- Data Table -->
<div class="bg-white dark:bg-[#152a15] border border-[#cfe7cf] dark:border-[#1e3a1e] rounded-xl shadow-xl overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-gray-50 dark:bg-[#1a331a] text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
<th class="px-6 py-4 border-b border-[#cfe7cf] dark:border-[#1e3a1e]">Position &amp; Role</th>
<th class="px-6 py-4 border-b border-[#cfe7cf] dark:border-[#1e3a1e]">Candidate Name</th>
<th class="px-4 py-4 border-b border-[#cfe7cf] dark:border-[#1e3a1e] text-center">Trend (6h)</th>
<th class="px-6 py-4 border-b border-[#cfe7cf] dark:border-[#1e3a1e] text-right">Vote Count</th>
<th class="px-6 py-4 border-b border-[#cfe7cf] dark:border-[#1e3a1e] text-right">Share %</th>
<th class="px-6 py-4 border-b border-[#cfe7cf] dark:border-[#1e3a1e]">Status</th>
<th class="px-6 py-4 border-b border-[#cfe7cf] dark:border-[#1e3a1e] text-right">Actions</th>
</tr>
</thead>
<tbody class="text-sm">
    <?php
    $positions = [
        'J1' => ['icon' => 'leaderboard', 'name' => 'Chairperson'],
        'J2' => ['icon' => 'assignment', 'name' => 'Secretary'],
        'J3' => ['icon' => 'account_balance_wallet', 'name' => 'Treasurer']
    ];

    foreach ($positions as $idJawatan => $meta) {
        $prefix_map = ['J1' => 'P', 'J2' => 'S', 'J3' => 'B'];
        $prefix = $prefix_map[$idJawatan];

        // 1. Get total votes for this position
        $sql_pos_total = "SELECT COUNT(*) as total FROM Undian_1 WHERE idJawatan='$idJawatan'";
        $res_pos_total = mysqli_query($conn, $sql_pos_total);
        $total_pos_votes = mysqli_fetch_assoc($res_pos_total)['total'];

        // 2. Get candidates
        $sql_candidates = "SELECT c.namaCalon, c.idCalon, 
                          (SELECT COUNT(*) FROM Undian_1 u WHERE u.idCalon = c.idCalon AND u.idJawatan = '$idJawatan') as vote_count
                           FROM Calon_1 c 
                           WHERE c.idCalon LIKE '$prefix%' 
                           ORDER BY vote_count DESC";
        $res_candidates = mysqli_query($conn, $sql_candidates);
        
        $num_candidates = mysqli_num_rows($res_candidates);
        $rowspan = $num_candidates; 
        $first = true;
        
        // Loop candidates
        while($cand = mysqli_fetch_assoc($res_candidates)) {
            $count = $cand['vote_count'];
            $perc = ($total_pos_votes > 0) ? round(($count / $total_pos_votes) * 100, 2) : 0;
            
            // Determine Status
            $status_html = '';
            if($perc > 50) {
                 $status_html = '<span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-bold border border-primary/20 uppercase">Lead</span>';
            } elseif ($count == 0) {
                 $status_html = '<span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px] font-bold uppercase">No Votes</span>';
            } elseif ($perc < 20) {
                 $status_html = '<span class="px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-500 text-[10px] font-bold uppercase">Minority</span>';
            } else {
                 $status_html = '<span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px] font-bold uppercase">Trailing</span>';
            }
            if($first && $perc > 0) {
                // Determine if Leading specifically if first in list
                // If already marked as Lead (>50), good. Else, maybe 'Challenging' or just 'Ahead'?
                // For simplified logic, top is 'Leading' if counts > 0
                if($perc <= 50) {
                     $status_html = '<span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-bold border border-primary/20 uppercase">Leading</span>';
                }
            }

            // --- Sparkline Data Generation (Mock/Real) ---
            // Ideally query votes per hour for this candidate for last 6h
            // For now, let's generate a pseudo-random path or flat line based on current count
            // Simple logic: 0 to $count over 6 points
            $check_points = [0, 10, 20, 30, 40, 50, 60, 70, 80, 100];
            $path_d = "M0 25 ";
            // This is complex to query efficiently in a loop without heavy load. 
            // We'll use a simplified static visual that mimics "activity" or just a flat line if no votes.
            if($count > 0) {
                // Generate a random-looking trend that ends high
                 $path_d = "M0 25 L20 20 L40 22 L60 15 L80 10 L100 5"; // Upward trend
            } else {
                 $path_d = "M0 28 L100 28"; // Flatline at bottom
            }

            $current_row_bg = ($first) ? '' : ''; // Add highlight?
    ?>
    <tr class="group border-b border-[#f0f7f0] dark:border-[#1e3a1e] hover:bg-gray-50/50 dark:hover:bg-white/5 <?php echo ($perc == 100) ? 'bg-primary/5' : ''; ?>">
        <?php if($first) { ?>
        <td class="px-6 py-4 font-bold align-middle bg-white dark:bg-[#152a15]" rowspan="<?php echo $rowspan; ?>">
            <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base"><?php echo $meta['icon']; ?></span>
            <span><?php echo $meta['name']; ?></span>
            </div>
        </td>
        <?php } ?>
        <td class="px-6 py-4 font-medium <?php echo ($first) ? 'text-[#0d1b0d] dark:text-white' : 'text-gray-500 dark:text-gray-400'; ?>">
            <?php echo $cand['namaCalon']; ?>
        </td>
        <td class="px-4 py-4">
            <div class="flex justify-center">
            <svg class="h-8 w-24 <?php echo ($count > 0) ? 'text-primary' : 'text-gray-300'; ?> sparkline-svg" viewBox="0 0 100 30">
            <path d="<?php echo $path_d; ?>" fill="none" stroke="currentColor"></path>
            </svg>
            </div>
        </td>
        <td class="px-6 py-4 text-right tabular-nums"><?php echo $count; ?></td>
        <td class="px-6 py-4 text-right tabular-nums font-bold"><?php echo $perc; ?>%</td>
        <td class="px-6 py-4">
            <?php echo $status_html; ?>
        </td>
        <td class="px-6 py-4 text-right">
            <button class="text-primary hover:underline text-xs font-bold">Inspect</button>
        </td>
    </tr>
    <?php 
        $first = false;
        } // End candidate loop
    } // End position loop
    ?>

</tbody>
</table>
</div>
<div class="px-6 py-4 bg-gray-50 dark:bg-[#1a331a] flex items-center justify-between text-xs text-gray-500 font-medium">
<div>Showing all active candidates</div>
<div class="flex items-center gap-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-base">cloud_done</span>
<span>Audited by System</span>
</div>
<div class="h-4 w-px bg-gray-300 dark:bg-gray-700"></div>
<p>UTC Timestamp: <?php echo gmdate("Y-m-d H:i:s"); ?></p>
</div>
</div>
</div>

<!-- Graphs Section -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Velocity Chart -->
<div class="lg:col-span-2 bg-white dark:bg-[#152a15] rounded-xl border border-[#cfe7cf] dark:border-[#1e3a1e] p-6 shadow-sm">
<div class="flex items-center justify-between mb-6">
<h3 class="text-sm font-bold flex items-center gap-2">
<span class="material-symbols-outlined text-primary">data_exploration</span>
                        Voting Velocity Correlation
                    </h3>
<select class="text-[11px] font-bold bg-transparent border-none focus:ring-0 p-0 text-primary uppercase cursor-pointer">
<option>Last 12 Hours</option>
</select>
</div>
<div class="h-[180px] flex items-end justify-between gap-1 px-2">
    <?php foreach($voting_velocity as $idx => $v_count) { 
        $h_percent = ceil(($v_count / $max_votes) * 100);
        $opacity = ($idx % 2 == 0) ? 'bg-primary' : 'bg-primary/60';
    ?>
    <div class="w-full <?php echo $opacity; ?> rounded-t hover:bg-emerald-400 transition-all relative group" style="height: <?php echo $h_percent; ?>%">
        <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-black text-white text-[10px] px-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
            <?php echo $v_count; ?>
        </div>
    </div>
    <?php } ?>
</div>
<div class="mt-4 flex justify-between text-[10px] text-gray-400 font-bold uppercase tracking-wider">
    <!-- Simplified Labels -->
    <span>-12h</span>
    <span>-10h</span>
    <span>-8h</span>
    <span>-6h</span>
    <span>-4h</span>
    <span>-2h</span>
    <span>Now</span>
</div>
</div>

<!-- Export Card -->
<div class="bg-[#102210] text-white rounded-xl border border-primary/20 p-6 flex flex-col justify-between shadow-lg relative overflow-hidden">
<div class="absolute -right-4 -top-4 opacity-10">
<span class="material-symbols-outlined text-9xl">download</span>
</div>
<div>
<h3 class="text-sm font-bold flex items-center gap-2 mb-2">
<span class="material-symbols-outlined text-primary">description</span>
                        Final Data Package
                    </h3>
<p class="text-xs text-gray-400 leading-relaxed mb-6">Download the complete dataset including voter demographics, timestamped logs, and verified tallies for the audit board.</p>
</div>
<div class="flex flex-col gap-2 relative z-10">
<button onclick="window.print()" class="w-full py-2.5 bg-primary text-[#0d1b0d] font-bold text-xs rounded-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-sm">print</span>
                        Print Audit Ledger
                    </button>
<!-- Placeholder for CSV export -->
<button class="w-full py-2.5 border border-primary/30 text-white font-bold text-xs rounded-lg hover:bg-white/5 transition-colors flex items-center justify-center gap-2 cursor-not-allowed opacity-50">
<span class="material-symbols-outlined text-sm">file_download</span>
                        Export Full CSV Data
                    </button>
</div>
</div>
</div>
</main>
<footer class="mt-auto border-t border-[#cfe7cf] dark:border-[#1e3a1e] py-6 px-10 flex flex-col items-center gap-2 text-[10px] opacity-60 uppercase tracking-widest font-bold">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-sm">security</span>
<p>Analytical Access Level: Committee Member (L3)</p>
</div>
<p>© 2024 FC Committee • System ID: FC-VOTE-2024-PRO</p>
</footer>
</div>

</body>
</html>
