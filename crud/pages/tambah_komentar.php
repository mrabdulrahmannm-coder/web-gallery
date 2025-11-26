<?php
include('../koneksi.php');  // Memanggil koneksi ke database
session_start();  // Memulai sesi, pastikan pengguna sudah login

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>Anda harus login untuk memberikan komentar.</div>";
    exit;
}

// Cek apakah metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input komentar
    if (empty($_POST['komentar']) || empty($_POST['foto_id']) || empty($_GET['album_id'])) {
        echo "<div class='alert alert-danger'>Semua data harus diisi.</div>";
        exit;
    }

    $foto_id = intval($_POST['foto_id']);  // Mendapatkan foto_id dari form dan mengkonversi ke integer
    $album_id = intval($_GET['album_id']);  // Mendapatkan album_id dari URL dan mengkonversi ke integer
    $user_id = intval($_SESSION['user_id']);  // Mendapatkan user_id dari session
    $komentar = trim($_POST['komentar']);  // Mendapatkan komentar dari form, dan membersihkan input
    $tanggal_komentar = date('Y-m-d H:i:s');  // Menyimpan tanggal dan waktu sekarang

    // Menyimpan komentar ke dalam database
    $query = "INSERT INTO komentarfoto (foto_id, user_id, isikomentar, tanggalkomentar) VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    
    // Cek apakah prepare berhasil
    if (!$stmt) {
        echo "<div class='alert alert-danger'>Kesalahan dalam query: " . $connection->error . "</div>";
        exit;
    }

    // Bind parameter dan eksekusi
    $stmt->bind_param("iiss", $foto_id, $user_id, $komentar, $tanggal_komentar);
    
    if ($stmt->execute()) {
        // Redirect ke halaman daftar foto setelah komentar berhasil ditambahkan
        header("Location: daftar_foto.php?album_id=" . $album_id);
        exit;
    } else {
        echo "<div class='alert alert-danger'>Kesalahan saat menyimpan komentar: " . $stmt->error . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Akses tidak valid.</div>";
}
?>
