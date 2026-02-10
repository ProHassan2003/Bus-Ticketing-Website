<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/library.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/NoteRepository.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$repo = new NoteRepository($db->pdo());

$userId = (int)$_SESSION['user']['id'];
$error = '';

/** Create booking from the home search form */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'book') {
    // Basic CSRF check (uses token made in config.php)
    $posted = (string)($_POST['csrf'] ?? '');
    $session = (string)($_SESSION['csrf'] ?? '');
    if ($posted === '' || $session === '' || !hash_equals($session, $posted)) {
        $error = 'Invalid CSRF token. Please try again.';
    } else {
        $from = clean((string)($_POST['from'] ?? ''), 60);
        $to = clean((string)($_POST['to'] ?? ''), 60);
        $date = clean((string)($_POST['date'] ?? ''), 20);
        $passengers = (int)($_POST['passengers'] ?? 1);

        if ($from === '' || $to === '' || $date === '' || $passengers <= 0) {
            $error = 'Please fill all fields.';
        } else {
            $title = $from . " → " . $to;
            $body = "Departure: {$date}\nPassengers: {$passengers}\nStatus: Confirmed";
            $repo->create($userId, $title, $body);

            header("Location: notes.php");
            exit;
        }
    }
}

$bookings = $repo->allByUserId($userId);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>My Bookings — GreenBus</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="brand">GREENBUS</div>
    <div class="nav">
      <a href="index.php">Plan Your Journey</a>
      <a href="dashboard.php">My Account</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</div>

<div class="section">
  <div class="container">

    <div class="card" style="margin-bottom:14px;">
      <h1 style="margin:0 0 10px;">Manage My Booking</h1>
      <p class="meta" style="margin:0;">Create a new booking from the homepage search, or manage existing bookings below.</p>

      <?php if ($error): ?>
        <p style="color:#b42318; font-weight:700; margin-top:12px;"><?= e($error) ?></p>
      <?php endif; ?>
    </div>

    <div class="cards">
      <?php if (empty($bookings)): ?>
        <div class="card">
          <h3 style="margin-top:0;">No bookings yet</h3>
          <p class="meta">Go to the homepage and search a route to create your first booking.</p>
          <a class="btn" href="index.php">Plan a Journey</a>
        </div>
      <?php else: ?>
        <?php foreach ($bookings as $b): ?>
          <div class="card booking">
            <div>
              <h3><?= e($b['title']) ?></h3>
              <div class="meta" style="white-space:pre-line;"><?= e($b['body']) ?></div>
            </div>
            <div class="actions">
              <a class="btn secondary" href="edit_note.php?id=<?= (int)$b['id'] ?>">Edit</a>
              <a class="btn secondary" href="delete_note.php?id=<?= (int)$b['id'] ?>" onclick="return confirm('Cancel this booking?');">Cancel</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </div>
</div>

<div class="footer">© <?= date('Y') ?> GreenBus</div>
</body>
</html>
