<?php
include("auth_session.php");
require('db_config.php');

// Admin Check
$admins = ['D6290', 'admin']; 
if (!in_array($_SESSION['idPengguna'], $admins)) {
     header("Location: dashboard.php");
     exit();
}

$msg = "";
$msg_type = ""; // "success" or "error"
$edit_mode = false;
$edit_data = null;

// Handle Update
if (isset($_POST['update_candidate'])) {
    $idCalon = mysqli_real_escape_string($conn, $_POST['idCalon']);
    $namaCalon = mysqli_real_escape_string($conn, $_POST['namaCalon']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $gambar = mysqli_real_escape_string($conn, $_POST['gambar']);
    
    $sql_update = "UPDATE Calon_1 SET namaCalon='$namaCalon', kelas='$kelas', gambar='$gambar' WHERE idCalon='$idCalon'";
    if(mysqli_query($conn, $sql_update)) {
        $msg = "Calon berjaya dikemaskini.";
        $msg_type = "success";
    } else {
        $msg = "Ralat kemaskini: " . mysqli_error($conn);
        $msg_type = "error";
    }
}

// Handle Deletion
if (isset($_GET['delete'])) {
    $idCalon = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql_del = "DELETE FROM Calon_1 WHERE idCalon='$idCalon'";
    if(mysqli_query($conn, $sql_del)) {
        $msg = "Calon berjaya dipadam.";
        $msg_type = "success";
    } else {
        $msg = "Ralat padam: " . mysqli_error($conn);
        $msg_type = "error";
    }
}

// Handle Edit Fetch
if (isset($_GET['edit'])) {
    $idCalon = mysqli_real_escape_string($conn, $_GET['edit']);
    $sql_fetch = "SELECT * FROM Calon_1 WHERE idCalon='$idCalon'";
    $res_fetch = mysqli_query($conn, $sql_fetch);
    if(mysqli_num_rows($res_fetch) > 0) {
        $edit_data = mysqli_fetch_assoc($res_fetch);
        $edit_mode = true;
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="ms">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Panel Admin - Kelab Bola Sepak</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
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
    .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; display: flex; align-items: center; justify-content: center; }
    .modal-card { background: white; border-radius: 1rem; padding: 2rem; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen">

<?php if($msg && $msg_type == "success"): ?>
<div class="modal-backdrop" id="msgModal">
    <div class="modal-card">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-green-500 text-4xl">check_circle</span>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Berjaya!</h3>
        <p class="text-gray-600 text-sm mb-6"><?php echo $msg; ?></p>
        <button onclick="document.getElementById('msgModal').style.display='none'" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-lg">Tutup</button>
    </div>
</div>
<?php elseif($msg && $msg_type == "error"): ?>
<div class="modal-backdrop" id="msgModal">
    <div class="modal-card">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-red-500 text-4xl">error</span>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Ralat!</h3>
        <p class="text-gray-600 text-sm mb-6"><?php echo $msg; ?></p>
        <button onclick="document.getElementById('msgModal').style.display='none'" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg">Tutup</button>
    </div>
</div>
<?php endif; ?>

<!-- Top Admin Bar -->
<div class="bg-primary px-4 py-1.5 flex items-center justify-center gap-2 text-deep-green font-bold text-xs uppercase tracking-wider">
    <span class="material-symbols-outlined text-base">verified_user</span>
    <span>Sesi Admin Selamat • Tahap Akses: Tinggi</span>
</div>

<!-- Header -->
<header class="bg-deep-green text-white px-8 py-4 flex items-center justify-between shadow-xl">
    <div class="flex items-center gap-3">
        <div class="bg-primary p-2 rounded-lg">
            <span class="material-symbols-outlined text-deep-green">sports_soccer</span>
        </div>
        <div class="text-right">
            <h1 class="text-lg font-bold leading-tight">Panel Admin</h1>
            <p class="text-primary text-xs font-semibold">Kelab Bola Sepak 2024</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="admin_results.php" class="flex items-center gap-2 bg-primary/20 hover:bg-primary/30 text-primary font-bold py-2 px-4 rounded-lg text-sm transition-all">
            <span class="material-symbols-outlined text-sm">analytics</span> Keputusan
        </a>
        <a href="dashboard.php" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-bold py-2 px-4 rounded-lg text-sm transition-all">
            <span class="material-symbols-outlined text-sm">dashboard</span> Papan Utama
        </a>
        <a href="logout.php" class="flex items-center gap-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 font-bold py-2 px-4 rounded-lg text-sm transition-all">
            <span class="material-symbols-outlined text-sm">logout</span> Log Keluar
        </a>
    </div>
</header>

<div class="max-w-6xl mx-auto p-8">
    <!-- Page Title -->
    <div class="text-right mb-8">
        <h2 class="text-2xl font-black text-gray-800 dark:text-white">Urus Calon</h2>
        <p class="text-gray-500 text-sm mt-1">Tambah, edit atau padam maklumat calon pilihan raya</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- List Section -->
        <?php if($edit_mode) { ?>
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-deep-green rounded-xl shadow-lg p-6 border-l-4 border-primary">
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center justify-end gap-2 text-right">
                    Kemas Kini Calon <span class="material-symbols-outlined text-primary">edit</span>
                </h3>
                <form method="POST" action="admin.php">
                    <input type="hidden" name="idCalon" value="<?php echo $edit_data['idCalon']; ?>">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2 text-right">ID Calon (Baca Sahaja)</label>
                        <input type="text" value="<?php echo $edit_data['idCalon']; ?>" class="w-full p-2 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 text-right outline-none" readonly>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2 text-right">Nama Penuh Calon</label>
                        <input type="text" name="namaCalon" value="<?php echo $edit_data['namaCalon']; ?>" class="w-full p-2 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 focus:ring-2 focus:ring-primary outline-none transition-all" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2 text-right">Kelas / Tingkatan</label>
                        <input type="text" name="kelas" value="<?php echo $edit_data['kelas']; ?>" class="w-full p-2 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 focus:ring-2 focus:ring-primary outline-none transition-all" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2 text-right">Laluan Fail / URL Gambar</label>
                        <input type="text" name="gambar" value="<?php echo $edit_data['gambar']; ?>" class="w-full p-2 border border-gray-200 dark:border-white/10 rounded-lg bg-gray-50 dark:bg-white/5 focus:ring-2 focus:ring-primary outline-none transition-all">
                        <p class="text-[10px] text-gray-500 mt-1 text-right italic">Gunakan nama fail (cth: ali.png) atau pautan imej penuh.</p>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="update_candidate" class="w-full bg-primary hover:bg-primary/90 text-deep-green font-bold py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                            <span class="material-symbols-outlined text-sm">save</span> Simpan Perubahan
                        </button>
                        <a href="admin.php" class="w-full bg-gray-200 dark:bg-white/10 hover:bg-gray-300 dark:hover:bg-white/20 text-gray-800 dark:text-white font-bold py-3 px-4 rounded-lg text-center flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-sm">close</span> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php } else { ?>
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-deep-green rounded-xl p-8 border border-primary/20 text-right shadow-sm flex flex-col items-end">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-4xl text-primary">ads_click</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Pilih Calon</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Sila pilih mana-mana calon daripada senarai di sebelah untuk mengemas kini maklumat atau memadam rekod calon tersebut.</p>
            </div>
        </div>
        <?php } ?>

        <!-- Candidates List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-deep-green shadow-lg rounded-xl overflow-hidden">
                <div class="bg-deep-green px-6 py-4 border-b border-primary/20 text-right">
                    <h3 class="font-bold text-primary uppercase tracking-wider text-sm">Senarai Semua Calon</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Calon</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kelas</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM Calon_1 ORDER BY idCalon ASC";
                            $result = mysqli_query($conn, $sql);
                            if (!$result || mysqli_num_rows($result) == 0) {
                                echo '<tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">Tiada calon ditemui dalam pangkalan data.</td></tr>';
                            } else {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $active_row = ($edit_mode && $edit_data['idCalon'] == $row['idCalon']) ? 'bg-primary/5' : 'bg-white';
                            ?>
                            <tr class="<?php echo $active_row; ?> hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <?php if($row['gambar']) { ?>
                                            <img class="w-full h-full rounded-full object-cover" src="<?php echo $row['gambar']; ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($row['namaCalon']); ?>&background=random'" />
                                            <?php } else { ?>
                                            <div class="w-full h-full rounded-full bg-primary/20 flex items-center justify-center text-xs font-bold text-primary"><?php echo strtoupper(substr($row['namaCalon'],0,1)); ?></div>
                                            <?php } ?>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-gray-900 font-bold"><?php echo $row['namaCalon']; ?></p>
                                            <p class="text-gray-500 text-xs">ID: <?php echo $row['idCalon']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                        <?php echo $row['kelas']; ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 text-sm text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="admin.php?edit=<?php echo $row['idCalon']; ?>" class="text-blue-600 hover:text-blue-900 border border-blue-200 rounded-lg px-3 py-1 text-xs font-bold hover:bg-blue-50 flex items-center gap-1 transition-all">
                                            <span class="material-symbols-outlined text-sm">edit</span> Edit
                                        </a>
                                        <a href="admin.php?delete=<?php echo $row['idCalon']; ?>" onclick="return confirm('Padam calon ini? Tindakan ini tidak boleh dibuat asal.');" class="text-red-600 hover:text-red-900 border border-red-200 rounded-lg px-3 py-1 text-xs font-bold hover:bg-red-50 flex items-center gap-1 transition-all">
                                            <span class="material-symbols-outlined text-sm">delete</span> Padam
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php }} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
