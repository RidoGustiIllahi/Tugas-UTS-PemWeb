<?php
session_start();

$NIP = $_GET['NIP'];

if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login'){
    echo "<script> alert('Anda perlu login terlebih dahulu!');</script>";
    echo "<script> window.location = 'logout.php';</script>"; 
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    echo "Anda tidak memiliki akses ke halaman ini.";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

if (!isset($_GET['NIP']) || $_GET['NIP'] !== $_SESSION['NIP']) {
    echo "<script> alert('Anda hanya bisa mengakses profil Anda sendiri!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

include 'koneksi.php';

$no = 1;
$query = "
    SELECT 
        m.IDMataPelajaran,
        m.Nama AS NamaKelas,
        g.Nama AS NamaGuru,
        r.IDRuangan AS NomorRuangan,
        r.Nama AS NamaRuangan,
        h.Hari AS NamaHari,
        j.Waktu
    FROM 
        matapelajaran m
    JOIN 
        guru g ON m.NIP = g.NIP
    JOIN 
        jadwal j ON m.IDMataPelajaran = j.IDMatapelajaran
    JOIN 
        hari h ON j.IDHari = h.IDHari
    JOIN 
        ruangan r ON j.IDRuangan = r.IDRuangan
    WHERE 
        g.NIP = '$NIP'
    ORDER BY 
        m.IDMataPelajaran
";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Mengajar Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <header class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Selamat Datang, <?php echo htmlspecialchars($NIP); ?></h2>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="profileguru.php?NIP=<?php echo urlencode($NIP); ?>" class="hover:text-blue-300">Profile</a></li>
                    <li><a href="jadwalguru.php?NIP=<?php echo urlencode($NIP); ?>" class="hover:text-blue-300">Jadwal Mengajar</a></li>
                    <li><a href="kehadiranguru.php?NIP=<?php echo urlencode($NIP); ?>" class="hover:text-blue-300">Rekap Kehadiran Siswa</a></li>
                </ul>
            </nav>
            <form method="POST" action="logout.php">
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Log Out</button>
            </form>
        </div>
    </header>

    <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg max-w-4xl">
        <h2 class="text-2xl font-semibold text-center text-blue-600 mb-6">Jadwal Mengajar Guru</h2>
        <div class="overflow-x-auto">
            <table class="table-auto w-full border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2 border-b">No</th>
                        <th class="px-4 py-2 border-b">ID Kelas</th>
                        <th class="px-4 py-2 border-b">Kelas</th>
                        <th class="px-4 py-2 border-b">Guru</th>
                        <th class="px-4 py-2 border-b">Nomor Ruangan</th>
                        <th class="px-4 py-2 border-b">Ruangan</th>
                        <th class="px-4 py-2 border-b">Hari</th>
                        <th class="px-4 py-2 border-b">Waktu</th>
                        <th class="px-4 py-2 border-b">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr class='border-b hover:bg-gray-50'>";
                            echo "<td class='px-4 py-2'>" . $no++ . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['IDMataPelajaran']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['NamaKelas']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['NamaGuru']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['NomorRuangan']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['NamaRuangan']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['NamaHari']) . "</td>";
                            echo "<td class='px-4 py-2'>" . htmlspecialchars($row['Waktu']) . "</td>";
                            echo "<td class='px-4 py-2'>
                                    <form method='POST' action='ubahjadwal.php?NIP=".urlencode($NIP)."'>
                                        <input type='hidden' name='IDMataPelajaran' value='" . htmlspecialchars($row['IDMataPelajaran']) . "'>
                                        <button class='bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded'>UBAH</button>
                                    </form>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='px-4 py-4 text-center text-gray-500'>Tidak ada data yang tersedia.</td></tr>";
                    }
                    mysqli_close($koneksi); 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>