<?php
/**
 * login.php
 * Verifies credentials against MySQL, creates session token in Redis.
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

$stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit;
}

$token = bin2hex(random_bytes(32));

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
