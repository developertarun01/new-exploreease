<?php

/**
 * Mock Search Handler - For testing without Amadeus API
 * Use this instead of search.php while setting up Amadeus credentials
 * 
 * To use: Change form action from "search.php" to "search-mock.php"
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

    // ============================================
    // MOCK FLIGHTS - For development/testing only
    // ============================================
    $mockFlights = [
        [
            'id' => 'flight_' . uniqid(),
            'airline' => 'BA',
            'flight_number' => '112',
            'departure_airport' => strtoupper($origin),
            'arrival_airport' => strtoupper($destination),
            'departure_time' => '15:30',
            'arrival_time' => '03:45',
            'duration' => 'PT7H15M',
            'stops' => 0,
            'price' => '750.00',
            'currency' => 'USD',
            'seat_class' => 'ECONOMY'
        ],
        [
            'id' => 'flight_' . uniqid(),
            'airline' => 'AA',
            'flight_number' => '115',
            'departure_airport' => strtoupper($origin),
            'arrival_airport' => strtoupper($destination),
            'departure_time' => '10:00',
            'arrival_time' => '22:15',
            'duration' => 'PT8H15M',
            'stops' => 1,
            'price' => '650.00',
            'currency' => 'USD',
            'seat_class' => 'ECONOMY'
        ],
        [
            'id' => 'flight_' . uniqid(),
            'airline' => 'LH',
            'flight_number' => '1024',
            'departure_airport' => strtoupper($origin),
            'arrival_airport' => strtoupper($destination),
            'departure_time' => '20:00',
            'arrival_time' => '08:30',
            'duration' => 'PT7H30M',
            'stops' => 0,
            'price' => '900.00',
            'currency' => 'USD',
            'seat_class' => 'BUSINESS'
        ]
    ];

    // Return successful response with mock flights
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mock flights loaded (Amadeus API not configured)',
        'data' => [
            'flights' => $mockFlights,
            'search_criteria' => BookingSession::getSearchCriteria()
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log('Mock search error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);

    exit;
}
