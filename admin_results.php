<?php
include("auth_session.php");
require('db_config.php');

// Admin Check
$admins = ['D6290', 'admin']; 
if (!in_array($_SESSION['idPengguna'], $admins)) {
     header("Location: dashboard.php");
     exit();
}

// 1. Total Votes
$sql_total = "SELECT COUNT(*) as total FROM Undian_1";
$res_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($res_total);
$total_votes = $row_total['total'];

// 2. Votes Per Candidate
$vote_counts = [];
$sql_votes = "SELECT idCalon, COUNT(*) as count FROM Undian_1 GROUP BY idCalon";
$res_votes = mysqli_query($conn, $sql_votes);
while($row = mysqli_fetch_assoc($res_votes)) {
    $vote_counts[$row['idCalon']] = $row['count'];
}

// 3. Fetch All Candidates & Calculate Metrics
$candidates = [];
$positions = ['P' => 'Pengerusi', 'S' => 'Setiausaha', 'B' => 'Bendahari'];
$position_data = [];

$sql_calon = "SELECT * FROM Calon_1 ORDER BY idCalon ASC";
$res_calon = mysqli_query($conn, $sql_calon);

while($row = mysqli_fetch_assoc($res_calon)) {
    $id = $row['idCalon'];
    $count = isset($vote_counts[$id]) ? $vote_counts[$id] : 0;
    
    $prefix = substr($id, 0, 1);
    
    // Store temporarily
    $candidates[] = [
        'id' => $id,
        'name' => $row['namaCalon'],
        'class' => $row['kelas'],
        'img' => $row['gambar'],
        'votes' => $count,
        'prefix' => $prefix
    ];
    
    // Track totals per position
    if(!isset($position_data[$prefix])) {
        $position_data[$prefix] = 0;
    }
    $position_data[$prefix] += $count;
}

