<?php
ob_start(); // Memulai penampungan output

include('../koneksi.php'); // Memanggil koneksi.php
include('header.php'); // Memanggil header.php

// Mendapatkan ID album dari URL
$album_id = $_GET['id'];
$query = "SELECT * FROM album WHERE album_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $album_id);
$stmt->execute();
$result = $stmt->get_result();
$album = $result->fetch_assoc();

$error_message = '';

// Proses edit album
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaalbum = $_POST['namaalbum'];
    $deskripsi = $_POST['deskripsi'];

    $query = "UPDATE album SET namaalbum = ?, deskripsi = ? WHERE album_id = ?";
    $stmt = $connection->prepare($query);

    if (!$stmt) {
        $error_message = "Kesalahan dalam mempersiapkan pernyataan SQL: " . $connection->error;
    } else {
        $stmt->bind_param("ssi", $namaalbum, $deskripsi, $album_id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error_message = "Kesalahan saat mengedit album: " . $stmt->error;
        }
    }
}

ob_end_flush(); // Mengeluarkan output yang sudah ditampung
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Album</title>
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
        .form-control, .btn-success, .btn-primary {
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
        <h2>Edit Album</h2>

        <!-- Pesan Kesalahan -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Form Edit Album -->
        <form method="post">
            <div class="form-group">
                <label for="namaalbum">Nama Album</label>
                <input type="text" class="form-control" name="namaalbum" value="<?= htmlspecialchars($album['namaalbum']) ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" required><?= htmlspecialchars($album['deskripsi']) ?></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Simpan Perubahan</button>
        </form>
        <a href="index.php" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>
</html>
