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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted = (string)($_POST['csrf'] ?? '');
    $session = (string)($_SESSION['csrf'] ?? '');
    if ($posted === '' || $session === '' || !hash_equals($session, $posted)) {
        $error = 'Invalid CSRF token. Please try again.';
    } else {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $error = 'Please enter email and password.';
        } else {
            $db = new Database();
            $users = new UserRepository($db->pdo());

            // Common method name: findByEmail
            // If your class uses a different name, tell me and I’ll adjust.
            $user = $users->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                $error = 'Invalid email or password.';
            } else {
                // Save only safe fields in session
                $_SESSION['user'] = [
                    'id' => (int)$user['id'],
                    'name' => $user['name'] ?? ($user['username'] ?? 'User'),
                    'email' => $user['email'] ?? $email
                ];
                header("Location: dashboard.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login — GreenBus</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="brand">GREENBUS</div>
    <div class="nav">
      <a href="index.php">Home</a>
      <a href="register.php">Register</a>
    </div>
  </div>
</div>

<div class="auth-wrap">
  <div class="container">
    <div class="card auth-card">
      <h1 class="auth-title">Login</h1>
      <p class="auth-sub">Access your bookings and manage your trips.</p>

      <?php if ($error): ?>
        <p style="color:#b42318; font-weight:800; margin: 0 0 12px;"><?= e($error) ?></p>
      <?php endif; ?>

      <form method="post" action="login.php">
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf'] ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn" type="submit">Login</button>
      </form>

      <div class="auth-links">
        <a class="btn secondary" href="oauth/google_start.php">Login with Google</a>
        <a href="register.php">Register</a>
        <a href="index.php">Home</a>
      </div>
    </div>
  </div>
</div>

<div class="footer">© <?= date('Y') ?> GreenBus</div>
</body>
</html>
