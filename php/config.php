<?php
/**
 * config.php
 * Central configuration file: MySQL, Redis and MongoDB connections.
 * Only connection logic lives here — no HTML, no CSS, no client-side JS.
 */

// ---------------- MySQL (PDO) Configuration ----------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'guvi_internship');
define('DB_USER', 'root');
define('DB_PASS', '');

function getMySQLConnection()
{
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }
}

// Composer autoload — needed for Predis (Redis) and mongodb/mongodb libraries
require __DIR__ . '/../vendor/autoload.php';

use Predis\Client as RedisClient;
use MongoDB\Client as MongoClient;

// ---------------- Redis Configuration (session storage) ----------------
function getRedisConnection()
{
    return new RedisClient([
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ]);
}

// ---------------- MongoDB Configuration (profile storage) ----------------
function getMongoConnection()
{
    $client = new MongoClient("mongodb://127.0.0.1:27017");
    return $client->guvi_internship->profiles; // database -> collection
}
