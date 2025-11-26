<?php
session_start(); // Mulai sesi

// Mendapatkan username dan level dari sesi
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Pengguna';
$level = isset($_SESSION['level']) ? $_SESSION['level'] : 'User'; // Asumsikan level default adalah User
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Tambahkan Font Awesome -->
    <style>
        .navbar {
            background-color: white; /* Warna navbar putih */
        }

        .navbar-brand {
            color: #08C2FF;
        }

        .nav-link {
            color: gray; /* Warna teks tautan abu-abu */
            position: relative; /* Membutuhkan posisi relatif untuk efek garis bawah */
        }

        .nav-link:hover {
            color: #227B94; /* Warna teks tautan saat hover */
        }

        /* Garis bawah untuk setiap tautan dengan warna berbeda */
        .nav-link.beranda::after {
            background-color: cyan; /* Warna garis untuk Beranda */
        }
        
        .nav-link.tambah-foto::after {
            background-color: magenta; /* Warna garis untuk Tambah Foto */
        }

        .nav-link.tambah-album::after {
            background-color: lime; /* Warna garis untuk Tambah Album */
        }

        .nav-link.daftar-album::after {
            background-color: orange; /* Warna garis untuk Daftar Album */
        }

        .nav-link.daftar-users::after {
            background-color: purple; /* Warna garis untuk Daftar Users */
        }

        .nav-link::after {
            content: ""; /* Membuat elemen garis bawah */
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px; /* Ketebalan garis */
            width: 100%; /* Panjang garis */
            transform: scaleX(0); /* Memulai dari ukuran 0 */
            transition: transform 0.3s ease; /* Efek transisi */
        }

        .nav-link:hover::after {
            transform: scaleX(1); /* Mengubah ukuran garis saat hover */
        }

        .navbar-text {
            color: gray; /* Warna teks selamat datang abu-abu */
        }

        .welcome-container {
            display: flex;
            align-items: center;
            margin-left: auto; /* Tempatkan ke kanan */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
            <i class="fas fa-film mr-2"></i>
            Galeri-z</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link beranda" href="index.php"><i class="fas fa-home"></i> Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tambah-foto" href="http://localhost/crud/pages/tambah_foto.php"><i class="fas fa-upload"></i> Tambah Foto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tambah-album" href="http://localhost/crud/pages/tambah_album.php"><i class="fas fa-folder-plus"></i> Tambah Album</a>
                    </li>
                    <?php if ($level === 'Admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link daftar-album" href="daftar_album.php"><i class="fas fa-images"></i> Daftar Album</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link daftar-users" href="list_users.php"><i class="fas fa-users"></i> Daftar Users</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="welcome-container">
                    <span class="navbar-text welcome-text">
                        Selamat datang, <?= htmlspecialchars($username) ?>
                    </span>
                    <?php if (isset($_SESSION['username'])): ?>
                        <a class="nav-link" href="http://localhost/crud/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a class="nav-link" href="http://localhost/crud/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
