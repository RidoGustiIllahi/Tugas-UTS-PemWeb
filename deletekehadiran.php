<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    echo "<script> alert('Anda perlu login terlebih dahulu!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    echo "Anda tidak memiliki akses ke halaman ini.";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

include 'koneksi.php';

// Check if IDKehadiran is set
if (isset($_POST['IDKehadiran'])) {
    $IDKehadiran = $_POST['IDKehadiran'];

    // Prepare the delete query
    $query = "DELETE FROM kehadiran WHERE IDKehadiran = '$IDKehadiran'";

    // Execute the query
    if (mysqli_query($koneksi, $query)) {
        echo "<script> alert('Data kehadiran berhasil dihapus!');</script>";
    } else {
        echo "<script> alert('Gagal menghapus data kehadiran.');</script>";
    }
} else {
    echo "<script> alert('ID Kehadiran tidak ditemukan.');</script>";
}

echo "<script> window.location = 'kehadiranguru.php?NIP=" . urlencode($_SESSION['NIP']) . "';</script>";
?>
