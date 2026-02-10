<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../library.php';

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/UserRepository.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['error' => 'Use POST'], 405);
}

$email = clean($_POST['email'] ?? '', 190);
$password = (string)($_POST['password'] ?? '');

$db = new Database();
$users = new UserRepository($db->pdo());

$user = $users->findByEmail($email);

if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
    json_response(['error' => 'Invalid credentials'], 401);
}

$token = jwt_create([
    'sub' => (int)$user['id'],
    'email' => (string)$user['email'],
], 3600);

json_response([
    'token' => $token,
    'token_type' => 'Bearer',
    'expires_in' => 3600
]);
