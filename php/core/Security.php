<?php

/**
 * Security Class - Handles CSRF protection, input sanitization, and output encoding
 */
class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token)
    {
        if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        return true;
    }

    /**
     * Sanitize input - remove HTML tags and special characters
     */
    public static function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }

        // Remove leading/trailing whitespace
        $input = trim($input);

        // Remove HTML tags
        $input = strip_tags($input);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        return $input;
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email)
    {
        $email = trim($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return $email;
    }

    /**
     * Sanitize for output - escape for HTML display
     */
    public static function escapeHTML($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'escapeHTML'], $data);
        }

        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate URL format
     */
    public static function isValidUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Check for injection attempts
     */
    public static function checkForInjection($input)
    {
        if (is_array($input)) {
            foreach ($input as $value) {
                if (self::checkForInjection($value)) {
                    return true;
                }
            }
            return false;
        }

        // Check for common SQL injection patterns
        $dangerous_patterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bor\b.*=.*)/i',
            '/(\bdrop\b)/i',
            '/(\binsert\b)/i',
            '/(\bupdate\b)/i',
            '/(\bdelete\b)/i',
            '/(;\s*drop\b)/i'
        ];

        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mask credit card number - display only last 4 digits
     */
    public static function maskCardNumber($cardNumber)
    {
        // Remove spaces and dashes
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        // Show only last 4 digits
        if (strlen($cardNumber) >= 4) {
            return '**** **** **** ' . substr($cardNumber, -4);
        }

        return '****';
    }

    /**
     * Validate credit card format (basic Luhn algorithm)
     */
    public static function validateCardNumber($cardNumber)
    {
        // Remove spaces and dashes
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        // Check if it's between 13 and 19 digits
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }

        // Luhn algorithm
        $sum = 0;
        $isEven = false;

        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $digit = (int)$cardNumber[$i];

            if ($isEven) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $isEven = !$isEven;
        }

        return $sum % 10 === 0;
    }

    /**
     * Generate secure random token
     */
    public static function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Validate password strength (optional - if needed later)
     */
    public static function validatePasswordStrength($password)
    {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            return true;
        }
        return false;
    }
}
