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
    // Load database configuration
    require_once __DIR__ . '/config/database.php';
    
    // Get database instance (note: $this is already a Database object from require_once)
    $db = new Database();
    
    // Prepare and execute query
    $query = 'INSERT INTO contacts (name, email, subject, message, status, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, NOW(), NOW())';
    
    $status = 'new';
    $params = [$name, $email, $subject, $message, $status];
    
    // Types: s = string for all parameters
    $types = 'sssss';
    
    // Execute the query
    $affected_rows = $db->executeUpdate($query, $params, $types);
    
    // Disconnect from database
    $db->disconnect();

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
