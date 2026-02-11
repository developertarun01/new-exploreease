<?php

/**
 * Amadeus Flight Search Service
 * Handles all API calls to Amadeus Flight API
 */
class AmadeusService
{
    private $config;
    private $accessToken;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/amadeus-config.php';
    }

    /**
     * Get access token from Amadeus API
     */
    private function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $baseUrl = $this->config['environment'] === 'test'
            ? $this->config['base_url_test']
            : $this->config['base_url_prod'];

        $url = $baseUrl . '/v1/security/oauth2/token';

        // Debug: Verify credentials are loaded
        if (strpos($this->config['client_id'], 'YOUR_CLIENT_ID') !== false) {
            throw new Exception('Amadeus credentials not properly configured in .env file');
        }

        $postFields = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret']
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception('Curl error: ' . $curlError);
        }

        if ($httpCode !== 200) {
            $errorResponse = json_decode($response, true);
            $errorMsg = isset($errorResponse['error_description']) ? $errorResponse['error_description'] : 'Unknown error';
            throw new Exception('Authentication failed (HTTP ' . $httpCode . '): ' . $errorMsg);
        }

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            throw new Exception('Failed to obtain access token');
        }

        $this->accessToken = $data['access_token'];
        return $this->accessToken;
    }

    /**
     * Search for flights
     */
    public function searchFlights($origin, $destination, $departureDate, $passengers = 1, $returnDate = null)
    {
        try {
            // Validate inputs
            if (empty($origin) || empty($destination) || empty($departureDate)) {
                throw new Exception('Missing required search parameters');
            }

            // Get access token
            $token = $this->getAccessToken();

            // Build API URL
            $baseUrl = $this->config['environment'] === 'test'
                ? $this->config['base_url_test']
                : $this->config['base_url_prod'];

            $params = [
                'originLocationCode' => strtoupper($origin),
                'destinationLocationCode' => strtoupper($destination),
                'departureDate' => $departureDate,
                'adults' => $passengers,
                'currencyCode' => 'USD',
                'max' => 250 // Get top 10 results
            ];

            // Add return date if round trip
            if ($returnDate) {
                $params['returnDate'] = $returnDate;
            }

            $url = $baseUrl . '/v2/shopping/flight-offers?' . http_build_query($params);

            // Make API call
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new Exception('Curl error: ' . $curlError);
            }

            if ($httpCode !== 200) {
                throw new Exception('API request failed. HTTP Code: ' . $httpCode);
            }

            $data = json_decode($response, true);

            if (!$data) {
                throw new Exception('Invalid API response');
            }

            // Process and format flight data
            return $this->formatFlightData($data);
        } catch (Exception $e) {
            // Log error (in production, log to file)
            error_log('Flight search error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Format Amadeus API response into app-friendly format
     */
    private function formatFlightData($apiData)
    {
        if (!isset($apiData['data']) || empty($apiData['data'])) {
            return [];
        }

        $flights = [];

        foreach ($apiData['data'] as $flight) {
            // Extract main flight segment
            if (!isset($flight['itineraries']) || empty($flight['itineraries'])) {
                continue;
            }

            $itinerary = $flight['itineraries'][0];
            $firstSegment = $itinerary['segments'][0] ?? null;
            $lastSegment = end($itinerary['segments']);

            if (!$firstSegment || !$lastSegment) {
                continue;
            }

            $formattedFlight = [
                'id' => uniqid('flight_', true), // Generate unique ID for app
                'api_id' => $flight['id'], // Keep original API ID
                'airline' => $firstSegment['operating']['carrierCode'] ?? $firstSegment['carrierCode'],
                'flight_number' => $firstSegment['number'],
                'departure_airport' => $firstSegment['departure']['iataCode'],
                'arrival_airport' => $lastSegment['arrival']['iataCode'],
                'departure_time' => $this->formatTime($firstSegment['departure']['at']),
                'arrival_time' => $this->formatTime($lastSegment['arrival']['at']),
                'duration' => $itinerary['duration'],
                'stops' => count($itinerary['segments']) - 1,
                'price' => $flight['price']['total'] ?? 0,
                'currency' => $flight['price']['currency'] ?? 'USD',
                'seat_class' => $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'] ?? 'ECONOMY'
            ];

            $flights[] = $formattedFlight;
        }

        return $flights;
    }

    /**
     * Format ISO 8601 datetime to readable format
     */
    private function formatTime($isoTime)
    {
        try {
            $date = new DateTime($isoTime);
            return $date->format('H:i'); // Format as HH:MM
        } catch (Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get flight price details
     */
    public function getFlightDetails($flightId, $apiId = null)
    {
        // This would fetch detailed information for a specific flight
        // For now, we'll return basic details from session
        // In production, you might cache this or fetch from API
        return [
            'flight_id' => $flightId,
            'api_id' => $apiId,
            'details' => 'Flight details from API'
        ];
    }
}
