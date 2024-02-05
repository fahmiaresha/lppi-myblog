<?php
require_once('koneksi.php');

// Ambil data blog dari database
$blogData = fetchDataFromDatabase("SELECT posts.*, categories.category_name, users.username 
                                    FROM posts 
                                    JOIN categories ON posts.category_id = categories.category_id 
                                    JOIN users ON posts.user_id = users.user_id");

// Ambil data kategori blog dari database
$categoryData = fetchDataFromDatabase("SELECT * FROM categories");

// Ambil data komentar dari database
$commentData = fetchDataFromDatabase("SELECT comments.*, users.username 
                                       FROM comments 
                                       JOIN users ON comments.user_id = users.user_id");

// Ambil data pengguna dari database
$userData = fetchDataFromDatabase("SELECT * FROM users");

function fetchDataFromDatabase($query) {
    global $conn;

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query($query);
    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    return $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Awesome Blog - M.Fahmi Aresha</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            background-color: #f8f9fa;
        }
        .blog-section {
            margin-top: 50px;
        }
        .blog-card {
            margin-bottom: 30px;
        }
        .comment-card {
            margin-bottom: 20px;
        }
        .sidebar {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">My Awesome Blog - M.Fahmi Aresha</a>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login/login.php">Login Admin</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Blog Section -->
<section class="blog-section">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h2>Latest Blogs</h2>
                <?php foreach ($blogData as $blog): ?>
                    <div class="card blog-card">
                        <img src="<?php echo $blog['image_url']; ?>" class="card-img-top" alt="Blog Image">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $blog['title']; ?></h3>
                            <p class="card-text"><?php echo $blog['content']; ?></p>
                            <p>Category: <?php echo $blog['category_name']; ?></p>
                            <p>Author: <?php echo $blog['username']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-4">
                <div class="sidebar">
                    <h2>Categories</h2>
                    <ul class="list-group">
                        <?php foreach ($categoryData as $category): ?>
                            <li class="list-group-item"><?php echo $category['category_name']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="sidebar">
                    <h2>Latest Comments</h2>
                    <?php
                    foreach ($commentData as $comment):
                        // Find the post associated with this comment
                        $postId = $comment['post_id'];
                        $associatedPost = array_filter($blogData, function ($post) use ($postId) {
                            return $post['post_id'] == $postId;
                        });

                        // Display comment details along with the associated post title
                        foreach ($associatedPost as $post):
                    ?>
                            <div class="card comment-card">
                                <div class="card-body">
                                    <h5 class="card-title">User Commented: <?php echo $comment['username']; ?></h5>
                                    <p class="card-text"><?php echo $comment['content']; ?></p>
                                    <p class="card-title">Title: <?php echo $post['title']; ?></p>
                                </div>
                            </div>
                    <?php
                        endforeach;
                    endforeach;
                    ?>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
