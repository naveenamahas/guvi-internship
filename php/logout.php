<?php
/**
 * logout.php
 * Deletes the session token from REDIS. The browser separately clears its
 * localStorage entry via js/profile.js.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$headers = getallheaders();
$token   = $headers['Authorization'] ?? ($_POST['token'] ?? '');
$token   = str_replace('Bearer ', '', trim($token));

if ($token !== '') {
    $redis = getRedisConnection();
    $redis->del("session:$token");
}

echo json_encode(['success' => true, 'message' => 'Logged out.']);
