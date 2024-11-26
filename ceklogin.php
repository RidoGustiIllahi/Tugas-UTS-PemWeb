<?php
    session_start();
    include 'koneksi.php';

    $username = $_POST['username'];
    $password = $_POST['password'];

    $querySiswa = "SELECT * FROM akunsiswa WHERE username = '$username' AND password = '$password'";
    $resultSiswa = mysqli_query($koneksi, $querySiswa);

    $queryGuru = "SELECT * FROM akunguru WHERE username = '$username' AND password = '$password'";
    $resultGuru = mysqli_query($koneksi, $queryGuru);

    if (mysqli_num_rows($resultSiswa) > 0) {

        $dataNIS = mysqli_query($koneksi, "SELECT NIS FROM siswa WHERE username = '$username'");
        $dNIS = mysqli_fetch_assoc($dataNIS);
        $nis = $dNIS['NIS'];

        $_SESSION['NIS'] = $nis;
        $_SESSION['role'] = 'siswa';
        $_SESSION['status'] = 'login';
                
        header("Location: profilesiswa.php?NIS=$nis");
    }

    elseif (mysqli_num_rows($resultGuru) > 0){
        $dataNIP = mysqli_query($koneksi, "SELECT NIP FROM guru WHERE username = '$username'");
        $dNIP = mysqli_fetch_assoc($dataNIP);
        $nip = $dNIP['NIP'];

        $_SESSION['NIP'] = $nip;
        $_SESSION['role'] = 'guru';
        $_SESSION['status'] = 'login';
        
        header("Location: profileguru.php?NIP=$nip");
    }

    else {
        echo "<script> alert('Login gagal! Username dan password tidak benar');</script>";
        echo "<script> window.location = 'index.html';</script>";
    }