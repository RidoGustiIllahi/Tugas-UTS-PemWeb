<?php
include('koneksi.php');

$NIP = $_POST['nip'];
$username = $_POST['username'];
$passwordbaru = $_POST['passwordbaru'];
$nama = $_POST['nama'];  
$tanggallahir = $_POST['tanggallahir'];
$alamat = $_POST['alamat'];
$email = $_POST['email'];

// Query untuk mendapatkan data guru berdasarkan NIP
$queryGuru = "SELECT Foto, Nama, TanggalLahir, Alamat, Email, username FROM guru WHERE NIP = '$NIP'";
$resultGuru = mysqli_query($koneksi, $queryGuru);

// Ambil data guru
$guru = mysqli_fetch_assoc($resultGuru);

// Query untuk mendapatkan data akun berdasarkan username
$dataAkun = "SELECT username, password FROM akunguru WHERE username = '".$guru['username']."'";
$resultAkun = mysqli_query($koneksi, $dataAkun);

// Check if the account exists
if (mysqli_num_rows($resultAkun) > 0) {
    $akun = mysqli_fetch_assoc($resultAkun);
} else {
    // Handle the error, e.g., username not found
    echo "Username not found!";
    exit;
}

// Initialize query for updating akunguru table
$queryAkun = "";

// Determine which fields to update based on input
if ($username !== $akun['username'] && $passwordbaru !== '') {
    // Both username and password are changing
    $queryAkun = "UPDATE akunguru SET username = '$username', password = '$passwordbaru' WHERE username = '".$akun['username']."'";
} elseif ($username !== $akun['username']) {
    // Only the username is changing
    $queryAkun = "UPDATE akunguru SET username = '$username' WHERE username = '".$akun['username']."'";
} elseif ($passwordbaru !== '') {
    // Only the password is changing
    $queryAkun = "UPDATE akunguru SET password = '$passwordbaru' WHERE username = '".$akun['username']."'";
}

// Execute the akunguru update if there's a query to run
if ($queryAkun && $koneksi->query($queryAkun)) {
    // After updating the akunguru table, update the guru table
    $queryGuruUpdate = "UPDATE guru SET username = '$username', Nama = '$nama', TanggalLahir = '$tanggallahir', Alamat = '$alamat', Email = '$email' WHERE NIP = '$NIP'";
    if ($koneksi->query($queryGuruUpdate)) {
        // Redirect to the profile page
        header("location: profileguru.php?NIP=$NIP");
    } else {
        // Show error message for guru table update failure
        echo "Data Gagal Diupdate!";
    }
} elseif ($queryAkun == "") {
    // Only guru details are being updated
    $queryGuruUpdate = "UPDATE guru SET Nama = '$nama', TanggalLahir = '$tanggallahir', Alamat = '$alamat', Email = '$email' WHERE NIP = '$NIP'";
    if ($koneksi->query($queryGuruUpdate)) {
        // Redirect to the profile page
        echo "<script> alert ('Data Berhasil di Update');</script>";
        echo "<script> window.location ='profileguru.php?NIP=$NIP';</script>";
    } else {
        // Show error message for guru table update failure
        echo "Data Gagal Diupdate!";
    }
} else {
    // Show error message for akunguru table update failure
    echo "Data Gagal Diupdate!";
}
?>
