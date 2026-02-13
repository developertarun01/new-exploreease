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

        // Build curl options - with SSL verification flexible for shared hosting
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ];

        // Try with SSL verification first, but handle failures
        $curlOptions[CURLOPT_SSL_VERIFYPEER] = true;
        $curlOptions[CURLOPT_SSL_VERIFYHOST] = 2;

        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // If SSL verification failed, retry with SSL disabled (common on shared hosting)
        if ($curlError && strpos($curlError, 'SSL') !== false) {
            error_log('Retrying Amadeus auth without SSL verification due to: ' . $curlError);

            $ch = curl_init();
            $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
            curl_setopt_array($ch, $curlOptions);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
        }

        if ($curlError) {
            throw new Exception('Authentication request failed: ' . $curlError);
        }

        if ($httpCode !== 200) {
            $errorResponse = json_decode($response, true);
            $errorMsg = isset($errorResponse['error_description']) ? $errorResponse['error_description'] : 'Unknown error';
            $errorDetails = 'HTTP ' . $httpCode . ': ' . $errorMsg;
            if (isset($errorResponse['error'])) {
                $errorDetails .= ' (' . $errorResponse['error'] . ')';
            }
            throw new Exception('Authentication failed - ' . $errorDetails);
        }

        $data = json_decode($response, true);

        if (!isset($data['access_token'])) {
            error_log('Auth response: ' . print_r($data, true));
            throw new Exception('Failed to obtain access token from authentication response');
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
                'max' => 100
            ];

            // Add return date if round trip
            if ($returnDate) {
                $params['returnDate'] = $returnDate;
            }

            $url = $baseUrl . '/v2/shopping/flight-offers?' . http_build_query($params);

            // Make API call
            $ch = curl_init();

            $curlOptions = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token,
                    'Accept: application/json'
                ]
            ];

            curl_setopt_array($ch, $curlOptions);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // If SSL verification failed, retry with SSL disabled
            if ($curlError && strpos($curlError, 'SSL') !== false) {
                error_log('Retrying flight search without SSL verification due to: ' . $curlError);

                $ch = curl_init();
                $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
                $curlOptions[CURLOPT_SSL_VERIFYHOST] = 0;
                curl_setopt_array($ch, $curlOptions);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);
            }

            if ($curlError) {
                throw new Exception('Flight search request failed: ' . $curlError);
            }

            if ($httpCode !== 200) {
                $errorData = json_decode($response, true);
                $errorMsg = 'API HTTP ' . $httpCode;

                if (is_array($errorData) && isset($errorData['errors'])) {
                    foreach ($errorData['errors'] as $error) {
                        if (isset($error['detail'])) {
                            $errorMsg .= ' - ' . $error['detail'];
                            break;
                        }
                    }
                }

                error_log('Flight search failed: ' . $response);
                throw new Exception('Flight search failed: ' . $errorMsg);
            }

            $data = json_decode($response, true);

            if (!$data) {
                throw new Exception('Invalid API response - could not parse JSON');
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
