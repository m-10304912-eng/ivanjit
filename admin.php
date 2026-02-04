<?php
include("auth_session.php");
require('db_config.php');

// Simple Admin Check
$admins = ['D6290', 'admin']; 
if (!in_array($_SESSION['idPengguna'], $admins)) {
     header("Location: dashboard.php");
     exit();
}

$msg = "";
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
    } else {
        $msg = "Ralat kemaskini: " . mysqli_error($conn);
    }
}

// Handle Deletion
if (isset($_GET['delete'])) {
    $idCalon = mysqli_real_escape_string($conn, $_GET['delete']);
    $sql_del = "DELETE FROM Calon_1 WHERE idCalon='$idCalon'";
    if(mysqli_query($conn, $sql_del)) {
        $msg = "Calon berjaya dipadam.";
    } else {
        $msg = "Ralat padam: " . mysqli_error($conn);
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
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Panel Admin - Undian FC</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
<style> body { font-family: 'Lexend', sans-serif; } </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen p-8">

<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl text-primary">admin_panel_settings</span>
            Panel Admin
        </h1>
        <div class="flex gap-2">
            <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                <span class="material-symbols-outlined">dashboard</span> Papan Utama
            </a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded flex items-center gap-2">
                <span class="material-symbols-outlined">logout</span> Log Keluar
            </a>
        </div>
    </div>

    <?php if($msg) { ?>
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded shadow" role="alert">
            <p class="font-bold">Mesej Sistem:</p>
            <p><?php echo $msg; ?></p>
        </div>
    <?php } ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Edit Form Section -->
        <?php if($edit_mode) { ?>
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-primary/20">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined">edit</span> Kemas Kini Calon
                </h2>
                <form method="POST" action="admin.php">
                    <input type="hidden" name="idCalon" value="<?php echo $edit_data['idCalon']; ?>">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">ID (Baca Sahaja)</label>
                        <input type="text" value="<?php echo $edit_data['idCalon']; ?>" class="w-full p-2 border rounded bg-gray-100" readonly>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Penuh</label>
                        <input type="text" name="namaCalon" value="<?php echo $edit_data['namaCalon']; ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-primary outline-none" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Kelas</label>
                        <input type="text" name="kelas" value="<?php echo $edit_data['kelas']; ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-primary outline-none" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Laluan Imej/URL</label>
                        <input type="text" name="gambar" value="<?php echo $edit_data['gambar']; ?>" class="w-full p-2 border rounded focus:ring-2 focus:ring-primary outline-none">
                        <p class="text-xs text-gray-500 mt-1">Laluan relatif (cth. Sivarama.png) atau URL.</p>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="update_candidate" class="w-full bg-primary hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Kemas Kini</button>
                        <a href="admin.php" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <?php } else { ?>
        <div class="lg:col-span-1">
            <div class="bg-blue-50 dark:bg-gray-800 rounded-xl p-6 border border-blue-100 text-center">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">touch_app</span>
                <p class="text-gray-500 font-medium">Pilih calon dari senarai untuk mengedit butiran.</p>
            </div>
        </div>
        <?php } ?>

        <!-- List Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-600 uppercase tracking-wider text-sm">Semua Calon</h3>
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
                            while($row = mysqli_fetch_assoc($result)) {
                                $active_row = ($edit_mode && $edit_data['idCalon'] == $row['idCalon']) ? 'bg-blue-50' : 'bg-white';
                            ?>
                            <tr class="<?php echo $active_row; ?> hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <?php if($row['gambar']) { ?>
                                            <img class="w-full h-full rounded-full object-cover" src="<?php echo $row['gambar']; ?>" />
                                            <?php } else { ?>
                                            <div class="w-full h-full rounded-full bg-gray-200 flex items-center justify-center text-xs">N/A</div>
                                            <?php } ?>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-gray-900 whitespace-no-wrap font-bold"><?php echo $row['namaCalon']; ?></p>
                                            <p class="text-gray-500 whitespace-no-wrap text-xs"><?php echo $row['idCalon']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                        <span aria-hidden="true" class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                        <span class="relative"><?php echo $row['kelas']; ?></span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-200 text-sm text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="admin.php?edit=<?php echo $row['idCalon']; ?>" class="text-blue-600 hover:text-blue-900 border border-blue-200 rounded px-3 py-1 text-xs font-bold hover:bg-blue-50">Edit</a>
                                        <a href="admin.php?delete=<?php echo $row['idCalon']; ?>" onclick="return confirm('Padam calon ini?');" class="text-red-600 hover:text-red-900 border border-red-200 rounded px-3 py-1 text-xs font-bold hover:bg-red-50">Padam</a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
