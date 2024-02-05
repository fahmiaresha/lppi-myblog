<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Login Form</title>
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      background-color: #f8f9fa; /* Background color */
    }

    .card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-title {
      color: #495057; /* Text color */
    }

    .form-control {
      border: 1px solid #ced4da; /* Input border color */
    }

    .btn-primary {
      background-color: #007bff; /* Button color */
      border: none;
    }

    .btn-primary:hover {
      background-color: #0056b3; /* Button hover color */
    }
  </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4">
          <div class="text-center mb-4">
            <img src="https://i.ibb.co/yVGxFPR/2.png" alt="Logo" style="max-width: 100px;">
            <h3 class="card-title mt-2">Login Admin</h3>
          </div>
          <?php
            session_start();

            $error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
            unset($_SESSION['error_message']);
            ?>

            <?php
            if (!empty($error_message)) {
                echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
            }
            ?>
          <form action="proses_login.php" method="post">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS CDN (optional, only needed if you are using Bootstrap JavaScript features) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
