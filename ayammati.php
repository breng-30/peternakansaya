<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peternakan Saya - Laporan Ayam Mati</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <nav class="bg-blue-500 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <h1 class="text-2xl font-bold">Peternakan Saya</h1>
            <ul class="flex items-center">
                <li class="mr-6">
                    <a href="index.html" class="text-gray-300 hover:text-white">Home</a>
                </li>
                <li class="mr-6">
                    <a href="buatlaporan.php" class="text-gray-300 hover:text-white">Buat Laporan</a>
                </li>
                <li class="mr-6">
                    <a href="lihatlaporan.php" class="text-gray-300 hover:text-white">Lihat Laporan</a>
                </li>
                <li>
                    <a href="login.php" class="text-gray-300 hover:text-white">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mx-auto p-4 pt-6 mt-6">
        <h1 class="text-3xl font-bold mb-4">Laporan Ayam Mati</h1>
        <form class="w-full max-w-lg" action="ayammati.php" method="POST">
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="jumlah-ayam-mati">
                        Jumlah Ayam Mati
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="jumlah-ayam-mati" name="jumlah_ayam_mati" type="number" required>
                </div>
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="tanggal-ayam-mati">
                        Tanggal Ayam Mati
                    </label>
                    <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="tanggal-ayam-mati" name="tanggal_ayam_mati" type="date" required>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Konfirmasi Laporan
                </button>
            </div>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sertakan file koneksi database
        include 'connection.php'; // Pastikan file connection.php sudah ada dan terhubung ke database

        // Ambil data dari form
        $jumlah_ayam_mati = $_POST['jumlah_ayam_mati'];
        $tanggal_ayam_mati = $_POST['tanggal_ayam_mati'];

        // Cek apakah sudah ada laporan pada tanggal yang sama
        $query_check = "SELECT jumlah_ayam_masuk, jumlah_ayam_mati FROM laporan_ayam WHERE tanggal_laporan = ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("s", $tanggal_ayam_mati);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Jika data sudah ada, tambahkan jumlah ayam mati
            $row = $result_check->fetch_assoc();
            $jumlah_ayam_masuk = $row['jumlah_ayam_masuk'];
            $jumlah_ayam_mati_tercatat = $row['jumlah_ayam_mati'];

            // Cek apakah jumlah ayam mati yang diinput melebihi jumlah ayam masuk
            if ($jumlah_ayam_mati_tercatat + $jumlah_ayam_mati > $jumlah_ayam_masuk) {
                echo "<div class='container mx-auto p-4 mt-4 text-red-500 font-bold'>Jumlah ayam mati tidak boleh lebih dari jumlah ayam masuk pada tanggal yang dipilih. Jumlah ayam masuk: $jumlah_ayam_masuk. Jumlah ayam mati saat ini: $jumlah_ayam_mati_tercatat.</div>";
            } else {
                $query_update = "UPDATE laporan_ayam SET jumlah_ayam_mati = jumlah_ayam_mati + ? WHERE tanggal_laporan = ?";
                $stmt_update = $conn->prepare($query_update);
                $stmt_update->bind_param("is", $jumlah_ayam_mati, $tanggal_ayam_mati);
                $stmt_update->execute();
                $stmt_update->close();
                
                // Redirect ke halaman lihatlaporan.php
                header("Location: lihatlaporan.php");
                exit();
            }
        } else {
            // Jika data belum ada, insert data baru
            echo "<div class='container mx-auto p-4 mt-4 text-red-500 font-bold'>Laporan ayam masuk belum ada untuk tanggal tersebut. Harap input data ayam masuk terlebih dahulu.</div>";
        }

        // Tutup statement
        $stmt_check->close();

        // Tutup koneksi database
        $conn->close();
    }
    ?>
</body>
</html>
