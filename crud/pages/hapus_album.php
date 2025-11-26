<?php
session_start();
include('../koneksi.php'); // Memanggil koneksi database

// Cek apakah user sudah login dan memiliki akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'Admin') {
    echo "<script>
            alert('Anda tidak memiliki akses ke halaman ini.');
            window.location.href = 'index.php';
          </script>";
    exit;
}

// Cek apakah album ID ada di URL
if (isset($_GET['id'])) {
    $albumId = intval($_GET['id']); // Mengamankan input

    // Mulai transaksi
    $connection->begin_transaction();

    try {
        // Hapus semua foto dalam album
        $deletePhotosQuery = "DELETE FROM foto WHERE album_id = ?";
        $stmt = $connection->prepare($deletePhotosQuery);
        $stmt->bind_param("i", $albumId);
        $stmt->execute();

        // Periksa apakah foto berhasil dihapus
        if ($stmt->affected_rows >= 0) { // Dapatkan 0 jika tidak ada foto yang dihapus, ini valid
            // Hapus album setelah foto dihapus
            $deleteAlbumQuery = "DELETE FROM album WHERE album_id = ?";
            $stmt = $connection->prepare($deleteAlbumQuery);
            $stmt->bind_param("i", $albumId);
            $stmt->execute();

            // Periksa apakah album berhasil dihapus
            if ($stmt->affected_rows > 0) {
                // Commit transaksi jika berhasil
                $connection->commit();
                echo "<script>
                        alert('Album berhasil dihapus.');
                        window.location.href = 'index.php';
                      </script>";
            } else {
                throw new Exception("Album tidak ditemukan atau sudah dihapus sebelumnya.");
            }
        } else {
            throw new Exception("Foto dalam album tidak ditemukan.");
        }
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $connection->rollback();
        echo "<script>
                alert('Terjadi kesalahan saat menghapus album: " . $e->getMessage() . "');
                window.location.href = 'index.php';
              </script>";
    }
} else {
    echo "<script>
            alert('ID album tidak ditemukan.');
            window.location.href = 'index.php';
          </script>";
    exit;
}
?>
