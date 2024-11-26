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
    $query = "SELECT * FROM siswa WHERE NIS = '$NIS'";
    $result = mysqli_query($koneksi, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $foto = $row['Foto'];
        $username = $row['username'];
        $nama = $row['Nama'];
        $tanggalLahir = $row['TanggalLahir'];
        $alamat = $row['Alamat'];
        $email = $row['Email'];
    } else {
        echo "Data siswa tidak ditemukan.";
        exit();
    }
    
    mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

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
    
        <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg max-w-md">
        <h1 class="text-3xl font-semibold text-center text-blue-600 mb-6">Profile Siswa</h1>
            <div class="flex flex-col items-center">
                <?php if ($foto): ?>
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto Siswa" class="w-36 h-36 rounded-full border border-gray-300">
                <?php else: ?>
                    <p class="text-gray-500">Foto tidak tersedia</p>
                <?php endif; ?>
            </div>

        <table class="table-auto w-full mt-6 border border-gray-200 rounded-lg">
            <tbody>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                        NIS
                    </th>
                    <td class="px-4 py-2 text-gray-600 border-b border-gray-200"><?php echo htmlspecialchars($NIS); ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                        Username
                    </th>
                    <td class="px-4 py-2 text-gray-600 border-b border-gray-200"><?php echo htmlspecialchars($username); ?></td>
                </tr>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                        Nama
                    </th>
                    <td class="px-4 py-2 text-gray-600 border-b border-gray-200"><?php echo htmlspecialchars($nama); ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                        Tanggal Lahir
                    </th>
                    <td class="px-4 py-2 text-gray-600 border-b border-gray-200"><?php echo htmlspecialchars($tanggalLahir); ?></td>
                </tr>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                        Alamat
                    </th>
                    <td class="px-4 py-2 text-gray-600 border-b border-gray-200"><?php echo htmlspecialchars($alamat); ?></td>
                </tr>
                <tr>
                    <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                        Email
                    </th>
                    <td class="px-4 py-2 text-gray-600 border-b border-gray-200"><?php echo htmlspecialchars($email); ?></td>
                </tr>
            </tbody>
        </table>
        <form method="GET" action="editprofilesiswa.php" class="flex justify-center mt-4">
            <input type="hidden" name="NIS" value="<?php echo htmlspecialchars($NIS); ?>">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Ubah</button>
        </form>

    </div>
</body>
</html>
