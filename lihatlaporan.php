<?php
// Sertakan file koneksi database
include 'connection.php'; // Pastikan file connection.php sudah ada dan terhubung ke database

// Query untuk mengambil semua laporan ayam
$query = "SELECT * FROM laporan_ayam ORDER BY tanggal_laporan DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peternakan Saya - Lihat Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            nav {
                display: none;
            }
            table {
                font-size: 10px;
            }
            .aksi {
                display: none;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="bg-blue-500 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <h1 class="text-2xl font-bold">Peternakan Saya</h1>
            <ul class="flex items-center">
                <li class="mr-6"><a href="index.html" class="text-gray-300 hover:text-white">Home</a></li>
                <li class="mr-6"><a href="buatlaporan.php" class="text-gray-300 hover:text-white">Buat Laporan</a></li>
                <li class="mr-6"><a href="lihatlaporan.php" class="text-gray-300 hover:text-white">Lihat Laporan</a></li>
                <li><a href="login.php" class="text-gray-300 hover:text-white">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mx-auto p-4 pt-6 mt-6">
        <h1 class="text-3xl font-bold mb-4">Lihat Semua Laporan Ayam</h1>
        <div id="laporan-container" class="bg-white rounded shadow-md p-4">
            <h2 class="text-xl font-bold mb-2">Data Laporan</h2>
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border border-gray-200">Tanggal</th>
                        <th class="px-4 py-2 border border-gray-200">Jumlah Ayam Masuk</th>
                        <th class="px-4 py-2 border border-gray-200">Jumlah Ayam Mati</th>
                        <th class="px-4 py-2 border border-gray-200">Jumlah Ayam Sakit</th>
                        <th class="px-4 py-2 border border-gray-200 aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data dari setiap row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td class='border px-4 py-2 text-center'>" . $row['tanggal_laporan'] . "</td>";
                            echo "<td class='border px-4 py-2 text-center'>" . $row['jumlah_ayam_masuk'] . "</td>";
                            echo "<td class='border px-4 py-2 text-center'>" . $row['jumlah_ayam_mati'] . "</td>";
                            echo "<td class='border px-4 py-2 text-center'>" . $row['jumlah_ayam_sakit'] . "</td>";
                            echo "<td class='border px-4 py-2 text-center aksi'>
                                    <button class='bg-red-500 text-white px-2 py-1 rounded' onclick=\"confirmDelete(" . $row['id'] . ")\">Hapus</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Tidak ada laporan yang tersedia.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="mt-4">
                <button class="bg-blue-500 text-white px-4 py-2 rounded print-button" onclick="printReport()">Cetak Laporan</button>
            </div>
        </div>

        <!-- Tabel Laporan Panen -->
        <div id="laporan-panen-container" class="bg-white rounded shadow-md p-4 mt-8">
            <h2 class="text-xl font-bold mb-2">Data Laporan Panen</h2>
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border border-gray-200">Tanggal Masuk</th>
                        <th class="px-4 py-2 border border-gray-200">Total Ayam Hidup</th>
                        <th class="px-4 py-2 border border-gray-200">Total Ayam Sakit</th>
                        <th class="px-4 py-2 border border-gray-200">Estimasi Tanggal Panen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset result pointer and loop through data again for panen table
                    $result->data_seek(0);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $tanggal_masuk = $row['tanggal_laporan'];
                            $jumlah_ayam_masuk = $row['jumlah_ayam_masuk'];
                            $jumlah_ayam_mati = $row['jumlah_ayam_mati'];
                            $jumlah_ayam_sakit = $row['jumlah_ayam_sakit'];

                            // Hitung total ayam hidup
                            $total_ayam_hidup = $jumlah_ayam_masuk - $jumlah_ayam_mati - $jumlah_ayam_sakit;

                            // Hitung estimasi tanggal panen (30-35 hari dari tanggal masuk)
                            $estimasi_panen_start = date('Y-m-d', strtotime($tanggal_masuk . ' + 30 days'));
                            $estimasi_panen_end = date('Y-m-d', strtotime($tanggal_masuk . ' + 35 days'));
                            $estimasi_panen = "$estimasi_panen_start - $estimasi_panen_end";

                            echo "<tr>";
                            echo "<td class='border px-4 py-2 text-center'>" . $tanggal_masuk . "</td>";
                            echo "<td class='border px-4 py-2 text-center'>" . max($total_ayam_hidup, 0) . "</td>";
                            echo "<td class='border px-4 py-2 text-center'>" . $jumlah_ayam_sakit . "</td>";
                            echo "<td class='border px-4 py-2 text-center'>" . $estimasi_panen . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Tidak ada laporan yang tersedia.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Fungsi untuk mengonfirmasi penghapusan laporan
        function confirmDelete(id) {
            const confirmation = confirm("Apakah Anda yakin ingin menghapus laporan ini?");
            if (confirmation) {
                window.location.href = "deletelaporan.php?id=" + id;
            }
        }

        // Fungsi untuk mencetak laporan
        function printReport() {
            window.print();
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
