<?php
session_start();
include 'connect.php';

// Agar cookie set hai to automatic login
if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['first_name'] . " " . $row['last_name'];
        header("Location: index.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember_me']); // Checkbox check

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['first_name'] . " " . $row['last_name'];

            if ($remember) {
                // ✅ Remember Me ON → token generate karke save karo
                $token = bin2hex(random_bytes(16)); 
                setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true); 
                $stmt = $conn->prepare("UPDATE users SET remember_token=? WHERE id=?");
                $stmt->bind_param("si", $token, $row['id']);
                $stmt->execute();
            } else {
                // ✅ Remember Me OFF → purani cookie + DB token clear karo
                setcookie("remember_token", "", time() - 3600, "/", "", false, true);
                $stmt = $conn->prepare("UPDATE users SET remember_token=NULL WHERE id=?");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
            }

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Log in to your Expense Voyage account to manage travel bookings, track trip expenses, and plan your journeys with ease.">
<title>Login - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- AOS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(120deg, #0855ca, #ff7a00);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      border: none;
      border-radius: 20px;
      overflow: hidden;
      backdrop-filter: blur(10px);
      background: rgba(255,255,255,0.15);
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      animation: floatUp 1s ease forwards;
      opacity: 0;
    }
    @keyframes floatUp {
      from { transform: translateY(50px); opacity: 0; }
      to   { transform: translateY(0); opacity: 1; }
    }
    .card-header {
      background: linear-gradient(90deg, #007bff, #00c6ff);
      color: white;
      text-align: center;
      padding: 25px;
    }
    .card-header h3 {
      margin: 0;
      font-weight: bold;
    }
    .form-control {
      border-radius: 12px;
      padding: 12px;
      border: 2px solid transparent;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }
    .form-control:focus {
      border: 2px solid #00c6ff;
      box-shadow: 0 0 12px rgba(0,198,255,0.6);
    }
    .btn-gradient {
      background: linear-gradient(90deg,#007bff,#00c6ff);
      border: none;
      border-radius: 12px;
      color: white;
      padding: 12px;
      font-size: 16px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-gradient:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(0,123,255,0.6);
      color: white;
    }
    .card-footer {
      text-align: center;
      background: rgba(255,255,255,0.2);
      padding: 15px;
      border-top: none;
    }
    .form-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #007bff;
    }
    .alert {
      border-radius: 12px;
    }
    .password-wrapper {
      position: relative;
    }
    .password-wrapper input {
      padding-right: 45px; /* Eye icon ke liye space */
      padding-left: 40px;  /* Left icon ke liye space */
    }
    .password-wrapper i.toggle-eye {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 18px;
      color: #6c757d;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5" data-aos="zoom-in">
        <div class="card shadow-lg">
          <div class="card-header">
            <h3><i class="fa-solid fa-right-to-bracket me-2"></i>Login</h3>
          </div>
          <div class="card-body p-4">
            <?php if(!empty($_SESSION['success'])): ?>
              <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if(!empty($error)): ?>
              <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
              <!-- Email -->
              <div class="mb-3 position-relative">
                <i class="fa-solid fa-envelope form-icon"></i>
                <input type="email" name="email" class="form-control ps-5" placeholder="Email" required maxlength="35" minlength="4">
              </div>
              <!-- Password -->
              <div class="mb-3 password-wrapper">
                <i class="fa-solid fa-lock form-icon"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required minlength="8" maxlength="30">
                <i class="bi bi-eye-slash toggle-eye" id="togglePassword"></i>
              </div>
              <div class="mb-3 form-check">
            <input type="checkbox" name="remember_me" class="form-check-input" id="rememberMe" checked>
            <label class="form-check-label text-white" for="rememberMe">Remember Me</label>
          </div>
              <!-- Button -->
              <button type="submit" class="btn btn-gradient w-100">
                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Login
              </button>

            </form>
          </div>
          <div class="card-footer">
            <p class="mb-0 text-white">Don't have an account? 
              <a href="register.php" class="fw-bold text-decoration-none" style="color:#00e1ff;">Sign Up</a>
            </p>
          </div>
          <!-- Forgot Password -->
        <div class="text-center my-3">
        <a href="forgot_password.php" class="fw-bold text-decoration-none" style="color:#00e1ff;">
            Forgot Password?
        </a>
        </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    AOS.init({
      duration: 900,
      easing: 'ease-in-out',
      once: true
    });

    // Toggle Password
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);

      this.classList.toggle('bi-eye');
      this.classList.toggle('bi-eye-slash');
    });
  </script>
</body>
</html>
