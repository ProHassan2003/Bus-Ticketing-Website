<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../library.php';
require_once __DIR__ . '/../classes/Database.php';



// 1) Validate OAuth state
$stateSession = $_SESSION['oauth_state'] ?? '';
$stateGet = $_GET['state'] ?? '';
if (!$stateSession || !$stateGet || !hash_equals($stateSession, (string)$stateGet)) {
    http_response_code(400);
    exit('Invalid OAuth state');
}

// 2) Check code
$code = $_GET['code'] ?? '';
if (!$code) {
    http_response_code(400);
    exit('Missing code');
}

// 3) Exchange code -> tokens
$tokenUrl = 'https://oauth2.googleapis.com/token';
$postData = [
    'code' => (string)$code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code',
];

$tokenUrl = 'https://oauth2.googleapis.com/token';
$postData = [
    'code' => (string)$code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code',
];

$ch = curl_init($tokenUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_RETURNTRANSFER => true,

    // important for local MAMP / Windows
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
CURLOPT_CAINFO => 'C:\MAMP\bin\php\cacert.pem',
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    $err = curl_error($ch);
    $errno = curl_errno($ch);
    curl_close($ch);

    echo "<pre>";
    echo "cURL failed\n";
    echo "errno: $errno\n";
    echo "error: $err\n";
    echo "</pre>";
    exit;
}

curl_close($ch);

$data = json_decode((string)$response, true);

if ($http !== 200) {
    echo "<pre>";
    echo "Token exchange failed\n";
    echo "HTTP: $http\n\n";
    echo "Raw response:\n$response\n\n";
    echo "Decoded:\n";
    print_r($data);
    echo "</pre>";
    exit;
}


$data = json_decode((string)$response, true);
if ($http !== 200 || !is_array($data) || empty($data['access_token'])) {
    http_response_code(400);
    $data = json_decode((string)$response, true);

if ($http !== 200) {
    echo "<pre>";
    echo "Token exchange failed\n";
    echo "HTTP: " . $http . "\n\n";
    echo "Raw response:\n" . $response . "\n\n";
    echo "Decoded:\n";
    print_r($data);
    echo "</pre>";
    exit;
}

}

$accessToken = (string)$data['access_token'];

// 4) Get user profile (email/name/id)
$infoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init($infoUrl);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => ["Authorization: Bearer $accessToken"],
    CURLOPT_RETURNTRANSFER => true,
]);
$userRaw = curl_exec($ch);
$http2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$user = json_decode((string)$userRaw, true);
if ($http2 !== 200 || !is_array($user) || empty($user['email'])) {
    http_response_code(400);
    exit('Failed to fetch user info: ' . htmlspecialchars((string)$userRaw));
}

$email = (string)$user['email'];
$name  = (string)($user['name'] ?? 'Google User');
$googleId = (string)($user['id'] ?? '');

// 5) Find/Create user in DB
$db = new Database();
$pdo = $db->pdo();

// Find by email
$stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = ?");
$stmt->execute([$email]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$existing) {
    // Create new user (OAuth user has no password_hash)
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, oauth_provider, oauth_subject)
        VALUES (?, ?, NULL, 'google', ?)
    ");
    $stmt->execute([$name, $email, $googleId]);

    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 6) Log in (session) and redirect
$_SESSION['user'] = [
    'id' => (int)$existing['id'],
    'name' => (string)$existing['name'],
    'email' => (string)$existing['email'],
];
redirect('../dashboard.php');

