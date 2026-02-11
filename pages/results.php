<?php

/**
 * Flight Results Page - Step 2 of Booking Flow
 */
session_start();

require_once __DIR__ . '/../php/core/Security.php';
require_once __DIR__ . '/../php/core/BookingSession.php';

BookingSession::init();

// Determine which version to use based on some condition
// For now, I'm assuming you want to use the second version with proper CSRF token
$useModernVersion = true; // Set this based on your actual requirements

if ($useModernVersion) {
    $csrfToken = bin2hex(random_bytes(32)); // Generate CSRF token
    $_SESSION['csrf_token'] = $csrfToken;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Flight - Exploreease</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <style>
        /* Consolidated and deduplicated CSS */
        :root {
            --primary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --dark: #2c3e50;
            --gray: #7f8c8d;
            --light: #ecf0f1;
            --white: #ffffff;
        }

        .booking-container,
        .results-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Step Indicator - Consolidated */
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
            transition: background 0.3s;
        }

        .step.active {
            background: var(--primary);
            color: white;
        }

        .step.completed {
            background: var(--success);
            color: white;
        }

        /* Search Criteria/Summary - Consolidated */
        .search-criteria,
        .search-summary {
            background: var(--light);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .search-criteria {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .criteria-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .criteria-label,
        .detail-label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
        }

        .criteria-value {
            font-size: 16px;
            font-weight: 700;
            color: var(--dark);
        }

        /* Flight Cards - Consolidated */
        .flights-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .flight-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background: var(--white);
            transition: all 0.3s;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .flight-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        }

        .flight-card.selected {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .flight-info {
            flex: 1;
        }

        .flight-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            width: 100%;
        }

        .flight-airline {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .airline-code {
            font-weight: 700;
            font-size: 18px;
            color: var(--primary);
            min-width: 50px;
        }

        .flight-meta {
            font-size: 12px;
            color: var(--gray);
        }

        .flight-route {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 20px;
            align-items: center;
            margin: 15px 0;
            font-size: 24px;
            font-weight: bold;
            color: var(--dark);
        }

        .time-section {
            text-align: center;
        }

        .time-large {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }

        .airport-code {
            font-size: 12px;
            color: var(--gray);
            margin-top: 5px;
        }

        .journey-icon {
            color: #95a5a6;
            font-size: 18px;
        }

        .details-row,
        .flight-details {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            font-size: 13px;
            color: #666;
            margin-top: 10px;
        }

        .detail-item,
        .flight-detail-item {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .flight-price-section,
        .flight-price {
            text-align: right;
            margin-left: 20px;
        }

        .flight-price,
        .price {
            font-size: 28px;
            font-weight: 700;
            color: var(--success);
        }

        .currency {
            font-size: 14px;
            color: var(--gray);
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-results-icon {
            font-size: 64px;
            color: #bdc3c7;
            margin-bottom: 15px;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        /* Alerts */
        .error-message,
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error-message,
        .alert-error {
            background: #ffe6e6;
            color: #c0392b;
            border: 1px solid var(--danger);
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn,
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

        .btn-continue,
        .btn-continue {
            background: var(--success);
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
            background: var(--gray);
        }

        .btn:disabled,
        button:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .flight-route {
                grid-template-columns: 1fr;
            }

            .search-criteria {
                flex-direction: column;
                align-items: flex-start;
            }

            .details-row,
            .flight-details {
                grid-template-columns: repeat(2, 1fr);
            }

            .flight-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .flight-price-section,
            .flight-price {
                text-align: left;
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <!-- Step Indicator (Consolidated) -->
        <div class="step-indicator">
            <div class="step completed">1. Search</div>
            <div class="step active">2. Select Flight</div>
            <div class="step">3. Personal Info</div>
            <div class="step">4. Payment</div>
            <div class="step">5. Confirm</div>
        </div>

        <h1>Select Your Flight</h1>

        <!-- Error/Alert Container -->
        <div id="errorContainer"></div>
        <div id="alertContainer"></div>

        <!-- Loading Container -->
        <div id="loadingContainer" class="loading">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; margin-bottom: 20px;"></i>
            <p>Loading your flights...</p>
        </div>

        <!-- Main Content Container -->
        <div id="contentContainer" style="display: none;">
            <!-- Search Criteria/Summary -->
            <div id="searchCriteria" class="search-criteria"></div>
            <div id="searchSummary" class="search-summary" style="display: none;"></div>

            <!-- Flights List -->
            <div id="flightsList" class="flights-list"></div>
            <div id="flightsContainer" style="display: none;"></div>

            <!-- No Results -->
            <div id="noResults" class="no-results" style="display: none;">
                <div class="no-results-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>No flights found</h3>
                <p>Try adjusting your search criteria</p>
                <button class="btn btn-back" onclick="window.location.href='/';" style="max-width: 300px; margin: 20px auto 0;">New Search</button>
            </div>

            <!-- Selection Form -->
            <form id="selectionForm" style="display: none;">
                <?php if (isset($csrfToken)): ?>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <?php endif; ?>
                <div id="selectedFlightInputs"></div>
                <div id="selectedFlightInput" style="display: none;"></div>
                
                <div class="button-group">
                    <button type="button" class="btn btn-back" onclick="goBack()">New Search</button>
                    <button type="submit" class="btn btn-continue" id="continueBtn" disabled>Continue to Personal Details</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Consolidated JavaScript
        let flights = [];
        let selectedFlightId = null;
        let selectedFlightIndex = null;
        let useModernImplementation = <?php echo $useModernVersion ? 'true' : 'false'; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            if (useModernImplementation) {
                initializeModern();
            } else {
                initializeLegacy();
            }
        });

        // Legacy Implementation (from first version)
        function initializeLegacy() {
            try {
                const flightDataJson = sessionStorage.getItem('flightResults');
                const searchDataJson = sessionStorage.getItem('searchCriteria');

                if (!flightDataJson) {
                    showError('No flight data found. Please search again.');
                    return;
                }

                const flightData = JSON.parse(flightDataJson);
                const searchData = searchDataJson ? JSON.parse(searchDataJson) : null;

                flights = flightData.flights || [];

                document.getElementById('loadingContainer').style.display = 'none';
                document.getElementById('contentContainer').style.display = 'block';

                if (searchData) {
                    displaySearchCriteria(searchData);
                }

                if (flights.length === 0) {
                    document.getElementById('flightsList').style.display = 'none';
                    document.getElementById('noResults').style.display = 'block';
                    document.getElementById('selectionForm').style.display = 'none';
                } else {
                    displayFlightsList();
                    document.getElementById('selectionForm').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading flights:', error);
                showError('Error loading flights: ' + error.message);
            }
        }

        // Modern Implementation (from second version)
        function initializeModern() {
            const flightsData = sessionStorage.getItem('flights') || sessionStorage.getItem('flightResults');
            const searchData = sessionStorage.getItem('searchCriteria');

            if (!flightsData || !searchData) {
                showAlert('error', 'No flights found. Please search again.');
                setTimeout(() => window.location.href = '/', 2000);
                return;
            }

            try {
                flights = JSON.parse(flightsData);
                if (!Array.isArray(flights) && flights.flights) {
                    flights = flights.flights;
                }
            } catch (e) {
                flights = [];
            }

            const searchCriteria = JSON.parse(searchData);

            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('contentContainer').style.display = 'block';

            displaySearchSummary(searchCriteria);
            document.getElementById('searchSummary').style.display = 'block';
            document.getElementById('searchCriteria').style.display = 'none';

            if (flights.length > 0) {
                displayFlightsModern();
                document.getElementById('selectionForm').style.display = 'block';
                document.getElementById('flightsList').style.display = 'block';
                document.getElementById('flightsContainer').style.display = 'none';
            } else {
                document.getElementById('flightsList').innerHTML = 
                    '<p style="text-align: center; color: #e74c3c;">No flights found. Please search again.</p>';
                document.getElementById('noResults').style.display = 'block';
            }
        }

        // Display Functions
        function displaySearchCriteria(data) {
            const html = `
                <div class="criteria-item">
                    <div class="criteria-label">From</div>
                    <div class="criteria-value">${escapeHtml(data.origin)}</div>
                </div>
                <div class="criteria-item">
                    <div class="criteria-label">To</div>
                    <div class="criteria-value">${escapeHtml(data.destination)}</div>
                </div>
                <div class="criteria-item">
                    <div class="criteria-label">Depart</div>
                    <div class="criteria-value">${formatDate(data.departure_date)}</div>
                </div>
                ${data.return_date ? `
                    <div class="criteria-item">
                        <div class="criteria-label">Return</div>
                        <div class="criteria-value">${formatDate(data.return_date)}</div>
                    </div>
                ` : ''}
                <div class="criteria-item">
                    <div class="criteria-label">Passengers</div>
                    <div class="criteria-value">${data.passengers || data.passenger_count || 1}</div>
                </div>
            `;
            document.getElementById('searchCriteria').innerHTML = html;
        }

        function displaySearchSummary(criteria) {
            const summary = document.getElementById('searchSummary');
            summary.innerHTML = `
                <strong>${criteria.origin || 'N/A'} → ${criteria.destination || 'N/A'}</strong> - 
                ${formatDate(criteria.departure_date)} (${criteria.passengers || criteria.passenger_count || 1} passenger${(criteria.passengers || criteria.passenger_count || 1) > 1 ? 's' : ''})
                ${criteria.return_date ? `• Return: ${formatDate(criteria.return_date)}` : '• One-way'}
            `;
            summary.style.display = 'block';
        }

        function displayFlightsList() {
            const container = document.getElementById('flightsList');
            container.innerHTML = '';
            flights.forEach((flight, index) => {
                container.appendChild(createFlightCard(flight, index, false));
            });
        }

        function displayFlightsModern() {
            const container = document.getElementById('flightsList');
            container.innerHTML = '';
            flights.forEach((flight, index) => {
                container.appendChild(createFlightCard(flight, index, true));
            });
        }

        function createFlightCard(flight, index, isModern = false) {
            const card = document.createElement('div');
            card.className = 'flight-card';
            card.dataset.index = index;
            card.dataset.flightId = flight.id || `flight-${index}`;

            const stopsText = flight.stops === 0 ? 'Direct' : `${flight.stops} stop${flight.stops > 1 ? 's' : ''}`;
            const duration = formatDuration(flight.duration);
            const price = parseFloat(flight.price).toFixed(2);
            const currency = flight.currency || 'USD';

            if (isModern) {
                // Modern card layout
                card.innerHTML = `
                    <div class="flight-info">
                        <div class="flight-route">${escapeHtml(flight.departure_airport)} → ${escapeHtml(flight.arrival_airport)}</div>
                        <div class="flight-details">
                            <div class="flight-detail-item">
                                <span class="detail-label">Depart:</span>
                                <span>${escapeHtml(flight.departure_time)}</span>
                            </div>
                            <div class="flight-detail-item">
                                <span class="detail-label">Arrive:</span>
                                <span>${escapeHtml(flight.arrival_time)}</span>
                            </div>
                            <div class="flight-detail-item">
                                <span class="detail-label">Duration:</span>
                                <span>${duration}</span>
                            </div>
                            <div class="flight-detail-item">
                                <span class="detail-label">Stops:</span>
                                <span>${stopsText}</span>
                            </div>
                        </div>
                        <div style="font-size: 12px; color: #7f8c8d; margin-top: 8px;">
                            ${escapeHtml(flight.airline)} • ${escapeHtml(flight.seat_class || 'ECONOMY')} • Flight ${escapeHtml(flight.flight_number)}
                        </div>
                    </div>
                    <div class="flight-price">
                        <div class="price">${currency} ${price}</div>
                        <div class="currency">${currency}</div>
                    </div>
                `;
            } else {
                // Legacy card layout
                card.innerHTML = `
                    <div class="flight-header">
                        <div class="flight-airline">
                            <div class="airline-code">${escapeHtml(flight.airline)}</div>
                            <div>
                                <div style="font-weight: 600;">#${escapeHtml(flight.flight_number)}</div>
                                <div class="flight-meta">${escapeHtml(flight.seat_class || 'ECONOMY')}</div>
                            </div>
                        </div>
                        <div class="flight-price-section">
                            <div class="flight-price">${currency} ${price}</div>
                        </div>
                    </div>
                    <div class="flight-route">
                        <div class="time-section">
                            <div class="time-large">${escapeHtml(flight.departure_time)}</div>
                            <div class="airport-code">${escapeHtml(flight.departure_airport)}</div>
                        </div>
                        <div style="text-align: center;">
                            <div class="journey-icon"><i class="fas fa-arrow-right"></i></div>
                            <div class="flight-meta" style="margin-top: 5px;">${stopsText}</div>
                        </div>
                        <div class="time-section" style="text-align: right;">
                            <div class="time-large">${escapeHtml(flight.arrival_time)}</div>
                            <div class="airport-code">${escapeHtml(flight.arrival_airport)}</div>
                        </div>
                    </div>
                    <div class="details-row">
                        <div class="detail-item">
                            <span class="detail-label">Duration:</span>
                            <span>${duration}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Stops:</span>
                            <span>${stopsText}</span>
                        </div>
                    </div>
                `;
            }

            card.addEventListener('click', () => selectFlight(flight, index, card));
            return card;
        }

        function selectFlight(flight, index, cardElement) {
            // Deselect previous
            if (selectedFlightId || selectedFlightIndex !== null) {
                const prevCard = document.querySelector(`[data-flight-id="${selectedFlightId}"]`) || 
                               document.querySelector(`[data-index="${selectedFlightIndex}"]`);
                if (prevCard) {
                    prevCard.classList.remove('selected');
                }
            }

            // Select new flight
            selectedFlightId = flight.id || index;
            selectedFlightIndex = index;
            cardElement.classList.add('selected');

            // Update hidden inputs (support both formats)
            const inputsModern = `
                <input type="hidden" name="id" value="${escapeHtml(flight.id || '')}">
                <input type="hidden" name="airline" value="${escapeHtml(flight.airline)}">
                <input type="hidden" name="flight_number" value="${escapeHtml(flight.flight_number)}">
                <input type="hidden" name="departure_airport" value="${escapeHtml(flight.departure_airport)}">
                <input type="hidden" name="arrival_airport" value="${escapeHtml(flight.arrival_airport)}">
                <input type="hidden" name="departure_time" value="${escapeHtml(flight.departure_time)}">
                <input type="hidden" name="arrival_time" value="${escapeHtml(flight.arrival_time)}">
                <input type="hidden" name="duration" value="${escapeHtml(flight.duration)}">
                <input type="hidden" name="stops" value="${flight.stops}">
                <input type="hidden" name="price" value="${flight.price}">
                <input type="hidden" name="currency" value="${escapeHtml(flight.currency || 'USD')}">
                <input type="hidden" name="seat_class" value="${escapeHtml(flight.seat_class || 'ECONOMY')}">
            `;

            const inputsLegacy = `
                <input type="hidden" name="selected_flight_index" value="${index}">
                <input type="hidden" name="flight_id" value="${escapeHtml(flight.id || '')}">
            `;

            document.getElementById('selectedFlightInputs').innerHTML = inputsLegacy;
            document.getElementById('selectedFlightInput').innerHTML = inputsModern;
            
            // Store selected flight in sessionStorage
            sessionStorage.setItem('selectedFlight', JSON.stringify(flight));

            // Enable continue button
            document.getElementById('continueBtn').disabled = false;
        }

        // Form submission handler
        document.getElementById('selectionForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            if (selectedFlightId === null && selectedFlightIndex === null) {
                if (useModernImplementation) {
                    showAlert('error', 'Please select a flight');
                } else {
                    alert('Please select a flight');
                }
                return;
            }

            if (useModernImplementation) {
                // Modern: AJAX submission
                const formData = new FormData(document.getElementById('selectionForm'));

                try {
                    const response = await fetch('../php/handlers/select-flight.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        window.location.href = 'personal-details.php';
                    } else {
                        showAlert('error', result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred. Please try again.');
                }
            } else {
                // Legacy: direct redirect
                window.location.href = 'personal-details.php';
            }
        });

        // Utility Functions
        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            errorContainer.innerHTML = `<div class="error-message">${escapeHtml(message)}</div>`;
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('contentContainer').style.display = 'block';
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            document.getElementById('alertContainer').innerHTML = '';
            document.getElementById('alertContainer').appendChild(alertDiv);
        }

        function goBack() {
            if (!useModernImplementation || confirm('Your flight selection will be retained. Continue?')) {
                window.location.href = '/';
            }
        }

        function formatDate(dateStr) {
            try {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch {
                return dateStr;
            }
        }

        function formatDuration(duration) {
            if (!duration) return 'N/A';
            const match = duration.match(/PT(\d+H)?(\d+M)?/);
            if (!match) return duration;
            let result = '';
            if (match[1]) result += match[1].replace('H', 'h ');
            if (match[2]) result += match[2].replace('M', 'm');
            return result.trim() || duration;
        }

        function escapeHtml(text) {
            if (text === undefined || text === null) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>

</html>