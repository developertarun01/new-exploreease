<?php

/**
 * Validator Class - Validates input data for the entire booking flow
 */
class Validator
{
    private $errors = [];

    /**
     * Get validation errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check if validation passed
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Add error message
     */
    public function addError($field, $message)
    {
        $this->errors[$field] = $message;
    }

    /**
     * Validate required field
     */
    public function validateRequired($field, $value, $displayName)
    {
        if (empty(trim($value))) {
            $this->addError($field, "$displayName is required");
            return false;
        }
        return true;
    }

    /**
     * Validate email format
     */
    public function validateEmail($field, $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'Invalid email address');
            return false;
        }
        return true;
    }

    /**
     * Validate phone number (basic validation)
     */
    public function validatePhone($field, $phone)
    {
        // Remove common characters
        $cleanPhone = preg_replace('/[\s\-\(\)\.]/i', '', $phone);

        if (!preg_match('/^\+?[1-9]\d{1,14}$/', $cleanPhone)) {
            $this->addError($field, 'Invalid phone number');
            return false;
        }
        return true;
    }

    /**
     * Validate name (letters, spaces, hyphens only)
     */
    public function validateName($field, $name, $displayName)
    {
        if (!preg_match('/^[a-zA-Z\s\-\']+$/', $name)) {
            $this->addError($field, "$displayName can only contain letters, spaces, hyphens, and apostrophes");
            return false;
        }

        // Check minimum length
        if (strlen(trim($name)) < 2) {
            $this->addError($field, "$displayName must be at least 2 characters");
            return false;
        }

        // Check maximum length
        if (strlen($name) > 100) {
            $this->addError($field, "$displayName cannot exceed 100 characters");
            return false;
        }

        return true;
    }

    /**
     * Validate credit card number
     */
    public function validateCardNumber($field, $cardNumber)
    {
        if (empty($cardNumber)) {
            $this->addError($field, 'Card number is required');
            return false;
        }

        if (Security::validateCardNumber($cardNumber)) {
            return true;
        }

        $this->addError($field, 'Invalid card number');
        return false;
    }

    /**
     * Validate card expiry date (MM/YY format)
     */
    public function validateCardExpiry($field, $expiry)
    {
        if (empty($expiry)) {
            $this->addError($field, 'Expiry date is required');
            return false;
        }

        if (!preg_match('/^\d{2}\/\d{2}$/', $expiry)) {
            $this->addError($field, 'Expiry date must be in MM/YY format');
            return false;
        }

        list($month, $year) = explode('/', $expiry);
        $month = (int)$month;
        $year = (int)('20' . $year);
        $currentDate = new DateTime();

        if ($month < 1 || $month > 12) {
            $this->addError($field, 'Invalid month');
            return false;
        }

        // Create date for last day of expiry month
        $expiryDate = new DateTime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01');
        $expiryDate->modify('last day of this month');

        if ($expiryDate < $currentDate) {
            $this->addError($field, 'Card has expired');
            return false;
        }

        return true;
    }

    /**
     * Validate CVV (3-4 digits)
     */
    public function validateCVV($field, $cvv)
    {
        if (empty($cvv)) {
            $this->addError($field, 'CVV is required');
            return false;
        }

        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            $this->addError($field, 'CVV must be 3 or 4 digits');
            return false;
        }

        return true;
    }

    /**
     * Validate number of passengers
     */
    public function validatePassengerCount($field, $count)
    {
        $count = (int)$count;

        if ($count < 1 || $count > 9) {
            $this->addError($field, 'Number of passengers must be between 1 and 9');
            return false;
        }

        return true;
    }

    /**
     * Validate travel date (must be future date)
     */
    public function validateTravelDate($field, $date)
    {
        if (empty($date)) {
            $this->addError($field, 'Travel date is required');
            return false;
        }

        try {
            $travelDate = new DateTime($date);
            $today = new DateTime('today');

            if ($travelDate < $today) {
                $this->addError($field, 'Travel date must be in the future');
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->addError($field, 'Invalid date format');
            return false;
        }
    }

    /**
     * Validate airport code (IATA format)
     */
    public function validateAirportCode($field, $code)
    {
        $code = strtoupper($code);

        if (!preg_match('/^[A-Z]{3}$/', $code)) {
            $this->addError($field, 'Invalid airport code (must be 3 letters)');
            return false;
        }

        return true;
    }

    /**
     * Validate flight ID format (alphanumeric)
     */
    public function validateFlightId($field, $flightId)
    {
        if (!preg_match('/^[a-zA-Z0-9\-]+$/', $flightId)) {
            $this->addError($field, 'Invalid flight ID');
            return false;
        }

        return true;
    }

    /**
     * Validate string length
     */
    public function validateLength($field, $value, $minLength, $maxLength, $displayName)
    {
        $length = strlen(trim($value));

        if ($length < $minLength) {
            $this->addError($field, "$displayName must be at least $minLength characters");
            return false;
        }

        if ($length > $maxLength) {
            $this->addError($field, "$displayName cannot exceed $maxLength characters");
            return false;
        }

        return true;
    }

    /**
     * Clear all errors
     */
    public function clearErrors()
    {
        $this->errors = [];
    }
}
