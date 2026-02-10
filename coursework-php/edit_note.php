<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/library.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/NoteRepository.php';

if (!isset($_SESSION['user'])) {
    redirect('login.php');
}

$db = new Database();
$notesRepo = new NoteRepository($db->pdo());

$userId = (int)$_SESSION['user']['id'];
$noteId = (int)($_GET['id'] ?? 0);

$note = $notesRepo->findOneById($noteId);
if (!$note || (int)$note['user_id'] !== $userId) {
    die('Note not found.');
}

$error = '';

if (is_post()) {
    require_csrf();

    $title = clean($_POST['title'] ?? '', 190);
    $body  = clean($_POST['body'] ?? '', 5000);

    if ($title === '' || $body === '') {
        $error = 'Title and Body are required.';
    } else {
        $notesRepo->update($noteId, $title, $body);
        redirect('notes.php');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Edit Note</title>
</head>
<body>

<h1>Edit Note</h1>

<p>
    <a href="notes.php">Back to Notes</a> |
    <a href="logout.php">Logout</a>
</p>

<?php if ($error): ?>
    <p style="color:red"><?= e($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

    <p>
        Title:<br>
        <input type="text" name="title" value="<?= e($note['title']) ?>" required>
    </p>

    <p>
        Body:<br>
        <textarea name="body" rows="5" cols="50" required><?= e($note['body']) ?></textarea>
    </p>

    <button type="submit">Save</button>
</form>

</body>
</html>
