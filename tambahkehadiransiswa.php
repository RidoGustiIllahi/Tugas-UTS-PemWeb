<?php
    include 'koneksi.php';

    $NIS = $_POST['NIS'];
    $IDKelas = $_POST['IDKelas'];
    $NamaKelas = $_POST['NamaKelas'];
    $Tanggal = $_POST['Tanggal'];
    $Waktu = $_POST['Waktu'];

    $query = "INSERT INTO kehadiran (IDMataPelajaran, Tanggal, Waktu, NIS) VALUES ('$IDKelas', '$Tanggal', '$Waktu', '$NIS')";

    mysqli_query($koneksi, $query);

    header("location:kehadiransiswa.php?NIS=$NIS")
?>