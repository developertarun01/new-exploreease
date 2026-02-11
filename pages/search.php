<?php

/**
 * Search Page - Step 1 of Booking Flow
 */
session_start();

// Generate CSRF token
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
    <title>Flight Search - Exploreease</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .booking-container {
            max-width: 800px;
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
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        button {
            background: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }

        button:hover {
            background: #2980b9;
        }

        button:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.show {
            display: block;
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

        .alert-success {
            background: #e6ffe6;
            color: #27ae60;
            border: 1px solid #27ae60;
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <div class="step-indicator">
            <div class="step active">1. Search</div>
            <div class="step">2. Select Flight</div>
            <div class="step">3. Personal Info</div>
            <div class="step">4. Payment</div>
            <div class="step">5. Confirm</div>
        </div>

        <h1>Search for Flights</h1>
        <p>Enter your travel details to find available flights</p>

        <div id="alertContainer"></div>

        <form id="searchForm" method="POST" action="../php/handlers/search.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="origin">Departure Airport *</label>
                    <input type="text" id="origin" name="origin" placeholder="e.g., JFK, LAX, LHR"
                        maxlength="3" required>
                    <div id="origin-error" class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="destination">Arrival Airport *</label>
                    <input type="text" id="destination" name="destination" placeholder="e.g., LHR, CDG, NRT"
                        maxlength="3" required>
                    <div id="destination-error" class="error-message"></div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="departure_date">Departure Date *</label>
                    <input type="date" id="departure_date" name="departure_date" required>
                    <div id="departure_date-error" class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="return_date">Return Date (Optional)</label>
                    <input type="date" id="return_date" name="return_date">
                    <div id="return_date-error" class="error-message"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="passengers">Number of Passengers *</label>
                <select id="passengers" name="passengers" required>
                    <option value="">Select number of passengers</option>
                    <option value="1">1 Passenger</option>
                    <option value="2">2 Passengers</option>
                    <option value="3">3 Passengers</option>
                    <option value="4">4 Passengers</option>
                    <option value="5">5 Passengers</option>
                    <option value="6">6 Passengers</option>
                    <option value="7">7 Passengers</option>
                    <option value="8">8 Passengers</option>
                    <option value="9">9 Passengers</option>
                </select>
                <div id="passengers-error" class="error-message"></div>
            </div>

            <button type="submit" id="searchBtn">Search Flights</button>
        </form>

        <div id="loading" class="loading">
            <p>Searching for flights...</p>
        </div>
    </div>

    <script>
        const searchForm = document.getElementById('searchForm');
        const loadingDiv = document.getElementById('loading');
        const alertContainer = document.getElementById('alertContainer');

        searchForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Clear previous errors
            clearErrors();
            alertContainer.innerHTML = '';

            // Show loading
            loadingDiv.classList.add('show');
            document.getElementById('searchBtn').disabled = true;

            try {
                const formData = new FormData(searchForm);

                // Convert to URL-encoded format (POST data)
                const response = await fetch('../php/handlers/search.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Store flights in session storage
                    sessionStorage.setItem('flights', JSON.stringify(result.data.flights));
                    sessionStorage.setItem('searchCriteria', JSON.stringify(result.data.search_criteria));

                    // Redirect to results page
                    window.location.href = 'results.php';
                } else {
                    // Show error
                    showAlert('error', result.message);
                    if (result.errors) {
                        displayErrors(result.errors);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
            } finally {
                loadingDiv.classList.remove('show');
                document.getElementById('searchBtn').disabled = false;
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
            alertContainer.appendChild(alertDiv);
        }

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('departure_date').setAttribute('min', today);
        document.getElementById('return_date').setAttribute('min', today);

        // Convert to uppercase for airport codes
        document.getElementById('origin').addEventListener('change', (e) => {
            e.target.value = e.target.value.toUpperCase();
        });

        document.getElementById('destination').addEventListener('change', (e) => {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>
</body>

</html>