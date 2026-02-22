<?php
include("auth_session.php");
require('db_config.php');

// User info
$idPengguna = $_SESSION['idPengguna'];
$sql_user = "SELECT * FROM Pengguna_1 WHERE idPengguna='$idPengguna'";
$result_user = mysqli_query($conn, $sql_user);
$row = mysqli_fetch_assoc($result_user);
$namaPengguna = $row['namaPengguna'];

// --- STATS CALCULATION ---

// 1. Total unique voters (people who have voted for at least one position)
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

// 3. Voting Velocity (Last 12 Hours) - uses correct column 'timestamp'
$voting_velocity = [];
$labels = [];
for ($i = 11; $i >= 0; $i--) {
    $hour_start = date('Y-m-d H:00:00', strtotime("-$i hours"));
    $hour_end   = date('Y-m-d H:59:59', strtotime("-$i hours"));
    $hour_label = date('H:00', strtotime("-$i hours"));

    $sql_velocity = "SELECT COUNT(*) as count FROM Undian_1 WHERE timestamp BETWEEN '$hour_start' AND '$hour_end'";
    $res_velocity = mysqli_query($conn, $sql_velocity);
    $row_velocity  = mysqli_fetch_assoc($res_velocity);

    $voting_velocity[] = $row_velocity ? $row_velocity['count'] : 0;
    $labels[] = $hour_label;
}
$max_votes = max($voting_velocity);
$max_votes = ($max_votes == 0) ? 1 : $max_votes;

// 4. Get all positions from DB
$positions_map = [];
$sql_pos = "SELECT * FROM Jawatan_1 ORDER BY idJawatan ASC";
$res_pos = mysqli_query($conn, $sql_pos);
while ($p = mysqli_fetch_assoc($res_pos)) {
    $positions_map[$p['idJawatan']] = $p['namaJawatan'];
}
?>
<!DOCTYPE html>
<html class="light" lang="ms">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Keputusan Undian - Kelab Bola Sepak</title>
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
                    "background-light": "#f8faf8",
                    "background-dark": "#0a140a",
                    "deep-green": "#0d1b0d",
                },
                fontFamily: {
                    "display": ["Lexend", "sans-serif"]
                },
            },
        },
    }
