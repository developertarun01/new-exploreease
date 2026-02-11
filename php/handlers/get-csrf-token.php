<?php

/**
 * CSRF Token Generator
 * Returns a fresh CSRF token for form protection
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../core/BookingSession.php';
require_once __DIR__ . '/../core/Security.php';

try {
    // Initialize session
    BookingSession::init();

    // Generate and return CSRF token
    $token = Security::generateCSRFToken();

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'csrf_token' => $token
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to generate CSRF token: ' . $e->getMessage()
    ]);
    exit;
}
