<?php
// Sertakan file koneksi database
include 'connection.php'; // Pastikan file connection.php sudah ada dan terhubung ke database

// Mengecek apakah ada parameter 'tanggal' yang dikirim melalui metode POST
if (isset($_POST['tanggal'])) {
    $tanggal = $_POST['tanggal'];

    // Query untuk mengambil jumlah ayam sakit pada tanggal tertentu
    $query = "SELECT jumlah_ayam_sakit FROM laporan_ayam WHERE tanggal_laporan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tanggal);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika ada data untuk tanggal yang dicari
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['jumlah_ayam_sakit']; // Output jumlah ayam sakit pada tanggal tersebut
    } else {
        // Jika tidak ada data untuk tanggal yang dicari
        echo 0;
    }

    // Tutup statement dan koneksi database
    $stmt->close();
    $conn->close();
} else {
    echo 0; // Jika parameter tanggal tidak disediakan
}
?>
