<?php
session_start();

// Ensure the user is logged in as a teacher
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    echo "<script> alert('Anda perlu login terlebih dahulu!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

// Ensure the user has access as a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    echo "<script> alert('Anda tidak memiliki akses ke halaman ini.');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

// Check if NIP is set in the URL and matches the session NIP
if (!isset($_GET['NIP']) || $_GET['NIP'] !== $_SESSION['NIP']) {
    echo "<script> alert('Anda hanya bisa mengakses jadwal Anda sendiri!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

$NIP = $_GET['NIP']; // Get NIP from URL
include 'koneksi.php';

// Get the schedule data for the selected subject
if (isset($_POST['IDMataPelajaran'])) {
    $IDMataPelajaran = $_POST['IDMataPelajaran'];

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
            g.NIP = '$NIP' AND m.IDMataPelajaran = '$IDMataPelajaran'
    ";
    $result = mysqli_query($koneksi, $query);
    $jadwal = mysqli_fetch_assoc($result);
}

// Fetch available rooms and days for selection
$queryRuangan = "SELECT IDRuangan, Nama FROM ruangan";
$resultRuangan = mysqli_query($koneksi, $queryRuangan);

$queryHari = "SELECT IDHari, Hari FROM hari";
$resultHari = mysqli_query($koneksi, $queryHari);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Jadwal Mengajar</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">


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

<div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg max-w-md">
    <h1 class="text-3xl font-semibold text-center text-blue-600 mb-6">Ubah Jadwal Mengajar</h1>

    <form method="POST" action="updatejadwal.php">
        <!-- Display ID Mata Pelajaran (Read-only) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">ID Kelas</label>
            <input type="text" name="IDMataPelajaran" value="<?php echo isset($jadwal['IDMataPelajaran']) ? htmlspecialchars($jadwal['IDMataPelajaran']) : ''; ?>" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
        </div>

        <!-- Display Nama Kelas (Read-only) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Kelas</label>
            <input type="text" value="<?php echo isset($jadwal['NamaKelas']) ? htmlspecialchars($jadwal['NamaKelas']) : ''; ?>" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
        </div>

        <!-- Display Nama Guru (Read-only) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Guru</label>
            <input type="hidden" name="nip" value="<?php echo htmlspecialchars($NIP); ?>" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
            <input type="text" value="<?php echo isset($jadwal['NamaGuru']) ? htmlspecialchars($jadwal['NamaGuru']) : ''; ?>" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
        </div>

        <!-- Select Ruangan -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Ruangan</label>
            <select name="IDRuangan" class="w-full px-3 py-2 border-gray-300 rounded-md">
                <?php
                while ($rowRuangan = mysqli_fetch_assoc($resultRuangan)) {
                    // Concatenate the room number (IDRuangan) and the room name (Nama)
                    $ruanganText = htmlspecialchars($rowRuangan['IDRuangan']) . " - " . htmlspecialchars($rowRuangan['Nama']);
                    $selected = isset($jadwal['NomorRuangan']) && $rowRuangan['IDRuangan'] == $jadwal['NomorRuangan'] ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($rowRuangan['IDRuangan']) . "' $selected>" . $ruanganText . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Select Hari -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Hari</label>
            <select name="IDHari" class="w-full px-3 py-2 border-gray-300 rounded-md">
                <?php
                while ($rowHari = mysqli_fetch_assoc($resultHari)) {
                    $selected = isset($jadwal['IDHari']) && $rowHari['IDHari'] == $jadwal['IDHari'] ? "selected" : "";
                    echo "<option value='".htmlspecialchars($rowHari['IDHari'])."' $selected>".htmlspecialchars($rowHari['Hari'])."</option>";
                }
                ?>
            </select>
        </div>

        <!-- Input Waktu -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Waktu</label>
            <input type="time" name="Waktu" value="<?php echo isset($jadwal['Waktu']) ? htmlspecialchars($jadwal['Waktu']) : ''; ?>" class="w-full px-3 py-2 border-gray-300 rounded-md">
        </div>

        <div class="flex justify-center mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>

<?php
mysqli_close($koneksi);
?>