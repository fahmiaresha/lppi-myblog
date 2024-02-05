<?php
session_start();
// Pastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once('../koneksi.php');

// Fungsi untuk mendapatkan daftar komentar
function getComments() {
    $conn = connectDB();
    $query = "SELECT * FROM comments";
    $result = $conn->query($query);
    $comments = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $comments;
}

// Fungsi untuk menambah komentar
function addComment($user_id, $post_id, $content) {
    $conn = connectDB();
    $content = $conn->real_escape_string($content);
    $query = "INSERT INTO comments (user_id, post_id, content) VALUES ($user_id, $post_id, '$content')";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Fungsi untuk mengedit komentar
function editComment($comment_id, $content,$post_id) {
    $conn = connectDB();
    $content = $conn->real_escape_string($content);
    $query = "UPDATE comments SET content='$content',post_id='$post_id' WHERE comment_id=$comment_id";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Fungsi untuk menghapus komentar
function deleteComment($comment_id) {
    $conn = connectDB();
    $query = "DELETE FROM comments WHERE comment_id=$comment_id";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Proses Form Tambah Komentar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_comment"])) {
    $user_id = $_POST["user_id"];
    $post_id = $_POST["post_id"];
    $content = $_POST["content"];

    if (addComment($user_id, $post_id, $content)) {
        header("Location: comments.php");
        exit();
    } else {
        $add_comment_error = "Failed to add comment.";
    }
}

// Proses Form Edit Komentar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_comment"])) {
    $comment_id = $_POST["edit_comment_id"];
    $content = $_POST["edit_content"];
    $post_id = $_POST["post_id"];

    if (editComment($comment_id, $content,$post_id)) {
        header("Location: comments.php");
        exit();
    } else {
        $edit_comment_error = "Failed to edit comment.";
    }
}

// Proses Hapus Komentar
if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $comment_id = $_GET["id"];

    if (deleteComment($comment_id)) {
        header("Location: comments.php");
        exit();
    } else {
        $delete_comment_error = "Failed to delete comment.";
    }
}

// Ambil daftar komentar
$comments = getComments();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>

<body>
    <div class="container mt-5">
        <br>
        <div class="card">
            <div class="card-body">
                <h1 class="">Manage Comments</h1>
                <table id="commentsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <?php
                            // Query untuk mendapatkan data user berdasarkan user_id
                            $userQuery = "SELECT username FROM users WHERE user_id = " . $comment['user_id'];
                            $userResult = $conn->query($userQuery);
                            $user = $userResult->fetch_assoc();

                            // Query untuk mendapatkan data post berdasarkan post_id
                            $postQuery = "SELECT title FROM posts WHERE post_id = " . $comment['post_id'];
                            $postResult = $conn->query($postQuery);
                            $post = $postResult->fetch_assoc();
                            ?>
                            <tr>
                                <td><?php echo $comment['comment_id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $post['title']; ?></td>
                                <td><?php echo $comment['content']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editCommentModal<?php echo $comment['comment_id']; ?>">
                                        Edit
                                    </button>
                                    <a href="comments.php?action=delete&id=<?php echo $comment['comment_id']; ?>"
                                        class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <!-- Modal untuk Edit Komentar -->
                            <div class="modal fade" id="editCommentModal<?php echo $comment['comment_id']; ?>" tabindex="-1"
                                aria-labelledby="editCommentModalLabel<?php echo $comment['comment_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCommentModalLabel<?php echo $comment['comment_id']; ?>">
                                                Edit Comment
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Form untuk Edit Komentar -->
                                            <form action="comments.php" method="post">
                                                <input type="hidden" name="edit_comment_id"
                                                    value="<?php echo $comment['comment_id']; ?>">

                                                    <label for="edit_category_id"
                                                            class="form-label">Title</label>
                                                        <select class="form-select" id="post_id"
                                                            name="post_id" required>
                                                            <?php

                                                                $conn = connectDB();
                                                                $query = "SELECT * FROM posts";
                                                                $result = $conn->query($query);

                                                                // Tampilkan data kategori sebagai opsi
                                                                while ($row = $result->fetch_assoc()) {
                                                                    $selected = ($row['post_id'] == $comment['post_id']) ? 'selected' : '';
                                                                    echo '<option value="' . $row['post_id'] . '" ' . $selected . '>' . $row['title'] . '</option>';
                                                                }

                                                                // $conn->close();
                                                                ?>
                                                        </select>
                                                <div class="mb-3">
                                                    <label for="edit_content" class="form-label">Content</label>
                                                    <textarea class="form-control" id="edit_content" name="edit_content"
                                                        rows="4" required><?php echo $comment['content']; ?></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="edit_comment">Save
                                                    Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Modal untuk Tambah Komentar -->
                <div class="modal fade" id="addCommentModal" tabindex="-1" aria-labelledby="addCommentModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCommentModalLabel">Add New Comment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form untuk Tambah Komentar -->
                                <form action="comments.php" method="post">
                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">User</label>
                                        <select class="form-select" id="user_id" name="user_id" required>
                                            <?php
                                           
                                            $conn = connectDB();
                                            $query = "SELECT * FROM users";
                                            $result = $conn->query($query);

                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['user_id'] . '">' . $row['username'] . '</option>';
                                            }
                                            ?>
                                            </select>
                                    </div>
                                    <div class="mb-3">
                                        <!-- <label for="post_id" class="form-label">Post ID</label>
                                        <input type="text" class="form-control" id="post_id" name="post_id" required> -->

                                        <label for="user_id" class="form-label">Title</label>
                                        <select class="form-select" id="post_id" name="post_id" required>
                                            <?php
                                           
                                            $conn = connectDB();
                                            $query = "SELECT * FROM posts";
                                            $result = $conn->query($query);

                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['post_id'] . '">' . $row['title'] . '</option>';
                                            }
                                            ?>
                                            </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content</label>
                                        <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="add_comment">Add Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($add_comment_error)) {
                    echo '<div class="alert alert-danger mt-3" role="alert">' . $add_comment_error . '</div>';
                }
                if (isset($edit_comment_error)) {
                    echo '<div class="alert alert-danger mt-3" role="alert">' . $edit_comment_error . '</div>';
                }
                if (isset($delete_comment_error)) {
                    echo '<div class="alert alert-danger mt-3" role="alert">' . $delete_comment_error . '</div>';
                }
                ?>

                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCommentModal">
                    Add Comment
                </button>
            </div>
        </div>

        <h2 class="mt-4">Manage Post, Categories, and Users</h2>
        <p>Links to manage post, categories, and users go here...</p>

        <a href="../post/post.php" class="btn btn-success">Manage Post</a>
        <a href="../categories/categories.php" class="btn btn-success">Manage Categories</a>
        <a href="../user/users.php" class="btn btn-success">Manage Users</a>
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS CDN -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#commentsTable').DataTable();
        });
    </script>
</body>

</html>
