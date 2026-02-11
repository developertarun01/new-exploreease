<?php

/**
 * Simple .env file loader
 * Loads environment variables from .env file into $_ENV and getenv()
 */
class EnvLoader
{
    public static function load($envFile = null)
    {
        // Determine .env file location
        if (!$envFile) {
            // Go up to project root from current directory
            $envFile = dirname(__DIR__, 2) . '/.env';
        }

        // Check if .env exists
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found at: ' . $envFile);
        }

        // Read .env file
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)
            ) {
                $value = substr($value, 1, -1);
            }

            // Set environment variable
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}
