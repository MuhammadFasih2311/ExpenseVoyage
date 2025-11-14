<?php
session_start();
include("connect.php"); // database connection

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check user in DB
    $query = "SELECT * FROM admin WHERE username=? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin-home.php");
        exit;
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - ExpenseVoyage</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      height: 100vh;
      margin: 0;
      background: linear-gradient(135deg, #0d6efd, #fd7e14);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: "Segoe UI", sans-serif;
    }
    .login-card {
      width: 400px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 30px rgba(0,0,0,.4);
      color: #fff;
      animation: fadeInUp .7s ease;
    }
    .login-card h3 {
      font-weight: 700;
      background: linear-gradient(90deg, #fff, #ffe4d1);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .form-control {
      border-radius: 10px;
      padding: 0.75rem 1rem;
      border: 1px solid rgba(255,255,255,.3);
      background: rgba(255,255,255,.1);
      color: #fff;
    }
    .form-control:focus {
      box-shadow: 0 0 0 .25rem rgba(13,110,253,.35);
      border-color: #fff;
      background: rgba(255,255,255,.2);
    }
    .btn-gradient {
      background: linear-gradient(90deg, #0d6efd, #fd7e14);
      border: none;
      border-radius: 10px;
      font-weight: 600;
      color: #fff;
      transition: all .3s ease;
    }
    .btn-gradient:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0,0,0,.3);
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .alert-danger {
      border-radius: 10px;
      background: rgba(220,53,69,.8);
      color: #fff;
      border: none;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h3 class="text-center mb-4"><i class="bi bi-shield-lock"></i> Admin Login</h3>

    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required maxlength="30">
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required maxlength="30">
      </div>
      <button type="submit" name="login" class="btn btn-gradient w-100 py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Login
      </button>
    </form>
  </div>

</body>
</html>
