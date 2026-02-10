<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/library.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/UserRepository.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = (string)($_POST['csrf'] ?? '');
    $session = (string)($_SESSION['csrf'] ?? '');
    if ($posted === '' || $session === '' || !hash_equals($session, $posted)) {
        $error = 'Invalid CSRF token. Please try again.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($name === '' || $email === '' || $password === '') {
            $error = 'Please fill all fields.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            $db = new Database();
            $users = new UserRepository($db->pdo());

            // Common method name: findByEmail
            $existing = $users->findByEmail($email);
            if ($existing) {
                $error = 'Email is already registered. Please login.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Common method name: create
                // If your class uses a different name, tell me and I’ll adjust.
                $users->create($name, $email, $hash);

                $success = 'Account created! You can login now.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Register — GreenBus</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="brand">GREENBUS</div>
    <div class="nav">
      <a href="index.php">Home</a>
      <a href="login.php">Login</a>
    </div>
  </div>
</div>

<div class="auth-wrap">
  <div class="container">
    <div class="card auth-card">
      <h1 class="auth-title">Create account</h1>
      <p class="auth-sub">Register to book trips and manage your bookings.</p>

      <?php if ($error): ?>
        <p style="color:#b42318; font-weight:800; margin: 0 0 12px;"><?= e($error) ?></p>
      <?php endif; ?>

      <?php if ($success): ?>
        <p style="color:#027a48; font-weight:800; margin: 0 0 12px;"><?= e($success) ?></p>
      <?php endif; ?>

      <form method="post" action="register.php">
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf'] ?? '') ?>">

        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn" type="submit">Register</button>
      </form>

      <div class="auth-links">
        <a href="login.php">Already have an account? Login</a>
        <a href="index.php">Home</a>
      </div>
    </div>
  </div>
</div>

<div class="footer">© <?= date('Y') ?> GreenBus</div>
</body>
</html>
