<?php
session_start();
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

if(!isset($_SESSION["idPengguna"])) {
    header("Location: login.php");
    exit();
}
?>
