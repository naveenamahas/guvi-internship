<?php
/**
 * config.php
 * Updated configuration for Aiven MySQL (SSL), Remote Redis, and MongoDB Atlas.
 */

require __DIR__ . '/../vendor/autoload.php';

use Predis\Client as RedisClient;
use MongoDB\Client as MongoClient;

// ---------------- MySQL (PDO with SSL Bypass Verification) ----------------
define('DB_HOST', 'guvi-mysql-naveena-b99e.h.aivencloud.com');
define('DB_PORT', '17848');
define('DB_NAME', 'defaultdb');
define('DB_USER', 'avnadmin');
define('DB_PASS', 'AVNS_6QybbSd8THU61UXaDJb');

function getMySQLConnection()
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE       => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT                  => 10,
        ];

        // Enable SSL options if defined in PHP MySQL driver
        if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Database connection failed: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ---------------- Redis Configuration ----------------
function getRedisConnection()
{
    return new RedisClient([
        'scheme'   => 'rediss',
        'host'     => 'generous-harmony-powder-97476.db.redis.io',
        'port'     => 12386,
        'user'     => 'default',
        'password' => 'wAOF46KmlYOOMUDJa1Dxm5uSZiZO0wd2',
        'timeout'  => 5.0,
    ]);
}

// ---------------- MongoDB Configuration ----------------
function getMongoConnection()
{
    $uri = "mongodb+srv://naveenaarjava_db_user:WrKuvyjdk6gFMYFL@cluster0.abf05yo.mongodb.net/?appName=Cluster0";
    $client = new MongoClient($uri);
    return $client->guvi_internship->profiles;
}
