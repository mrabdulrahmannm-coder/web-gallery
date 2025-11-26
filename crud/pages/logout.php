<?php
session_start(); // Memulai sesi

// Menghapus semua session variables
$_SESSION = array(); // Mengosongkan semua variabel sesi

// Menghancurkan sesi
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Menghancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: ../index.php");
exit;
?>