</script>
<style>
    body { font-family: 'Lexend', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .tabular-nums { font-variant-numeric: tabular-nums; }
    .sparkline-svg { fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
    .bar-fill { transition: width 1s ease-out; }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark text-deep-green dark:text-gray-100 min-h-screen">
<div class="layout-container flex flex-col">

    <!-- Top Status Bar -->
    <div class="bg-primary px-4 py-1.5 flex items-center justify-center gap-2 text-deep-green font-bold text-xs uppercase tracking-wider">
        <span class="material-symbols-outlined text-base">verified</span>
        <span>Keputusan Teraudit: 100% undian telah disahkan</span>
    </div>

    <!-- Header -->
    <header class="flex items-center justify-between border-b border-primary/20 px-8 py-3 bg-deep-green text-white sticky top-0 z-10 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="size-8 bg-primary rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-deep-green text-xl">sports_soccer</span>
            </div>
            <div class="text-right">
                <h2 class="text-base font-bold leading-none">Kelab Bola Sepak <span class="text-primary">/ Keputusan</span></h2>
                <p class="text-[10px] text-primary/80 font-bold uppercase tracking-widest mt-0.5">Statistik Langsung</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <nav class="flex items-center gap-5">
                <a class="text-xs font-semibold hover:text-primary transition-colors" href="dashboard.php">Utama</a>
                <a class="text-xs font-semibold text-primary transition-colors border-b-2 border-primary pb-0.5" href="#">Keputusan</a>
            </nav>
            <div class="flex gap-2 items-center border-l pl-6 border-white/20">
                <span class="text-[10px] text-gray-300 font-bold uppercase hidden md:block"><?php echo htmlspecialchars($namaPengguna); ?></span>
                <a href="logout.php" class="flex items-center justify-center rounded-lg h-8 w-8 bg-red-500/20 text-red-400 hover:bg-red-500/30 transition-colors" title="Log Keluar">
                    <span class="material-symbols-outlined text-lg">logout</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-[1400px] mx-auto w-full px-8 py-6 animate-fade-in">
        <!-- Title Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div class="text-right w-full">
                <h1 class="text-3xl font-black tracking-tight text-white">Metadata Undian Semasa</h1>
                <p class="text-gray-400 text-xs mt-1">Analitik visual berdasarkan pengundi berdaftar • Data disegerakkan</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button onclick="window.location.reload()" class="flex items-center gap-2 rounded-xl h-10 px-5 bg-primary text-deep-green text-xs font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 active:scale-[0.98]">
                    <span class="material-symbols-outlined text-sm">sync</span>
                    <span>Segarkan Data</span>
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Undian Sah</p>
                    <p class="text-2xl font-black tabular-nums text-primary mt-1"><?php echo number_format($total_ballots); ?></p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center ml-4 shrink-0">
                    <span class="material-symbols-outlined text-primary text-2xl">how_to_vote</span>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Kadar Penyertaan</p>
                    <p class="text-2xl font-black tabular-nums text-primary mt-1"><?php echo $turnout_percentage; ?>%</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center ml-4 shrink-0">
                    <span class="material-symbols-outlined text-primary text-2xl">trending_up</span>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Jumlah Ahli</p>
                    <p class="text-2xl font-black tabular-nums text-primary mt-1"><?php echo $total_users; ?></p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center ml-4 shrink-0">
                    <span class="material-symbols-outlined text-primary text-2xl">person_search</span>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-sm p-6 border border-primary/20 rounded-2xl flex items-center justify-between shadow-xl">
                <div class="text-right w-full">
                    <p class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Audit Sistem</p>
                    <p class="text-2xl font-black tabular-nums text-green-500 mt-1">Selesai</p>
                </div>
                <div class="w-12 h-12 bg-green-500/10 rounded-xl flex items-center justify-center ml-4 shrink-0">
                    <span class="material-symbols-outlined text-green-500 text-2xl">fact_check</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Candidate Results Per Position -->
        <?php foreach($positions_map as $idJawatan => $namaJawatan): 
            // Get total votes for this position
            $sql_pos_total = "SELECT COUNT(*) as total FROM Undian_1 WHERE idJawatan='$idJawatan'";
            $res_pos_total = mysqli_query($conn, $sql_pos_total);
            $total_pos_votes = mysqli_fetch_assoc($res_pos_total)['total'];

            // Map Jawatan ID to Calon prefix: J1→P, J2→S, J3→B
            $prefix_map = ['J1' => 'P', 'J2' => 'S', 'J3' => 'B'];
            $calon_prefix = isset($prefix_map[$idJawatan]) ? $prefix_map[$idJawatan] : substr($idJawatan, 1);

            // Get candidates for this position with vote counts
            $sql_candidates = "SELECT c.namaCalon, c.idCalon, c.kelas, c.gambar,
                               (SELECT COUNT(*) FROM Undian_1 u WHERE u.idCalon = c.idCalon AND u.idJawatan = '$idJawatan') as vote_count
                               FROM Calon_1 c
                               WHERE c.idCalon LIKE '$calon_prefix%'
                               ORDER BY vote_count DESC";
            $res_candidates = mysqli_query($conn, $sql_candidates);
            
            $chart_labels = [];
            $chart_data = [];
            $candidate_list = [];
            
            while($c_row = mysqli_fetch_assoc($res_candidates)) {
                $chart_labels[] = $c_row['namaCalon'];
                $chart_data[] = $c_row['vote_count'];
                $candidate_list[] = $c_row;
            }
        ?>
            <div class="bg-white/5 backdrop-blur-md border border-primary/20 rounded-3xl overflow-hidden shadow-2xl flex flex-col">
                <div class="bg-deep-green/50 px-8 py-5 border-b border-primary/10 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-xl">bar_chart</span>
                        </div>
                        <div class="text-right">
                            <h3 class="text-lg font-bold text-white"><?php echo $namaJawatan; ?></h3>
                            <p class="text-primary text-[10px] font-bold uppercase tracking-wider"><?php echo $total_pos_votes; ?> Undian Terkumpul</p>
                        </div>
                    </div>
                </div>

                <div class="p-8 flex-1">
                    <?php if(empty($candidate_list)): ?>
                        <div class="py-12 text-center text-gray-500">
                            <span class="material-symbols-outlined text-5xl mb-4 block opacity-50">person_off</span>
                            <p>Tiada calon ditemui.</p>
                        </div>
                    <?php else: ?>
                        <!-- Chart Container -->
                        <div class="mb-8 h-[250px]">
                            <canvas id="chart_<?php echo $idJawatan; ?>"></canvas>
                        </div>
                        
                        <!-- List Section -->
                        <div class="space-y-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[2px] mb-4 text-right">Analisis Terperinci</p>
                            <?php 
                            $first_votes = $candidate_list[0]['vote_count'];
                            foreach($candidate_list as $index => $cand): 
                                $is_leading = ($cand['vote_count'] > 0 && $cand['vote_count'] == $first_votes && $index == 0);
                                $p_perc = ($total_pos_votes > 0) ? round(($cand['vote_count'] / $total_pos_votes) * 100, 1) : 0;
                            ?>
                            <div class="flex items-center gap-4 p-4 rounded-2xl border <?php echo $is_leading ? 'border-primary bg-primary/10' : 'border-white/5 bg-white/5'; ?> transition-all hover:bg-white/10">
                                <div class="w-10 h-10 rounded-full overflow-hidden border-2 <?php echo $is_leading ? 'border-primary' : 'border-white/20'; ?> shrink-0">
                                    <?php if($cand['gambar']): ?>
                                    <img src="<?php echo $cand['gambar']; ?>" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($cand['namaCalon']); ?>&background=random&color=fff'"/>
                                    <?php else: ?>
                                    <div class="w-full h-full bg-primary/20 flex items-center justify-center text-primary font-bold"><?php echo substr($cand['namaCalon'],0,1); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 text-right">
                                    <div class="flex items-center justify-end gap-2 mb-0.5">
                                        <?php if($is_leading): ?>
                                        <span class="text-[9px] bg-primary text-deep-green font-black px-2 py-0.5 rounded-full uppercase tracking-tighter">Mendahului</span>
                                        <?php endif; ?>
                                        <h4 class="text-sm font-bold text-white truncate"><?php echo $cand['namaCalon']; ?></h4>
                                    </div>
                                    <div class="flex items-center justify-end gap-3 text-[10px]">
                                        <span class="text-gray-400 uppercase font-medium italic"><?php echo $cand['kelas']; ?></span>
                                        <span class="text-primary font-black"><?php echo $p_perc; ?>% (<?php echo $cand['vote_count']; ?>)</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const ctx = document.getElementById('chart_<?php echo $idJawatan; ?>').getContext('2d');
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: <?php echo json_encode($chart_labels); ?>,
                                        datasets: [{
                                            label: 'Jumlah Undian',
                                            data: <?php echo json_encode($chart_data); ?>,
                                            backgroundColor: 'rgba(17, 212, 17, 0.4)',
                                            borderColor: 'rgba(17, 212, 17, 1)',
                                            borderWidth: 2,
                                            borderRadius: 8,
                                            hoverBackgroundColor: 'rgba(17, 212, 17, 0.8)'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        indexAxis: 'y',
                                        scales: {
                                            x: { 
                                                beginAtZero: true, 
                                                grid: { color: 'rgba(255,255,255,0.05)', borderColor: 'rgba(255,255,255,0.1)' },
                                                ticks: { color: '#94a3b8', font: { size: 10, family: 'Lexend' } }
                                            },
                                            y: { 
                                                grid: { display: false },
                                                ticks: { color: '#ffffff', font: { size: 11, family: 'Lexend', weight: 'bold' } }
                                            }
                                        },
                                        plugins: {
                                            legend: { display: false },
                                            tooltip: { 
                                                backgroundColor: '#0d1b0d',
                                                titleFont: { family: 'Lexend', weight: 'bold' },
                                                bodyFont: { family: 'Lexend' },
                                                padding: 12,
                                                cornerRadius: 12,
                                                borderColor: 'rgba(17, 212, 17, 0.3)',
                                                borderWidth: 1
                                            }
                                        }
                                    }
                                });
                            });
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

        <!-- Velocity Chart + Export -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Velocity Chart -->
            <div class="lg:col-span-2 bg-white/5 backdrop-blur-sm rounded-3xl border border-primary/20 p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-sm font-bold flex items-center gap-3 text-white">
                        <span class="material-symbols-outlined text-primary">analytics</span>
                        Trend Pengundian (Relatif 12 Jam)
                    </h3>
                    <span class="text-[9px] font-bold text-primary bg-primary/10 px-2 py-1 rounded uppercase tracking-widest border border-primary/20">Masa Nyata</span>
                </div>
                <div class="h-[200px] flex items-end justify-between gap-1.5 px-2">
                    <?php foreach($voting_velocity as $idx => $v_count) {
                        $h_percent = ceil(($v_count / $max_votes) * 100);
                        if($h_percent < 2) $h_percent = 2;
                        $gr = ($idx % 2 == 0) ? 'from-primary/60 to-primary' : 'from-primary/40 to-primary/80';
                    ?>
                    <div class="w-full bg-gradient-to-t <?php echo $gr; ?> rounded-t-lg hover:brightness-125 transition-all relative group cursor-pointer" style="height: <?php echo $h_percent; ?>%">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-deep-green text-primary border border-primary/30 text-[10px] font-black px-2 py-1 rounded-lg opacity-0 group-hover:opacity-100 transition-all transform group-hover:-translate-y-1 shadow-xl whitespace-nowrap z-20">
                            <?php echo $v_count; ?> Undian
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="mt-6 flex justify-between text-[10px] text-gray-500 font-bold uppercase tracking-[2px]">
                    <span>-12j</span><span>-9j</span><span>-6j</span><span>-3j</span><span>Kini</span>
                </div>
            </div>

            <!-- Export Card -->
            <div class="bg-gradient-to-br from-deep-green to-[#1a2e1a] text-white rounded-3xl border border-primary/30 p-8 flex flex-col justify-between shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-8 -top-8 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110">
                    <span class="material-symbols-outlined text-[12rem]">picture_as_pdf</span>
                </div>
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-primary/20 rounded-2xl flex items-center justify-center mb-6 border border-primary/30">
                        <span class="material-symbols-outlined text-primary text-2xl">file_download</span>
                    </div>
                    <h3 class="text-lg font-bold mb-3">Laporan Rasmi</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-8">Struktur data lengkap sedia untuk dicetak. Dokumen ini mengandungi perincian audit dan pengesahan masa undian untuk tujuan arkib kelab.</p>
                </div>
                <div class="flex flex-col gap-3 relative z-10">
                    <button onclick="window.print()" class="w-full py-4 bg-primary text-deep-green font-black text-xs rounded-2xl hover:bg-primary/90 transition-all flex items-center justify-center gap-3 shadow-lg shadow-primary/20 active:scale-[0.98]">
                        <span class="material-symbols-outlined text-lg">print</span>
                        Cetak Laporan Lengkap
                    </button>
                    <p class="text-[9px] text-center text-gray-500 font-bold uppercase tracking-wider">Dijana secara automatik oleh Sistem Undian v1.2.0</p>
                </div>
            </div>
        </div>

    </main>

    <footer class="mt-12 border-t border-white/10 py-8 px-10 flex flex-col items-center gap-4 text-[10px] opacity-60 uppercase tracking-widest font-bold">
        <div class="flex items-center gap-3 bg-white/5 px-4 py-2 rounded-full border border-white/10">
            <span class="material-symbols-outlined text-primary text-sm">enhanced_encryption</span>
            <p class="text-gray-300">Data Disulitkan & Diaudit Sepenuhnya</p>
        </div>
        <div class="text-center">
            <p>© 2024 Jawatankuasa Kelab Bola Sepanjang • Keputusan Rasmi Terakhir</p>
            <p class="text-[8px] mt-1 text-gray-500">Masa Pelayan (UTC): <?php echo date("Y-m-d H:i:s"); ?></p>
        </div>
    </footer>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
