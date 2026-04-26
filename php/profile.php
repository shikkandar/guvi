<?php
require_once __DIR__ . '/../config/db.php';

// Handle POST request only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

$action = isset($_POST['action']) ? $_POST['action'] : 'fetch';
$sessionToken = isset($_POST['sessionToken']) ? $_POST['sessionToken'] : '';

// Validate session token
if (empty($sessionToken)) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Session token required']));
}

// Verify session
$sessionData = verifySession($sessionToken);
if (!$sessionData) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Invalid or expired session']));
}

try {
    if ($action === 'fetch') {
        handleFetch($sessionData);
    } elseif ($action === 'update') {
        handleUpdate($_POST, $sessionData);
    } else {
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Invalid action']));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Operation failed. Please try again.']);
    error_log('Profile error: ' . $e->getMessage());
}

function verifySession($token) {
    $redis = getRedisConnection();
    if ($redis === null) {
        return false;
    }
    $sessionJson = $redis->get('session_' . $token);
    $redis->close();
    if ($sessionJson) {
        return json_decode($sessionJson, true);
    }
    return false;
}

function handleFetch($sessionData) {
    try {
        $userId = $sessionData['user_id'];
        $email = $sessionData['email'];

        // Use MongoDB driver directly (no library)
        $manager = new MongoDB\Driver\Manager(MONGO_URI);
        $query = new MongoDB\Driver\Query(['email' => $email]);
        $result = $manager->executeQuery(MONGO_DB . '.profiles', $query);

        $profile = null;
        foreach ($result as $doc) {
            $profile = (array)$doc;
            break;
        }

        if ($profile) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => [
                    'age' => $profile['age'] ?? '',
                    'dob' => $profile['dob'] ?? '',
                    'contact' => $profile['contact'] ?? '',
                    'address' => $profile['address'] ?? ''
                ]
            ]);
            return;
        }

        // No profile exists yet, return empty data
        http_response_code(200);
        echo json_encode([
            'success' => false,
            'message' => 'No profile data found',
            'data' => [
                'age' => '',
                'dob' => '',
                'contact' => '',
                'address' => ''
            ]
        ]);
    } catch (Exception $e) {
        throw new Exception('Fetch failed: ' . $e->getMessage());
    }
}

function handleUpdate($postData, $sessionData) {
    $userId = $sessionData['user_id'];
    $email = $sessionData['email'];
    $fullname = $sessionData['fullname'];

    $age = isset($postData['age']) && !empty($postData['age']) ? (int)$postData['age'] : null;
    $dob = isset($postData['dob']) && !empty($postData['dob']) ? $postData['dob'] : null;
    $contact = isset($postData['contact']) && !empty($postData['contact']) ? $postData['contact'] : null;
    $address = isset($postData['address']) && !empty($postData['address']) ? $postData['address'] : null;

    // Validate age if provided
    if ($age !== null && ($age < 1 || $age > 150)) {
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Invalid age']));
    }

    // Validate contact number if provided
    if ($contact !== null && !preg_match('/^[0-9\-\+\s\(\)]{10,}$/', $contact)) {
        http_response_code(400);
        die(json_encode(['success' => false, 'message' => 'Invalid contact number']));
    }

    try {
        // Use MongoDB driver directly (no library)
        $manager = new MongoDB\Driver\Manager(MONGO_URI);

        // Prepare update data
        $updateData = [];
        if ($age !== null) $updateData['age'] = (int)$age;
        if ($dob !== null) $updateData['dob'] = $dob;
        if ($contact !== null) $updateData['contact'] = $contact;
        if ($address !== null) $updateData['address'] = $address;
        $updateData['updated_at'] = date('Y-m-d H:i:s');
        $updateData['user_id'] = $userId;
        $updateData['email'] = $email;
        $updateData['fullname'] = $fullname;

        // Update with upsert
        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->update(
            ['email' => $email],
            ['$set' => $updateData],
            ['upsert' => true]
        );

        $result = $manager->executeBulkWrite(MONGO_DB . '.profiles', $bulk);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    } catch (Exception $e) {
        throw new Exception('Update failed: ' . $e->getMessage());
    }
}
?>
