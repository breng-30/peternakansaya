<?php
// Sertakan file koneksi database
include 'connection.php'; // Pastikan file connection.php sudah ada dan terhubung ke database

// Cek apakah data dari form dikirim dengan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Enkripsi password menggunakan bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Cek apakah email sudah terdaftar dengan prepared statement
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_check = $stmt->get_result();

    if ($result_check->num_rows > 0) {
        // Jika email sudah ada di database, beri pesan error
        echo "Email sudah terdaftar. Silakan gunakan email lain atau <a href='login.html'>login di sini</a>.";
        $stmt->close(); // Tutup statement $stmt
    } else {
        // Masukkan data pengguna baru ke tabel user dengan prepared statement
        $stmt->close(); // Tutup statement $stmt sebelum menggunakan statement baru

        $stmt_insert = $conn->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");
        if ($stmt_insert) {
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);
            if ($stmt_insert->execute()) {
                // Redirect ke halaman login setelah berhasil mendaftar
                header("Location: login.html");
                exit();
            } else {
                echo "Error: " . $stmt_insert->error;
            }
            $stmt_insert->close(); // Tutup statement $stmt_insert jika sudah selesai
        } else {
            echo "Error dalam mempersiapkan statement: " . $conn->error;
        }
    }
} else {
    echo "Metode permintaan tidak didukung.";
}

$conn->close(); // Tutup koneksi
?>
