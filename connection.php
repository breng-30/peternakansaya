<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'peternakan'; // Nama database yang sudah Anda buat

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi apakah berhasil atau tidak
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
