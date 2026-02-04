<?php
include("auth_session.php");
require('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idPengguna = $_SESSION['idPengguna'];
    $idJawatan = mysqli_real_escape_string($conn, $_POST['idJawatan']);
    $idCalon = mysqli_real_escape_string($conn, $_POST['idCalon']);
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Double check if already voted
    $check_query = "SELECT * FROM Undian_1 WHERE idPengguna='$idPengguna' AND idJawatan='$idJawatan'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Already voted
        header("Location: dashboard.php?pos=$idJawatan&error=You already voted for this position.");
    } else {
        // Insert vote
        $insert_query = "INSERT INTO Undian_1 (idPengguna, idJawatan, idCalon, ip_address) VALUES ('$idPengguna', '$idJawatan', '$idCalon', '$ip_address')";
        if (mysqli_query($conn, $insert_query)) {
            header("Location: dashboard.php?pos=$idJawatan&success=1");
        } else {
            header("Location: dashboard.php?pos=$idJawatan&error=Database error.");
        }
    }
} else {
    header("Location: dashboard.php");
}
?>
