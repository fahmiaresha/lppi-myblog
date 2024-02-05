<?php
session_start();
// Pastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once('../koneksi.php');

// Fungsi untuk mendapatkan daftar kategori
function getCategories() {
    $conn = connectDB();
    $query = "SELECT * FROM categories";
    $result = $conn->query($query);
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $categories;
}

// Fungsi untuk menambah kategori
function addCategory($category_name) {
    $conn = connectDB();
    $category_name = $conn->real_escape_string($category_name);
    $query = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Fungsi untuk mengedit kategori
function editCategory($category_id, $category_name) {
    $conn = connectDB();
    $category_name = $conn->real_escape_string($category_name);
    $query = "UPDATE categories SET category_name='$category_name' WHERE category_id=$category_id";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Fungsi untuk menghapus kategori
function deleteCategory($category_id) {
    $conn = connectDB();
    $query = "DELETE FROM categories WHERE category_id=$category_id";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Proses Form Tambah Kategori
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_category"])) {
    $category_name = $_POST["category_name"];

    if (addCategory($category_name)) {
        header("Location: categories.php");
        exit();
    } else {
        $add_category_error = "Failed to add category.";
    }
}

// Proses Form Edit Kategori
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_category"])) {
    $category_id = $_POST["edit_category_id"];
    $category_name = $_POST["edit_category_name"];

    if (editCategory($category_id, $category_name)) {
        header("Location: categories.php");
        exit();
    } else {
        $edit_category_error = "Failed to edit category.";
    }
}

// Proses Hapus Kategori
if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $category_id = $_GET["id"];

    if (deleteCategory($category_id)) {
        header("Location: categories.php");
        exit();
    } else {
        $delete_category_error = "Failed to delete category.";
    }
}

// Ambil daftar kategori
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
            <h1 class="">Manage Categories</h1>
        <table id="categoriesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['category_id']; ?></td>
                        <td><?php echo $category['category_name']; ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editCategoryModal<?php echo $category['category_id']; ?>">
                                Edit
                            </button>
                            <a href="categories.php?action=delete&id=<?php echo $category['category_id']; ?>"
                                class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                    <!-- Modal untuk Edit Kategori -->
                    <div class="modal fade" id="editCategoryModal<?php echo $category['category_id']; ?>" tabindex="-1"
                        aria-labelledby="editCategoryModalLabel<?php echo $category['category_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCategoryModalLabel<?php echo $category['category_id']; ?>">
                                        Edit Category
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form untuk Edit Kategori -->
                                    <form action="categories.php" method="post">
                                        <input type="hidden" name="edit_category_id"
                                            value="<?php echo $category['category_id']; ?>">
                                        <div class="mb-3">
                                            <label for="edit_category_name" class="form-label">Category Name</label>
                                            <input type="text" class="form-control" id="edit_category_name"
                                                name="edit_category_name" value="<?php echo $category['category_name']; ?>"
                                                required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="edit_category">Save
                                            Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal untuk Tambah Kategori -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form untuk Tambah Kategori -->
                        <form action="categories.php" method="post">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="category_name" name="category_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_category">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
               
        <?php
        if (isset($add_category_error)) {
            echo '<div class="alert alert-danger mt-3" role="alert">' . $add_category_error . '</div>';
        }
        if (isset($edit_category_error)) {
            echo '<div class="alert alert-danger mt-3" role="alert">' . $edit_category_error . '</div>';
        }
        if (isset($delete_category_error)) {
            echo '<div class="alert alert-danger mt-3" role="alert">' . $delete_category_error . '</div>';
        }
        ?>

        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            Add Category
        </button>
        </div>
                </div>

        <h2 class="mt-4">Manage Post, Users, and Comments</h2>
        <p>Links to manage post, users, and comments go here...</p>

        <a href="../post/post.php" class="btn btn-success">Manage Post</a>
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
            $('#categoriesTable').DataTable();
        });
    </script>
</body>

</html>
