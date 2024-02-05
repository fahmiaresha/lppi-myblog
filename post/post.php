<?php
session_start();
// Pastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once('../koneksi.php');

// Fungsi untuk mendapatkan daftar posting
function getPosts() {
    $conn = connectDB();
    $query = "SELECT * FROM posts";
    $result = $conn->query($query);
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    return $posts;
}

$posts = getPosts();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <div class="card mt-4">
            <div class="card-body">
                <h2>Posts</h2>
                <table id="postsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Image URL</th>
                            <th>Category Name</th>
                            <th>Username</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($posts as $post): ?>
                            <?php
                            // Query untuk mendapatkan data kategori berdasarkan category_id
                            $categoryQuery = "SELECT category_name FROM categories WHERE category_id = " . $post['category_id'];
                            $categoryResult = $conn->query($categoryQuery);
                            $category = $categoryResult->fetch_assoc();

                            // Query untuk mendapatkan data user berdasarkan user_id
                            $userQuery = "SELECT username FROM users WHERE user_id = " . $post['user_id'];
                            $userResult = $conn->query($userQuery);
                            $user = $userResult->fetch_assoc();
                            ?>
                        <tr>
                            
                            <td><?php echo $post['post_id']; ?></td>
                            <td><?php echo $post['title']; ?></td>
                            <td><?php echo $post['content']; ?></td>
                            <td><?php echo $post['image_url']; ?></td>
                            <td><?php echo $category['category_name']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $post['created_at']; ?></td>
                            <td>

                                <!-- Tombol untuk membuka modal edit -->
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editPostModal<?php echo $post['post_id']; ?>">
                                    Edit
                                </button>

                                <!-- Modal untuk Edit Post -->
                                <div class="modal fade" id="editPostModal<?php echo $post['post_id']; ?>" tabindex="-1"
                                    aria-labelledby="editPostModalLabel<?php echo $post['post_id']; ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="editPostModalLabel<?php echo $post['post_id']; ?>">Edit Post
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Form untuk Edit Post -->
                                                <form action="edit_post.php" method="post">
                                                    <input type="hidden" name="post_id"
                                                        value="<?php echo $post['post_id']; ?>">

                                                    <div class="mb-3">
                                                        <label for="edit_title" class="form-label">Title</label>
                                                        <input type="text" class="form-control" id="edit_title"
                                                            name="edit_title" value="<?php echo $post['title']; ?>"
                                                            required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_content" class="form-label">Content</label>
                                                        <textarea class="form-control" id="edit_content"
                                                            name="edit_content" rows="3"
                                                            required><?php echo $post['content']; ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_image_url" class="form-label">Image URL</label>
                                                        <input type="text" class="form-control" id="edit_image_url"
                                                            name="edit_image_url"
                                                            value="<?php echo $post['image_url']; ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_category_id"
                                                            class="form-label">Category</label>
                                                        <select class="form-select" id="edit_category_id"
                                                            name="edit_category_id" required>
                                                            <?php
                      // Menampilkan daftar kategori yang terdapat Ambil data kategori dari database
                                                                $conn = connectDB();
                                                                $query = "SELECT * FROM categories";
                                                                $result = $conn->query($query);

                                                                // Tampilkan data kategori sebagai opsi
                                                                while ($row = $result->fetch_assoc()) {
                                                                    $selected = ($row['category_id'] == $post['category_id']) ? 'selected' : '';
                                                                    echo '<option value="' . $row['category_id'] . '" ' . $selected . '>' . $row['category_name'] . '</option>';
                                                                }

                                                                // $conn->close();
                                                                ?>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deletePostModal<?php echo $post['post_id']; ?>">
                                    Delete
                                </button>

                                <div class="modal fade" id="deletePostModal<?php echo $post['post_id']; ?>"
                                    tabindex="-1" aria-labelledby="deletePostModalLabel<?php echo $post['post_id']; ?>"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="deletePostModalLabel<?php echo $post['post_id']; ?>">Confirm
                                                    Deletion</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this post?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <a href="delete_post.php?id=<?php echo $post['post_id']; ?>"
                                                    class="btn btn-danger">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


                <!-- Tambahkan button untuk Add New Post di dalam card body -->
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                    data-bs-target="#addPostModal">
                    Add New Post
                </button>

                <!-- Tabel dan kontennya tetap sama seperti sebelumnya -->

                <!-- Modal untuk Add New Post -->
                <div class="modal fade" id="addPostModal" tabindex="-1" aria-labelledby="addPostModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addPostModalLabel">Add New Post</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form untuk Add New Post -->
                                <form action="add_post.php" method="post">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content</label>
                                        <textarea class="form-control" id="content" name="content" rows="3"
                                            required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image_url" class="form-label">Image URL</label>
                                        <input type="text" class="form-control" id="image_url" name="image_url"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <?php
                                            // Ambil data kategori dari database
                                            $conn = connectDB();
                                            $query = "SELECT * FROM categories";
                                            $result = $conn->query($query);

                                            // Tampilkan data kategori sebagai opsi
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['category_id'] . '">' . $row['category_name'] . '</option>';
                                            }

                                            // $conn->close();
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <h2 class="mt-4">Manage Categories, Users, and Comments</h2>
        <p>Links to manage categories, users, and comments go here...</p>

        <a href="../categories/categories.php" class="btn btn-success">Manage Categories</a>
        <a href="../user/users.php" class="btn btn-success">Manage Users</a>
        <a href="../comment/comments.php" class="btn btn-success">Manage Comments</a>
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
            $('#postsTable').DataTable();
        });
    </script>
</body>

</html>
