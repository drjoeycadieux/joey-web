<?php
/**
 * Contact Form API
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);

    exit;
}

// Read request body
$input = file_get_contents('php://input');

if ($input === false || trim($input) === '') {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Empty request body'
    ]);

    exit;
}

// Decode JSON
$data = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON'
    ]);

    exit;
}

// Required fields
$name = trim((string)($data['name'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$subject = trim((string)($data['subject'] ?? ''));
$message = trim((string)($data['message'] ?? ''));

// Validate required fields
if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Name, email and message are required.'
    ]);

    exit;
}

// Validate lengths
if (strlen($name) > 100) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Name is too long.'
    ]);

    exit;
}

if (strlen($email) > 255) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Email address is too long.'
    ]);

    exit;
}

if (strlen($subject) > 200) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Subject is too long.'
    ]);

    exit;
}

if (strlen($message) > 10000) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Message is too long.'
    ]);

    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid email address.'
    ]);

    exit;
}

$db = null;

try {

    /*
     * database.php returns a Database object.
     */
    $db = require_once __DIR__ . '/../config/database.php';

    $query = "
        INSERT INTO contacts
        (
            name,
            email,
            subject,
            message,
            status,
            created_at,
            updated_at
        )
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ";

    $status = 'new';

    $params = [
        $name,
        $email,
        $subject,
        $message,
        $status
    ];

    $types = 'sssss';

    $affectedRows = $db->executeUpdate(
        $query,
        $params,
        $types
    );

    if ($affectedRows !== 1) {
        throw new Exception(
            'Database insert did not affect one row.'
        );
    }

    $db->disconnect();

    http_response_code(200);

    echo json_encode([
        'success' => true,
        'message' => 'Message received successfully. We will review and respond shortly.'
    ]);

} catch (Throwable $e) {

    if ($db instanceof Database) {
        $db->disconnect();
    }

    // Log the real error server-side
    error_log(
        'Contact form error: ' .
        $e->getMessage()
    );

    // Never expose database credentials/errors to visitor
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to send your message right now. Please try again later.'
    ]);
}

exit;