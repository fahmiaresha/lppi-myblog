<?php
session_start();
require_once('../koneksi.php'); // Menggunakan file koneksi.php

// Fungsi untuk melakukan autentikasi
// Fungsi untuk melakukan autentikasi
function authenticateUser($username, $password) {
    $conn = connectDB();

    // Lindungi dari serangan SQL Injection
    $username = $conn->real_escape_string($username);

    // Query untuk mencari pengguna berdasarkan username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password menggunakan password_verify
        if (password_verify($password, $user['password'])) {
            // Simpan informasi pengguna ke dalam session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            
            // Tutup koneksi dan kembalikan true
            $conn->close();
            return true;
        }
    }

    // Tutup koneksi dan kembalikan false jika autentikasi gagal
    $conn->close();
    return false;
}


// Proses form login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lakukan autentikasi
    if (authenticateUser($username, $password)) {
        // Jika autentikasi berhasil, arahkan ke halaman lain atau lakukan tindakan lain
        header("Location: ../post/post.php");
        exit();
    } else {
        // Jika autentikasi gagal, tampilkan pesan kesalahan atau lakukan tindakan lain
        $_SESSION['error_message'] = "Username atau password salah.";
        header("Location: login.php");  // Redirect kembali ke halaman login
        exit();
    }
}
?>
