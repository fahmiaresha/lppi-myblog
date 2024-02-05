<?php
session_start();

// Pastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

require_once('../koneksi.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = $_POST['image_url'];
    $category_id = $_POST['category_id']; // Ambil category_id dari formulir

    // Ambil user_id dari sesi pengguna yang telah login
    $user_id = $_SESSION['user_id'];

    // Lakukan validasi atau sanitasi data sesuai kebutuhan
    
    $conn = connectDB();

    // Lindungi dari serangan SQL Injection
    $title = $conn->real_escape_string($title);
    $content = $conn->real_escape_string($content);
    $image_url = $conn->real_escape_string($image_url);
    $user_id = $conn->real_escape_string($user_id);
    $category_id = $conn->real_escape_string($category_id);

    // Query untuk menambahkan posting baru
    $query = "INSERT INTO posts (title, content, image_url, category_id, user_id) VALUES ('$title', '$content', '$image_url', '$category_id', '$user_id')";

    if ($conn->query($query) === TRUE) {
        // Jika penambahan berhasil, arahkan kembali ke halaman dashboard
        header("Location: post.php");
        exit();
    } else {
        // Jika terjadi kesalahan, tampilkan pesan kesalahan
        echo "Error: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
