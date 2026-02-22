<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html class="light" lang="ms">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Log Keluar - Kelab Bola Sepak</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: { extend: { colors: { "primary": "#11d411", "background-light": "#f6f8f6", "background-dark": "#102210", "deep-green": "#0d1b0d" }, fontFamily: { "display": ["Lexend", "sans-serif"] } } }
    }
</script>
<style>
    body { font-family: 'Lexend', sans-serif; }
    .stadium-overlay {
        background: linear-gradient(rgba(16, 34, 16, 0.85), rgba(16, 34, 16, 0.95)), url('https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=2070&auto=format&fit=crop');
        background-size: cover; background-position: center;
    }
</style>
<meta http-equiv="refresh" content="3;url=login.php"/>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col">
<div class="relative flex min-h-screen w-full flex-col stadium-overlay items-center justify-center p-4">
    <div class="w-full max-w-[420px] bg-white rounded-xl shadow-2xl overflow-hidden border border-white/10 p-10 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-green-500 text-5xl">check_circle</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Log Keluar Berjaya!</h2>
        <p class="text-gray-500 text-sm mb-2">Anda telah berjaya log keluar dari sistem.</p>
        <p class="text-gray-400 text-xs mb-8">Anda akan diarahkan ke halaman log masuk dalam beberapa saat...</p>
        <a href="login.php" class="w-full inline-block bg-primary hover:bg-primary/90 text-white font-bold py-3 px-6 rounded-lg transition-all">
            Log Masuk Semula
        </a>
    </div>
</div>
</body>
</html>
