<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

// Database (MAMP)
define('DB_HOST', 'localhost');
define('DB_PORT', 8889);
define('DB_NAME', 'coursework_php');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// App URL
define('APP_URL', 'http://localhost:8888/coursework-php');

// Google OAuth
define('GOOGLE_CLIENT_ID','181122799632-b23isnht8pertf9a8k1dvp0s6td2378s.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET','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');
define('GOOGLE_REDIRECT_URI', APP_URL . '/oauth/google_callback.php');




// JWT configuration (used for token-based authentication)
define('JWT_SECRET', '9f8a3c1e7b2d4a6f0c5e8b1a7d9c2f4e');
define('JWT_ISSUER', 'greenbus-app');




// CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
