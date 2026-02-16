<?php

/**
 * Payment Details Page - Step 4 of Booking Flow
 * 
 * ⚠️ SECURITY NOTES:
 * - Full card numbers are NEVER sent or stored
 * - Masked versions are shown only after validation
 * - CVV is validated but NEVER stored
 * - This is for booking confirmation only, NOT actual payment
 */
session_start();

require_once __DIR__ . '/../php/core/Security.php';
require_once __DIR__ . '/../php/core/BookingSession.php';

BookingSession::init();
$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Exploreease</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .booking-container {
            max-width: 700px;
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

        .security-notice {
            background: #fffacd;
            border: 2px solid #f39c12;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #333;
        }

        .security-notice strong {
            display: block;
            margin-bottom: 5px;
            color: #e67e22;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        input[type="text"],
        input[type="tel"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
            letter-spacing: 0.1em;
        }

        input:focus {
            outline: none;
            border-color: #e74c3c;
            box-shadow: 0 0 5px rgba(231, 76, 60, 0.3);
        }

        .card-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .card-inputs-3col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 768px) {

            .card-inputs,
            .card-inputs-3col {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .step-indicator {
                display: none;
            }

            .no-lable {
                display: none;
            }

            .form-group {
                margin-bottom: 15px;
            }
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-continue {
            background: #27ae60;
            color: white;
        }

        .btn-continue:hover:not(:disabled) {
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

        .required-note {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 10px;
        }

        .card-logo {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            height: 24px;
            opacity: 0.3;
            pointer-events: none;
        }

        .card-input-wrapper {
            position: relative;
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <div class="step-indicator">
            <div class="step completed">1. Search</div>
            <div class="step completed">2. Select</div>
            <div class="step completed">3. Personal</div>
            <div class="step active">4. Payment</div>
            <div class="step">5. Confirm</div>
        </div>

        <h1>Payment Details</h1>
        <p>Enter your card information for booking confirmation</p>

        <div class="security-notice">
            <strong>ℹ️ Important Security Information:</strong>
            This is a booking confirmation form only. No amount will be charged to your card.
            Your payment information is encrypted and used only for confirmation purposes.
        </div>

        <div id="alertContainer"></div>

        <form id="paymentForm" method="POST" action="../php/handlers/payment-details.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <div class="form-group">
                <label for="cardholder">Cardholder Name *</label>
                <input type="text" id="cardholder" name="cardholder_name"
                    placeholder="John Doe" required maxlength="100">
                <div id="cardholder_name-error" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="card_number">Card Number *</label>
                <div class="card-input-wrapper">
                    <input type="text" id="card_number" name="card_number"
                        placeholder="1234 5678 9012 3456" required maxlength="19"
                        inputmode="numeric" autocomplete="cc-number">
                </div>
                <div id="card_number-error" class="error-message"></div>
            </div>

            <div class="card-inputs-3col">
                <div class="form-group">
                    <label for="expiry">Expiry Date (MM/YY) *</label>
                    <input type="text" id="expiry" name="expiry_date"
                        placeholder="12/25" required maxlength="5"
                        inputmode="numeric" autocomplete="cc-exp">
                    <div id="expiry_date-error" class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="cvv">CVV *</label>
                    <input type="tel" id="cvv" name="cvv"
                        placeholder="123" required maxlength="4"
                        inputmode="numeric" autocomplete="cc-csc">
                    <div id="cvv-error" class="error-message"></div>
                </div>

                <div class="form-group">
                    <label class="no-lable">&nbsp;</label>
                    <small style="display: block; color: #7f8c8d; padding-top: 10px;">
                        3 or 4 digits on back of card
                    </small>
                </div>
            </div>

            <div class="required-note">
                * Required fields • All information is encrypted
            </div>

            <div class="button-group">
                <button type="button" class="btn-back" onclick="goBack()">Back</button>
                <button type="submit" class="btn-continue">Proceed</button>
            </div>
        </form>
    </div>

    <script>
        const paymentForm = document.getElementById('paymentForm');
        const cardNumberInput = document.getElementById('card_number');
        const expiryInput = document.getElementById('expiry');

        // Format card number with spaces
        cardNumberInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\s/g, '');
            value = value.replace(/\D/g, ''); // Remove non-digits
            value = value.substring(0, 16); // Max 16 digits

            // Add spaces every 4 digits
            let formatted = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = formatted;
        });

        // Format expiry date (MM/YY)
        expiryInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');

            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }

            e.target.value = value;
        });

        // Allow only digits in CVV
        document.getElementById('cvv').addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        paymentForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            clearErrors();

            const formData = new FormData(paymentForm);

            try {
                const response = await fetch('../php/handlers/payment-details.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'confirmation.php';
                } else {
                    showAlert('error', result.message);
                    if (result.errors) {
                        displayErrors(result.errors);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
            }
        });

        function displayErrors(errors) {
            for (const [field, message] of Object.entries(errors)) {
                const errorDiv = document.getElementById(`${field}-error`);
                if (errorDiv) {
                    errorDiv.textContent = message;
                }
            }
        }

        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
            });
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            document.getElementById('alertContainer').appendChild(alertDiv);
        }

        function goBack() {
            window.location.href = 'personal-details.php';
        }
    </script>
</body>

</html>