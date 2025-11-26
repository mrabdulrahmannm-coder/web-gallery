<?php
include('../koneksi.php'); // Memanggil koneksi.php
include('header.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah pengguna memiliki hak akses admin
if ($_SESSION['level'] !== 'Admin') {
    header("Location: ../index.php"); // Redirect ke halaman lain jika bukan admin
    exit;
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $namalengkap = $_POST['namalengkap'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $level = $_POST['level'];
    
    // Hash password sebelum disimpan (gunakan password_hash untuk keamanan)
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Query untuk menambah pengguna ke database
    $query = "INSERT INTO user (namalengkap, username, email, alamat, password, level) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssss", $namalengkap, $username, $email, $alamat, $password, $level);
    
    if ($stmt->execute()) {
        $message = "Pengguna berhasil ditambahkan!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Tambah Pengguna</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="namalengkap">Nama Lengkap</label>
                <input type="text" class="form-control" id="namalengkap" name="namalengkap" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" required></textarea>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="level">Level</label>
                <select class="form-control" id="level" name="level" required>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
            <a href="daftar_pengguna.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
