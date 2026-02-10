<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/library.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>My Account — GreenBus</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="brand">GREENBUS</div>
    <div class="nav">
      <a href="index.php">Home</a>
      <a href="notes.php">My Bookings</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="section">
  <div class="container">
    <div class="card">
      <h1 style="margin-top:0;">My Account</h1>
      <p class="meta">Welcome back, <b><?= e($user['name'] ?? 'User') ?></b></p>
      <p class="meta">Email: <?= e($user['email'] ?? '') ?></p>

      <div class="actions" style="margin-top:14px;">
        <a class="btn" href="notes.php">Manage My Bookings</a>
        <a class="btn secondary" href="index.php">Plan a new journey</a>
      </div>
    </div>
  </div>
</div>

<div class="footer">© <?= date('Y') ?> GreenBus</div>
</body>
</html>
