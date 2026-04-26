<?php
// Database Configuration

// MySQL Configuration
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_DB', 'guvi');

// Redis Configuration
define('REDIS_HOST', 'localhost');
define('REDIS_PORT', 6379);

// MongoDB Configuration
define('MONGO_URI', 'mongodb://localhost:27017');
define('MONGO_DB', 'guvi');

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// CORS Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Function to get MySQL connection
function getMysqlConnection() {
    try {
        $conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);

        if ($conn->connect_error) {
            throw new Exception("MySQL Connection failed: " . $conn->connect_error);
        }

        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Database connection error']));
    }
}

// Function to get Redis connection
function getRedisConnection() {
    try {
        if (!class_exists('Redis')) {
            throw new Exception('Redis extension not loaded');
        }
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        return $redis;
    } catch (Exception $e) {
        // Redis not available
        return null;
    }
}

// Function to get MongoDB connection
function getMongoConnection() {
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        if (!class_exists('MongoDB\Driver\Manager')) {
            return null;
        }
        $client = new MongoDB\Client(MONGO_URI);
        return $client->selectDatabase(MONGO_DB);
    } catch (Exception $e) {
        return null;
    }
}

// Redis is required
define('USE_REDIS', true);
?>
