<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
include 'connect.php';
$user_id = $_SESSION['user_id'];

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$user_id"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
  $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
  $email      = mysqli_real_escape_string($conn, $_POST['email']);

  $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE id=$user_id";
  if (mysqli_query($conn,$sql)) {
    $_SESSION['success'] = "✅ Profile updated successfully!";
    header("Location: profile.php");
    exit();
  } else {
    $error = "❌ Failed to update profile.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Update your personal details on Expense Voyage. Edit your profile to keep your travel bookings and expense records accurate and up to date.">
<title>Edit Profile - Expense Voyage</title>
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
    <h3 class="fw-bold mb-4 text-center">Edit Profile</h3>

    <?php if(isset($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($user['first_name']); ?>" minlength="3" maxlength="30" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
      </div>
      <div class="mb-3">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($user['last_name']); ?>" minlength="3" maxlength="30" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']); ?>" minlength="4" maxlength="30">
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-gradient px-4"><i class="bi bi-check2-circle"></i> Save Changes</button>
        <a href="profile.php" class="btn btn-secondary px-4">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init({ duration:800, easing:"ease-in-out",});</script>
</body>
</html>
