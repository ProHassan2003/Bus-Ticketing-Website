<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/library.php';

$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>GreenBus — Low cost bus travel</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="brand">GREENBUS</div>
    <div class="nav">
      <a href="index.php">Plan Your Journey</a>
      <a href="<?= $user ? 'notes.php' : 'login.php' ?>">Manage My Booking</a>
      <a href="<?= $user ? 'dashboard.php' : 'login.php' ?>">My Account</a>
      <?php if ($user): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="hero">
  <div class="container">
    <h1>Low cost bus travel</h1>
    <p>Search routes, book in seconds, and manage your trips in one place.</p>

    <div class="search-card">
      <form method="post" action="notes.php">
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf'] ?? '') ?>">
        <input type="hidden" name="action" value="book">

        <div class="form-row">
          <div>
            <label>From</label>
            <input name="from" placeholder="Vilnius" required>
          </div>

          <div>
            <label>To</label>
            <input name="to" placeholder="Kaunas" required>
          </div>

          <div>
            <label>Departure</label>
            <input type="date" name="date" required>
          </div>

          <div>
            <label>Passengers</label>
            <select name="passengers">
              <?php for ($i=1; $i<=6; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?> <?= $i===1 ? 'Adult' : 'Adults' ?></option>
              <?php endfor; ?>
            </select>
          </div>

          <div>
            <button class="btn" type="submit">Search</button>
          </div>
        </div>

        <p class="meta" style="margin:10px 0 0;">
          <?php if (!$user): ?>
            You can search now, but you’ll be asked to login to save the booking.
          <?php else: ?>
            Logged in as <b><?= e($user['name'] ?? 'User') ?></b>.
          <?php endif; ?>
        </p>
      </form>
    </div>
  </div>
</div>

<div class="section">
  <div class="container grid-3">
    <div class="feature">
      <h3>Connecting you to the world</h3>
      <p></p>
    </div>
    <div class="feature">
      <h3>Comfort on the go</h3>
      <p></p>
    </div>
    <div class="feature">
      <h3>Choose, book, travel</h3>
      <p></p>
    </div>
  </div>
</div>

<div class="footer">
  © <?= date('Y') ?> GreenBus — PHP Coursework Project
</div>

</body>
</html>

