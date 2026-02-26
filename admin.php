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
    
    // Handle Image Upload
    if(isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION);
        $new_filename = $idCalon . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
            $gambar = $target_file;
        } else {
            $msg = "Ralat memuat naik gambar.";
        }
    }

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

// Handle CSV Import
if (isset($_POST['import_csv'])) {
    if (is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $csv_file = fopen($_FILES['csv_file']['tmp_name'], 'r');
        // Skip header row if exists
        fgetcsv($csv_file); 
        while (($line = fgetcsv($csv_file)) !== FALSE) {
            // Check if column count matches (id, name, class, image)
            if (count($line) >= 3) {
                $id = mysqli_real_escape_string($conn, $line[0]);
                $nama = mysqli_real_escape_string($conn, $line[1]);
                $kelas = mysqli_real_escape_string($conn, $line[2]);
                $img = isset($line[3]) ? mysqli_real_escape_string($conn, $line[3]) : '';
                
                // Insert or Update
                $sql_import = "INSERT INTO Calon_1 (idCalon, namaCalon, kelas, gambar) VALUES ('$id', '$nama', '$kelas', '$img') 
                               ON DUPLICATE KEY UPDATE namaCalon='$nama', kelas='$kelas', gambar='$img'";
                mysqli_query($conn, $sql_import);
            }
        }
        fclose($csv_file);
        $msg = "Import CSV berjaya diproses.";
    } else {
        $msg = "Sila pilih fail CSV.";
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Panel Admin - Sistem Undian FC</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen font-display text-gray-900 dark:text-white">

<div class="flex h-screen overflow-hidden flex-row-reverse">
    <!-- Sidebar Navigation -->
    <aside class="w-72 bg-sidebar-dark text-slate-300 flex flex-col justify-between py-6 px-4 shadow-xl shrink-0 hidden md:flex border-l border-white/5 order-2">
        <div class="flex flex-col gap-8">
            <!-- Brand/Logo -->
            <div class="flex items-center gap-3 px-4">
                <div class="bg-primary p-2 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(128,242,13,0.3)]">
                    <span class="material-symbols-outlined text-black font-extrabold">&#xe8e1;</span>
                </div>
                <div class="flex flex-col text-right">
                    <h1 class="text-sm font-extrabold leading-tight uppercase tracking-tight text-white">Portal Pentadbir</h1>
                    <p class="text-primary text-[10px] font-black uppercase tracking-widest">Sesi 2024/2025</p>
                </div>
            </div>
            <!-- Nav Categories -->
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all text-slate-400 hover:text-white flex-row-reverse group" href="dashboard.php">
                    <span class="material-symbols-outlined group-hover:text-primary">&#xe871;</span>
                    <span class="text-sm font-black uppercase">Papan Utama</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/5 transition-all text-slate-400 hover:text-white flex-row-reverse group" href="results.php">
                    <span class="material-symbols-outlined group-hover:text-primary">&#xe24b;</span>
                    <span class="text-sm font-black uppercase">Keputusan</span>
                </a>
                <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary/10 text-white border-r-4 border-primary transition-all flex-row-reverse">
                    <span class="material-symbols-outlined text-primary font-black">&#xe3c9;</span>
                    <span class="text-sm font-black uppercase">Urus Calon</span>
                </div>
            </nav>
        </div>
        
        <!-- Bottom Nav -->
        <div class="flex flex-col gap-2 pt-6 border-t border-white/5">
            <div class="flex items-center gap-3 px-4 py-3 bg-white/5 rounded-2xl mx-2 flex-row-reverse">
                <div class="size-10 rounded-full bg-primary flex items-center justify-center text-black font-black text-lg">
                    A
                </div>
                <div class="flex flex-col overflow-hidden text-right">
                    <p class="text-xs font-black text-white truncate">Pentadbir</p>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest tracking-tighter">Akses Penuh</p>
                </div>
            </div>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-red-400 transition-colors text-slate-500 flex-row-reverse group" href="logout.php">
                <span class="material-symbols-outlined group-hover:animate-pulse font-black">&#xe9ba;</span>
                <span class="text-xs font-black uppercase tracking-widest">Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto bg-background-light dark:bg-background-dark order-1">
        <!-- Mobile Header -->
        <header class="md:hidden flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-8 py-4 bg-white dark:bg-card-dark">
             <h1 class="text-lg font-extrabold text-right w-full dark:text-white">Panel Pentadbir</h1>
             <a href="logout.php" class="text-red-500 font-bold text-sm ml-4">Keluar</a>
        </header>

        <div class="p-8 max-w-7xl mx-auto w-full">
            <div class="flex justify-between items-center mb-8">
                 <h1 class="text-3xl font-black text-gray-800 dark:text-white flex items-center gap-2">
                    Pengurusan Calon
                </h1>
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
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center gap-2 justify-end w-full text-right">
                    <span class="material-symbols-outlined">edit</span> Kemas Kini Calon
                </h2>
                <form method="POST" action="admin.php" enctype="multipart/form-data">
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
                        <label class="block text-gray-700 text-sm font-bold mb-2">Gambar Calon</label>
                        <!-- Drag and Drop Zone -->
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <span class="material-symbols-outlined text-gray-400 text-3xl mb-2">cloud_upload</span>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk muat naik</span> atau seret dan lepas</p>
                                    <p class="text-xs text-gray-500">PNG, JPG (MAKS. 2MB)</p>
                                </div>
                                <input id="dropzone-file" name="image_file" type="file" class="hidden" accept="image/*" />
                            </label>
                        </div>
                        <input type="hidden" name="gambar" value="<?php echo $edit_data['gambar']; ?>">
                        <p class="text-xs text-gray-500 mt-2">Gambar Semasa: <?php echo basename($edit_data['gambar']); ?></p>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="update_candidate" class="w-full bg-primary hover:bg-green-600 text-white font-bold py-2 px-4 rounded flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">save</span> Kemas Kini
                        </button>
                        <a href="admin.php" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">cancel</span> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php } else { ?>
        <div class="lg:col-span-1 space-y-6">
            <!-- Select Candidate Message -->
            <div class="bg-primary/5 dark:bg-card-dark rounded-3xl p-8 border border-primary/20 text-center">
                <span class="material-symbols-outlined text-6xl text-primary/40 mb-4 font-black">&#xe913;</span>
                <p class="text-slate-500 font-black uppercase tracking-widest text-xs">Pilih calon untuk mula mengedit</p>
            </div>

            <!-- Bulk Upload CSV Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-primary/20">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white flex items-center gap-2 justify-end w-full text-right">
                    <span class="material-symbols-outlined">upload_file</span> Muat Naik Pukal (CSV)
                </h2>
                <form method="POST" action="admin.php" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Fail CSV</label>
                        <input type="file" name="csv_file" accept=".csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/20 file:text-primary hover:file:bg-primary/30" required>
                        <p class="text-xs text-gray-500 mt-2">Format: ID, Nama, Kelas, URL Imej (Pilihan). <a href="#" onclick="downloadSampleCSV()" class="text-primary hover:underline">Muat turun contoh CSV</a></p>
                    </div>
<script>
function downloadSampleCSV() {
    const csvContent = "data:text/csv;charset=utf-8,ID,Nama,Kelas,Imej\nP101,Ahmad Ali,4 Amanah,uploads/sample.jpg\nS201,Siti Aminah,5 Bakti,\nB301,Tan Ah Kow,6 Cerdas,";
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "contoh_calon.csv");
    document.body.appendChild(link);
    link.click();
}
</script>
                    <button type="submit" name="import_csv" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">upload</span> Muat Naik
                    </button>
                </form>
            </div>
        </div>
        <?php } ?>

        <!-- List Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-right">
                    <h3 class="font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider text-sm">Semua Calon</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Calon</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Kelas</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php
                            $sql = "SELECT * FROM Calon_1 ORDER BY idCalon ASC";
                            $result = mysqli_query($conn, $sql);
                            while($row = mysqli_fetch_assoc($result)) {
                                $active_row = ($edit_mode && $edit_data['idCalon'] == $row['idCalon']) ? 'bg-indigo-50 dark:bg-indigo-900/20' : 'bg-white dark:bg-gray-800';
                            ?>
                            <tr class="<?php echo $active_row; ?> hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-5 py-4 text-sm">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <?php if($row['gambar'] && file_exists($row['gambar'])) { ?>
                                            <img class="w-full h-full rounded-full object-cover" src="<?php echo $row['gambar']; ?>" alt="<?php echo $row['namaCalon']; ?>" />
                                            <?php } else if ($row['gambar'] && !file_exists($row['gambar'])) { ?>
                                                 <!-- Fallback for broken paths -->
                                                 <div class="w-full h-full rounded-full bg-red-100 flex items-center justify-center text-xs text-red-500 font-bold" title="Imej Tidak Ditemui">!</div>
                                            <?php } else { ?>
                                            <div class="w-full h-full rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                                <?php echo substr($row['namaCalon'], 0, 1); ?>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-gray-900 dark:text-white whitespace-no-wrap font-bold"><?php echo $row['namaCalon']; ?></p>
                                            <p class="text-gray-500 dark:text-gray-400 whitespace-no-wrap text-xs"><?php echo $row['idCalon']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                        <span aria-hidden="true" class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                        <span class="relative"><?php echo $row['kelas']; ?></span>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="admin.php?edit=<?php echo $row['idCalon']; ?>" class="text-indigo-600 hover:text-indigo-900 border border-indigo-200 rounded px-3 py-1 text-xs font-bold hover:bg-indigo-50 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">edit</span> Edit
                                        </a>
                                        <a href="admin.php?delete=<?php echo $row['idCalon']; ?>" onclick="return confirm('Padam calon ini?');" class="text-red-600 hover:text-red-900 border border-red-200 rounded px-3 py-1 text-xs font-bold hover:bg-red-50 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm">delete</span> Padam
                                        </a>
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
</main>
</div>
<script>
    const dropzone = document.getElementById('dropzone-file').parentElement;
    const fileInput = document.getElementById('dropzone-file');

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-primary', 'bg-primary/5');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-primary', 'bg-primary/5');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary', 'bg-primary/5');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            // Trigger visual feedback
            const p = dropzone.querySelector('p.text-sm');
            p.innerHTML = `<span class="font-semibold text-primary">Fail sedia: ${e.dataTransfer.files[0].name}</span>`;
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            const p = dropzone.querySelector('p.text-sm');
            p.innerHTML = `<span class="font-semibold text-primary">Fail sedia: ${fileInput.files[0].name}</span>`;
        }
    });
</script>
</body>
</html>
