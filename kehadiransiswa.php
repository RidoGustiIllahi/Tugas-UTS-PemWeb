<?php
    session_start();

    $NIS = $_GET['NIS'];

    if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login'){
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

    include 'koneksi.php';

    $classFilter = isset($_POST['kelas']) ? $_POST['kelas'] : 'semua';

    $query = "
    SELECT 
        mp.IDMataPelajaran AS IDKelas,
        mp.Nama AS NamaKelas,
        k.Tanggal,
        k.Waktu
    FROM 
        kehadiran k
    JOIN 
        matapelajaran mp ON k.IDMataPelajaran = mp.IDMataPelajaran
    WHERE 
        k.NIS = '$NIS'";

    if ($classFilter !== 'semua') {
        $query .= " AND mp.IDMataPelajaran = '$classFilter'";
    }

    $query .= " ORDER BY k.Tanggal DESC, k.Waktu DESC";

    $result = mysqli_query($koneksi, $query);

    $classQuery = "
    SELECT 
        mp.IDMataPelajaran, 
        mp.Nama
    FROM 
        siswamatapelajaran sm
    JOIN 
        matapelajaran mp ON sm.IDMataPelajaran = mp.IDMataPelajaran
    WHERE 
        sm.NIS = '$NIS'
    ORDER BY mp.Nama
    ";

    $classResult = mysqli_query($koneksi, $classQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kehadiran Siswa</title>
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
        <h2 class="text-2xl font-semibold text-center text-blue-600 mb-6">Riwayat Kehadiran Kelas</h2>

        <div class="flex justify-between items-center mb-6">
            <form method="POST" action="kehadiransiswa.php?NIS=<?php echo urlencode($NIS); ?>">
                <label for="kelas" class="text-gray-600 mr-2">Pilih Kelas:</label>
                <select name="kelas" id="kelas" class="border-gray-300 rounded-md shadow-sm px-4 py-2" onchange="this.form.submit()">
                    <option value="semua" <?php if ($classFilter === 'semua') echo 'selected'; ?>>Semua Kelas</option>
                    <?php while ($classRow = mysqli_fetch_assoc($classResult)) { ?>
                        <option value="<?php echo htmlspecialchars($classRow['IDMataPelajaran']); ?>" <?php if ($classRow['IDMataPelajaran'] == $classFilter) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($classRow['Nama']); ?>
                        </option>
                    <?php } ?>
                </select>
            </form>
            <form method="POST" action="presensisiswa.php?NIS=<?php echo urlencode($NIS); ?>">
                <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Presensi</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table-auto w-full border border-gray-200 rounded-lg max-w-2xl mx-auto">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-2 py-2 border-b w-1/6">ID Kelas</th>
                        <th class="px-2 py-2 border-b w-1/3">Kelas</th>
                        <th class="px-2 py-2 border-b w-1/4">Tanggal</th>
                        <th class="px-2 py-2 border-b w-1/4">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) { 
                        while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['IDKelas']); ?></td>
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['NamaKelas']); ?></td>
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['Tanggal']); ?></td>
                                <td class="px-2 py-2"><?php echo htmlspecialchars($row['Waktu']); ?></td>
                            </tr>
                    <?php } } else { ?>
                        <tr>
                            <td colspan="4" class="px-2 py-4 text-center text-gray-500">Tidak ada data kehadiran tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
