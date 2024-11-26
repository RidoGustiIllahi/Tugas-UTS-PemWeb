<?php
session_start();

$NIP = $_GET['NIP'];

// Pastikan pengguna telah login
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    echo "<script> alert('Anda perlu login terlebih dahulu!');</script>";
    echo "<script> window.location = 'logout.php';</script>"; 
    exit();
}

// Pastikan pengguna memiliki peran 'guru'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    echo "Anda tidak memiliki akses ke halaman ini.";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

// Pastikan pengguna hanya dapat mengakses profilnya sendiri
if (!isset($_GET['NIP']) || $_GET['NIP'] !== $_SESSION['NIP']) {
    echo "<script> alert('Anda hanya bisa mengakses profil Anda sendiri!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

// Ambil daftar kelas yang diampu oleh guru
$classQuery = "
SELECT mp.IDMataPelajaran, mp.Nama 
FROM matapelajaran mp 
WHERE mp.NIP = '$NIP'
ORDER BY mp.Nama
";
$classResult = mysqli_query($koneksi, $classQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
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

    <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg max-w-3xl">
        <h2 class="text-2xl font-semibold text-center text-blue-600 mb-6">Presensi Kelas</h2>

        <form method="POST" action="tambahkehadiranguru.php">
            <div class="mb-4">
                <label for="kelas" class="text-gray-600 mb-2">Pilih Kelas:</label>
                <select name="kelas" id="kelas" class="border-gray-300 rounded-md shadow-sm px-4 py-2 w-full" required>
                    <option value="">-Pilih Kelas-</option>
                    <?php while ($classRow = mysqli_fetch_assoc($classResult)) { ?>
                        <option value="<?php echo htmlspecialchars($classRow['IDMataPelajaran']); ?>">
                            <?php echo htmlspecialchars($classRow['Nama']); ?>
                        </option>
                    <?php } ?>
                </select>
                <label for="nis" class="text-gray-600 mt-4">Masukkan NIS Siswa:</label>
                <input type="text" name="nis" id="nis" class="border-gray-300 rounded-md shadow-sm px-4 py-2 w-full mt-2" placeholder="NIS siswa" required />
                <input type="hidden" name="NIP" value="<?php echo $NIP; ?>">
            </div>

            <div class="overflow-x-auto">
                <table class="table-auto w-full border border-gray-200 rounded-lg max-w-2xl mx-auto">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-2 py-2 border-b w-1/4">ID Kelas</th>
                            <th class="px-2 py-2 border-b w-1/3">Nama Kelas</th>
                            <th class="px-2 py-2 border-b w-1/4">Tanggal</th>
                            <th class="px-2 py-2 border-b w-1/4">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-2 py-2">
                                <input type="text" name="IDKelas" id="IDKelas" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="NamaKelas" id="NamaKelas" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="Tanggal" value="<?php echo date('Y-m-d'); ?>" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="Waktu" id="Waktu" readonly class="w-full px-3 py-2 border-gray-300 rounded-md">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">Simpan</button>
                <a href="jadwalguru.php?NIP=<?php echo urlencode($NIP); ?>" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded">Batal</a>
            </div>
        </form>
    </div>

    <script>
        // Update IDKelas and NamaKelas based on selected class
        document.getElementById('kelas').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('IDKelas').value = selectedOption.value;
            document.getElementById('NamaKelas').value = selectedOption.text;
        });

        // Function to update Waktu with the current time
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('Waktu').value = `${hours}:${minutes}:${seconds}`;
        }

        // Update time when page loads
        updateTime();
    </script>
</body>
</html>
