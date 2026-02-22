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
    $candidates[] = [
        'id'     => $id,
        'name'   => $row['namaCalon'],
        'class'  => $row['kelas'],
        'img'    => $row['gambar'],
        'votes'  => $count,
        'prefix' => $prefix
    ];
    if(!isset($position_data[$prefix])) $position_data[$prefix] = 0;
    $position_data[$prefix] += $count;
}

foreach($candidates as &$c) {
    $pos_total = isset($position_data[$c['prefix']]) ? $position_data[$c['prefix']] : 0;
    $c['percent'] = ($pos_total > 0) ? ($c['votes'] / $pos_total) * 100 : 0;
}
unset($c);
?>
<!DOCTYPE html>
<html class="light" lang="ms">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Keputusan Admin - Kelab Bola Sepak</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&family=Noto+Sans:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#11d411",
                    "background-light": "#f6f8f6",
                    "background-dark": "#102210",
                    "deep-green": "#0d1b0d",
                },
                fontFamily: { "display": ["Lexend", "sans-serif"] },
            },
        },
    }
</script>
<style>
    body { font-family: 'Lexend', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .tabular-nums { font-variant-numeric: tabular-nums; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    .bar-fill { transition: width 1s ease-out; }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark text-deep-green dark:text-white min-h-screen">
<div class="layout-container flex flex-col min-h-screen">
    
    <!-- Top Admin Bar -->
    <div class="bg-primary px-4 py-1.5 flex items-center justify-center gap-2 text-deep-green font-bold text-xs uppercase tracking-wider shadow-md">
        <span class="material-symbols-outlined text-base">verified_user</span>
        <span>Sesi Admin Selamat • Tahap Akses: Tinggi</span>
    </div>

    <!-- Sticky Header -->
    <header class="flex items-center justify-between border-b border-primary/20 px-8 py-3 bg-deep-green text-white sticky top-0 z-10 shadow-xl">
        <div class="flex items-center gap-3">
            <div class="size-8 bg-primary rounded-lg flex items-center justify-center shadow-lg">
                <span class="material-symbols-outlined text-deep-green text-xl">sports_soccer</span>
            </div>
            <div class="text-right">
                <h2 class="text-base font-bold tracking-tight leading-none">Panel Admin</h2>
                <span class="text-[10px] font-bold text-primary uppercase tracking-widest">Analitik Langsung</span>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex items-center gap-4">
            <nav class="flex items-center gap-1 bg-white/10 p-1 rounded-lg">
                <a class="px-3 py-1.5 text-xs font-bold rounded-md bg-primary text-deep-green transition-all" href="#">Analitik</a>
                <a class="px-3 py-1.5 text-xs font-bold rounded-md text-white/60 hover:text-white hover:bg-white/10 transition-all" href="admin.php">Urus Calon</a>
                <a class="px-3 py-1.5 text-xs font-bold rounded-md text-white/60 hover:text-white hover:bg-white/10 transition-all" href="dashboard.php">Papan Utama</a>
            </nav>
            <a href="logout.php" class="flex items-center justify-center rounded-lg h-8 px-3 bg-red-500/20 text-red-400 hover:bg-red-500/30 transition-colors gap-2">
                <span class="material-symbols-outlined text-base">logout</span>
                <span class="text-xs font-bold">Log Keluar</span>
            </a>
        </div>
    </header>

    <main class="max-w-[1400px] mx-auto w-full px-8 py-8 space-y-8 animate-fade-in">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div class="text-right w-full">
                <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">Keputusan Undian</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Visualisasi data undian masa nyata.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-green-700 bg-green-100 px-3 py-1 rounded-full shrink-0">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Sistem Dalam Talian
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Jumlah Undi</p>
                    <p class="text-2xl font-black tabular-nums text-primary mt-1"><?php echo $total_votes; ?></p>
                </div>
                <div class="p-3 bg-primary/10 rounded-xl text-primary ml-4 shrink-0">
                    <span class="material-symbols-outlined">ballot</span>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Calon Berdaftar</p>
                    <p class="text-2xl font-black tabular-nums text-blue-500 mt-1"><?php echo count($candidates); ?></p>
                </div>
                <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500 ml-4 shrink-0">
                    <span class="material-symbols-outlined">groups</span>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Kategori Jawatan</p>
                    <p class="text-2xl font-black tabular-nums text-emerald-500 mt-1"><?php echo count($positions); ?></p>
                </div>
                <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500 ml-4 shrink-0">
                    <span class="material-symbols-outlined">work</span>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Sistem Aktif</p>
                    <p class="text-2xl font-black text-green-500 mt-1">Normal</p>
                </div>
                <div class="p-3 bg-green-500/10 rounded-xl text-green-500 ml-4 shrink-0">
                    <span class="material-symbols-outlined">wifi_tethering</span>
                </div>
            </div>
        </div>

        <!-- Primary Bar Chart Visualization -->
        <div class="bg-white/5 backdrop-blur-md rounded-3xl border border-primary/20 p-8 shadow-2xl">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-xl font-bold flex items-center gap-3 text-white">
                    <span class="material-symbols-outlined text-primary">equalizer</span>
                    Visualisasi Agregat Undian
                </h3>
                <button onclick="window.print()" class="flex items-center gap-2 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 font-bold py-2.5 px-5 rounded-xl text-xs transition-all active:scale-[0.98]">
                    <span class="material-symbols-outlined text-sm">print</span> Muat Turun PDF
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <?php foreach($positions as $prefix => $title) { 
                    $pos_candidates = array_filter($candidates, function($c) use ($prefix) {
                        return $c['prefix'] == $prefix;
                    });
                    usort($pos_candidates, function($a, $b) { return $b['votes'] - $a['votes']; });
                    $winner_vote = !empty($pos_candidates) ? reset($pos_candidates)['votes'] : 0;
                    
                    $c_labels = []; $c_values = [];
                    foreach($pos_candidates as $pc) { $c_labels[] = $pc['name']; $c_values[] = $pc['votes']; }
                ?>
                <div class="space-y-6">
                    <div class="text-right border-b border-white/10 pb-3">
                        <h4 class="text-sm font-black uppercase tracking-[3px] text-primary"><?php echo $title; ?></h4>
                    </div>
                    
                    <div class="h-[200px]">
                        <canvas id="canvas_<?php echo $prefix; ?>"></canvas>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('canvas_<?php echo $prefix; ?>').getContext('2d');
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: <?php echo json_encode($c_labels); ?>,
                                    datasets: [{
                                        data: <?php echo json_encode($c_values); ?>,
                                        backgroundColor: [
                                            'rgba(17, 212, 17, 0.8)',
                                            'rgba(17, 212, 17, 0.5)',
                                            'rgba(17, 212, 17, 0.3)',
                                            'rgba(17, 212, 17, 0.1)'
                                        ],
                                        borderColor: 'rgba(16, 34, 16, 1)',
                                        borderWidth: 2
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { 
                                            backgroundColor: '#0d1b0d',
                                            titleFont: { family: 'Lexend', weight: 'bold' },
                                            bodyFont: { family: 'Lexend' },
                                            padding: 10, cornerRadius: 8
                                        }
                                    },
                                    cutout: '70%'
                                }
                            });
                        });
                    </script>

                    <div class="space-y-4">
                    <?php foreach($pos_candidates as $c) { 
                        $is_winner = ($c['votes'] > 0 && $c['votes'] == $winner_vote);
                    ?>
                        <div class="flex items-center justify-between text-[11px] font-bold">
                            <div class="flex flex-col items-end w-full">
                                <span class="text-gray-300"><?php echo $c['name']; ?></span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-gray-500 italic"><?php echo number_format($c['percent'], 1); ?>%</span>
                                    <span class="text-primary tabular-nums"><?php echo $c['votes']; ?> UNDI</span>
                                    <?php if($is_winner): ?>
                                    <span class="text-[8px] bg-primary text-deep-green px-1.5 py-0.5 rounded-full uppercase font-black">UNGGUL</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- Detailed Table View -->
        <div class="bg-white/5 backdrop-blur-md border border-primary/20 rounded-3xl shadow-2xl overflow-hidden">
            <div class="px-8 py-5 border-b border-primary/20 bg-deep-green/30 flex justify-between items-center">
                <h3 class="text-sm font-black text-primary uppercase tracking-[4px] text-right w-full">Audit Data Terperinci</h3>
            </div>
            <div class="overflow-x-auto text-right">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-white/5 text-[10px] font-black uppercase tracking-widest text-gray-400">
                            <th class="px-8 py-4">Kategori Calon</th>
                            <th class="px-8 py-4">Nama Lengkap</th>
                            <th class="px-8 py-4">Potret</th>
                            <th class="px-8 py-4">Tabulasi Undi</th>
                            <th class="px-8 py-4">Pencapaian %</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-white/5">
                        <?php foreach($candidates as $c) { ?>
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-8 py-5 font-bold text-gray-500 uppercase tracking-tighter text-xs">
                                <?php echo isset($positions[$c['prefix']]) ? $positions[$c['prefix']] : '-'; ?>
                            </td>
                            <td class="px-8 py-5 font-black text-white group-hover:text-primary transition-colors">
                                <?php echo $c['name']; ?>
                            </td>
                            <td class="px-8 py-5">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 border border-primary/20 flex items-center justify-center overflow-hidden float-right">
                                    <?php if($c['img']) { ?>
                                    <img src="<?php echo $c['img']; ?>" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($c['name']); ?>&background=11d411&color=fff'"/>
                                    <?php } else { ?>
                                    <span class="font-black text-primary"><?php echo substr($c['name'],0,1); ?></span>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="px-8 py-5 tabular-nums text-white font-black"><?php echo $c['votes']; ?></td>
                            <td class="px-8 py-5 tabular-nums font-black text-primary">
                                <?php echo number_format($c['percent'], 1); ?>%
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="mt-auto border-t border-primary/20 py-6 px-10 flex flex-col items-center gap-2 text-[10px] opacity-60 uppercase tracking-widest font-bold text-gray-500">
        <p>© 2024 Jawatankuasa Kelab Bola Sepak • Panel Admin</p>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
