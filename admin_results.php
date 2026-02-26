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
<title>Papan Analitik Pentadbir - Portal Undian</title>
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
            },
        },
    }
</script>
<style type="text/tailwindcss">
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
        .bar-fill { transition: width 1s ease-out; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-100 min-h-screen">
<div class="layout-container flex flex-col min-h-screen">
    <div class="flex h-screen overflow-hidden flex-row-reverse">
    <!-- Sidebar Navigation -->
    <aside class="w-72 bg-sidebar-dark text-slate-300 flex flex-col justify-between py-6 px-4 shadow-xl shrink-0 hidden md:flex border-l border-white/5 order-2 h-full">
        <div class="flex flex-col gap-8">
            <div class="flex items-center gap-3 px-4">
                <div class="bg-primary p-2 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(128,242,13,0.3)]">
                    <span class="material-symbols-outlined text-black font-extrabold">&#xe8e1;</span>
                </div>
                <div class="flex flex-col text-right">
                    <h1 class="text-sm font-extrabold leading-tight uppercase tracking-tight text-white">Panel Pentadbir</h1>
                    <p class="text-primary text-[10px] font-black uppercase tracking-widest">Pusat Kawalan</p>
                </div>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all text-slate-400 hover:text-white flex-row-reverse group" href="admin.php">
                    <span class="material-symbols-outlined group-hover:text-primary">&#xe3c9;</span>
                    <span class="text-sm font-black uppercase tracking-tight">Urus Calon</span>
                </a>
                <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/10 text-white border-r-4 border-primary transition-all flex-row-reverse">
                    <span class="material-symbols-outlined text-primary font-black">&#xe24b;</span>
                    <span class="text-sm font-black uppercase tracking-tight">Keputusan Pentadbir</span>
                </div>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all text-slate-400 hover:text-white flex-row-reverse group" href="dashboard.php">
                    <span class="material-symbols-outlined group-hover:text-primary">&#xe871;</span>
                    <span class="text-sm font-black uppercase tracking-tight">Papan Utama</span>
                </a>
            </nav>
        </div>
        <div class="flex flex-col gap-2 pt-6 border-t border-white/5">
            <div class="flex items-center gap-3 px-4 py-3 bg-white/5 rounded-2xl mx-2 flex-row-reverse">
                <div class="size-10 rounded-full bg-primary flex items-center justify-center text-black font-black text-lg">A</div>
                <div class="flex flex-col overflow-hidden text-right">
                    <p class="text-xs font-black text-white truncate">Pentadbir</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest tracking-tighter">Akses Penuh</p>
                </div>
            </div>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-red-400 transition-colors text-slate-500 flex-row-reverse group" href="logout.php">
                <span class="material-symbols-outlined group-hover:animate-pulse font-black">&#xe9ba;</span>
                <span class="text-[10px] font-black uppercase tracking-widest">Keluar</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto bg-background-light dark:bg-background-dark order-1">
        <header class="md:hidden flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-8 py-4 bg-white dark:bg-card-dark">
             <h1 class="text-lg font-extrabold text-right w-full dark:text-white">Pentadbir</h1>
             <a href="logout.php" class="text-red-500 font-bold text-sm ml-4">Keluar</a>
        </header>

    <main class="max-w-[1400px] mx-auto w-full px-8 py-8 space-y-8 animate-fade-in">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div class="text-right w-full">
                <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">Keputusan Undian</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Visualisasi data undian masa nyata.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Sistem Dalam Talian
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-card-dark p-6 border border-slate-200 dark:border-white/5 rounded-3xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div class="text-right flex-1">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Jumlah Undian</p>
                    <p class="text-3xl font-black tabular-nums text-slate-800 dark:text-white mt-1"><?php echo $total_votes; ?></p>
                </div>
                <div class="p-3 bg-primary/10 rounded-2xl text-primary ml-4 order-first">
                    <span class="material-symbols-outlined font-black">&#xef49;</span>
                </div>
            </div>
            
            <div class="bg-white dark:bg-card-dark p-6 border border-slate-200 dark:border-white/5 rounded-3xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div class="text-right flex-1">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Calon</p>
                    <p class="text-3xl font-black tabular-nums text-slate-800 dark:text-white mt-1"><?php echo count($candidates); ?></p>
                </div>
                <div class="p-3 bg-blue-500/10 rounded-2xl text-blue-500 ml-4 order-first">
                    <span class="material-symbols-outlined font-black">&#xe7ef;</span>
                </div>
            </div>
            
            <div class="bg-white dark:bg-card-dark p-6 border border-slate-200 dark:border-white/5 rounded-3xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div class="text-right flex-1">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Penyertaan</p>
                    <p class="text-3xl font-black tabular-nums text-slate-800 dark:text-white mt-1">84%</p>
                </div>
                <div class="p-3 bg-emerald-500/10 rounded-2xl text-emerald-500 ml-4 order-first">
                    <span class="material-symbols-outlined font-black">&#xe85d;</span>
                </div>
            </div>
            
             <div class="bg-white dark:bg-card-dark p-6 border border-slate-200 dark:border-white/5 rounded-3xl flex items-center justify-between shadow-sm hover:shadow-md transition-shadow">
                <div class="text-right flex-1">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Status</p>
                    <p class="text-3xl font-black tabular-nums text-primary mt-1">AKTIF</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-2xl text-primary ml-4 order-first">
                    <span class="material-symbols-outlined font-black">&#xe63e;</span>
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
                        Keputusan Undian Langsung
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
                             <div class="text-[10px] text-right text-slate-400 font-mono"><?php echo $c['votes']; ?> undian</div>
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
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">Laporan Terperinci</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Jawatan</th>
                            <th class="px-6 py-4">Calon</th>
                            <th class="px-6 py-4 text-right">Undian</th>
                            <th class="px-6 py-4 text-right">Peratusan</th>
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
        <p>© 2024 Jawatankuasa FC • ID Sistem: FC-ADMIN-V2</p>
    </footer>
</div>
</body>
</html>
