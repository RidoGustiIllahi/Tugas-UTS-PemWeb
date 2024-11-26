<?php
session_start();

$NIS = $_GET['NIS'];

if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    echo "<script> alert('Anda perlu login terlebih dahulu!');</script>";
    echo "<script> window.location = 'logout.php';</script>"; 
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    echo "Anda tidak memiliki akses ke halaman ini.";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

if (!isset($_GET['NIS']) || $_GET['NIS'] !== $_SESSION['NIS']) {
    echo "<script> alert('Anda hanya bisa mengakses profil Anda sendiri!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

date_default_timezone_set('Asia/Jakarta');

include 'koneksi.php';

$classQuery = "
SELECT mp.IDMataPelajaran, mp.Nama 
FROM siswamatapelajaran sm 
JOIN matapelajaran mp ON sm.IDMataPelajaran = mp.IDMataPelajaran 
WHERE sm.NIS = '$NIS'
ORDER BY mp.Nama
";
$classResult = mysqli_query($koneksi, $classQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-blue-600 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Selamat Datang, <?php echo htmlspecialchars($NIS); ?></h2>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="profilesiswa.php?NIS=<?php echo urlencode($NIS); ?>" class="hover:text-blue-300">Profile</a></li>
                    <li><a href="jadwalsiswa.php?NIS=<?php echo urlencode($NIS); ?>" class="hover:text-blue-300">Jadwal Kelas</a></li>
                    <li><a href="kehadiransiswa.php?NIS=<?php echo urlencode($NIS); ?>" class="hover:text-blue-300">Kehadiran</a></li>
                </ul>
            </nav>
            <form method="POST" action="logout.php">
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Log Out</button>
            </form>
        </div>
    </header>

    <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg max-w-3xl">
        <h2 class="text-2xl font-semibold text-center text-blue-600 mb-6">Presensi Kelas</h2>

        <form method="POST" action="tambahkehadiransiswa.php">
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
                                <input type="hidden" name="NIS" value="<?php echo $NIS; ?>">
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
                <a href="jadwalsiswa.php?NIS=<?php echo urlencode($NIS); ?>" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded">Batal</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('kelas').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('IDKelas').value = selectedOption.value;
            document.getElementById('NamaKelas').value = selectedOption.text;
        });

        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('Waktu').value = `${hours}:${minutes}:${seconds}`;
        }
        updateTime();
    </script>
</body>
</html>
