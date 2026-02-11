<?php

/**
 * Payment Details Handler - Step 4: User enters payment information
 * 
 * ⚠️ CRITICAL SECURITY NOTES:
 * - Full card number is NEVER stored
 * - Only masked version is stored in session
 * - CVV is NEVER stored (validated on submission only)
 * - No actual payment processing occurs
 * - This is for booking confirmation only
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
    if (
        !BookingSession::hasBookingData('search') ||
        !BookingSession::hasBookingData('flight') ||
        !BookingSession::hasBookingData('personal')
    ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please complete previous steps first']);
        exit;
    }

    // Get form data
    $cardholder = Security::sanitizeInput($_POST['cardholder_name'] ?? '');
    $cardNumber = Security::sanitizeInput($_POST['card_number'] ?? '');
    $expiryDate = Security::sanitizeInput($_POST['expiry_date'] ?? '');
    $cvv = Security::sanitizeInput($_POST['cvv'] ?? '');

    // Check for injection attempts
    if (Security::checkForInjection([$cardholder, $expiryDate])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input detected']);
        exit;
    }

    // Validate input
    $validator = new Validator();
    $validator->validateRequired('cardholder_name', $cardholder, 'Cardholder name');
    $validator->validateRequired('card_number', $cardNumber, 'Card number');
    $validator->validateRequired('expiry_date', $expiryDate, 'Expiry date');
    $validator->validateRequired('cvv', $cvv, 'CVV');
    $validator->validateName('cardholder_name', $cardholder, 'Cardholder name');
    $validator->validateCardNumber('card_number', $cardNumber);
    $validator->validateCardExpiry('expiry_date', $expiryDate);
    $validator->validateCVV('cvv', $cvv);

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

    // Extract last 4 digits for reference
    $cardDigits = preg_replace('/\D/', '', $cardNumber);
    $lastFour = substr($cardDigits, -4);

    // Mask card number
    $maskedCard = Security::maskCardNumber($cardNumber);

    // Store MASKED payment details only in session
    // IMPORTANT: Full card number is NOT stored
    // CVV is NOT stored (only validated in this request)
    BookingSession::setPaymentDetails(
        $cardholder,
        $maskedCard,
        $expiryDate,
        $lastFour
    );

    // Security measure: Do not echo back card info
    // Return success without sensitive data
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Payment details saved successfully',
        'data' => [
            'payment_status' => 'verified',
            'masked_card' => $maskedCard
        ]
    ]);
} catch (Exception $e) {
    // Log error (but not sensitive data)
    error_log('Payment details error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while saving payment details'
    ]);

    exit;
}
