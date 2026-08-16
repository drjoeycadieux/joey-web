<?php
/**
 * Contact Form Processor
 * Handles contact form submissions and stores them in the database
 * 
 * Database: contact_db
 * Table: contacts
 */

// Set response header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (!isset($data['name']) || !isset($data['email']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Sanitize and validate input
$name = trim($data['name']);
$email = trim($data['email']);
$subject = isset($data['subject']) ? trim($data['subject']) : null;
$message = trim($data['message']);

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Validate field lengths
if (strlen($name) > 100 || strlen($email) > 255 || strlen($subject) > 200 || strlen($message) < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid field lengths']);
    exit;
}

try {
    // Database connection - Update credentials and host as needed
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_user = getenv('DB_USER') ?: 'admin';
    $db_pass = getenv('DB_PASS') ?: '@Wteamred900';
    $db_name = 'contact_db';

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Set charset to UTF-8
    $conn->set_charset('utf8mb4');

    // Prepare statement
    $stmt = $conn->prepare('INSERT INTO contacts (name, email, subject, message, status, created_at, updated_at) 
                            VALUES (?, ?, ?, ?, ?, NOW(), NOW())');

    if (!$stmt) {
        throw new Exception('Statement preparation failed: ' . $conn->error);
    }

    // Bind parameters
    // b = blob, s = string, i = integer, d = double
    $status = 'new';
    $stmt->bind_param('sssss', $name, $email, $subject, $message, $status);

    // Execute statement
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Message received successfully. We will review and respond shortly.'
    ]);

} catch (Exception $e) {
    // Log error for debugging (in production, log to file instead)
    error_log('Contact form error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your message. Please try again later.'
    ]);
}
exit;
?>
