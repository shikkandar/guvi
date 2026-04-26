<?php
// Database Configuration

function loadEnvFile($path) {
    if (!is_readable($path)) {
        return;
    }

    $envValues = parse_ini_file($path, false, INI_SCANNER_RAW);
    if (!is_array($envValues)) {
        return;
    }

    foreach ($envValues as $key => $value) {
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

function envValue($key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

loadEnvFile(__DIR__ . '/../.env');

$mysqlUrl = envValue('MYSQL_URL', '');
$mysqlUrlParts = $mysqlUrl !== '' ? parse_url($mysqlUrl) : false;

// MySQL Configuration
define('MYSQL_HOST', is_array($mysqlUrlParts) && !empty($mysqlUrlParts['host']) ? $mysqlUrlParts['host'] : envValue('MYSQL_HOST', 'localhost'));
define('MYSQL_PORT', is_array($mysqlUrlParts) && !empty($mysqlUrlParts['port']) ? (int) $mysqlUrlParts['port'] : (int) envValue('MYSQL_PORT', 3306));
define('MYSQL_USER', is_array($mysqlUrlParts) && array_key_exists('user', $mysqlUrlParts) ? urldecode($mysqlUrlParts['user']) : envValue('MYSQL_USER', 'root'));
define('MYSQL_PASSWORD', is_array($mysqlUrlParts) && array_key_exists('pass', $mysqlUrlParts) ? urldecode($mysqlUrlParts['pass']) : envValue('MYSQL_PASSWORD', ''));
define('MYSQL_DB', is_array($mysqlUrlParts) && !empty($mysqlUrlParts['path']) ? ltrim($mysqlUrlParts['path'], '/') : envValue('MYSQL_DB', 'guvi'));

// Redis Configuration
define('REDIS_URL', envValue('REDIS_URL', ''));
define('REDIS_HOST', envValue('REDIS_HOST', 'localhost'));
define('REDIS_PORT', (int) envValue('REDIS_PORT', 6379));

// MongoDB Configuration
define('MONGO_URI', envValue('MONGO_URI', 'mongodb://localhost:27017'));
define('MONGO_DB', envValue('MONGO_DB', 'guvi'));

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// CORS Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function createRedisConnection($timeout = 2.0) {
    if (!class_exists('Redis')) {
        throw new Exception('Redis extension not loaded');
    }

    $redis = new Redis();

    if (REDIS_URL !== '') {
        $parts = parse_url(REDIS_URL);
        if ($parts === false || empty($parts['host'])) {
            throw new Exception('Invalid REDIS_URL');
        }

        $scheme = $parts['scheme'] ?? 'redis';
        $host = $parts['host'];
        $port = isset($parts['port']) ? (int) $parts['port'] : 6379;
        $username = $parts['user'] ?? null;
        $password = $parts['pass'] ?? null;

        if ($scheme === 'rediss') {
            $host = 'tls://' . $host;
        }

        $redis->connect($host, $port, $timeout);

        if ($username !== null && $username !== '' && $password !== null && $password !== '') {
            $redis->auth([$username, $password]);
        } elseif ($password !== null && $password !== '') {
            $redis->auth($password);
        }

        return $redis;
    }

    $redis->connect(REDIS_HOST, REDIS_PORT, $timeout);
    return $redis;
}

// Function to get MySQL connection
function getMysqlConnection() {
    try {
        $conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);

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
        return createRedisConnection();
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
