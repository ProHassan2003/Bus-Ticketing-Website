<?php
declare(strict_types=1);

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function clean(string $value, int $max = 255): string {
    $value = trim($value);
    if (strlen($value) > $max) {
        $value = substr($value, 0, $max);
    }
    return $value;
}

function is_post(): bool {
    return (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST');
}

function require_csrf(): void {
    $posted = (string)($_POST['csrf'] ?? '');
    $session = (string)($_SESSION['csrf'] ?? '');
    if ($posted === '' || $session === '' || !hash_equals($session, $posted)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}

function redirect(string $path): void {
    header("Location: $path");
    exit;
}

function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) $data .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($data, '-_', '+/')) ?: '';
}

// Create JWT (HS256)
function jwt_create(array $payload, int $ttlSeconds = 3600): string {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];

    $now = time();
    $payload = array_merge([
        'iss' => JWT_ISSUER,
        'iat' => $now,
        'exp' => $now + $ttlSeconds,
    ], $payload);

    $h = base64url_encode(json_encode($header));
    $p = base64url_encode(json_encode($payload));
    $sig = hash_hmac('sha256', "$h.$p", JWT_SECRET, true);
    $s = base64url_encode($sig);

    return "$h.$p.$s";
}

// Verify JWT (HS256) and return payload or null
function jwt_verify(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$h, $p, $s] = $parts;

    $expected = base64url_encode(hash_hmac('sha256', "$h.$p", JWT_SECRET, true));
    if (!hash_equals($expected, $s)) return null;

    $payloadJson = base64url_decode($p);
    $payload = json_decode($payloadJson, true);
    if (!is_array($payload)) return null;

    if (!isset($payload['exp']) || time() > (int)$payload['exp']) return null;

    return $payload;
}

// Get Bearer token from Authorization header
function bearer_token(): string {
    $hdr = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if ($hdr === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $hdr = $headers['Authorization'] ?? '';
    }
    if (preg_match('/Bearer\s+(\S+)/', $hdr, $m)) {
        return $m[1];
    }
    return '';
}

// API helper: JSON response
function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
