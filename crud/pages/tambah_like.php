<?php
include('../koneksi.php'); // Memanggil koneksi.php
session_start();

$user_id = $_SESSION['user_id']; // Ambil user_id dari session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foto_id = $_POST['foto_id'];

    // Memeriksa apakah like sudah ada
    $check_like_query = "SELECT COUNT(*) AS total_likes FROM likefoto WHERE foto_id = ? AND user_id = ?";
    $check_like_stmt = $connection->prepare($check_like_query);
    $check_like_stmt->bind_param("ii", $foto_id, $user_id);
    $check_like_stmt->execute();
    $check_like_result = $check_like_stmt->get_result();
    $check_like_data = $check_like_result->fetch_assoc();

    if ($check_like_data['total_likes'] > 0) {
        // Jika sudah like, maka unlike
        $delete_like_query = "DELETE FROM likefoto WHERE foto_id = ? AND user_id = ?";
        $delete_like_stmt = $connection->prepare($delete_like_query);
        $delete_like_stmt->bind_param("ii", $foto_id, $user_id);
        $delete_like_stmt->execute();
    } else {
        // Jika belum like, maka like
        $insert_like_query = "INSERT INTO likefoto (foto_id, user_id, tanggallike) VALUES (?, ?, NOW())";
        $insert_like_stmt = $connection->prepare($insert_like_query);
        $insert_like_stmt->bind_param("ii", $foto_id, $user_id);
        $insert_like_stmt->execute();
    }

    // Redirect kembali ke halaman daftar foto
    header("Location: " . $_SERVER['HTTP_REFERER']); // Kembali ke halaman sebelumnya
    exit;
}
?>
