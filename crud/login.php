<?php
include('koneksi.php'); // Memanggil koneksi.php
session_start(); // Memastikan session sudah dimulai

$error_message = ""; // Inisialisasi variabel error_message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Proses login
    $result = mysqli_query($connection, "SELECT * FROM user WHERE username='$username' AND password='$password'");

    // Cek jika query berhasil
    if ($result) {
        $user = mysqli_fetch_object($result);
        
        if ($user) {
            // Set session berdasarkan level
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['level'] = $user->level;
            $_SESSION['username'] = $user->username; // Simpan username di session
            header("Location: pages");
            exit;
        } else {
            $error_message = "Login gagal! Username atau password salah.";
        }
    } else {
        $error_message = "Kesalahan dalam query: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Menambahkan Font Awesome -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
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
    <div class="login-container mt-5">
        <h2 class="text-center"><i class="fas fa-sign-in-alt"></i> Login</h2> <!-- Menambahkan ikon login di judul -->
        
        <!-- Menampilkan pesan error jika ada -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        
        <!-- Form untuk melakukan login -->
        <form method="post" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <!-- Input untuk username, wajib diisi -->
                <input type="text" class="form-control" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <!-- Input untuk password, wajib diisi -->
                <input type="password" class="form-control" name="password" required>
            </div>
            
            <!-- Tombol untuk submit form -->
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Login</button> <!-- Menambahkan ikon di tombol login -->
        </form>
        <div class="login-link">
            Sudah belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>

    <!-- Script JavaScript untuk mendukung Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
