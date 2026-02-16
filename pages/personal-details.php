<?php

/**
 * Personal Details Page - Step 3 of Booking Flow
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
    <title>Personal Details - Exploreease</title>
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
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
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

        @media (max-width: 768px) {
            .step-indicator {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <div class="step-indicator">
            <div class="step completed">1. Search</div>
            <div class="step completed">2. Select</div>
            <div class="step active">3. Personal Info</div>
            <div class="step">4. Payment</div>
            <div class="step">5. Confirm</div>
        </div>

        <h1>Personal Details</h1>
        <p>Please enter your personal information</p>

        <div id="alertContainer"></div>

        <form id="personalForm" method="POST" action="../php/handlers/personal-details.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" placeholder="John Doe"
                    required maxlength="100">
                <div id="name-error" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" placeholder="john@example.com"
                    required maxlength="254">
                <div id="email-error" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" placeholder="+1 (555) 123-4567"
                    required maxlength="20">
                <div id="phone-error" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="passport">Passport Number (Optional)</label>
                <input type="text" id="passport" name="passport_number" placeholder="A12345678"
                    maxlength="20">
                <div id="passport-error" class="error-message"></div>
            </div>

            <div class="required-note">
                * Required fields
            </div>

            <div class="button-group">
                <button type="button" class="btn-back" onclick="goBack()">Back</button>
                <button type="submit" class="btn-continue">Continue to Payment</button>
            </div>
        </form>
    </div>

    <script>
        const personalForm = document.getElementById('personalForm');

        personalForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            clearErrors();

            const formData = new FormData(personalForm);

            try {
                const response = await fetch('../php/handlers/personal-details.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = 'payment-details.php';
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
            window.location.href = 'results.php';
        }
    </script>
</body>

</html>