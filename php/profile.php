<?php
/**
 * profile.php
 * Validates session token via Redis, stores/fetches profile in MongoDB.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$headers = getallheaders();
$token   = $headers['Authorization'] ?? ($_POST['token'] ?? $_GET['token'] ?? '');
$token   = str_replace('Bearer ', '', trim($token));

if ($token === '') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Missing session token.']);
    exit;
}

$redis       = getRedisConnection();
$sessionData = $redis->get("session:$token");

if (!$sessionData) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Session expired or invalid. Please log in again.']);
    exit;
}

$session  = json_decode($sessionData, true);
$userId   = $session['user_id'];
$profiles = getMongoConnection();
$method   = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $doc = $profiles->findOne(['user_id' => $userId]);

    echo json_encode([
        'success'  => true,
        'username' => $session['username'],
        'profile'  => [
            'age'     => $doc['age'] ?? '',
            'dob'     => $doc['dob'] ?? '',
            'contact' => $doc['contact'] ?? '',
        ],
    ]);
    exit;
}

if ($method === 'POST') {
    $age     = $_POST['age'] ?? '';
    $dob     = $_POST['dob'] ?? '';
    $contact = $_POST['contact'] ?? '';

    $profiles->updateOne(
        ['user_id' => $userId],
        ['$set' => [
            'user_id' => $userId,
            'age'     => $age,
            'dob'     => $dob,
            'contact' => $contact,
        ]],
        ['upsert' => true]
    );

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
