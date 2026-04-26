<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';

echo "Setting up GUVI Application...\n\n";

try {
    // Step 1: Create MySQL Database
    echo "1. Setting up MySQL Database...\n";

    $conn = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);
    if ($conn->connect_error) {
        throw new Exception("MySQL Connection failed: " . $conn->connect_error);
    }

    // Create database
    $dbName = MYSQL_DB;
    $sql = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

    if (!$conn->query($sql)) {
        throw new Exception("Database creation failed: " . $conn->error);
    }
    echo "   ✓ Database created/verified\n";

    // Select database
    $conn->select_db($dbName);

    // Create users table
    $usersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$conn->query($usersTable)) {
        throw new Exception("Users table creation failed: " . $conn->error);
    }
    echo "   ✓ Users table created/verified\n";

    // Create profiles table
    $profilesTable = "CREATE TABLE IF NOT EXISTS profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        age INT,
        dob DATE,
        contact VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_email (email),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    if (!$conn->query($profilesTable)) {
        throw new Exception("Profiles table creation failed: " . $conn->error);
    }
    echo "   ✓ Profiles table created/verified\n";

    $conn->close();

    // Step 2: Verify MongoDB
    echo "\n2. Setting up MongoDB Connection...\n";
    if (!extension_loaded('mongodb')) {
        echo "   ⚠ MongoDB PHP extension not installed\n";
        echo "   Install with: pecl install mongodb\n";
        echo "   Or configure MongoDB support for production use\n";
    } else {
        try {
            $client = new MongoDB\Client(MONGO_URI);
            $db = $client->selectDatabase(MONGO_DB);

            // Create profiles collection (will be auto-created on first insert)
            $collections = $db->listCollections();
            $collectionExists = false;

            foreach ($collections as $collection) {
                if ($collection->getName() === 'profiles') {
                    $collectionExists = true;
                    break;
                }
            }

            if (!$collectionExists) {
                echo "   ℹ Profiles collection will be created on first profile update\n";
            } else {
                echo "   ✓ Profiles collection exists\n";
            }
        } catch (Exception $e) {
            echo "   ⚠ Warning: MongoDB not available yet - " . $e->getMessage() . "\n";
            echo "   Make sure MongoDB is running: brew services start mongodb-community\n";
        }
    }

    // Step 3: Verify Redis (optional)
    echo "\n3. Checking Redis Connection...\n";
    if (USE_REDIS) {
        try {
            $redis = new Redis();
            $redis->connect(REDIS_HOST, REDIS_PORT);
            $redis->ping();
            echo "   ✓ Redis is running\n";
            $redis->close();
        } catch (Exception $e) {
            echo "   ⚠ Warning: Redis not available - Using file-based sessions\n";
            echo "   Make sure Redis is running: brew services start redis\n";
        }
    } else {
        echo "   ⚠ Redis PHP extension not installed - Using file-based sessions\n";
        echo "   Install: pecl install redis\n";
    }

    // Step 4: Create sessions directory
    echo "\n4. Setting up Session Storage...\n";
    $sessionsDir = __DIR__ . '/../sessions';
    if (!is_dir($sessionsDir)) {
        mkdir($sessionsDir, 0755, true);
        echo "   ✓ Sessions directory created\n";
    } else {
        echo "   ✓ Sessions directory exists\n";
    }

    echo "\n✓ Setup completed successfully!\n";
    echo "\nYou can now:\n";
    echo "1. Start MySQL (if not running): brew services start mysql\n";
    echo "2. Start MongoDB: brew services start mongodb-community\n";
    echo "3. Start Redis: brew services start redis\n";
    echo "4. Navigate to: http://localhost/guvi (after setting up web server)\n";

} catch (Exception $e) {
    echo "✗ Setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
