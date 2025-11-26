<?php
include('../koneksi.php'); 
session_start(); // Pastikan session dimulai

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

// Hapus pengguna berdasarkan ID
$query = "DELETE FROM user WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $user_id);
if ($stmt->execute()) {
    echo "<div class='alert alert-success'>Pengguna berhasil dihapus.</div>";
} else {
    echo "<div class='alert alert-danger'>Kesalahan saat menghapus pengguna.</div>";
}

// Redirect kembali ke daftar pengguna setelah proses selesai
header("Location: list_users.php");
exit;
?>
