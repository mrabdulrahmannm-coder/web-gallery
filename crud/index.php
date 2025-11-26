<?php
include('koneksi.php'); // Memanggil koneksi.php
include('pages/header.php'); // Memanggil header.php

// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['user_id']);
$currentUserId = $isLoggedIn ? $_SESSION['user_id'] : null;
$isAdmin = $isLoggedIn && $_SESSION['level'] === 'Admin'; // Cek apakah user adalah admin

// Mendapatkan nilai pencarian dari formulir
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Mendapatkan daftar album dari database dengan pencarian
// Mendapatkan nilai pencarian dari formulir
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Atur batas jumlah album per halaman
$limit = 4; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Mendapatkan daftar album dari database dengan pencarian dan paginasi
$query = "
    SELECT album.* 
    FROM album 
    WHERE album.namaalbum LIKE ?
    LIMIT ?, ?";
$stmt = $connection->prepare($query);
$searchTermLike = '%' . $searchTerm . '%'; // Menambahkan wildcard untuk pencarian
$stmt->bind_param("sii", $searchTermLike, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Menghitung total album untuk paginasi
$count_query = "
    SELECT COUNT(*) AS total
    FROM album 
    WHERE album.namaalbum LIKE ?";
$count_stmt = $connection->prepare($count_query);
$count_stmt->bind_param("s", $searchTermLike);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$totalAlbums = $count_result->fetch_assoc()['total'];
$totalPages = ceil($totalAlbums / $limit);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Album</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Warna latar terang */
            font-family: Arial, sans-serif;
            color: #212529; /* Warna teks gelap */
        }


          h2 {
        font-weight: bold;
        color: #227B94;
    }

        .card {
            cursor: pointer;
            position: relative;
            overflow: hidden;
            background: #ffffff; /* Background terang untuk card */
            transition: transform 0.2s;
            max-width: 300px; /* Mengatur lebar maksimum card */
            margin: 0 auto; /* Mengatur margin agar card berada di tengah */
        }

        .card:hover {
            transform: scale(1.01);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            /* Bayangan lebih ringan */
        }

        .card::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: #007bff;
            /* Warna garis biru */
            transition: width 0.4s ease, left 0.4s ease;
        }

        .card:hover::before {
            width: 100%;
            left: 0;
        }


        .card img {
            height: 150px; /* Mengubah tinggi gambar menjadi lebih kecil */
            object-fit: cover;
        }

        .card-body {
            position: relative; /* Mengatur posisi relatif untuk penempatan elemen di dalamnya */
        }

        .card-footer {
            position: absolute; /* Mengatur posisi absolut */
            bottom: 10px; /* Jarak dari bawah */
            right: 10px; /* Jarak dari kanan */
            background-color: rgba(255, 255, 255, 0.8); /* Latar belakang putih transparan */
            padding: 5px;
            border-radius: 5px; /* Sudut membulat */
        }

        .tm-container-small { max-width: 1050px; }
        .tm-bg-gray { background-color: #EEEEEE; }
        .tm-footer-links li {
    list-style: none;
    margin-bottom: 5px;    
}

.tm-footer-links li a { color: #999999; }
.tm-footer-links li a:hover { color: #009999; }
.tm-footer { font-size: 0.95rem; }
.tm-footer-title { font-size: 1.4rem; }
.tm-social-links li {
    list-style: none;
    margin-right: 15px;
}

.tm-social-links li:last-child { margin-right: 0; }

.tm-social-links li a  {
    color: #999999;
    width: 44px;
    height: 44px;
    display: flex;
    background-color: #fff;
    align-items: center;
    justify-content: center;
}

.tm-social-links li a:hover {
    color: #fff;
    background-color: #009999;
}
.tm-text-primary { color: #009999; }
.tm-footer-links li {
    list-style: none;
    margin-bottom: 5px;    
}

.tm-footer-links li a { color: #999999; }
.tm-footer-links li a:hover { color: #009999; }

    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <!-- Form Pencarian -->
        <div class="tm-hero d-flex justify-content-center align-items-center" style="background-image: url('img/hero.jpg'); background-size: cover; background-position: center; min-height: 200px;">
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control rounded-start" placeholder="Cari album..." value="<?= htmlspecialchars($searchTerm) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary rounded-end" type="submit">
                            <i class="fas fa-search"></i> <!-- Ikon pencarian -->
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <h2 class="col-6 mt-5">Daftar Album</h2>

        <div class="row justify-content-center mx-2">
            <?php while ($album = $result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card" onclick="window.location.href='pages/daftar_foto.php?album_id=<?= $album['album_id'] ?>'">
                        <?php
                        // Mengambil foto pertama di album sebagai cover
                        $album_id = $album['album_id'];
                        $foto_query = "SELECT lokasifile, tanggalunggah FROM foto WHERE album_id = ? ORDER BY tanggalunggah ASC LIMIT 1";
                        $stmt_foto = $connection->prepare($foto_query);
                        $stmt_foto->bind_param("i", $album_id);
                        $stmt_foto->execute();
                        $foto_result = $stmt_foto->get_result();
                        $foto = $foto_result->fetch_assoc();

                        // Jika ada foto, gunakan sebagai cover, jika tidak gunakan gambar default
                        $imagePath = $foto ? "uploads/" . htmlspecialchars($foto['lokasifile']) : "cover-kosong/kosong.jpg"; // Gambar default jika tidak ada foto
                        // Ambil tanggal foto dan format hanya tanggal
                        $tanggalFoto = $foto ? date('Y-m-d', strtotime($foto['tanggalunggah'])) : ''; // Ambil hanya tanggal
                        ?>
                        <!-- Menampilkan gambar album -->
                        <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($album['namaalbum']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-folder-open"></i> <?= htmlspecialchars($album['namaalbum']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($album['deskripsi']) ?></p>
                            <!-- Menampilkan tanggal foto di bagian bawah kanan -->
                            <div class="card-footer">
                                <small class="text-muted"><?= $tanggalFoto ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

     <!-- Paginasi -->
     <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchTerm) ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($searchTerm) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchTerm) ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>


    <footer class="tm-bg-gray pt-5 pb-3 tm-text-gray tm-footer">
    <div class="container-fluid tm-container-small">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-12 px-5 mb-5">
                <h3 class="tm-text-primary mb-4 tm-footer-title">Tentang Gallery-Z</h3>
                <p>Gallery-Z adalah platform galeri foto yang memungkinkan pengguna untuk mengunggah, membagikan, dan mengeksplorasi foto-foto luar biasa dari seluruh dunia. Kami berkomitmen untuk memberikan pengalaman berbagi foto yang mudah dan menyenangkan untuk semua pengguna.</p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 px-5 mb-5">
                <h3 class="tm-text-primary mb-4 tm-footer-title">Tautan Penting</h3>
                <ul class="tm-footer-links pl-0">
                    <li><a href="#">Bergabung dengan Kami</a></li>
                    <li><a href="#">Dukungan</a></li>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12 px-5 mb-5">
                <ul class="tm-social-links d-flex justify-content-end pl-0 mb-5">
                    <li class="mb-2"><a href="https://facebook.com"><i class="fab fa-facebook"></i></a></li>
                    <li class="mb-2"><a href="https://twitter.com"><i class="fab fa-twitter"></i></a></li>
                    <li class="mb-2"><a href="https://instagram.com"><i class="fab fa-instagram"></i></a></li>
                    <li class="mb-2"><a href="https://pinterest.com"><i class="fab fa-pinterest"></i></a></li>
                </ul>
                <a href="#" class="tm-text-gray text-right d-block mb-2">Syarat dan Ketentuan</a>
                <a href="#" class="tm-text-gray text-right d-block">Kebijakan Privasi</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-7 col-12 px-5 mb-3">
                Copyright &copy; 2024 Gallery-Z. Semua hak dilindungi.
            </div>
            <div class="col-lg-4 col-md-5 col-12 px-5 text-right">
                Dirancang oleh <a href="http://localhost/ABSEN/home/tmpl.php?id=22" class="tm-text-gray" rel="sponsored" target="_parent">Nanasi</a>
            </div>
        </div>
    </div>
</footer>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
