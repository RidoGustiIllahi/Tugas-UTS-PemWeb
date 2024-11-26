<?php
include 'koneksi.php';


$NIS = $_POST['nis'];
$IDKelas = $_POST['IDKelas'];
$NamaKelas = $_POST['NamaKelas'];
$Tanggal = $_POST['Tanggal'];
$Waktu = $_POST['Waktu'];
$NIP = $_POST['NIP'];

// Cek apakah siswa sudah enroll di kelas tersebut
$enrollCheckQuery = "SELECT * FROM siswamatapelajaran WHERE IDMataPelajaran = '$IDKelas' AND NIS = '$NIS'";
$enrollCheckResult = mysqli_query($koneksi, $enrollCheckQuery);

if (mysqli_num_rows($enrollCheckResult) > 0) {
    // Jika siswa sudah enroll, simpan data kehadiran
    $query = "INSERT INTO kehadiran (IDMataPelajaran, Tanggal, Waktu, NIS) VALUES ('$IDKelas', '$Tanggal', '$Waktu', '$NIS')";
    mysqli_query($koneksi, $query);

    // Redirect kembali ke halaman presensi guru
    header("Location: kehadiranguru.php?NIP=$NIP");
    exit(); // Pastikan untuk menghentikan skrip setelah redirect
} else {
    // Jika siswa tidak enroll di kelas tersebut, tampilkan pesan peringatan
    echo "<script>alert('Siswa tidak meng-enroll kelas tersebut');</script>";
    echo "<script>window.location = 'kehadiranguru.php?NIP=$NIP';</script>";
}
?>
