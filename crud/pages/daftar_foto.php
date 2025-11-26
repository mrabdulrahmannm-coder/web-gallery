<?php
include('../koneksi.php'); // Memanggil koneksi.php
include('header.php'); // Memanggil header.php

// Mendapatkan ID album dari URL
$album_id = isset($_GET['album_id']) ? intval($_GET['album_id']) : null;

if ($album_id === null) {
    echo "<div class='alert alert-danger'>Album ID tidak valid.</div>";
    exit;
}

// Menangani pencarian
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Mendapatkan daftar foto berdasarkan album_id dengan pencarian
$stmt = $connection->prepare("SELECT * FROM foto WHERE album_id = ? AND (judulfoto LIKE ? OR deskripsifoto LIKE ?)");
$search_param = '%' . $search_query . '%';
$stmt->bind_param("iss", $album_id, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Tidak ada foto ditemukan untuk album ini.</div>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Foto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/df_foto.css">
</head>
<style>
    body {
    background-color: #f7f7f7;
}

.container {
    margin-top: 20px;
}

.comment-container {
    max-height: 150px;
    /* Ubah sesuai kebutuhan Anda */
    overflow-y: auto;
    /* Menampilkan scrollbar jika diperlukan */
    border: 1px solid #ddd;
    /* Garis batas untuk komentar */
    padding: 10px;
    /* Ruang di dalam kontainer */
    border-radius: 5px;
    /* Sudut melengkung */
    background-color: #f9f9f9;
    /* Warna latar belakang */
}

.photo-item {
    display: flex;
    margin-bottom: 20px;
    padding: 15px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    cursor: pointer;
}

.photo-item img {
    max-width: 250px;
    border-radius: 10px;
    margin-right: 15px;
    object-fit: cover;
}

.detail-container {
    flex-grow: 1;
    padding: 10px;
}

.comment {
    max-height: 100px;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.comment.expanded {
    max-height: 500px;
}

.like-form .btn {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.like-form .btn:hover {
    transform: scale(1.01);
    box-shadow: 0 6px 15px rgba(255, 0, 0, 0.3);
    /* Bayangan merah untuk tombol Like */
}

.btn-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
}
</style>
<body>
    <div class="container">

     <!-- Form Pencarian -->
     <form method="post" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari foto..." value="<?= htmlspecialchars($search_query) ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="photo-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php if (isset($_SESSION['level']) && $_SESSION['level'] === 'Admin'): ?>
                    
                        <a href="edit_foto.php?foto_id=<?= $row['foto_id'] ?>" class="btn btn-sm btn-warning mb-1">
                            <i class="fas fa-edit"></i> Edit Foto
                        </a>
                        <form action="hapus_foto.php" method="POST" style="display:inline;">
                            <input type="hidden" name="foto_id" value="<?= $row['foto_id'] ?>">
                            <input type="hidden" name="lokasifile" value="<?= $row['lokasifile'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger mb-1"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?');">
                                <i class="fas fa-trash"></i> Hapus Foto
                            </button>
                        </form>
                   
                <?php endif; ?>


                <div class="photo-item">
                    <img src="../uploads/<?= htmlspecialchars($row['lokasifile']) ?>"
                        alt="<?= htmlspecialchars($row['judulfoto']) ?>"
                        onclick="openModal('<?= htmlspecialchars($row['lokasifile']) ?>', '<?= htmlspecialchars($row['judulfoto']) ?>', '<?= htmlspecialchars($row['deskripsifoto']) ?>')">
                    <div class="detail-container">
                        <h5><?= htmlspecialchars($row['judulfoto']) ?></h5>
                        <p><?= htmlspecialchars($row['deskripsifoto']) ?></p>

                        <?php
                        // Menghitung total likes
                        $foto_id = $row['foto_id'];
                        $like_query = "SELECT COUNT(*) AS total_likes FROM likefoto WHERE foto_id = ?";
                        $like_stmt = $connection->prepare($like_query);
                        $like_stmt->bind_param("i", $foto_id);
                        $like_stmt->execute();
                        $like_data = $like_stmt->get_result()->fetch_assoc();
                        $total_likes = $like_data['total_likes'];

                        if (isset($_SESSION['user_id'])) {
                            $user_id = $_SESSION['user_id'];
                            $check_like_query = "SELECT COUNT(*) AS total_likes FROM likefoto WHERE foto_id = ? AND user_id = ?";
                            $check_like_stmt = $connection->prepare($check_like_query);
                            $check_like_stmt->bind_param("ii", $foto_id, $user_id);
                            $check_like_stmt->execute();
                            $has_liked = $check_like_stmt->get_result()->fetch_assoc()['total_likes'] > 0;
                            ?>
                            <form action="tambah_like.php" method="POST" class="like-form mb-1">
                                <input type="hidden" name="foto_id" value="<?= $row['foto_id'] ?>">
                                <input type="hidden" name="album_id" value="<?= $album_id ?>">
                                <button type="submit" class="btn <?= $has_liked ? 'btn-warning' : 'btn-danger' ?>">
                                    <i class="fas <?= $has_liked ? 'fa-thumbs-down' : 'fa-heart' ?>"></i>
                                </button>
                                <span><?= $total_likes ?> Likes</span>
                            </form>
                            <?php
                        } else {
                            echo "<p>Anda harus <a href='../login.php'>login</a> untuk menyukai foto ini.</p>";
                        }

                        // Menampilkan komentar
                        $komentar_query = "SELECT k.isikomentar, k.tanggalkomentar, u.username FROM komentarfoto k JOIN user u ON k.user_id = u.user_id WHERE k.foto_id = ? ORDER BY k.tanggalkomentar DESC";
                        $komentar_stmt = $connection->prepare($komentar_query);
                        $komentar_stmt->bind_param("i", $row['foto_id']);
                        $komentar_stmt->execute();
                        $komentar_result = $komentar_stmt->get_result();

                        if ($komentar_result->num_rows > 0) {
                            echo "<div class='comment-container'>";
                            echo "<h5 class='mt-4'>Komentar:</h5>";
                            while ($komentar_row = $komentar_result->fetch_assoc()) {
                                echo "<div class='comment'>";
                                echo "<strong>" . htmlspecialchars($komentar_row['username']) . "</strong> <small class='text-muted'>" . htmlspecialchars($komentar_row['tanggalkomentar']) . "</small>";
                                echo "<p>" . htmlspecialchars($komentar_row['isikomentar']) . "</p>";
                                echo "</div>";
                            }
                            echo "</div>";
                        } else {
                            echo "<div class='alert alert-warning'>Belum ada komentar.</div>";
                        }
                        if (isset($_SESSION['user_id'])) {
                            ?>
                            <form action="tambah_komentar.php?album_id=<?= $album_id ?>" method="POST">
                                <input type="hidden" name="foto_id" value="<?= $row['foto_id'] ?>">
                                <div class="form-group mt-3">
                                    <label for="komentar">Tulis Komentar:</label>
                                    <input type="text" class="form-control" name="komentar" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Kirim Komentar</button>
                            </form>
                            <?php
                        } else {
                            echo "<p>Anda harus <a href='../login.php'>login</a> untuk mengomentari foto ini.</p>";
                        }
                        ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal untuk melihat foto -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Detail Foto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalPhoto" class="img-fluid" alt="Foto">
                    <h5 id="modalTitle" class="mt-3"></h5>
                    <p id="modalDescription"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="printButton"
                        onclick="return confirm('yakin ingin download');">Download</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(photoSrc, title, description) {
            document.getElementById('modalPhoto').src = '../uploads/' + photoSrc;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalDescription').textContent = description;
            $('#photoModal').modal('show');
        }

        document.getElementById('printButton').addEventListener('click', function () {
            const modalPhoto = document.getElementById('modalPhoto').src;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print Foto</title></head><body>');
            printWindow.document.write('<h2>Foto yang dicetak</h2>');
            printWindow.document.write('<img src="' + modalPhoto + '" style="width: 100%; height: auto;">');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>