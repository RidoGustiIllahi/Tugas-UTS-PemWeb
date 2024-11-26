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

    // Query untuk mendapatkan data guru berdasarkan NIP
    $queryGuru = "SELECT Foto, Nama, TanggalLahir, Alamat, Email, username FROM guru WHERE NIP = '$NIP'";
    $resultGuru = mysqli_query($koneksi, $queryGuru);

    // Ambil data guru
    $guru = mysqli_fetch_assoc($resultGuru);

    // Query untuk mendapatkan data akun berdasarkan username
    $queryAkun = "SELECT username, password FROM akunguru WHERE username = '".$guru['username']."'";
    $resultAkun = mysqli_query($koneksi, $queryAkun);

    // Ambil data akun
    $akun = mysqli_fetch_assoc($resultAkun);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Profile Guru</title>
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
                    <li><a href="kehadiranguru.php?NIP=<?php echo urlencode($NIP); ?>" class="hover:text-blue-300">Rekap Kehadiran</a></li>
                </ul>
            </nav>
            <form method="POST" action="logout.php">
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Log Out</button>
            </form>
        </div>
    </header>

    <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg max-w-md">
        <h1 class="text-3xl font-semibold text-center text-blue-600 mb-6">Edit Profile Guru</h1>

        <form method="POST" action="updateprofileguru.php">
            <div class="flex justify-center mb-6">
                <div class="text-center">
                    <img src="<?php echo htmlspecialchars($guru['Foto']); ?>" alt="Foto Guru" class="w-36 h-36 rounded-full border-4 border-blue-500 mx-auto mb-4">
                    <p class="text-gray-600 font-semibold">Foto Profil</p>
                </div>
            </div>

            <table class="table-auto w-full mt-6 border border-gray-200 rounded-lg">
                <tbody>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                            NIP
                        </th>
                        <td><input type="text" name="nip" value="<?php echo htmlspecialchars($NIP); ?>" readonly class="w-full px-3 py-2 border-gray-300 rounded-md"></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                            Username
                        </th>
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($guru['username']); ?>" class="w-full px-3 py-2 border-gray-300 rounded-md"></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                            Password
                        </th>
                        <td>
                            <input type="password" name="passwordbaru" placeholder="Masukkan password baru" class="w-full px-3 py-2 border-gray-300 rounded-md">
                        </td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                            Nama
                        </th>
                        <td><input type="text" name="nama" value="<?php echo htmlspecialchars($guru['Nama']); ?>" class="w-full px-3 py-2 border-gray-300 rounded-md"></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                            Tanggal Lahir
                        </th>
                        <td><input type="date" name="tanggallahir" value="<?php echo htmlspecialchars($guru['TanggalLahir']); ?>" class="w-full px-3 py-2 border-gray-300 rounded-md"></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                            Alamat
                        </th>
                        <td><input type="text" name="alamat" value="<?php echo htmlspecialchars($guru['Alamat']); ?>" class="w-full px-3 py-2 border-gray-300 rounded-md"></td>
                    </tr>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-gray-700 font-semibold border-b border-gray-200 w-1/3"> 
                            Email
                        </th>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($guru['Email']); ?>" class="w-full px-3 py-2 border-gray-300 rounded-md"></td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-center mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">Simpan Perubahan</button>
            </div>
        </form>
    </div>

</body>
</html>
