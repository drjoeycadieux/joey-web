<?php

header('Content-Type: application/json; charset=utf-8');

function respond($status, $data)
{
    http_response_code($status);

    echo json_encode($data);
    exit;
}

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, [
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}

// Read JSON
$input = file_get_contents('php://input');

if ($input === false || trim($input) === '') {
    respond(400, [
        'success' => false,
        'message' => 'Request body is empty'
    ]);
}

$data = json_decode($input, true);

if (!is_array($data)) {
    respond(400, [
        'success' => false,
        'message' => 'Invalid JSON received'
    ]);
}

// Get fields
$name = trim(isset($data['name']) ? $data['name'] : '');
$email = trim(isset($data['email']) ? $data['email'] : '');
$subject = trim(isset($data['subject']) ? $data['subject'] : '');
$message = trim(isset($data['message']) ? $data['message'] : '');

// Validate
if ($name === '' || $email === '' || $message === '') {
    respond(400, [
        'success' => false,
        'message' => 'Name, email and message are required'
    ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(400, [
        'success' => false,
        'message' => 'Invalid email address'
    ]);
}

if (strlen($name) > 100) {
    respond(400, [
        'success' => false,
        'message' => 'Name is too long'
    ]);
}

if (strlen($email) > 255) {
    respond(400, [
        'success' => false,
        'message' => 'Email is too long'
    ]);
}

if (strlen($subject) > 200) {
    respond(400, [
        'success' => false,
        'message' => 'Subject is too long'
    ]);
}

if (strlen($message) > 10000) {
    respond(400, [
        'success' => false,
        'message' => 'Message is too long'
    ]);
}

try {

    /*
     * IMPORTANT:
     *
     * contact.php is inside /api
     * database.php is inside /config
     *
     * Therefore we need ../config/
     */
    $db = require __DIR__ . '/../config/database.php';

    if (!$db) {
        throw new Exception('Database object was not returned');
    }

    $query = "
        INSERT INTO contacts
        (name, email, subject, message, status, created_at, updated_at)
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
            'Database insert failed. Affected rows: ' . $affectedRows
        );
    }

    $db->disconnect();

    respond(200, [
        'success' => true,
        'message' => 'Message received successfully.'
    ]);

} catch (Throwable $e) {

    error_log(
        'CONTACT FORM ERROR: ' .
        $e->getMessage()
    );

    respond(500, [
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}