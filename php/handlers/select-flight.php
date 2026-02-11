<?php

/**
 * Flight Selection Handler - Step 2: User selects a flight
 * POST request from results page
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

    // Check if search was completed
    if (!BookingSession::hasBookingData('search')) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please search for flights first']);
        exit;
    }

    // Get flight data from request
    $flightData = json_decode(file_get_contents('php://input'), true)
        ?? json_decode(http_build_query($_POST), true);

    if (!$flightData) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No flight data provided']);
        exit;
    }

    // Sanitize flight data
    $flight = [];
    $flight['id'] = Security::sanitizeInput($flightData['id'] ?? '');
    $flight['airline'] = Security::sanitizeInput($flightData['airline'] ?? '');
    $flight['flight_number'] = Security::sanitizeInput($flightData['flight_number'] ?? '');
    $flight['departure_airport'] = Security::sanitizeInput($flightData['departure_airport'] ?? '');
    $flight['arrival_airport'] = Security::sanitizeInput($flightData['arrival_airport'] ?? '');
    $flight['departure_time'] = Security::sanitizeInput($flightData['departure_time'] ?? '');
    $flight['arrival_time'] = Security::sanitizeInput($flightData['arrival_time'] ?? '');
    $flight['duration'] = Security::sanitizeInput($flightData['duration'] ?? '');
    $flight['stops'] = (int)($flightData['stops'] ?? 0);
    $flight['price'] = Security::sanitizeInput($flightData['price'] ?? '0');
    $flight['currency'] = Security::sanitizeInput($flightData['currency'] ?? 'USD');
    $flight['seat_class'] = Security::sanitizeInput($flightData['seat_class'] ?? 'ECONOMY');

    // Validate flight ID
    $validator = new Validator();
    $validator->validateRequired('flight_id', $flight['id'], 'Flight ID');
    $validator->validateRequired('airline', $flight['airline'], 'Airline');
    $validator->validateRequired('price', $flight['price'], 'Price');

    if ($validator->hasErrors()) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid flight data',
            'errors' => $validator->getErrors()
        ]);
        exit;
    }

    // Check for injection attempts
    if (Security::checkForInjection(array_values($flight))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input detected']);
        exit;
    }

    // Store selected flight in session
    BookingSession::setSelectedFlight($flight);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Flight selected successfully',
        'data' => [
            'flight' => $flight
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log('Flight selection error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while selecting the flight'
    ]);

    exit;
}
