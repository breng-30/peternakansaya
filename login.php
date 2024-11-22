<?php
// Sertakan file koneksi database
include 'connection.php'; // Pastikan file connection.php sudah ada dan terhubung ke database

session_start(); // Mulai session

// Inisialisasi variabel pesan error dan sukses
$error_message = '';
$success_message = '';

// Cek apakah data dari form dikirim dengan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            // Proses Login
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Cari pengguna berdasarkan email
            $query = "SELECT * FROM user WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                // Verifikasi password yang diinputkan dengan password terenkripsi di database
                if (password_verify($password, $user['password'])) {
                    // Login berhasil, simpan informasi pengguna di session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];

                    // Redirect ke halaman index setelah login berhasil
                    header("Location: index.html");
                    exit();
                } else {
                    // Password salah
                    $error_message = "Password salah. Silakan coba lagi.";
                }
            } else {
                // Email tidak ditemukan
                $error_message = "Email tidak ditemukan! Silakan daftar terlebih dahulu.";
            }

            $stmt->close();
        } elseif ($_POST['action'] === 'signup') {
            // Proses Signup
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            // Validasi password
            if (strlen($password) < 8) {
                $error_message = "Password harus minimal 8 karakter.";
            } elseif ($password !== $confirmPassword) {
                $error_message = "Password dan konfirmasi password tidak cocok.";
            } else {
                // Enkripsi password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Simpan data ke database
                $query = "INSERT INTO user (name, email, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sss", $name, $email, $hashed_password);

                if ($stmt->execute()) {
                    $success_message = "Registrasi berhasil! Silakan login.";
                } else {
                    $error_message = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
                }

                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Signup - Peternakan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        // Fungsi untuk toggle antara login dan signup
        function toggleForm(formType) {
            const loginForm = document.getElementById("loginForm");
            const signupForm = document.getElementById("signupForm");

            if (formType === "login") {
                loginForm.classList.remove("hidden");
                signupForm.classList.add("hidden");
            } else {
                loginForm.classList.add("hidden");
                signupForm.classList.remove("hidden");
            }
        }
    </script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 id="formTitle" class="text-2xl font-bold mb-4 text-center">Login / Signup</h2>

        <!-- Jika ada pesan error, tampilkan di sini -->
        <?php if (!empty($error_message)) : ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Jika ada pesan sukses, tampilkan di sini -->
        <?php if (!empty($success_message)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form id="loginForm" action="" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="mb-4">
                <label for="loginEmail" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" id="loginEmail" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="example@example.com" required>
            </div>
            <div class="mb-4">
                <label for="loginPassword" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" id="loginPassword" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="********" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Login</button>
                <a href="#" class="text-blue-500 hover:text-blue-700 text-sm font-bold" onclick="toggleForm('signup')">Belum punya akun? Daftar disini</a>
            </div>
        </form>

        <!-- Signup Form -->
        <form id="signupForm" action="" method="POST" class="hidden">
            <input type="hidden" name="action" value="signup">
            <div class="mb-4">
                <label for="signupName" class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" id="signupName" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Nama Lengkap" required>
            </div>
            <div class="mb-4">
                <label for="signupEmail" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" id="signupEmail" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="example@example.com" required>
            </div>
            <div class="mb-4">
                <label for="signupPassword" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" id="signupPassword" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Password minimal 8 karakter" required>
            </div>
            <div class="mb-4">
                <label for="signupConfirmPassword" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password</label>
                <input type="password" id="signupConfirmPassword" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Konfirmasi Password" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Daftar</button>
                <a href="#" class="text-blue-500 hover:text-blue-700 text-sm font-bold" onclick="toggleForm('login')">Sudah punya akun? Login</a>
            </div>
        </form>
    </div>
</body>
</html>
