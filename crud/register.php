<?php
include('koneksi.php'); // Memanggil koneksi.php
session_start(); // Memastikan session sudah dimulai

$error_message = ""; // Inisialisasi variabel pesan kesalahan
$success_message = ""; // Inisialisasi variabel pesan sukses

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $namalengkap = $_POST['namalengkap']; // Ambil Nama Lengkap
    $alamat = $_POST['alamat']; // Ambil Alamat
    $email = $_POST['email']; // Ambil Email

    // Cek apakah username sudah ada di database
    $check_query = "SELECT * FROM user WHERE username = ?";
    $stmt = $connection->prepare($check_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Username sudah terdaftar, silakan gunakan username lain.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak cocok!";
    } else {
        // Password akan di-hash menggunakan MD5 (atau pertimbangkan untuk menggunakan bcrypt)
        $hashed_password = md5($password);

        // Simpan data pengguna baru ke database
        $insert_query = "INSERT INTO user (username, password, namalengkap, alamat, email, level) VALUES (?, ?, ?, ?, ?, 'User')";
        $stmt = $connection->prepare($insert_query);
        $stmt->bind_param("sssss", $username, $hashed_password, $namalengkap, $alamat, $email);

        if ($stmt->execute()) {
            $success_message = "Pendaftaran berhasil! Silakan login.";
        } else {
            $error_message = "Terjadi kesalahan saat pendaftaran: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="register-container mt-5">
        <h2 class="text-center"><i class="fas fa-user-plus"></i> Registrasi</h2>

        <!-- Menampilkan pesan error jika ada -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Menampilkan pesan sukses jika ada -->
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <!-- Form untuk melakukan registrasi -->
        <form method="post" action="">
            <div class="form-group">
                <label for="namalengkap"><i class="fas fa-user"></i> Nama Lengkap</label>
                <input type="text" class="form-control" name="namalengkap" required>
            </div>
            
            <div class="form-group">
                <label for="alamat"><i class="fas fa-address-card"></i> Alamat</label>
                <textarea class="form-control" name="alamat" required></textarea>
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Alamat Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Konfirmasi Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> Daftar</button>
        </form>
        
        <div class="login-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>

    <!-- Script JavaScript untuk mendukung Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
