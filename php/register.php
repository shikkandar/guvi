<?php
require_once __DIR__ . '/../config/db.php';

// Handle POST request only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Get POST data
$fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($fullname) || empty($email) || empty($password)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'All fields are required']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid email format']));
}

if (strlen($password) < 6) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']));
}

try {
    $conn = getMysqlConnection();

    // Check if email already exists using prepared statement
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$checkStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $checkStmt->close();
        $conn->close();
        http_response_code(409);
        die(json_encode(['success' => false, 'message' => 'Email already registered']));
    }
    $checkStmt->close();

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into MySQL using prepared statement
    $insertStmt = $conn->prepare("INSERT INTO users (fullname, email, password, created_at) VALUES (?, ?, ?, NOW())");
    if (!$insertStmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $insertStmt->bind_param("sss", $fullname, $email, $hashedPassword);

    if ($insertStmt->execute()) {
        $insertStmt->close();
        $conn->close();
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Please login with your credentials.'
        ]);
    } else {
        throw new Exception("Execute failed: " . $insertStmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    error_log('Register error: ' . $e->getMessage());
}
?>
