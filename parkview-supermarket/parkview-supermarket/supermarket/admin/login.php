<?php
session_start();
require_once '../config.php';
$db = getDB();

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user !== '' && $pass !== '') {
        $stmt = $db->prepare("SELECT password FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$user]);
        $admin = $stmt->fetch();

        if ($admin && $pass === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user;
            header('Location: index.php');
            exit;
        }
    }

    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – Park View Supermarket</title>
  <link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <div class="logo-icon">🛒</div>
      <h1>Park View Supermarket Admin</h1>
      <p>Sign in to manage your store</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="admin" required autocomplete="username">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn-login">Sign In →</button>
    </form>
    <div class="login-back">
      <a href="../index.php">← Back to Store</a>
    </div>
  </div>
</div>
</body>
</html>
