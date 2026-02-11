<?php

/**
 * Booking Confirmation Handler - Final Step
 * Sends confirmation emails and completes booking
 */

header('Content-Type: application/json');

// Include core classes and PHPMailer
require_once __DIR__ . '/../core/Security.php';
require_once __DIR__ . '/../core/BookingSession.php';
require_once __DIR__ . '/../core/EmailService.php';
require_once __DIR__ . '/../../PHPMailer/Exception.php';
require_once __DIR__ . '/../../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/SMTP.php';

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

    // Check if booking is complete
    if (!BookingSession::isBookingComplete()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Booking is incomplete. Please complete all steps.']);
        exit;
    }

    // Check for session timeout (30 minutes)
    if (BookingSession::isSessionExpired()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Your booking session has expired. Please start over.']);
        exit;
    }

    // Get complete booking summary
    $bookingSummary = BookingSession::getBookingSummary();

    // Send emails
    $emailService = new EmailService();

    // Send confirmation email to customer
    try {
        $emailService->sendCustomerConfirmation($bookingSummary);
    } catch (Exception $e) {
        // Log email error but don't fail the booking
        error_log('Customer email failed: ' . $e->getMessage());
    }

    // Send notification to admin
    try {
        $emailService->sendAdminNotification(
            $bookingSummary,
            $bookingSummary['personal']['email']
        );
    } catch (Exception $e) {
        // Log email error but don't fail the booking
        error_log('Admin email failed: ' . $e->getMessage());
    }

    // Generate booking reference
    $bookingReference = 'BOOK-' . strtoupper(substr(md5(time()), 0, 6));

    // Get customer email for response
    $customerEmail = $bookingSummary['personal']['email'];

    // Clear booking session (delete all temporary data)
    BookingSession::clearBookingData();

    // Regenerate session ID for security
    BookingSession::regenerateSessionId();

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Booking confirmed successfully!',
        'data' => [
            'booking_reference' => $bookingReference,
            'customer_email' => Security::escapeHTML($customerEmail),
            'confirmation_message' => 'A confirmation email has been sent to your email address.'
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log('Booking confirmation error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while confirming your booking. Please contact support.'
    ]);

    exit;
}
