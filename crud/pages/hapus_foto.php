<?php
session_start();
include('../koneksi.php');

// Aktifkan error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'Admin') {
    echo "Akses ditolak.";
    exit;
}

// Cek apakah foto_id sudah diterima
if (isset($_POST['foto_id']) && isset($_POST['lokasifile'])) {
    $foto_id = intval($_POST['foto_id']);
    $lokasi_file = $_POST['lokasifile'];

    // Hapus komentar yang terkait dengan foto terlebih dahulu
    $delete_comments_stmt = $connection->prepare("DELETE FROM komentarfoto WHERE foto_id = ?");
    $delete_comments_stmt->bind_param("i", $foto_id);
    $delete_comments_stmt->execute();

    // Hapus like yang terkait dengan foto terlebih dahulu
    $delete_likes_stmt = $connection->prepare("DELETE FROM likefoto WHERE foto_id = ?");
    $delete_likes_stmt->bind_param("i", $foto_id);
    $delete_likes_stmt->execute();

    // Query untuk menghapus foto dari database
    $stmt = $connection->prepare("DELETE FROM foto WHERE foto_id = ?");
    $stmt->bind_param("i", $foto_id);

    // Eksekusi query dan tangkap error jika ada
    if ($stmt->execute()) {
        // Hapus file dari folder uploads
        $file_path = "../uploads/" . $lokasi_file;
        if (file_exists($file_path)) {
            unlink($file_path); // Menghapus file
        } else {
            echo "File tidak ditemukan: " . $file_path;
        }

        // Redirect kembali ke halaman daftar foto setelah berhasil dihapus
        echo "<script>
                alert('Foto berhasil dihapus!');
                window.location.href = 'index.php';
              </script>";
        exit;
    } else {
        echo "Gagal menghapus foto: " . $stmt->error; // Tampilkan error dari query
    }
} else {
    echo "Foto tidak ditemukan.";
}
?>