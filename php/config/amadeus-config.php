<?php

/**
 * Amadeus API Configuration
 * 
 * IMPORTANT: Obtain credentials from https://developers.amadeus.com/
 * For testing, use the Amadeus Sandbox environment
 */

// Load .env file
require_once __DIR__ . '/../core/EnvLoader.php';
EnvLoader::load();

return [
    'client_id' => getenv('AMADEUS_CLIENT_ID') ?? 'YOUR_CLIENT_ID_HERE',
    'client_secret' => getenv('AMADEUS_CLIENT_SECRET') ?? 'YOUR_CLIENT_SECRET_HERE',
    'environment' => getenv('AMADEUS_ENV') ?? 'test', // 'test' for sandbox, 'prod' for production
    'base_url_test' => 'https://test.api.amadeus.com',
    'base_url_prod' => 'https://api.amadeus.com',
];
