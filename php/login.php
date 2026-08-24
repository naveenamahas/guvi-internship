<?php
/**
 * login.php
 * Verifies credentials against MySQL (prepared statement), then creates a
 * random session token and stores the session details in REDIS (not a PHP
 * session). The token is returned to the browser, which stores it in
 * localStorage and sends it back on every future request.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

$pdo = getMySQLConnection();

// PREPARED STATEMENT lookup
$stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit;
}

// Generate a random opaque session token
$token = bin2hex(random_bytes(32));

// Store session info in REDIS (expires after 1 hour) — no PHP session used
$redis = getRedisConnection();
$redis->setex(
    "session:$token",
    3600,
    json_encode([
        'user_id'  => $user['id'],
        'username' => $user['username'],
    ])
);

echo json_encode([
    'success'  => true,
    'message'  => 'Login successful.',
    'token'    => $token,
    'username' => $user['username'],
    'email'    => $user['email'],
]);
