<?php
// Konfigurasi koneksi database
$connection = mysqli_connect('localhost', 'root', '', 'gallery');

// Cek koneksi
if (!$connection) {
    die("Koneksi gagal: "  . mysqli_connect_error());
}
?>
