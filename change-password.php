<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
include 'connect.php';
$user_id = $_SESSION['user_id'];

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current = $_POST['current_password'];
  $new     = $_POST['new_password'];
  $confirm = $_POST['confirm_password'];

  // Fetch old hashed password from DB
  $res = mysqli_query($conn, "SELECT password FROM users WHERE id=$user_id");
  $row = mysqli_fetch_assoc($res);

  if ($row && password_verify($current, $row['password'])) {

    // Check new password length
    if (strlen($new) < 8) {
      $error = "❌ New password must be at least 8 characters long.";
    } elseif ($new === $current) {
      // Prevent using the same old password
      $error = "⚠️ New password cannot be the same as your current password.";
    } elseif ($new !== $confirm) {
      $error = "❌ New passwords do not match.";
    } else {
      // Hash new password and update
      $hash = password_hash($new, PASSWORD_BCRYPT);
      $sql = "UPDATE users SET password='$hash' WHERE id=$user_id";
      if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "✅ Password changed successfully!";
        header("Location: profile.php");
        exit();
      } else {
        $error = "❌ Failed to update password.";
      }
    }
  } else {
    $error = "❌ Current password is incorrect.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Secure your Expense Voyage account by updating your password. Keep your travel bookings and expense management safe and protected.">
<title>Change Password - Expense Voyage</title>
  <link rel="icon" href="images/logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
  <style>
    html, body {
  overflow-x: hidden;
}
  </style>
</head>
<body class="bg-light">
<?php include 'header.php'; ?>
<div class="container py-5">
  <div class="card shadow-lg p-4 mx-auto" style="max-width:600px;" data-aos="zoom-in">
    <?php if(!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3 position-relative">
        <label class="form-label">Current Password</label>
        <div class="input-group">
          <input type="password" name="current_password" id="current_password" class="form-control" required minlength="8" maxlength="30">
          <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="current_password">
            <i class="bi bi-eye-slash"></i>
          </button>
        </div>
      </div>

      <div class="mb-3 position-relative">
        <label class="form-label">New Password</label>
        <div class="input-group">
          <input type="password" name="new_password" id="new_password" class="form-control" required minlength="8" maxlength="30">
          <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="new_password">
            <i class="bi bi-eye-slash"></i>
          </button>
        </div>
      </div>

      <div class="mb-3 position-relative">
        <label class="form-label">Confirm New Password</label>
        <div class="input-group">
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="8" maxlength="30">
          <button type="button" class="btn btn-outline-secondary toggle-pass" data-target="confirm_password">
            <i class="bi bi-eye-slash"></i>
          </button>
        </div>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-gradient px-4">
          <i class="bi bi-shield-lock"></i> Update Password
        </button>
        <a href="profile.php" class="btn btn-secondary px-4">Cancel</a>
      </div>
    </form>
  </div>
</div>
  </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
AOS.init({ duration:800, easing:"ease-in-out", once:false });

// ✅ Corrected toggle behavior
document.querySelectorAll(".toggle-pass").forEach(btn => {
  btn.addEventListener("click", function() {
    const input = document.getElementById(this.dataset.target);
    const icon = this.querySelector("i");

    // 👁️ If icon is open eye, hide it and show text
    if (icon.classList.contains("bi-eye-slash")) {
      input.type = "text"; // show password
      icon.classList.remove("bi-eye-slash");
      icon.classList.add("bi-eye"); // back to open eye (show mode)
    } 
    // 🙈 If icon is slash, revert to hidden
    else {
      input.type = "password"; // hide password
      icon.classList.remove("bi-eye");
      icon.classList.add("bi-eye-slash"); // now show slash (hide mode)
    }
  });
});
</script>
</body>
</html>
