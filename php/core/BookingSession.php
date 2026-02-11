<?php

/**
 * Session Class - Manages session data for the booking flow
 * Data is stored in memory only (no database)
 */
class BookingSession
{
    /**
     * Initialize session with secure settings
     */
    public static function init()
    {
        // Start session if not already started
        if (session_status() !== PHP_SESSION_ACTIVE) {
            // Set secure cookie parameters
            session_set_cookie_params([
                'lifetime' => 1800, // 30 minutes
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'] ?? '',
                'secure' => isset($_SERVER['HTTPS']), // Only HTTPS
                'httponly' => true, // Not accessible via JavaScript
                'samesite' => 'Strict' // CSRF protection
            ]);

            session_start();
        }

        // Initialize booking data if not exists
        if (!isset($_SESSION['booking'])) {
            $_SESSION['booking'] = [];
        }

        // Initialize timestamp for session expiry tracking
        if (!isset($_SESSION['booking_start_time'])) {
            $_SESSION['booking_start_time'] = time();
        }
    }

    /**
     * Set booking data
     */
    public static function setBookingData($key, $value)
    {
        $_SESSION['booking'][$key] = $value;
    }

    /**
     * Get booking data
     */
    public static function getBookingData($key, $default = null)
    {
        return $_SESSION['booking'][$key] ?? $default;
    }

    /**
     * Get all booking data
     */
    public static function getAllBookingData()
    {
        return $_SESSION['booking'] ?? [];
    }

    /**
     * Check if booking data exists
     */
    public static function hasBookingData($key)
    {
        return isset($_SESSION['booking'][$key]);
    }

    /**
     * Remove booking data
     */
    public static function removeBookingData($key)
    {
        unset($_SESSION['booking'][$key]);
    }

    /**
     * Set search criteria
     */
    public static function setSearchCriteria($origin, $destination, $departureDate, $passengers, $returnDate = null)
    {
        $_SESSION['booking']['search'] = [
            'origin' => $origin,
            'destination' => $destination,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
            'passenger_count' => $passengers,
            'created_at' => time()
        ];
    }

    /**
     * Get search criteria
     */
    public static function getSearchCriteria()
    {
        return $_SESSION['booking']['search'] ?? null;
    }

    /**
     * Set selected flight
     */
    public static function setSelectedFlight($flightData)
    {
        $_SESSION['booking']['flight'] = $flightData;
        $_SESSION['booking']['flight_selected_at'] = time();
    }

    /**
     * Get selected flight
     */
    public static function getSelectedFlight()
    {
        return $_SESSION['booking']['flight'] ?? null;
    }

    /**
     * Set personal details
     */
    public static function setPersonalDetails($name, $email, $phone, $passport = null)
    {
        $_SESSION['booking']['personal'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'passport_number' => $passport,
            'updated_at' => time()
        ];
    }

    /**
     * Get personal details
     */
    public static function getPersonalDetails()
    {
        return $_SESSION['booking']['personal'] ?? null;
    }

    /**
     * Set payment details (masked)
     */
    public static function setPaymentDetails($cardholderName, $cardNumber, $expiryDate, $lastFourDigits)
    {
        $_SESSION['booking']['payment'] = [
            'cardholder_name' => $cardholderName,
            'card_number_masked' => $cardNumber, // Already masked
            'card_last_four' => $lastFourDigits,
            'expiry_date' => $expiryDate, // Store as MM/YY, NEVER unmasked
            'updated_at' => time()
        ];

        // IMPORTANT: CVV is NOT stored, validated on client/form submission only
    }

    /**
     * Get payment details (already masked)
     */
    public static function getPaymentDetails()
    {
        return $_SESSION['booking']['payment'] ?? null;
    }

    /**
     * Get complete booking summary
     */
    public static function getBookingSummary()
    {
        return [
            'search' => self::getSearchCriteria(),
            'flight' => self::getSelectedFlight(),
            'personal' => self::getPersonalDetails(),
            'payment' => self::getPaymentDetails()
        ];
    }

    /**
     * Check if booking is complete (all steps filled)
     */
    public static function isBookingComplete()
    {
        return self::hasBookingData('search') &&
            self::hasBookingData('flight') &&
            self::hasBookingData('personal') &&
            self::hasBookingData('payment');
    }

    /**
     * Check if session has expired (30 minutes)
     */
    public static function isSessionExpired($timeoutMinutes = 30)
    {
        if (!isset($_SESSION['booking_start_time'])) {
            return false;
        }

        $elapsed = time() - $_SESSION['booking_start_time'];
        return $elapsed > ($timeoutMinutes * 60);
    }

    /**
     * Clear all booking data (after confirmation email sent)
     */
    public static function clearBookingData()
    {
        unset($_SESSION['booking']);
        unset($_SESSION['booking_start_time']);
    }

    /**
     * Clear all session data
     */
    public static function destroy()
    {
        session_destroy();
    }

    /**
     * Regenerate session ID (security best practice)
     */
    public static function regenerateSessionId()
    {
        session_regenerate_id(true);
    }
}
