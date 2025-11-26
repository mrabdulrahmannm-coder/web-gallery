<?php
include('../koneksi.php');
include('header.php');

// Mulai session
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Anda harus login terlebih dahulu.');
            window.location.href = '../index.php'; 
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaalbum = $_POST['namaalbum'];
    $deskripsi = $_POST['deskripsi'];
    $user_id = $_SESSION['user_id'];

    // Cek apakah nama album sudah ada
    $query = "SELECT COUNT(*) FROM album WHERE namaalbum = ? AND user_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("si", $namaalbum, $user_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // Jika nama album sudah ada, beri tahu pengguna
    if ($count > 0) {
        echo "<script>
                alert('Nama album sudah ada. Silakan gunakan nama lain.');
              </script>";
    } else {
        // Jika album belum ada, tambahkan ke database
        $query = "INSERT INTO album (namaalbum, deskripsi, user_id) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ssi", $namaalbum, $deskripsi, $user_id);
        $stmt->execute();
        echo "<script>
                alert('Album berhasil ditambahkan!');
                window.location.href = 'index.php';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Album</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    body {
        background-color: #f5f5f5;
    }

    .container {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #333;
    }

    .form-control,
    .btn-success,
    .btn-primary {
        transition: 0.3s ease-in-out;
    }

    .form-control:focus {
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.8);
        border-color: #007bff;
    }

    .btn-success:hover {
        background-color: #218838;
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
    }

    .btn-success:active {
        background-color: #1e7e34;
        transform: scale(1);
        box-shadow: none;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
    }
</style>

<body>
    <div class="container mt-5">
        <h2>Tambah Album Baru</h2>
        <form method="post">
            <div class="form-group">
                <label for="namaalbum">Nama Album</label>
                <input type="text" class="form-control" name="namaalbum" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" required></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Tambah Album</button>
        </form>
        <a href="index.php" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>

</html>
