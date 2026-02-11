<?php

/**
 * Search Handler - Step 1: Fetch flights from Amadeus API
 * POST request from search form
 */

header('Content-Type: application/json');

// Include core classes
require_once __DIR__ . '/../core/Security.php';
require_once __DIR__ . '/../core/BookingSession.php';
require_once __DIR__ . '/../core/Validator.php';
require_once __DIR__ . '/../api/AmadeusService.php';

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

    // Get form data
    $origin = Security::sanitizeInput($_POST['origin'] ?? '');
    $destination = Security::sanitizeInput($_POST['destination'] ?? '');
    $departureDate = Security::sanitizeInput($_POST['departure_date'] ?? '');
    $returnDate = Security::sanitizeInput($_POST['return_date'] ?? '');
    $passengers = Security::sanitizeInput($_POST['passengers'] ?? '1');

    // Check for injection attempts
    if (Security::checkForInjection([$origin, $destination, $departureDate, $returnDate, $passengers])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input detected']);
        exit;
    }

    // Validate input
    $validator = new Validator();
    $validator->validateRequired('origin', $origin, 'Origin airport');
    $validator->validateRequired('destination', $destination, 'Destination airport');
    $validator->validateRequired('departure_date', $departureDate, 'Departure date');
    $validator->validateRequired('passengers', $passengers, 'Number of passengers');
    $validator->validateAirportCode('origin', $origin);
    $validator->validateAirportCode('destination', $destination);
    $validator->validateTravelDate('departure_date', $departureDate);
    $validator->validatePassengerCount('passengers', $passengers);

    if ($returnDate) {
        $validator->validateTravelDate('return_date', $returnDate);
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

    // Store search criteria in session
    BookingSession::setSearchCriteria(
        $origin,
        $destination,
        $departureDate,
        $passengers,
        $returnDate
    );

    // Fetch flights from Amadeus API
    $amadeus = new AmadeusService();
    $flights = $amadeus->searchFlights(
        $origin,
        $destination,
        $departureDate,
        $passengers,
        $returnDate
    );

    // Return successful response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Flights found successfully',
        'data' => [
            'flights' => $flights,
            'search_criteria' => BookingSession::getSearchCriteria()
        ]
    ]);
} catch (Exception $e) {
    // Log error (in production)
    error_log('Search handler error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while searching for flights: ' . $e->getMessage()
    ]);

    exit;
}
