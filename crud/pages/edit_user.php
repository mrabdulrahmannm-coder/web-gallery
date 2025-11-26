<?php
include('../koneksi.php'); 
include('header.php');

// Cek apakah pengguna sudah login dan memiliki hak akses Admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Mendapatkan ID pengguna dari URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Jika user_id tidak valid, hentikan eksekusi
if (!$user_id) {
    echo "<div class='alert alert-danger'>ID pengguna tidak valid.</div>";
    exit;
}

// Ambil data pengguna berdasarkan ID
$query = "SELECT * FROM user WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update data pengguna ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $level = $_POST['level'];
    $password = $_POST['password'];
    $namalengkap = $_POST['namalengkap']; // Ambil Nama Lengkap
    $alamat = $_POST['alamat']; // Ambil Alamat

    // Jika password diisi, hash password baru
    if (!empty($password)) {
        $hashed_password = md5($password);
        $update_query = "UPDATE user SET username = ?, email = ?, level = ?, password = ?, namalengkap = ?, alamat = ? WHERE user_id = ?";
        $stmt = $connection->prepare($update_query);
        $stmt->bind_param('ssssssi', $username, $email, $level, $hashed_password, $namalengkap, $alamat, $user_id);
    } else {
        // Jika password tidak diubah, update tanpa password
        $update_query = "UPDATE user SET username = ?, email = ?, level = ?, namalengkap = ?, alamat = ? WHERE user_id = ?";
        $stmt = $connection->prepare($update_query);
        $stmt->bind_param('sssssi', $username, $email, $level, $namalengkap, $alamat, $user_id);
    }

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Data pengguna berhasil diperbarui.</div>";
    } else {
        echo "<div class='alert alert-danger'>Kesalahan saat memperbarui data pengguna.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Pengguna</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="namalengkap">Nama Lengkap</label>
            <input type="text" class="form-control" id="namalengkap" name="namalengkap" value="<?= htmlspecialchars($user['namalengkap']) ?>" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea class="form-control" id="alamat" name="alamat" required><?= htmlspecialchars($user['alamat']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="username">Nama Pengguna</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="level">Level Pengguna</label>
            <select class="form-control" id="level" name="level">
                <option value="Admin" <?= $user['level'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                <option value="User" <?= $user['level'] === 'User' ? 'selected' : '' ?>>User</option>
            </select>
        </div>
        <div class="form-group">
            <label for="password">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="list_users.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
