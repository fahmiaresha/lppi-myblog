<?php
session_start();

// Pastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: post.php");
    exit();
}

require_once('../koneksi.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['post_id'];
    $edit_title = $_POST['edit_title'];
    $edit_content = $_POST['edit_content'];
    $edit_image_url = $_POST['edit_image_url'];
    $edit_category_id = $_POST['edit_category_id'];

    // Lakukan validasi atau sanitasi data sesuai kebutuhan

    $conn = connectDB();

    // Lindungi dari serangan SQL Injection
    $post_id = $conn->real_escape_string($post_id);
    $edit_title = $conn->real_escape_string($edit_title);
    $edit_content = $conn->real_escape_string($edit_content);
    $edit_image_url = $conn->real_escape_string($edit_image_url);
    $edit_category_id = $conn->real_escape_string($edit_category_id);

    // Query untuk mengedit posting
    $query = "UPDATE posts SET title='$edit_title', content='$edit_content', image_url='$edit_image_url', category_id='$edit_category_id' WHERE post_id='$post_id'";

    if ($conn->query($query) === TRUE) {
        // Jika pengeditan berhasil, arahkan kembali ke halaman dashboard
        header("Location: post.php");
        exit();
    } else {
        // Jika terjadi kesalahan, tampilkan pesan kesalahan
        echo "Error: " . $query . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
