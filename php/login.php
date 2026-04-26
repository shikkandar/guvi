<?php
require_once __DIR__ . '/../config/db.php';

// Handle POST request only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Check for logout action
if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    handleLogout();
}

// Handle login
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($email) || empty($password)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Email and password are required']));
}

try {
    $conn = getMysqlConnection();

    // Get user from MySQL using prepared statement
    $stmt = $conn->prepare("SELECT id, fullname, email, password FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        http_response_code(401);
        die(json_encode(['success' => false, 'message' => 'Invalid email or password']));
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        $conn->close();
        http_response_code(401);
        die(json_encode(['success' => false, 'message' => 'Invalid email or password']));
    }

    // Generate session token
    $sessionToken = bin2hex(random_bytes(32));
    $sessionData = [
        'user_id' => $user['id'],
        'email' => $user['email'],
        'fullname' => $user['fullname'],
        'created_at' => time()
    ];

    // Store session in Redis
    $redis = getRedisConnection();
    $redis->setex('session_' . $sessionToken, 86400, json_encode($sessionData)); // 24 hour expiry
    $redis->close();

    $conn->close();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Login successful!',
        'token' => $sessionToken,
        'email' => $user['email'],
        'fullname' => $user['fullname']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
    error_log('Login error: ' . $e->getMessage());
}

function handleLogout() {
    $sessionToken = isset($_POST['sessionToken']) ? $_POST['sessionToken'] : '';

    if (empty($sessionToken)) {
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Session token required']));
    }

    try {
        $redis = getRedisConnection();
        $redis->del('session_' . $sessionToken);
        $redis->close();

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Logout successful']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Logout failed']);
        error_log('Logout error: ' . $e->getMessage());
    }
}
?>
