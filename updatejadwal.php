<?php
session_start();

$NIP = $_POST['nip'];
include 'koneksi.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $IDMataPelajaran = $_POST['IDMataPelajaran'];
    $IDRuangan = $_POST['IDRuangan'];
    $IDHari = $_POST['IDHari'];
    $Waktu = $_POST['Waktu'];

    // Prepare the update query
    $query = "
        UPDATE jadwal 
        SET 
            IDRuangan = '$IDRuangan', 
            IDHari = '$IDHari', 
            Waktu = '$Waktu'
        WHERE IDMatapelajaran = '$IDMataPelajaran'
    ";

    // Execute the query
    if (mysqli_query($koneksi, $query)) {
        // Redirect back to jadwalguru.php after the update
        echo "<script> alert('Update jadwal berhasil.');</script>";
        echo "<script> window.location = 'jadwalguru.php?NIP=" . urlencode($NIP) . "';</script>";
    } else {
        // If the update failed, display an error message
        echo "<script> alert('Terjadi kesalahan saat mengupdate jadwal.');</script>";
        echo "<script> window.location = 'jadwalguru.php?NIP=" . urlencode($NIP) . "';</script>";
    }
}

mysqli_close($koneksi);
?>
