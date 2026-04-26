<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db.php';

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║           GUVI - Database Connection Checker               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$allConnected = true;

// 1. Check MySQL
echo "1️⃣  MySQL Connection:\n";
try {
    $conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);
    if ($conn->connect_error) {
        echo "   ❌ FAILED: " . $conn->connect_error . "\n";
        $allConnected = false;
    } else {
        echo "   ✅ CONNECTED to " . MYSQL_DB . "@" . MYSQL_HOST . ":" . MYSQL_PORT . "\n";
        $conn->close();
    }
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    $allConnected = false;
}

// 2. Check Redis
echo "\n2️⃣  Redis Connection:\n";
try {
    if (!class_exists('Redis')) {
        echo "   ❌ FAILED: Redis PHP extension not loaded\n";
        echo "   Fix: pecl install redis\n";
        $allConnected = false;
    } else {
        $redis = createRedisConnection();
        $ping = $redis->ping();
        if ($ping) {
            echo "   ✅ CONNECTED to " . (REDIS_URL !== '' ? REDIS_URL : REDIS_HOST . ':' . REDIS_PORT) . "\n";
            $redis->close();
        }
    }
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    echo "   Fix: Verify the Redis URL or credentials in .env\n";
    $allConnected = false;
}

// 3. Check MySQL Profiles Table
echo "\n3️⃣  MySQL Profiles Table:\n";
try {
    $conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT);
    if ($conn->connect_error) {
        echo "   ❌ FAILED: " . $conn->connect_error . "\n";
        $allConnected = false;
    } else {
        $result = $conn->query("SHOW TABLES LIKE 'profiles'");
        if ($result && $result->num_rows > 0) {
            echo "   ✅ Profiles table exists in MySQL\n";
            $conn->close();
        } else {
            echo "   ❌ FAILED: Profiles table not found\n";
            echo "   Fix: Run 'php php/setup.php'\n";
            $allConnected = false;
            $conn->close();
        }
    }
} catch (Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n";
    $allConnected = false;
}

// Summary
echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
if ($allConnected) {
    echo "║  ✅ ALL CONNECTIONS OK - Server is ready to start!        ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
    echo "Start server with:\n";
    echo "  php -S localhost:8000\n\n";
} else {
    echo "║  ❌ SOME CONNECTIONS FAILED - Fix issues before starting  ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";
}
?>
