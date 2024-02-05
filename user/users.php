<?php
session_start();
// Pastikan pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once('../koneksi.php');

// Fungsi untuk mendapatkan daftar pengguna
function getUsers() {
    $conn = connectDB();
    $query = "SELECT * FROM users";
    $result = $conn->query($query);
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $users;
}

// Fungsi untuk menambah pengguna
function addUser($username, $email, $password) {
    $conn = connectDB();
    $username = $conn->real_escape_string($username);
    $email = $conn->real_escape_string($email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Fungsi untuk mengedit pengguna
function editUser($user_id, $username, $email, $password) {
    $conn = connectDB();
    $username = $conn->real_escape_string($username);
    $email = $conn->real_escape_string($email);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET username='$username', email='$email', password='$hashed_password' WHERE user_id=$user_id";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Fungsi untuk menghapus pengguna
function deleteUser($user_id) {
    $conn = connectDB();
    $query = "DELETE FROM users WHERE user_id=$user_id";
    $result = $conn->query($query);
    $conn->close();
    return $result;
}

// Proses Form Tambah Pengguna
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_user"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (addUser($username, $email, $password)) {
        header("Location: users.php");
        exit();
    } else {
        $add_user_error = "Failed to add user.";
    }
}

// Proses Form Edit Pengguna
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_user"])) {
    $user_id = $_POST["edit_user_id"];
    $username = $_POST["edit_username"];
    $email = $_POST["edit_email"];
    $password = $_POST["edit_password"];

    if (editUser($user_id, $username, $email, $password)) {
        header("Location: users.php");
        exit();
    } else {
        $edit_user_error = "Failed to edit user.";
    }
}

// Proses Hapus Pengguna
if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $user_id = $_GET["id"];

    if (deleteUser($user_id)) {
        header("Location: users.php");
        exit();
    } else {
        $delete_user_error = "Failed to delete user.";
    }
}

// Ambil daftar pengguna
$users = getUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
                <h1 class="">Manage Users</h1>
                <table id="usersTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>*********</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal<?php echo $user['user_id']; ?>">
                                        Edit
                                    </button>
                                    <a href="users.php?action=delete&id=<?php echo $user['user_id']; ?>"
                                        class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <!-- Modal untuk Edit Pengguna -->
                            <div class="modal fade" id="editUserModal<?php echo $user['user_id']; ?>" tabindex="-1"
                                aria-labelledby="editUserModalLabel<?php echo $user['user_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editUserModalLabel<?php echo $user['user_id']; ?>">
                                                Edit User
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Form untuk Edit Pengguna -->
                                            <form action="users.php" method="post">
                                                <input type="hidden" name="edit_user_id"
                                                    value="<?php echo $user['user_id']; ?>">
                                                <div class="mb-3">
                                                    <label for="edit_username" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="edit_username"
                                                        name="edit_username" value="<?php echo $user['username']; ?>"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_email" class="form-label">Email</label>
                                                    <input type="email" class="form-control" id="edit_email"
                                                        name="edit_email" value="<?php echo $user['email']; ?>"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_password" class="form-label">Password</label>
                                                    <input type="password" class="form-control" id="edit_password"
                                                        name="edit_password" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="edit_user">Save
                                                    Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Modal untuk Tambah Pengguna -->
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form untuk Tambah Pengguna -->
                                <form action="users.php" method="post">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($add_user_error)) {
                    echo '<div class="alert alert-danger mt-3" role="alert">' . $add_user_error . '</div>';
                }
                if (isset($edit_user_error)) {
                    echo '<div class="alert alert-danger mt-3" role="alert">' . $edit_user_error . '</div>';
                }
                if (isset($delete_user_error)) {
                    echo '<div class="alert alert-danger mt-3" role="alert">' . $delete_user_error . '</div>';
                }
                ?>

                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    Add User
                </button>
            </div>
        </div>

        <h2 class="mt-4">Manage Post, Categories, and Comments</h2>
        <p>Links to manage post, categories, and comments go here...</p>

        <a href="../post/post.php" class="btn btn-success">Manage Post</a>
        <a href="../categories/categories.php" class="btn btn-success">Manage Categories</a>
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
            $('#usersTable').DataTable();
        });
    </script>
</body>

</html>