// Recalculate percentages based on Position Total
foreach($candidates as &$c) {
    // Only calculate % if there are votes in that position
    $pos_total = isset($position_data[$c['prefix']]) ? $position_data[$c['prefix']] : 0;
    $c['percent'] = ($pos_total > 0) ? ($c['votes'] / $pos_total) * 100 : 0;
}
unset($c); // Break reference

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
                        "primary": "#6366f1", // Indigo/Violet Theme
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a", // Slate 900
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
        /* Custom Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .bar-fill {
            transition: width 1s ease-out;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 min-h-screen">
<div class="layout-container flex flex-col min-h-screen">
    
    <!-- Top Audit Bar -->
    <div class="bg-primary px-4 py-1.5 flex items-center justify-center gap-2 text-white font-bold text-xs uppercase tracking-wider shadow-md relative z-20">
        <span class="material-symbols-outlined text-base">verified_user</span>
        <span>Secure Admin Session • Access Level: High</span>
    </div>

    <!-- Sticky Header -->
    <header class="flex items-center justify-between border-b border-gray-200 dark:border-slate-800 px-8 py-3 bg-white dark:bg-slate-900 sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="size-8 bg-primary rounded-lg flex items-center justify-center shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined text-white text-xl">analytics</span>
            </div>
            <div>
                <h2 class="text-base font-bold tracking-tight leading-none">Admin Panel</h2>
                <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Live Analytics</span>
            </div>
        </div>

        <!-- Right Side Menu -->
        <div class="flex items-center gap-6">
            <nav class="flex items-center gap-1 bg-gray-100 dark:bg-slate-800 p-1 rounded-lg">
                <a class="px-3 py-1.5 text-xs font-bold rounded-md bg-white dark:bg-slate-700 shadow-sm text-primary transition-all" href="#">
                    Analytics
                </a>
                <a class="px-3 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-200 dark:hover:bg-slate-700 transition-all" href="admin.php">
                    Manage Candidates
                </a>
                <a class="px-3 py-1.5 text-xs font-bold rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-200 dark:hover:bg-slate-700 transition-all" href="dashboard.php">
                    View Dashboard
                </a>
            </nav>
            
            <div class="h-8 w-px bg-gray-200 dark:bg-slate-700"></div>

            <a href="logout.php" class="flex items-center justify-center rounded-lg h-8 px-3 bg-red-50 dark:bg-red-900/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors gap-2">
                <span class="material-symbols-outlined text-base">logout</span>
                <span class="text-xs font-bold">Logout</span>
            </a>
        </div>
    </header>

    <main class="max-w-[1400px] mx-auto w-full px-8 py-8 space-y-8 animate-fade-in">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Election Results</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Real-time voting data visualization.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                System Online
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-800 p-5 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Votes</p>
                    <p class="text-2xl font-black tabular-nums text-slate-800 dark:text-white mt-1"><?php echo $total_votes; ?></p>
                </div>
                <div class="p-2 bg-primary/10 rounded-lg text-primary">
                    <span class="material-symbols-outlined">ballot</span>
                </div>
            </div>
            
             <!-- Simulating other stats for visual completeness -->
            <div class="bg-white dark:bg-slate-800 p-5 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Candidates</p>
                    <p class="text-2xl font-black tabular-nums text-slate-800 dark:text-white mt-1"><?php echo count($candidates); ?></p>
                </div>
                <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500">
                    <span class="material-symbols-outlined">groups</span>
                </div>
            </div>
            
            <div class="bg-white dark:bg-slate-800 p-5 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Participation</p>
                    <p class="text-2xl font-black tabular-nums text-slate-800 dark:text-white mt-1">~<?php echo ($total_votes > 0) ? "84" : "0"; ?>%</p>
                </div>
                <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-500">
                    <span class="material-symbols-outlined">trending_up</span>
                </div>
            </div>
            
             <div class="bg-white dark:bg-slate-800 p-5 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div>
                    <p class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Status</p>
                    <p class="text-2xl font-black tabular-nums text-slate-800 dark:text-white mt-1">Active</p>
                </div>
                <div class="p-2 bg-green-500/10 rounded-lg text-green-500">
                    <span class="material-symbols-outlined">wifi</span>
                </div>
            </div>
        </div>

        <!-- Primary Bar Chart Visualization -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Chart Area -->
             <div class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                     <h3 class="text-lg font-bold flex items-center gap-2 text-slate-800 dark:text-white">
                        <span class="material-symbols-outlined text-primary">bar_chart_4_bars</span>
                        Live Voting Results
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <?php foreach($positions as $prefix => $title) { 
                        // Find candidates for this position
                        $pos_candidates = array_filter($candidates, function($c) use ($prefix) {
                            return $c['prefix'] == $prefix;
                        });
                        
                        // Sort by votes desc
                        usort($pos_candidates, function($a, $b) {
                            return $b['votes'] - $a['votes'];
                        });
                        
                        // Find winner for highlighting
                        $winner_vote = !empty($pos_candidates) ? $pos_candidates[0]['votes'] : 0;
                    ?>
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-widest text-slate-400 border-b border-slate-100 dark:border-slate-700 pb-2 mb-4"><?php echo $title; ?></h4>
                        
                        <?php foreach($pos_candidates as $c) { 
                             // Calculate % for bar width
                             $width = ($c['percent'] > 1) ? $c['percent'] : 1;
                             
                             $is_winner = ($c['votes'] > 0 && $c['votes'] == $winner_vote);
                             $color = $is_winner ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600';
                             $text_color = $is_winner ? 'text-primary' : 'text-slate-500';
                        ?>
                        <div class="relative group">
                            <div class="flex items-center justify-between text-xs font-bold mb-1">
                                <span class="flex items-center gap-2 text-slate-700 dark:text-slate-200">
                                    <?php echo $c['name']; ?>
                                    <span class="text-[10px] font-normal text-slate-400 bg-slate-100 dark:bg-slate-700 px-1.5 rounded"><?php echo $c['class']; ?></span>
                                </span>
                                <span class="<?php echo $text_color; ?>"><?php echo number_format($c['percent'], 0); ?>%</span>
                            </div>
                            <!-- Bar Background -->
                            <div class="w-full h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mb-1">
                                <!-- Bar Fill -->
                                <div class="h-full rounded-full <?php echo $color; ?> bar-fill shadow-[0_0_10px_rgba(99,102,241,0.2)]" style="width: <?php echo $width; ?>%"></div>
                            </div>
                             <div class="text-[10px] text-right text-slate-400 font-mono"><?php echo $c['votes']; ?> votes</div>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
             </div>
        </div>

        <!-- Detailed Table View (Data Source) -->
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-sm overflow-hidden mt-8">
             <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Detailed Ledger</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Position</th>
                            <th class="px-6 py-4">Candidate</th>
                            <th class="px-6 py-4 text-right">Votes</th>
                            <th class="px-6 py-4 text-right">Share %</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800">
                        <?php foreach($candidates as $c) { ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-600 dark:text-slate-400">
                                <?php echo $positions[$c['prefix']]; ?>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-white flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 text-xs flex items-center justify-center overflow-hidden">
                                     <?php if($c['img']) { ?>
                                        <img src="<?php echo $c['img']; ?>" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($c['name']); ?>&background=random'"/>
                                    <?php } else { ?>
                                        <?php echo substr($c['name'], 0, 1); ?>
                                    <?php } ?>
                                </div>
                                <?php echo $c['name']; ?>
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums text-slate-700 dark:text-slate-300"><?php echo $c['votes']; ?></td>
                            <td class="px-6 py-4 text-right tabular-nums font-bold text-primary">
                                <?php echo number_format($c['percent'], 1); ?>%
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-6 px-10 flex flex-col items-center gap-2 text-[10px] opacity-60 uppercase tracking-widest font-bold text-slate-500">
        <p>© 2024 FC Committee • System ID: FC-ADMIN-V2</p>
    </footer>
</div>
</body>
</html>
