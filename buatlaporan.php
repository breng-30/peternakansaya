<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peternakan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <nav class="bg-blue-500 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <h1 class="text-2xl font-bold">Peternakan Saya</h1>
            <ul class="flex items-center">
                <li class="mr-6"><a href="index.html" class="text-white hover:text-gray-200">Home</a></li>
                <li class="mr-6"><a href="buatlaporan.php" class="text-white hover:text-gray-200">Buat Laporan</a></li>
                <li class="mr-6"><a href="lihatlaporan.php" class="text-gray-200 hover:text-white">Lihat Laporan</a></li>
                <li><a href="login.php" class="text-white hover:text-gray-200">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mx-auto p-4 mt-4">
        <h2 class="text-blue-500 text-center text-2xl font-bold mb-4">Laporan Ayam</h2>
        <div class="flex flex-wrap justify-center">
            <a href="ayammasuk.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4 mb-4">Ayam Masuk</a>
            <a href="ayammati.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4 mb-4">Ayam Mati</a>
            <a href="ayamsakit.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4 mb-4">Ayam Sakit</a>
            <a href="ayamsembuh.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4 mb-4">Ayam Sembuh</a>
        </div>
    </div>

    <?php
    // Jika Anda ingin menambahkan logika PHP untuk memproses input data di sini, misalnya menyimpan laporan ke database, tambahkan kodenya di sini.
    // Contoh: menyimpan data laporan ayam masuk
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sertakan file koneksi database
        include 'connection.php'; // Pastikan file connection.php sudah ada dan terhubung ke database

        // Ambil data dari form
        $jumlah_ayam_masuk = $_POST['jumlah_ayam_masuk'];
        $tanggal_ayam_masuk = $_POST['tanggal_ayam_masuk'];

        // Query untuk memasukkan data ke dalam tabel laporan_ayam
        $query = "INSERT INTO laporan_ayam (tanggal_laporan, jumlah_ayam_masuk) VALUES (?, ?)";

        // Menggunakan prepared statement untuk keamanan
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $tanggal_ayam_masuk, $jumlah_ayam_masuk);

        if ($stmt->execute()) {
            // Jika berhasil, arahkan kembali ke halaman lihatlaporan.php
            echo "<script>alert('Laporan Ayam Masuk berhasil diinputkan!'); window.location.href = 'lihatlaporan.php';</script>";
        } else {
            // Jika gagal, tampilkan pesan error
            echo "<script>alert('Terjadi kesalahan saat menginputkan data. Silakan coba lagi.');</script>";
        }

        // Tutup statement
        $stmt->close();

        // Tutup koneksi database
        $conn->close();
    }
    ?>
</body>
</html>
