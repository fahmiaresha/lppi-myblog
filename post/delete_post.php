<?php
session_start();

// Pastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: post.php");
    exit();
}

require_once('../koneksi.php');

// Pastikan parameter id tersedia
if (!isset($_GET['id'])) {
    header("Location: post.php");
    exit();
}

$id = $_GET['id'];

// Hapus post dengan ID tertentu
$conn = connectDB();
$query = "DELETE FROM posts WHERE post_id = '$id'";
$result = $conn->query($query);

// Tutup koneksi setelah selesai menggunakan
$conn->close();

// Redirect kembali ke halaman dashboard
header("Location: post.php");
exit();
?>
