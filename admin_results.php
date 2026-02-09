<?php
include("auth_session.php");
require('db_config.php');

// Simple Admin Check
$admins = ['D6290', 'admin']; 
if (!in_array($_SESSION['idPengguna'], $admins)) {
     header("Location: dashboard.php");
     exit();
}

// Get filter parameter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query based on filter
$filter_condition = "";
$page_title = "Semua Keputusan";

if ($filter == 'pengerusi') {
    $filter_condition = "WHERE j.idJawatan = 'J1'";
    $page_title = "Keputusan Pengerusi";
} elseif ($filter == 'bendahari') {
    $filter_condition = "WHERE j.idJawatan = 'J3'";
    $page_title = "Keputusan Bendahari";
} elseif ($filter == 'setiausaha') {
    $filter_condition = "WHERE j.idJawatan = 'J2'";
    $page_title = "Keputusan Setiausaha";
}

// Query to get vote counts
$query = "
    SELECT 
        j.namaJawatan,
        c.idCalon,
        c.namaCalon,
        c.kelas,
        c.gambar,
        COUNT(u.idUndian) as jumlah_undi
    FROM Jawatan_1 j
    CROSS JOIN Calon_1 c
    LEFT JOIN Undian_1 u ON u.idJawatan = j.idJawatan AND u.idCalon = c.idCalon
    $filter_condition
    GROUP BY j.idJawatan, j.namaJawatan, c.idCalon, c.namaCalon, c.kelas, c.gambar
    ORDER BY j.idJawatan, jumlah_undi DESC, c.namaCalon
";

$result = mysqli_query($conn, $query);

// Group results by position
$results_by_position = [];
while ($row = mysqli_fetch_assoc($result)) {
    $position = $row['namaJawatan'];
    if (!isset($results_by_position[$position])) {
        $results_by_position[$position] = [];
    }
    $results_by_position[$position][] = $row;
}
?>
<!DOCTYPE html>
<html class="light" lang="ms">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Keputusan Undian - Panel Admin</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: { "primary": "#11d411", "background-light": "#f6f8f6", "background-dark": "#102210" },
                fontFamily: { "display": ["Lexend", "sans-serif"] },
            },
        },
    }
</script>
<style>
    body { font-family: 'Lexend', sans-serif; }
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .print-area { box-shadow: none !important; border: none !important; }
    }
</style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen p-8">

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 no-print">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl text-primary">poll</span>
            <?php echo $page_title; ?>
        </h1>
        <div class="flex gap-2">
            <a href="admin.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                <span class="material-symbols-outlined">admin_panel_settings</span> Panel Admin
            </a>
            <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                <span class="material-symbols-outlined">dashboard</span> Papan Utama
            </a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                <span class="material-symbols-outlined">logout</span> Log Keluar
            </a>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 no-print">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Tapis Mengikut Jawatan:</h2>
        <div class="flex flex-wrap gap-3">
            <a href="admin_results.php?filter=all" class="<?php echo $filter == 'all' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?> font-bold py-2 px-6 rounded-lg transition-all">
                Semua Jawatan
            </a>
            <a href="admin_results.php?filter=pengerusi" class="<?php echo $filter == 'pengerusi' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?> font-bold py-2 px-6 rounded-lg transition-all">
                Pengerusi
            </a>
            <a href="admin_results.php?filter=setiausaha" class="<?php echo $filter == 'setiausaha' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?> font-bold py-2 px-6 rounded-lg transition-all">
                Setiausaha
            </a>
            <a href="admin_results.php?filter=bendahari" class="<?php echo $filter == 'bendahari' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'; ?> font-bold py-2 px-6 rounded-lg transition-all">
                Bendahari
            </a>
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg flex items-center gap-2 ml-auto transition-all">
                <span class="material-symbols-outlined">print</span>
                Cetak
            </button>
        </div>
    </div>

    <!-- Results Display -->
    <div class="print-area space-y-6">
        <?php if (empty($results_by_position)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inbox</span>
                <p class="text-gray-500 font-medium">Tiada keputusan dijumpai.</p>
            </div>
        <?php else: ?>
            <?php foreach ($results_by_position as $position => $candidates): ?>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-primary px-6 py-4">
                        <h3 class="text-xl font-bold text-white"><?php echo $position; ?></h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kedudukan</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Calon</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kelas</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jumlah Undi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <?php 
                                $rank = 1;
                                foreach ($candidates as $candidate): 
                                    $rank_class = '';
                                    if ($rank == 1) $rank_class = 'bg-yellow-50 dark:bg-yellow-900/20';
                                    elseif ($rank == 2) $rank_class = 'bg-gray-50 dark:bg-gray-700/20';
                                    elseif ($rank == 3) $rank_class = 'bg-orange-50 dark:bg-orange-900/20';
                                ?>
                                <tr class="<?php echo $rank_class; ?> hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center w-8 h-8 rounded-full <?php echo $rank == 1 ? 'bg-yellow-400 text-white' : ($rank == 2 ? 'bg-gray-400 text-white' : ($rank == 3 ? 'bg-orange-400 text-white' : 'bg-gray-200 text-gray-700')); ?> font-bold text-sm">
                                            <?php echo $rank; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                <?php if($candidate['gambar']): ?>
                                                <img class="w-full h-full rounded-full object-cover" src="<?php echo $candidate['gambar']; ?>" alt="<?php echo $candidate['namaCalon']; ?>" />
                                                <?php else: ?>
                                                <div class="w-full h-full rounded-full bg-gray-200 flex items-center justify-center text-xs">N/A</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-gray-900 dark:text-white font-bold"><?php echo $candidate['namaCalon']; ?></p>
                                                <p class="text-gray-500 dark:text-gray-400 text-xs"><?php echo $candidate['idCalon']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <?php echo $candidate['kelas']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-2xl font-bold text-primary"><?php echo $candidate['jumlah_undi']; ?></span>
                                        <span class="text-xs text-gray-500 ml-1">undi</span>
                                    </td>
                                </tr>
                                <?php 
                                $rank++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Print Footer -->
    <div class="hidden print:block mt-8 text-center text-sm text-gray-600">
        <p>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Sistem Undian Jawatankuasa Kelab Bola Sepak</p>
    </div>
</div>

</body>
</html>
