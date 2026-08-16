<?php

header('Content-Type: application/json; charset=utf-8');

function responseJson(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Allow POST and OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responseJson(405, [
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}

// Read JSON request
$input = file_get_contents('php://input');

if ($input === false || trim($input) === '') {
    responseJson(400, [
        'success' => false,
        'message' => 'Empty request body'
    ]);
}

$data = json_decode($input, true);

if (!is_array($data)) {
    responseJson(400, [
        'success' => false,
        'message' => 'Invalid JSON'
    ]);
}

// Get form values
$name = trim((string)($data['name'] ?? ''));
$email = trim((string)($data['email'] ?? ''));
$subject = trim((string)($data['subject'] ?? ''));
$message = trim((string)($data['message'] ?? ''));

// Validate required fields
if ($name === '' || $email === '' || $message === '') {
    responseJson(400, [
        'success' => false,
        'message' => 'Name, email and message are required.'
    ]);
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responseJson(400, [
        'success' => false,
        'message' => 'Invalid email address.'
    ]);
}

// Validate lengths
if (strlen($name) > 100) {
    responseJson(400, [
        'success' => false,
        'message' => 'Name is too long.'
    ]);
}

if (strlen($email) > 255) {
    responseJson(400, [
        'success' => false,
        'message' => 'Email address is too long.'
    ]);
}

if (strlen($subject) > 200) {
    responseJson(400, [
        'success' => false,
        'message' => 'Subject is too long.'
    ]);
}

if (strlen($message) > 10000) {
    responseJson(400, [
        'success' => false,
        'message' => 'Message is too long.'
    ]);
}

try {

    /*
     * IMPORTANT:
     *
     * contact.php:
     *     /api/contact.php
     *
     * database.php:
     *     /api/config/database.php
     *
     * Therefore this path is correct:
     */
    $db = require __DIR__ . '/config/database.php';

    if (!$db instanceof Database) {
        throw new Exception('Database object was not created.');
    }

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
            'Database insert failed. Affected rows: ' . $affectedRows
        );
    }

    $db->disconnect();

    responseJson(200, [
        'success' => true,
        'message' => 'Message received successfully. We will review and respond shortly.'
    ]);

} catch (Throwable $e) {

    error_log(
        'Contact form error: ' . $e->getMessage()
    );

    /*
     * TEMPORARY DEBUGGING RESPONSE.
     * Remove the actual exception message after everything works.
     */
    responseJson(500, [
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}