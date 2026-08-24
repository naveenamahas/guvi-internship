<?php
/**
 * register.php
 * Receives AJAX POST data (username, email, password) and stores the
 * registered user in MySQL using a prepared statement.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}

$pdo = getMySQLConnection();

// Check for an existing username/email using a PREPARED STATEMENT
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);

if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Username or email is already registered.']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert new user using a PREPARED STATEMENT
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $hashedPassword]);

echo json_encode(['success' => true, 'message' => 'Registration successful. Please log in.']);
