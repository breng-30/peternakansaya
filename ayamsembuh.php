<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peternakan Saya - Ayam Sembuh</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <nav class="bg-blue-500 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <h1 class="text-2xl font-bold">Peternakan Saya</h1>
            <ul class="flex items-center">
                <li class="mr-6"><a href="index.php" class="text-gray-300 hover:text-white">Home</a></li>
                <li class="mr-6"><a href="buatlaporan.php" class="text-gray-300 hover:text-white">Buat Laporan</a></li>
                <li class="mr-6"><a href="lihatlaporan.php" class="text-gray-300 hover:text-white">Lihat Laporan</a></li>
                <li><a href="login.php" class="text-gray-300 hover:text-white">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto p-4 pt-6 mt-6">
        <h1 class="text-3xl font-bold mb-4">Input Ayam Sembuh dari Daftar</h1>
        <div class="bg-white rounded shadow-md p-4">
            <form method="POST" action="ayamsembuh.php" id="sembuhForm">
                <label for="tanggal" class="block mb-2">Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" class="border border-gray-300 rounded p-2 mb-4" required onchange="cekJumlahAyamSakit()"/>

                <div id="jumlahAyamSakitContainer" class="mb-4 text-blue-500 font-semibold hidden">
                    Jumlah Ayam Sakit pada tanggal yang dipilih: <span id="jumlahAyamSakit"></span>
                </div>

                <label for="jumlahSembuh" class="block mb-2">Jumlah Ayam Sembuh:</label>
                <input type="number" id="jumlahSembuh" name="jumlah_sembuh" class="border border-gray-300 rounded p-2 mb-4" required disabled />

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded" id="submitBtn" disabled>Input Sembuh</button>
            </form>

            <div id="message" class="mt-4 text-red-500 font-semibold"></div>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tanggal']) && isset($_POST['jumlah_sembuh'])) {
        include 'connection.php'; // Koneksi ke database

        $tanggal = $_POST['tanggal'];
        $jumlah_sembuh = $_POST['jumlah_sembuh'];

        if (empty($tanggal) || $jumlah_sembuh <= 0) {
            echo "<div id='message' class='mt-4 text-red-500 font-semibold'>Masukkan tanggal dan jumlah ayam sembuh yang valid.</div>";
        } else {
            $query_check = "SELECT jumlah_ayam_sakit FROM laporan_ayam WHERE tanggal_laporan = ?";
            $stmt_check = $conn->prepare($query_check);
            $stmt_check->bind_param("s", $tanggal);
            $stmt_check->execute();
            $result = $stmt_check->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $jumlah_ayam_sakit = $row['jumlah_ayam_sakit'];

                if ($jumlah_ayam_sakit > 0) {
                    if ($jumlah_sembuh > $jumlah_ayam_sakit) {
                        echo "<div id='message' class='mt-4 text-red-500 font-semibold'>Jumlah ayam sembuh tidak boleh lebih dari jumlah ayam sakit. Maksimal ayam sembuh: $jumlah_ayam_sakit.</div>";
                    } else {
                        $query_update = "UPDATE laporan_ayam SET jumlah_ayam_sakit = GREATEST(jumlah_ayam_sakit - ?, 0) WHERE tanggal_laporan = ?";
                        $stmt_update = $conn->prepare($query_update);
                        $stmt_update->bind_param("is", $jumlah_sembuh, $tanggal);

                        if ($stmt_update->execute()) {
                            echo "<script>window.location.href = 'lihatlaporan.php';</script>";
                        } else {
                            echo "<div id='message' class='mt-4 text-red-500 font-semibold'>Terjadi kesalahan saat mengupdate data. Silakan coba lagi.</div>";
                        }

                        $stmt_update->close();
                    }
                } else {
                    echo "<div id='message' class='mt-4 text-red-500 font-semibold'>Tidak ada ayam sakit pada tanggal yang dipilih. Tidak dapat menginputkan ayam sembuh.</div>";
                }
            } else {
                echo "<div id='message' class='mt-4 text-red-500 font-semibold'>Tidak ada laporan ayam sakit pada tanggal tersebut.</div>";
            }

            $stmt_check->close();
        }

        $conn->close();
    }
    ?>

    <script>
        function cekJumlahAyamSakit() {
            const tanggal = document.getElementById('tanggal').value;

            if (tanggal) {
                fetch('checkayamsakit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'tanggal=' + encodeURIComponent(tanggal)
                })
                .then(response => response.text())
                .then(data => {
                    const maxSembuh = parseInt(data);
                    const jumlahSembuhInput = document.getElementById('jumlahSembuh');
                    const submitBtn = document.getElementById('submitBtn');
                    const jumlahAyamSakitContainer = document.getElementById('jumlahAyamSakitContainer');
                    const jumlahAyamSakitText = document.getElementById('jumlahAyamSakit');

                    if (maxSembuh > 0) {
                        jumlahAyamSakitText.textContent = maxSembuh;
                        jumlahAyamSakitContainer.classList.remove('hidden');
                        jumlahSembuhInput.max = maxSembuh;
                        jumlahSembuhInput.value = '';
                        jumlahSembuhInput.disabled = false;
                        submitBtn.disabled = false;
                        document.getElementById('message').textContent = '';
                    } else {
                        jumlahSembuhInput.value = '';
                        jumlahSembuhInput.disabled = true;
                        submitBtn.disabled = true;
                        jumlahAyamSakitContainer.classList.add('hidden');
                        document.getElementById('message').textContent = 'Tidak ada ayam sakit pada tanggal yang dipilih.';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>
