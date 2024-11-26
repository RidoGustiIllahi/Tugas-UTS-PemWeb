<?php
include('koneksi.php');

$NIS = $_POST['nis'];
$username = $_POST['username'];
$passwordbaru = $_POST['passwordbaru'];
$nama = $_POST['nama'];  
$tanggallahir = $_POST['tanggallahir'];
$alamat = $_POST['alamat'];
$email = $_POST['email'];

$querySiswa = "SELECT username FROM siswa WHERE NIS = '$NIS'";
$resultSiswa = mysqli_query($koneksi, $querySiswa);

$siswa = mysqli_fetch_assoc($resultSiswa);

$dataAkun = "SELECT username, password FROM akunsiswa WHERE username = '".$siswa['username']."'";
$resultAkun = mysqli_query($koneksi, $dataAkun);

if (mysqli_num_rows($resultAkun) > 0) {
    $akun = mysqli_fetch_assoc($resultAkun);
} else {
    echo "Username not found!";
    exit;
}

$queryAkun = "";

if ($username !== $akun['username'] && $passwordbaru !== '') {
    $queryAkun = "UPDATE akunsiswa SET username = '$username', password = '$passwordbaru' WHERE username = '".$akun['username']."'";
} elseif ($username !== $akun['username']) {
    $queryAkun = "UPDATE akunsiswa SET username = '$username' WHERE username = '".$akun['username']."'";
} elseif ($passwordbaru !== '') {
    $queryAkun = "UPDATE akunsiswa SET password = '$passwordbaru' WHERE username = '".$akun['username']."'";
}

if ($queryAkun && $koneksi->query($queryAkun)) {
    $querySiswaUpdate = "UPDATE siswa SET username = '$username', Nama = '$nama', TanggalLahir = '$tanggallahir', Alamat = '$alamat', Email = '$email' WHERE NIS = '$NIS'";
    if ($koneksi->query($querySiswaUpdate)) {
        echo "<script> alert ('Data Berhasil di Update');</script>";
        echo "<script> window.location ='profilesiswa.php?NIS=$NIS';</script>";
    } else {
        echo "Data Gagal Diupdate!";
    }
} elseif ($queryAkun == "") {
    $querySiswaUpdate = "UPDATE siswa SET Nama = '$nama', TanggalLahir = '$tanggallahir', Alamat = '$alamat', Email = '$email' WHERE NIS = '$NIS'";
    if ($koneksi->query($querySiswaUpdate)) {
        echo "<script> alert ('Data Berhasil di Update');</script>";
        echo "<script> window.location ='profilesiswa.php?NIS=$NIS';</script>";
    } else {
        echo "Data Gagal Diupdate!";
    }
} else {
    echo "Data Gagal Diupdate!";
}
?>