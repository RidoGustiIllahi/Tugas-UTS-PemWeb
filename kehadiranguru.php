<?php
session_start();

$NIP = $_GET['NIP'] ?? $_SESSION['NIP']; // Use session NIP if not available in URL

// Check if user is logged in
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    echo "<script> alert('Anda perlu login terlebih dahulu!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

// Check if user is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    echo "Anda tidak memiliki akses ke halaman ini.";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

// Ensure the NIP matches the session NIP for security
if ($NIP !== $_SESSION['NIP']) {
    echo "<script> alert('Anda hanya bisa mengakses profil Anda sendiri!');</script>";
    echo "<script> window.location = 'logout.php';</script>";
    exit();
}

include 'koneksi.php';

// Get filter values from POST, or set default
$classFilter = isset($_POST['kelas']) ? $_POST['kelas'] : 'semua';
$nisFilter = isset($_POST['nis']) ? $_POST['nis'] : '';  // New filter for NIS

// Build the query
$query = "
SELECT 
    mp.IDMataPelajaran AS IDKelas,
    mp.Nama AS NamaKelas,
    s.NIS,
    s.Nama AS NamaSiswa,
    k.Tanggal,
    k.Waktu,
    k.IDKehadiran
FROM 
    kehadiran k
JOIN 
    matapelajaran mp ON k.IDMataPelajaran = mp.IDMataPelajaran
JOIN 
    siswa s ON k.NIS = s.NIS
WHERE 
    mp.NIP = '$NIP'";

// Add filters based on class and NIS
if ($classFilter !== 'semua') {
    $query .= " AND mp.IDMataPelajaran = '$classFilter'";
}

if (!empty($nisFilter)) {
    $query .= " AND s.NIS = '$nisFilter'";
}

$query .= " ORDER BY k.Tanggal DESC, k.Waktu DESC";

// Execute the query
$result = mysqli_query($koneksi, $query);

// Get available classes for the dropdown
$classQuery = "
SELECT 
    IDMataPelajaran, 
    Nama
FROM 
    matapelajaran
WHERE 
    NIP = '$NIP'
ORDER BY Nama
";
$classResult = mysqli_query($koneksi, $classQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Kehadiran Siswa</title>
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
    <h2 class="text-2xl font-semibold text-center text-blue-600 mb-6">Riwayat Kehadiran Siswa di Kelas Anda</h2>

    <div class="mb-6">
        <form method="POST" action="presensiguru.php?NIP=<?php echo urlencode($NIP); ?>">
            <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Presensi</button>
        </form>
    </div>

    <div class="flex justify-between items-center mb-6">
        <form method="POST" action="kehadiranguru.php?NIP=<?php echo urlencode($NIP); ?>" class="flex space-x-4">
            <div class="flex items-center">
                <label for="kelas" class="text-gray-600 mr-2">Pilih Kelas:</label>
                <select name="kelas" id="kelas" class="border-gray-300 rounded-md shadow-sm px-4 py-2" onchange="this.form.submit()">
                    <option value="semua" <?php if ($classFilter === 'semua') echo 'selected'; ?>>Semua Kelas</option>
                    <?php while ($classRow = mysqli_fetch_assoc($classResult)) { ?>
                        <option value="<?php echo htmlspecialchars($classRow['IDMataPelajaran']); ?>" <?php if ($classRow['IDMataPelajaran'] == $classFilter) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($classRow['Nama']); ?>
                        </option>
                    <?php } ?>
                </select>

                <input type="text" name="nis" id="nis" class="border-gray-300 rounded-md shadow-sm px-4 py-2" placeholder="NIS siswa" value="<?php echo htmlspecialchars($nisFilter); ?>" />
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="table-auto w-full table-fixed border border-gray-200 rounded-lg max-w-2xl mx-auto">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-2 py-2 border-b w-1/6">ID Kelas</th>
                <th class="px-2 py-2 border-b w-1/3">Kelas</th>
                <th class="px-2 py-2 border-b w-1/4">NIS</th>
                <th class="px-2 py-2 border-b w-1/4">Nama Siswa</th>
                <th class="px-2 py-2 border-b w-1/4">Tanggal</th> <!-- Prevent text wrap in Tanggal -->
                <th class="px-2 py-2 border-b w-1/4">Waktu</th>
                <th class="px-2 py-2 border-b w-1/4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0) { 
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['IDKelas']); ?></td>
                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['NamaKelas']); ?></td>
                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['NIS']); ?></td>
                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['NamaSiswa']); ?></td>
                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['Tanggal']); ?></td>
                        <td class="px-2 py-2"><?php echo htmlspecialchars($row['Waktu']); ?></td>
                        <td class="px-2 py-2">
                            <form method="POST" action="deletekehadiran.php">
                                <input type="hidden" name="IDKehadiran" value="<?php echo $row['IDKehadiran']; ?>">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
            <?php } } else { ?>
                    <tr>
                        <td colspan="7" class="px-2 py-4 text-center text-gray-500">Tidak ada data kehadiran tersedia.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

</div>

</div>
</body>
</html>
