<?php

/**
 * Email Class - Handles email sending using PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mail;
    private $adminEmail = 'tarunbusinessmail@gmail.com'; // Change to actual admin email
    private $fromEmail = 'tarunbusinessmail@gmail.com'; // Change to actual from email
    private $fromName = 'Exploreease Bookings';

    public function __construct()
    {
        // Initialize PHPMailer
        $this->mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $this->mail->isSMTP();
            $this->mail->Host = getenv('SMTP_HOST') ?? 'smtp.gmail.com'; // Change to your SMTP host
            $this->mail->SMTPAuth = true;
            $this->mail->Username = getenv('SMTP_USER') ?? 'tarunbusinessmail@gmail.com'; // Change to your email
            $this->mail->Password = getenv('SMTP_PASSWORD') ?? 'nfwocwqfsthgwbes'; // Change to your app password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;

            // Set default from address
            $this->mail->setFrom($this->fromEmail, $this->fromName);

            // Charset
            $this->mail->CharSet = PHPMailer::CHARSET_UTF8;
        } catch (Exception $e) {
            throw new Exception('PHPMailer initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Send booking confirmation email to customer
     */
    public function sendCustomerConfirmation($bookingSummary)
    {
        try {
            // Reset previous settings if sending multiple emails
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            // Recipient
            $this->mail->addAddress($bookingSummary['personal']['email']);

            // Subject
            $this->mail->Subject = 'Your Flight Booking Confirmation - Exploreease';

            // Build email body
            $emailBody = $this->buildCustomerEmailBody($bookingSummary);

            // HTML body
            $this->mail->isHTML(true);
            $this->mail->Body = $emailBody;

            // Plain text alternative
            $this->mail->AltBody = strip_tags($emailBody);

            // Send
            return $this->mail->send();
        } catch (Exception $e) {
            throw new Exception('Customer email failed: ' . $e->getMessage());
        }
    }

    /**
     * Send booking details to admin/support team
     */
    public function sendAdminNotification($bookingSummary, $customerEmail)
    {
        try {
            // Reset previous settings
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            // Admin recipient
            $this->mail->addAddress($this->adminEmail);

            // Subject
            $this->mail->Subject = 'New Flight Booking - ' . $bookingSummary['personal']['name'];

            // Build email body
            $emailBody = $this->buildAdminEmailBody($bookingSummary, $customerEmail);

            // HTML body
            $this->mail->isHTML(true);
            $this->mail->Body = $emailBody;

            // Plain text alternative
            $this->mail->AltBody = strip_tags($emailBody);

            // Send
            return $this->mail->send();
        } catch (Exception $e) {
            throw new Exception('Admin email failed: ' . $e->getMessage());
        }
    }

    /**
     * Build customer-friendly email body
     */
    private function buildCustomerEmailBody($booking)
    {
        // Safely extract data with default values
        $search = $booking['search'] ?? [];
        $flight = $booking['flight'] ?? [];
        $personal = $booking['personal'] ?? [];
        $payment = $booking['payment'] ?? [];

        // HTML escaping helper for security (prevents XSS)
        $escape = function ($value) {
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
        };

        // Number formatting helper for price
        $formatPrice = function ($price) {
            if ($price === null || $price === '') {
                return '0.00';
            }
            return number_format((float)$price, 2);
        };

        // Extract and escape all variables first
        $origin = $escape($search['origin'] ?? 'N/A');
        $destination = $escape($search['destination'] ?? 'N/A');
        $departureDate = $escape($search['departure_date'] ?? 'N/A');
        $returnDate = isset($search['return_date']) ? $escape($search['return_date']) : 'One-way flight';
        $passengerCount = $escape($search['passenger_count'] ?? '1');

        $departureDateTime = $escape($flight['departure_time'] ?? 'N/A');
        $arrivalDateTime = $escape($flight['arrival_time'] ?? 'N/A');
        $airline = $escape($flight['airline'] ?? 'N/A');
        $flightNumber = $escape($flight['flight_number'] ?? 'N/A');
        $flightPrice = $flight['price'] ?? 0;
        $formattedPrice = $formatPrice($flightPrice);

        $passengerName = $escape($personal['name'] ?? 'Not provided');
        $passengerEmail = $escape($personal['email'] ?? 'Not provided');
        $passengerPhone = $escape($personal['phone'] ?? 'Not provided');

        $cardMasked = $escape($payment['card_number_masked'] ?? '**** **** **** ****');

        // Current year for copyright
        $currentYear = date('Y');

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Exploreease</title>
    <style>
        /* Email-safe CSS - using tables for compatibility */
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            color: #333333; 
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .header { 
            background-color: #2c3e50; 
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .section { 
            background-color: #f8f9fa; 
            padding: 20px; 
            margin: 20px 0; 
            border-left: 4px solid #3498db;
            border-radius: 0 5px 5px 0;
        }
        .section h2 { 
            margin-top: 0; 
            margin-bottom: 15px;
            color: #2c3e50; 
            font-size: 20px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-row {
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label { 
            font-weight: bold; 
            color: #2c3e50; 
            padding: 10px 10px 10px 0;
            width: 40%;
        }
        .detail-value { 
            padding: 10px 0 10px 10px;
            color: #333333;
        }
        .footer { 
            background-color: #ecf0f1; 
            padding: 20px; 
            text-align: center; 
            font-size: 12px; 
            color: #7f8c8d;
            border-radius: 0 0 5px 5px;
        }
        .important { 
            color: #e74c3c; 
            font-weight: bold;
            background-color: #fdf0ed;
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0 0;
        }
        .price {
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
        }
        @media only screen and (max-width: 480px) {
            .container { padding: 10px; }
            .detail-table, .detail-row { display: block; }
            .detail-label, .detail-value { display: block; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✈️ Booking Confirmation</h1>
            <p>Thank you for booking with Exploreease!</p>
        </div>
        
        <div class="section">
            <h2>Flight Details</h2>
            <table class="detail-table" cellpadding="0" cellspacing="0">
                <tr class="detail-row">
                    <td class="detail-label">Route:</td>
                    <td class="detail-value">{$origin} → {$destination}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Departure:</td>
                    <td class="detail-value">{$departureDate} at {$departureDateTime}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Return:</td>
                    <td class="detail-value">{$returnDate}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Airline:</td>
                    <td class="detail-value">{$airline}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Flight Number:</td>
                    <td class="detail-value">{$flightNumber}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Passengers:</td>
                    <td class="detail-value">{$passengerCount}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Total Price:</td>
                    <td class="detail-value price">\${$formattedPrice}</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>Passenger Details</h2>
            <table class="detail-table" cellpadding="0" cellspacing="0">
                <tr class="detail-row">
                    <td class="detail-label">Full Name:</td>
                    <td class="detail-value">{$passengerName}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Email Address:</td>
                    <td class="detail-value">{$passengerEmail}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Phone Number:</td>
                    <td class="detail-value">{$passengerPhone}</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>Payment Information</h2>
            <table class="detail-table" cellpadding="0" cellspacing="0">
                <tr class="detail-row">
                    <td class="detail-label">Card Number:</td>
                    <td class="detail-value">{$cardMasked}</td>
                </tr>
            </table>
            <p class="important">⚠️ This is a booking confirmation only. No amount has been charged to your card.</p>
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Booking Reference:</strong> {$escape($booking['reference'] ?? strtoupper(uniqid()))}</p>
            <p style="margin: 10px 0;">Please keep this email for your records.</p>
            <p style="margin: 10px 0;">If you have any questions, contact us at <a href="mailto:support@exploreease.com" style="color: #3498db; text-decoration: none;">support@exploreease.com</a></p>
            <p style="margin: 15px 0 0 0; border-top: 1px solid #bdc3c7; padding-top: 15px;">&copy; {$currentYear} Exploreease. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Build admin notification email body
     */
    private function buildAdminEmailBody($booking, $customerEmail)
    {
        // Safely extract data with default values
        $search = $booking['search'] ?? [];
        $flight = $booking['flight'] ?? [];
        $personal = $booking['personal'] ?? [];
        $payment = $booking['payment'] ?? [];

        // HTML escaping helper for security (prevents XSS)
        $escape = function ($value) {
            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
        };

        // Number formatting helper for price
        $formatPrice = function ($price) {
            if ($price === null || $price === '') {
                return '0.00';
            }
            return number_format((float)$price, 2);
        };

        // Extract and escape all variables first
        $origin = $escape($search['origin'] ?? 'N/A');
        $destination = $escape($search['destination'] ?? 'N/A');
        $departureDate = $escape($search['departure_date'] ?? 'N/A');
        $passengerCount = $escape($search['passenger_count'] ?? '1');
        $returnDate = isset($search['return_date']) ? $escape($search['return_date']) : 'Not specified';

        $departureDateTime = $escape($flight['departure_time'] ?? 'N/A');
        $arrivalDateTime = $escape($flight['arrival_time'] ?? 'N/A');
        $airline = $escape($flight['airline'] ?? 'N/A');
        $flightNumber = $escape($flight['flight_number'] ?? 'N/A');
        $flightClass = $escape($flight['class'] ?? 'Economy');
        $flightPrice = $flight['price'] ?? 0;
        $formattedPrice = $formatPrice($flightPrice);

        $passengerName = $escape($personal['name'] ?? 'Not provided');
        $passengerEmail = $escape($personal['email'] ?? 'Not provided');
        $passengerPhone = $escape($personal['phone'] ?? 'Not provided');

        $cardholderName = $escape($payment['cardholder_name'] ?? 'Not provided');
        $cardMasked = $escape($payment['card_number_masked'] ?? '**** **** **** ****');
        $cardType = $escape($payment['card_type'] ?? 'Credit Card');

        // Customer email parameter
        $customerEmailEscaped = $escape($customerEmail);

        // Booking metadata
        $bookingTime = date('Y-m-d H:i:s');
        $bookingReference = $escape($booking['reference'] ?? strtoupper(uniqid('BK')));
        $bookingId = $escape($booking['id'] ?? 'N/A');

        // System info (for admin tracking) - FIXED: Extract server variables first
        $serverTime = date('Y-m-d H:i:s');
        $environment = $escape($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'production');
        $serverName = $escape($_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost');
        $currentYear = date('Y');

        // Set environment color class - FIXED: Added this missing variable
        $environmentClass = '';
        switch ($environment) {
            case 'production':
                $environmentClass = '#e74c3c';
                break;
            case 'staging':
                $environmentClass = '#f39c12';
                break;
            case 'development':
                $environmentClass = '#3498db';
                break;
            default:
                $environmentClass = '#7f8c8d';
        }

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Notification - Exploreease Admin</title>
    <style>
        /* Email-safe CSS */
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            color: #333333; 
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }
        .container { 
            max-width: 600px; 
            margin: 20px auto; 
            padding: 0;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header { 
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            letter-spacing: 1px;
        }
        .header .badge {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 15px;
            text-transform: uppercase;
        }
        .section { 
            background-color: #ffffff; 
            padding: 25px; 
            margin: 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .section:last-child {
            border-bottom: none;
        }
        .section h2 { 
            margin-top: 0; 
            margin-bottom: 20px;
            color: #2c3e50; 
            font-size: 18px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-row {
            border-bottom: 1px solid #ecf0f1;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label { 
            font-weight: bold; 
            color: #2c3e50; 
            padding: 12px 15px 12px 0;
            width: 160px;
            background-color: #f8f9fa;
            border-radius: 4px 0 0 4px;
        }
        .detail-value { 
            padding: 12px 0 12px 15px;
            color: #34495e;
            background-color: #ffffff;
        }
        .highlight {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .footer { 
            background-color: #ecf0f1; 
            padding: 20px; 
            text-align: center; 
            font-size: 12px; 
            color: #7f8c8d;
            border-radius: 0 0 8px 8px;
        }
        .price {
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
        }
        .admin-note {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin: 20px 0 0;
            font-size: 13px;
        }
        .booking-meta {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        @media only screen and (max-width: 480px) {
            .container { margin: 10px; }
            .section { padding: 15px; }
            .detail-label, .detail-value { 
                display: block; 
                width: 100%;
                padding: 8px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🆕 New Flight Booking</h1>
            <div class="badge">Action Required</div>
        </div>
        
        <div class="section">
            <div class="booking-meta">
                <table width="100%" cellpadding="5" cellspacing="0">
                    <tr>
                        <td><strong>Booking Reference:</strong></td>
                        <td>{$bookingReference}</td>
                    </tr>
                    <tr>
                        <td><strong>Booking ID:</strong></td>
                        <td>{$bookingId}</td>
                    </tr>
                    <tr>
                        <td><strong>Received:</strong></td>
                        <td>{$bookingTime}</td>
                    </tr>
                    <tr>
                        <td><strong>Environment:</strong></td>
                        <td><span style="color: {$environmentClass}; font-weight: bold; text-transform: uppercase;">{$environment}</span></td>
                    </tr>
                </table>
            </div>
            
            <h2>📋 Booking Summary</h2>
            <table class="detail-table" cellpadding="0" cellspacing="0">
                <tr class="detail-row">
                    <td class="detail-label">Customer Email:</td>
                    <td class="detail-value"><strong>{$customerEmailEscaped}</strong></td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Booking Time:</td>
                    <td class="detail-value">{$bookingTime}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Server Time:</td>
                    <td class="detail-value">{$serverTime}</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>✈️ Flight Information</h2>
            <table class="detail-table" cellpadding="0" cellspacing="0">
                <tr class="detail-row">
                    <td class="detail-label">Route:</td>
                    <td class="detail-value"><strong>{$origin}</strong> → <strong>{$destination}</strong></td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Departure:</td>
                    <td class="detail-value">{$departureDate} at {$departureDateTime}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Return:</td>
                    <td class="detail-value">{$returnDate}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Airline:</td>
                    <td class="detail-value">{$airline}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Flight Number:</td>
                    <td class="detail-value">{$flightNumber}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Class:</td>
                    <td class="detail-value">{$flightClass}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Passengers:</td>
                    <td class="detail-value">{$passengerCount}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Total Price:</td>
                    <td class="detail-value price">\${$formattedPrice}</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>👤 Customer Details</h2>
            <table class="detail-table" cellpadding="0" cellspacing="0">
                <tr class="detail-row">
                    <td class="detail-label">Full Name:</td>
                    <td class="detail-value">{$passengerName}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Email Address:</td>
                    <td class="detail-value"><a href="mailto:{$passengerEmail}" style="color: #3498db; text-decoration: none;">{$passengerEmail}</a></td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Phone Number:</td>
                    <td class="detail-value"><a href="tel:{$passengerPhone}" style="color: #3498db; text-decoration: none;">{$passengerPhone}</a></td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>💳 Payment Details (Masked)</h2>
            <table class="detail-table" cellpadding="0" cellspacing="0">
                <tr class="detail-row">
                    <td class="detail-label">Card Type:</td>
                    <td class="detail-value">{$cardType}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Cardholder Name:</td>
                    <td class="detail-value">{$cardholderName}</td>
                </tr>
                <tr class="detail-row">
                    <td class="detail-label">Card Number:</td>
                    <td class="detail-value">{$cardMasked}</td>
                </tr>
            </table>
            
            <div class="highlight">
                <strong>💡 Note:</strong> Payment verification required. No amount has been charged yet.
            </div>
        </div>
        
        <div class="section">
            <h2>⚡ Quick Actions</h2>
            <table width="100%" cellpadding="10" cellspacing="0">
                <tr>
                    <td align="center">
                        <a href="https://admin.exploreease.com/bookings/{$bookingId}/verify" 
                           style="display: inline-block; background-color: #27ae60; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 0 5px; font-weight: bold;">
                            ✅ Verify Booking
                        </a>
                        <a href="https://admin.exploreease.com/bookings/{$bookingId}/process" 
                           style="display: inline-block; background-color: #3498db; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 0 5px; font-weight: bold;">
                            🔄 Process Payment
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="admin-note">
            <strong>🔒 Security Notice:</strong> This is an automated admin notification. Customer payment data is partially masked according to PCI compliance. Never request full card details via email.
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0;"><strong>Exploreease Admin System</strong></p>
            <p style="margin: 5px 0;">This is an automated notification. Do not reply to this email.</p>
            <p style="margin: 15px 0 0 0; border-top: 1px solid #bdc3c7; padding-top: 15px;">
                &copy; {$currentYear} Exploreease. All rights reserved.
            </p>
            <p style="margin: 10px 0 0 0; font-size: 11px;">
                Server: {$serverName} | Environment: {$environment}
            </p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }
}
