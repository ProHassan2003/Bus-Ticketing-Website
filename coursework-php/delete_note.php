<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/library.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/NoteRepository.php';

if (!isset($_SESSION['user'])) {
    redirect('login.php');
}

$noteId = (int)($_GET['id'] ?? 0);
$csrf = (string)($_GET['csrf'] ?? '');

if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
    die('Invalid CSRF token');
}

$db = new Database();
$notesRepo = new NoteRepository($db->pdo());

$userId = (int)$_SESSION['user']['id'];
$note = $notesRepo->findOneById($noteId);

if ($note && (int)$note['user_id'] === $userId) {
    $notesRepo->delete($noteId);
}

redirect('notes.php');
