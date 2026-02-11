<?php

/**
 * Confirmation Page - Final Step of Booking Flow
 */
session_start();

require_once __DIR__ . '/../php/core/Security.php';
require_once __DIR__ . '/../php/core/BookingSession.php';

BookingSession::init();
$csrfToken = Security::generateCSRFToken();

// Get booking summary
$bookingSummary = BookingSession::getBookingSummary();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Exploreease</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .booking-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .step {
            flex: 1;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
            text-align: center;
            margin: 0 5px;
            font-weight: bold;
        }

        .step.active {
            background: #3498db;
            color: white;
        }

        .step.completed {
            background: #27ae60;
            color: white;
        }

        .summary-section {
            background: #f5f5f5;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            min-width: 200px;
        }

        .detail-value {
            color: #555;
            text-align: right;
            flex: 1;
        }

        .price-highlight {
            font-size: 24px;
            color: #27ae60;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-confirm {
            background: #27ae60;
            color: white;
        }

        .btn-confirm:hover:not(:disabled) {
            background: #229954;
        }

        .btn-back {
            background: #95a5a6;
            color: white;
        }

        .btn-back:hover {
            background: #7f8c8d;
        }

        button:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-error {
            background: #ffe6e6;
            color: #c0392b;
            border: 1px solid #e74c3c;
        }

        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #90caf9;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 600px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }

        .terms {
            background: #fff9e6;
            border: 1px solid #f0ad4e;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #333;
        }

        .terms input {
            margin-right: 8px;
        }

        .terms label {
            display: flex;
            align-items: center;
            font-weight: normal;
            margin-bottom: 0;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.show {
            display: block;
        }

        .checkmark {
            color: #27ae60;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <div class="step-indicator">
            <div class="step completed">1. Search</div>
            <div class="step completed">2. Select</div>
            <div class="step completed">3. Personal</div>
            <div class="step completed">4. Payment</div>
            <div class="step active">5. Confirm</div>
        </div>

        <h1>Confirm Your Booking</h1>
        <p>Please review your booking details below</p>

        <div id="alertContainer"></div>

        <?php if ($bookingSummary['flight']): ?>
            <div class="summary-section">
                <div class="section-title">✈️ Flight Details</div>
                <div class="grid-2">
                    <div>
                        <div class="detail-row">
                            <span class="detail-label">Route</span>
                            <span class="detail-value">
                                <?php echo Security::escapeHTML($bookingSummary['search']['origin']); ?> →
                                <?php echo Security::escapeHTML($bookingSummary['search']['destination']); ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Departure</span>
                            <span class="detail-value">
                                <?php echo Security::escapeHTML($bookingSummary['search']['departure_date']); ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Departure Time</span>
                            <span class="detail-value">
                                <?php echo Security::escapeHTML($bookingSummary['flight']['departure_time']); ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="detail-row">
                            <span class="detail-label">Airline</span>
                            <span class="detail-value">
                                <?php echo Security::escapeHTML($bookingSummary['flight']['airline']); ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Flight Number</span>
                            <span class="detail-value">
                                <?php echo Security::escapeHTML($bookingSummary['flight']['flight_number']); ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Stops</span>
                            <span class="detail-value">
                                <?php echo $bookingSummary['flight']['stops'] == 0 ? 'Direct' : $bookingSummary['flight']['stops'] . ' stop(s)'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($bookingSummary['personal']): ?>
            <div class="summary-section">
                <div class="section-title">👤 Passenger Details</div>
                <div class="detail-row">
                    <span class="detail-label">Name</span>
                    <span class="detail-value">
                        <?php echo Security::escapeHTML($bookingSummary['personal']['name']); ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">
                        <?php echo Security::escapeHTML($bookingSummary['personal']['email']); ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone</span>
                    <span class="detail-value">
                        <?php echo Security::escapeHTML($bookingSummary['personal']['phone']); ?>
                    </span>
                </div>
                <?php if ($bookingSummary['personal']['passport_number']): ?>
                    <div class="detail-row">
                        <span class="detail-label">Passport Number</span>
                        <span class="detail-value">
                            <?php echo Security::escapeHTML($bookingSummary['personal']['passport_number']); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($bookingSummary['payment']): ?>
            <div class="summary-section">
                <div class="section-title">💳 Payment Details</div>
                <div class="detail-row">
                    <span class="detail-label">Cardholder Name</span>
                    <span class="detail-value">
                        <?php echo Security::escapeHTML($bookingSummary['payment']['cardholder_name']); ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Card Number</span>
                    <span class="detail-value">
                        <?php echo Security::escapeHTML($bookingSummary['payment']['card_number_masked']); ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Expiry Date</span>
                    <span class="detail-value">
                        <?php echo Security::escapeHTML($bookingSummary['payment']['expiry_date']); ?>
                    </span>
                </div>
                <div class="alert alert-info">
                    ℹ️ This is a booking confirmation only. No charge will be processed on this card at this time.
                </div>
            </div>
        <?php endif; ?>

        <div class="summary-section">
            <div class="section-title">💰 Booking Summary</div>
            <div class="detail-row">
                <span class="detail-label">Number of Passengers</span>
                <span class="detail-value">
                    <?php echo $bookingSummary['search']['passenger_count']; ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Price per Passenger</span>
                <span class="detail-value">
                    $<?php echo number_format(floatval($bookingSummary['flight']['price']), 2); ?>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Price</span>
                <span class="detail-value price-highlight">
                    $<?php echo number_format(floatval($bookingSummary['flight']['price']) * $bookingSummary['search']['passenger_count'], 2); ?>
                </span>
            </div>
        </div>

        <form id="confirmForm" method="POST" action="../php/handlers/confirm-booking.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <div class="terms">
                <label>
                    <input type="checkbox" id="terms" required>
                    <span>I agree to the terms and conditions and understand that a confirmation
                        email will be sent to <?php echo Security::escapeHTML($bookingSummary['personal']['email']); ?></span>
                </label>
            </div>

            <div id="loading" class="loading">
                <p>Processing your booking...</p>
            </div>

            <div class="button-group">
                <button type="button" class="btn-back" onclick="goBack()">Back</button>
                <button type="submit" class="btn-confirm" id="confirmBtn">Confirm & Complete Booking</button>
            </div>
        </form>
    </div>

    <script>
        const confirmForm = document.getElementById('confirmForm');
        const termsCheckbox = document.getElementById('terms');
        const confirmBtn = document.getElementById('confirmBtn');
        const loading = document.getElementById('loading');

        termsCheckbox.addEventListener('change', () => {
            confirmBtn.disabled = !termsCheckbox.checked;
        });

        confirmForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!termsCheckbox.checked) {
                showAlert('error', 'Please agree to the terms and conditions');
                return;
            }

            loading.classList.add('show');
            confirmBtn.disabled = true;

            try {
                const formData = new FormData(confirmForm);

                const response = await fetch('../php/handlers/confirm-booking.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    document.querySelector('h1').textContent = '✅ Booking Confirmed!';
                    document.querySelector('p').textContent = 'Your booking has been successfully completed.';

                    // Replace form with success message
                    confirmForm.innerHTML = `
                        <div class="alert" style="background: #e6ffe6; color: #27ae60; border: 1px solid #27ae60;">
                            <strong>Success!</strong> A confirmation email has been sent to <strong>${result.data.customer_email}</strong>
                            <br><br>
                            <strong>Booking Reference: ${result.data.booking_reference}</strong>
                            <br><br>
                            Please check your email for complete booking details.
                        </div>
                        <div class="button-group">
                            <button type="button" class="btn-confirm" onclick="window.location.href='../index.html'">
                                Return to Home
                            </button>
                        </div>
                    `;
                } else {
                    showAlert('error', result.message || 'Failed to confirm booking');
                    loading.classList.remove('show');
                    confirmBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
                loading.classList.remove('show');
                confirmBtn.disabled = false;
            }
        });

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            document.getElementById('alertContainer').appendChild(alertDiv);
        }

        function goBack() {
            window.location.href = 'payment-details.php';
        }
    </script>
</body>

</html>