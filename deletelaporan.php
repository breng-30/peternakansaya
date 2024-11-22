<?php
// Sertakan file koneksi database
include 'connection.php'; // Pastikan file connection.php sudah ada dan terhubung ke database

// Periksa apakah ada parameter 'id' yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus laporan berdasarkan id
    $query = "DELETE FROM laporan_ayam WHERE id = ?";

    // Menggunakan prepared statement untuk menghindari SQL Injection
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Jika berhasil dihapus, arahkan kembali ke halaman lihatlaporan.php
        header("Location: lihatlaporan.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: Terjadi kesalahan saat menghapus data.";
    }

    // Tutup statement
    $stmt->close();
} else {
    echo "Error: Tidak ada ID yang diberikan.";
}

// Tutup koneksi database
$conn->close();
?>
