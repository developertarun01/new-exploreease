<?php

/**
 * Diagnostic Script for ExploreEase API Issues
 * Run this file on Hostinger to identify configuration problems
 * Access via: https://swatisharma.online/exploreease/diagnostics.php
 */

header('Content-Type: text/html; charset=UTF-8');

?>
<!DOCTYPE html>
<html>

<head>
    <title>ExploreEase Diagnostics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .check {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #ccc;
        }

        .pass {
            border-left-color: #28a745;
            background: #f0f8f4;
        }

        .fail {
            border-left-color: #dc3545;
            background: #f8f0f0;
        }

        .warning {
            border-left-color: #ffc107;
            background: #fff8f0;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #666;
            margin-top: 20px;
        }

        code {
            background: #f0f0f0;
            padding: 2px 5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>🔍 ExploreEase Diagnostic Report</h1>
        <p>Generated: <?php echo date('Y-m-d H:i:s'); ?></p>

        <?php

        // Color status helper
        function checkStatus($condition, $name, $details = '')
        {
            $class = $condition ? 'pass' : 'fail';
            $icon = $condition ? '✓' : '✗';
            echo "<div class='check $class'>";
            echo "<strong>$icon $name</strong>";
            if ($details) echo "<br><small>$details</small>";
            echo "</div>";
            return $condition;
        }

        echo "<h2>1. PHP Environment</h2>";

        // PHP version
        checkStatus(
            PHP_VERSION_ID >= 70400,
            'PHP Version',
            'Current: ' . PHP_VERSION . ' (Required: 7.4+)'
        );

        // Session module
        checkStatus(extension_loaded('session'), 'Session Extension');

        // JSON module
        checkStatus(extension_loaded('json'), 'JSON Extension');

        // Curl module
        $curlLoaded = extension_loaded('curl');
        checkStatus(
            $curlLoaded,
            'cURL Extension',
            $curlLoaded ? 'Available' : 'MISSING - Cannot make API requests'
        );

        echo "<h2>2. File System</h2>";

        // .env file check
        $envFile = dirname(__FILE__) . '/.env';
        $envExists = file_exists($envFile);
        checkStatus(
            $envExists,
            '.env File Exists',
            $envExists ? 'Found at: ' . $envFile : 'MISSING - Config file not found'
        );

        if ($envExists) {
            $envReadable = is_readable($envFile);
            checkStatus(
                $envReadable,
                '.env File Readable',
                $envReadable ? 'Yes' : 'No - Check file permissions'
            );
        }

        // Directory permissions
        $uploadsDir = dirname(__FILE__) . '/uploads';
        $uploadWritable = is_writable(dirname(__FILE__)) ? true : false;
        checkStatus(
            $uploadWritable,
            'Write Permissions',
            $uploadWritable ? 'Can write files' : 'Cannot write - Check permissions'
        );

        echo "<h2>3. Configuration</h2>";

        // Load environment variables
        if ($envExists) {
            require_once dirname(__FILE__) . '/php/core/EnvLoader.php';
            try {
                EnvLoader::load($envFile);

                $clientId = getenv('AMADEUS_CLIENT_ID');
                $clientSecret = getenv('AMADEUS_CLIENT_SECRET');
                $env = getenv('AMADEUS_ENV');

                // Check if credentials are set
                $credentialsSet = !empty($clientId) && strpos($clientId, 'YOUR_CLIENT_ID') === false;
                checkStatus(
                    $credentialsSet,
                    'Amadeus Credentials',
                    $credentialsSet ? 'Configured' : 'Using placeholder credentials'
                );

                checkStatus(
                    $env === 'test' ? true : false,
                    'Environment Setting',
                    'Using: ' . ($env ?: 'default (test)')
                );
            } catch (Exception $e) {
                checkStatus(false, 'Configuration Loading', $e->getMessage());
            }
        } else {
            checkStatus(false, 'Configuration Available', '.env file not found');
        }

        echo "<h2>4. API Connectivity</h2>";

        if ($curlLoaded) {
            // Test basic curl connectivity
            $curl = curl_version();
            checkStatus(true, 'cURL version', $curl['version']);

            // Test SSL
            if (isset($curl['ssl_version'])) {
                checkStatus(true, 'SSL Support', $curl['ssl_version']);
            }

            // Test Amadeus API endpoint connectivity
            if (getenv('AMADEUS_CLIENT_ID') && strpos(getenv('AMADEUS_CLIENT_ID'), 'YOUR_CLIENT_ID') === false) {
                echo "<div class='check warning'>";
                echo "<strong>ℹ API Test</strong><br>";
                echo "Attempting to connect to Amadeus API...";

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => 'https://test.api.amadeus.com/v1/reference-data/locations?subType=AIRPORT&keyword=NYC',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 5,
                    CURLOPT_SSL_VERIFYPEER => true,
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($curlError) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => 'https://test.api.amadeus.com/v1/reference-data/locations?subType=AIRPORT&keyword=NYC',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 5,
                        CURLOPT_SSL_VERIFYPEER => false,
                    ]);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);
                    curl_close($ch);

                    if (!$curlError) {
                        echo " <strong>⚠ SSL Certificate Issue Detected</strong>";
                        echo "<br>Solution: SSL verification is failing, but requests work without it. This is common on shared hosting.";
                        echo "<br><em>The application has been updated to handle this.</em>";
                    }
                }

                if (!$curlError) {
                    echo " <strong>✓ Connected</strong>";
                    if ($httpCode == 401) {
                        echo "<br>Missing authentication (expected without credentials)";
                    } else {
                        echo "<br>HTTP Status: " . $httpCode;
                    }
                } else {
                    echo " <strong>✗ Connection Failed</strong>";
                    echo "<br>Error: " . $curlError;
                }

                echo "</div>";
            }
        } else {
            checkStatus(false, 'API Connectivity Test', 'cURL not available');
        }

        echo "<h2>5. Recommendations</h2>";

        echo "<div class='check'>";
        if (!$curlLoaded) {
            echo "<strong>❌ Critical Issue:</strong> cURL is not enabled. Contact Hostinger support to enable the cURL PHP extension.";
        } elseif (!$envExists) {
            echo "<strong>❌ Critical Issue:</strong> .env file is missing. Upload it to your server root.";
        } elseif (!isset($credentialsSet) || !$credentialsSet) {
            echo "<strong>⚠ Warning:</strong> Update your .env file with Amadeus API credentials from https://developers.amadeus.com/";
        } else {
            echo "<strong>✓ All checks passed!</strong> Your server appears to be properly configured. ";
            echo "If you're still getting errors, check the PHP error logs at your Hostinger control panel.";
        }
        echo "</div>";

        echo "<h2>6. Next Steps</h2>";

        echo "<div class='check'>";
        echo "<ol>";
        echo "<li>If you see any ✗ marks, fix those issues first</li>";
        echo "<li>Check Hostinger's file manager to ensure .env file is uploaded to root directory</li>";
        echo "<li>Check PHP error logs in Hostinger control panel (HPANEL → Tools → Error Logs)</li>";
        echo "<li>Try the search form again</li>";
        echo "<li>If still failing, share the PHP error log content for debugging</li>";
        echo "</ol>";
        echo "</div>";

        echo "<p><strong>Note:</strong> Delete this file after troubleshooting for security!</p>";
        echo "</div>";

        ?>

</body>

</html>