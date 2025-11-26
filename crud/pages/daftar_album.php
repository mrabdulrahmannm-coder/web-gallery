<?php
include('../koneksi.php'); // Memanggil koneksi.php
include('header.php'); // Memanggil header.php

// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['user_id']);
$currentUserId = $isLoggedIn ? $_SESSION['user_id'] : null;
$isAdmin = $isLoggedIn && $_SESSION['level'] === 'Admin'; // Cek apakah user adalah admin

// Mendapatkan nilai pencarian dari formulir
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Mendapatkan daftar album dan nama pencipta dari database dengan pencarian
$query = "
    SELECT album.*, user.username 
    FROM album
    JOIN user ON album.user_id = user.user_id 
    WHERE album.namaalbum LIKE ?";  // Menambahkan filter pencarian

$stmt = $connection->prepare($query);
$searchTermLike = '%' . $searchTerm . '%'; // Menambahkan wildcard untuk pencarian
$stmt->bind_param("s", $searchTermLike);
$stmt->execute();
$result = $stmt->get_result();

// Fungsi untuk menyingkat string
function shortenString($string, $length = 20) {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . '...'; // Mengembalikan string yang disingkat
    }
    return $string; // Mengembalikan string asli jika tidak melebihi panjang yang ditentukan
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Album</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Tambahkan Font Awesome -->
</head>
<style>
    .input-group input {
        border-radius: 20px 0 0 20px;
        /* Mengatur border-radius untuk input */
    }

    .input-group .btn {
        border-radius: 0 20px 20px 0;
        /* Mengatur border-radius untuk tombol */
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

    .btn-warning:hover {
        background-color: #e0a800;
        /* Warna hover untuk tombol edit */
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(255, 193, 7, 0.4);
    }

    .btn-warning:active {
        background-color: #c69500;
        /* Warna aktif untuk tombol edit */
        transform: scale(1);
        box-shadow: none;
    }

    .btn-danger:hover {
        background-color: #c82333;
        /* Warna hover untuk tombol hapus */
        transform: scale(1.05);
        box-shadow: 0 6px 15px rgba(220, 53, 69, 0.4);
    }

    .btn-danger:active {
        background-color: #bd2130;
        /* Warna aktif untuk tombol hapus */
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
        <h2>Daftar Album</h2>

        <!-- Form Pencarian -->
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari album..."
                    value="<?= htmlspecialchars($searchTerm) ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <?php if ($isLoggedIn): ?>
            <!-- Tampilkan tombol "Tambah Album" hanya jika pengguna sudah login -->
            <a href="tambah_album.php" class="btn btn-primary mb-3">
                <i class="fas fa-plus-circle"></i> Tambah Album
            </a>
        <?php endif; ?>

        <!-- Tabel Daftar Album -->
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Nama Album</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Dibuat</th>
                    <th>Pencipta</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($album = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars(shortenString($album['namaalbum'], 20)) ?></td> <!-- Menyingkat nama album -->
                        <td><?= htmlspecialchars(shortenString($album['deskripsi'], 50)) ?></td> <!-- Menyingkat deskripsi -->
                        <td><?= htmlspecialchars($album['tanggaldibuat']) ?></td>
                        <td><?= htmlspecialchars($album['username']) ?></td>
                        <td>
                            <a href="daftar_foto.php?album_id=<?= $album['album_id'] ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-eye"></i> Lihat Foto
                            </a>

                            <?php if ($isLoggedIn && ($currentUserId == $album['user_id'] || $isAdmin)): ?>
                                <!-- Tampilkan tombol edit dan hapus hanya jika pengguna login dan merupakan pemilik album atau admin -->
                                <a href="edit_album.php?id=<?= $album['album_id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="hapus_album.php?id=<?= $album['album_id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus album ini?');">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
