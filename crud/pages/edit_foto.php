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

$foto_id = isset($_GET['foto_id']) ? intval($_GET['foto_id']) : null;

if ($foto_id === null) {
    echo "<div class='alert alert-danger'>Foto ID tidak valid.</div>";
    exit;
}

// Mendapatkan data foto berdasarkan foto_id
$query = "SELECT * FROM foto WHERE foto_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $foto_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Foto tidak ditemukan.</div>";
    exit;
}

$foto = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari form
    $judulfoto = $_POST['judulfoto'];
    $deskripsifoto = $_POST['deskripsifoto'];
    $lokasifile = $_FILES['lokasifile']['name'];

    // Validasi apakah judul foto sudah ada
    $check_query = "SELECT COUNT(*) as count FROM foto WHERE judulfoto = ? AND foto_id != ?";
    $check_stmt = $connection->prepare($check_query);
    $check_stmt->bind_param("si", $judulfoto, $foto_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        echo "<div class='alert alert-danger'>Judul foto sudah ada. Silakan gunakan judul lain.</div>";
    } else {
        // Memproses upload file jika ada file yang diunggah
        if ($lokasifile) {
            // Tentukan folder upload
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($lokasifile);
            
            // Memindahkan file yang diunggah
            if (move_uploaded_file($_FILES['lokasifile']['tmp_name'], $target_file)) {
                $update_query = "UPDATE foto SET judulfoto = ?, deskripsifoto = ?, lokasifile = ? WHERE foto_id = ?";
                $stmt_update = $connection->prepare($update_query);
                $stmt_update->bind_param("sssi", $judulfoto, $deskripsifoto, $lokasifile, $foto_id);
            } else {
                echo "<div class='alert alert-danger'>Terjadi kesalahan saat mengunggah foto baru.</div>";
                exit;
            }
        } else {
            // Jika tidak ada file baru, cukup update judul dan deskripsi
            $update_query = "UPDATE foto SET judulfoto = ?, deskripsifoto = ? WHERE foto_id = ?";
            $stmt_update = $connection->prepare($update_query);
            $stmt_update->bind_param("ssi", $judulfoto, $deskripsifoto, $foto_id);
        }

        if ($stmt_update->execute()) {
            echo "<div class='alert alert-success'>Foto berhasil diperbarui.</div>";
        } else {
            echo "<div class='alert alert-danger'>Terjadi kesalahan saat memperbarui foto: " . $connection->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Foto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Foto</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judulfoto">Judul Foto:</label>
                <input type="text" name="judulfoto" id="judulfoto" class="form-control" value="<?= htmlspecialchars($foto['judulfoto']) ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsifoto">Deskripsi Foto:</label>
                <textarea name="deskripsifoto" id="deskripsifoto" class="form-control" required><?= htmlspecialchars($foto['deskripsifoto']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="lokasifile">Unggah Foto Baru:</label>
                <input type="file" name="lokasifile" id="lokasifile" class="form-control">
                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="daftar_foto.php?album_id=<?= $foto['album_id'] ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
