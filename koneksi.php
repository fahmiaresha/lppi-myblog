<?php

$GLOBALS['conn'] = connectDB();
// Fungsi koneksi ke database
function connectDB() {
    $host = "localhost"; // Ganti dengan host Anda
    $username = "root"; // Ganti dengan username Anda
    $password = ""; // Ganti dengan password Anda
    $database = "my_blog"; // Ganti dengan nama database Anda

    $conn = new mysqli($host, $username, $password, $database);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }

    return $conn;
}
?>
