<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../library.php';

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/NoteRepository.php';

$token = bearer_token();
$payload = $token ? jwt_verify($token) : null;

if (!$payload) {
    json_response(['error' => 'Unauthorized (missing/invalid JWT)'], 401);
}

$userId = (int)($payload['sub'] ?? 0);
if ($userId <= 0) {
    json_response(['error' => 'Invalid token subject'], 401);
}

$db = new Database();
$notesRepo = new NoteRepository($db->pdo());

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    // List notes for user
    $notes = $notesRepo->allByUserId($userId);
    json_response(['notes' => $notes]);
}

if ($method === 'POST') {
    // Create note
    $title = clean($_POST['title'] ?? '', 190);
    $body  = clean($_POST['body'] ?? '', 5000);

    if ($title === '' || $body === '') {
        json_response(['error' => 'title and body required'], 400);
    }

    $notesRepo->create($userId, $title, $body);
    json_response(['message' => 'created']);
}

if ($method === 'PUT') {
    // Update note (send x-www-form-urlencoded body)
    parse_str(file_get_contents("php://input"), $data);

    $id = (int)($data['id'] ?? 0);
    $title = clean($data['title'] ?? '', 190);
    $body  = clean($data['body'] ?? '', 5000);

    $note = $notesRepo->findOneById($id);
    if (!$note || (int)$note['user_id'] !== $userId) {
        json_response(['error' => 'not found'], 404);
    }
    if ($title === '' || $body === '') {
        json_response(['error' => 'title and body required'], 400);
    }

    $notesRepo->update($id, $title, $body);
    json_response(['message' => 'updated']);
}

if ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = (int)($data['id'] ?? 0);

    $note = $notesRepo->findOneById($id);
    if ($note && (int)$note['user_id'] === $userId) {
        $notesRepo->delete($id);
    }
    json_response(['message' => 'deleted']);
}

json_response(['error' => 'Method not allowed'], 405);
