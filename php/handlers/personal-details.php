<?php

/**
 * Personal Details Handler - Step 3: User enters personal information
 * POST request from personal details page
 */

header('Content-Type: application/json');

// Include core classes
require_once __DIR__ . '/../core/Security.php';
require_once __DIR__ . '/../core/BookingSession.php';
require_once __DIR__ . '/../core/Validator.php';

try {
    // Initialize session
    BookingSession::init();

    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Verify CSRF token
    $csrf = $_POST['csrf_token'] ?? '';
    if (!Security::verifyCSRFToken($csrf)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid security token']);
        exit;
    }

    // Check if previous steps were completed
    if (!BookingSession::hasBookingData('search') || !BookingSession::hasBookingData('flight')) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please complete previous steps first']);
        exit;
    }

    // Get form data
    $name = Security::sanitizeInput($_POST['name'] ?? '');
    $email = Security::sanitizeEmail($_POST['email'] ?? '');
    $phone = Security::sanitizeInput($_POST['phone'] ?? '');
    $passport = Security::sanitizeInput($_POST['passport_number'] ?? '');

    // Check for injection attempts
    if (Security::checkForInjection([$name, $email, $phone, $passport])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input detected']);
        exit;
    }

    // Validate input
    $validator = new Validator();
    $validator->validateRequired('name', $name, 'Full name');
    $validator->validateRequired('email', $email, 'Email');
    $validator->validateRequired('phone', $phone, 'Phone number');
    $validator->validateName('name', $name, 'Full name');
    $validator->validateEmail('email', $email);
    $validator->validatePhone('phone', $phone);

    // Optional validation for passport
    if ($passport) {
        $validator->validateLength('passport', $passport, 6, 20, 'Passport number');
    }

    // Check for validation errors
    if ($validator->hasErrors()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->getErrors()
        ]);
        exit;
    }

    // Store personal details in session
    BookingSession::setPersonalDetails($name, $email, $phone, $passport);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Personal details saved successfully',
        'data' => [
            'personal' => BookingSession::getPersonalDetails()
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log('Personal details error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while saving personal details'
    ]);

    exit;
}
