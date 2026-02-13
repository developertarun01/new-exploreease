<?php

/**
 * Flight Results Page - Step 2 of Booking Flow
 */
session_start();

require_once __DIR__ . '/../php/core/Security.php';
require_once __DIR__ . '/../php/core/BookingSession.php';

BookingSession::init();

// Determine which version to use based on some condition
$useModernVersion = true; // Set this based on your actual requirements

if ($useModernVersion) {
    $csrfToken = bin2hex(random_bytes(32));
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
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background: var(--white);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Step Indicator */
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

        /* Layout */
        .results-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .results-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Filters Sidebar */
        .filters-sidebar {
            background: var(--white);
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light);
        }

        .filters-header h3 {
            margin: 0;
            color: var(--dark);
            font-size: 18px;
        }

        .clear-filters {
            color: var(--primary);
            background: none;
            border: none;
            padding: 5px 10px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: underline;
        }

        .filter-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .filter-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .filter-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .filter-checkbox,
        .filter-radio {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #555;
        }

        .filter-checkbox:hover,
        .filter-radio:hover {
            color: var(--primary);
        }

        .filter-checkbox input,
        .filter-radio input {
            cursor: pointer;
            width: 16px;
            height: 16px;
        }

        .filter-count {
            color: var(--gray);
            font-size: 12px;
            margin-left: auto;
        }

        /* Price Range Slider */
        .price-range {
            padding: 10px 0;
        }

        .price-inputs {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .price-input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .price-values {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 13px;
            color: var(--gray);
        }

        /* Active Filters */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: var(--white);
            border: 1px solid var(--primary);
            border-radius: 20px;
            font-size: 13px;
            color: var(--dark);
        }

        .filter-tag i {
            color: var(--gray);
            cursor: pointer;
            font-size: 12px;
        }

        .filter-tag i:hover {
            color: var(--danger);
        }

        /* Results Header */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .results-count {
            font-size: 15px;
            color: var(--gray);
        }

        .results-count strong {
            color: var(--dark);
            font-size: 18px;
        }

        .sort-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: var(--white);
            cursor: pointer;
        }

        /* Search Criteria */
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

        /* Flight Cards */
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
            position: relative;
        }

        .flight-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.2);
        }

        .flight-card.selected {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .duplicate-badge {
            position: absolute;
            top: -10px;
            right: 20px;
            background: var(--warning);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .identical-flight-badge {
            position: absolute;
            top: -10px;
            right: 20px;
            background: var(--gray);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .best-deal-badge {
            position: absolute;
            top: -10px;
            left: 20px;
            background: var(--success);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1;
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

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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

        .btn-apply {
            background: var(--primary);
            color: white;
            padding: 10px;
            margin-top: 10px;
        }

        .btn-apply:hover {
            background: #2980b9;
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

            .results-layout {
                grid-template-columns: 1fr;
            }

            .filters-sidebar {
                position: static;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="booking-container">
        <!-- Step Indicator -->
        <!-- <div class="step-indicator">
            <div class="step completed">1. Search</div>
            <div class="step active">2. Select Flight</div>
            <div class="step">3. Personal Info</div>
            <div class="step">4. Payment</div>
            <div class="step">5. Confirm</div>
        </div> -->

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

            <!-- Active Filters -->
            <div id="activeFilters" class="active-filters" style="display: none;"></div>

            <div class="results-layout">
                <!-- Filters Sidebar -->
                <div class="filters-sidebar">
                    <div class="filters-header">
                        <h3><i class="fas fa-filter" style="margin-right: 8px;"></i> Filters</h3>
                        <button id="clearAllFilters" class="clear-filters">Clear all</button>
                    </div>

                    <!-- Stops Filter -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-plane"></i> Stops
                        </div>
                        <div class="filter-options" id="stopsFilter">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="stops" value="0" id="filterNonStop">
                                <span>Non-stop</span>
                                <span class="filter-count" id="nonStopCount">0</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="stops" value="1" id="filter1Stop">
                                <span>1 Stop</span>
                                <span class="filter-count" id="oneStopCount">0</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="stops" value="2" id="filter2PlusStops">
                                <span>2+ Stops</span>
                                <span class="filter-count" id="twoPlusStopCount">0</span>
                            </label>
                        </div>
                    </div>

                    <!-- Airlines Filter -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-building"></i> Airlines
                        </div>
                        <div class="filter-options" id="airlinesFilter">
                            <!-- Dynamically populated -->
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-dollar-sign"></i> Price Range
                        </div>
                        <div class="price-range">
                            <div id="priceSlider" style="margin: 10px 0;"></div>
                            <div class="price-inputs">
                                <input type="number" id="minPrice" class="price-input" placeholder="Min" step="10">
                                <input type="number" id="maxPrice" class="price-input" placeholder="Max" step="10">
                            </div>
                            <button id="applyPriceFilter" class="btn btn-apply">Apply Price</button>
                        </div>
                    </div>

                    <!-- Departure Time Filter -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-clock"></i> Departure Time
                        </div>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="departure_time" value="morning" id="filterMorning">
                                <span>Morning (6AM - 12PM)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="departure_time" value="afternoon" id="filterAfternoon">
                                <span>Afternoon (12PM - 6PM)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="departure_time" value="evening" id="filterEvening">
                                <span>Evening (6PM - 12AM)</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="departure_time" value="night" id="filterNight">
                                <span>Night (12AM - 6AM)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Seat Class Filter -->
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="fas fa-chair"></i> Cabin Class
                        </div>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="seat_class" value="ECONOMY" id="filterEconomy">
                                <span>Economy</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="seat_class" value="PREMIUM_ECONOMY" id="filterPremiumEconomy">
                                <span>Premium Economy</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="seat_class" value="BUSINESS" id="filterBusiness">
                                <span>Business</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="seat_class" value="FIRST" id="filterFirst">
                                <span>First Class</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Results Area -->
                <div>
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-count">
                            <strong id="displayedFlightsCount">0</strong> <span id="totalFlightsCount"></span>
                        </div>
                        <select id="sortBy" class="sort-select">
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="duration">Duration</option>
                            <option value="departure">Earliest Departure</option>
                            <option value="arrival">Earliest Arrival</option>
                            <option value="stops">Least Stops</option>
                        </select>
                    </div>

                    <!-- Flights List -->
                    <div id="flightsList" class="flights-list"></div>
                    <div id="flightsContainer" style="display: none;"></div>

                    <!-- No Results -->
                    <div id="noResults" class="no-results" style="display: none;">
                        <div class="no-results-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3>No flights found</h3>
                        <p>Try adjusting your filters or search criteria</p>
                        <button class="btn btn-back" onclick="window.location.href='/';" style="max-width: 300px; margin: 20px auto 0;">New Search</button>
                    </div>
                </div>
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
        let originalFlights = [];
        let filteredFlights = [];
        let selectedFlightId = null;
        let selectedFlightIndex = null;
        let useModernImplementation = <?php echo $useModernVersion ? 'true' : 'false'; ?>;

        // Filter state
        let activeFilters = {
            stops: [],
            airlines: [],
            minPrice: null,
            maxPrice: null,
            departureTimes: [],
            seatClasses: []
        };

        document.addEventListener('DOMContentLoaded', function() {
            if (useModernImplementation) {
                initializeModern();
            } else {
                initializeLegacy();
            }

            // Initialize filter listeners
            initFilterListeners();
        });

        // Initialize filter event listeners
        function initFilterListeners() {
            // Stops filter
            document.querySelectorAll('input[name="stops"]').forEach(checkbox => {
                checkbox.addEventListener('change', applyFilters);
            });

            // Airlines filter (will be populated later)
            document.getElementById('clearAllFilters').addEventListener('click', clearAllFilters);
            document.getElementById('applyPriceFilter').addEventListener('click', applyPriceFilter);
            document.getElementById('sortBy').addEventListener('change', applySort);

            // Departure time filters
            document.querySelectorAll('input[name="departure_time"]').forEach(checkbox => {
                checkbox.addEventListener('change', applyFilters);
            });

            // Seat class filters
            document.querySelectorAll('input[name="seat_class"]').forEach(checkbox => {
                checkbox.addEventListener('change', applyFilters);
            });
        }

        /**
         * FIXED: Only remove EXACT duplicate flights (same flight number, same times, same route)
         * This preserves different flights that happen to share flight numbers
         */
        function removeExactDuplicateFlights(flightsArray) {
            const uniqueFlights = new Map();
            const duplicateGroups = new Map();

            // First pass: identify exact duplicates (same airline, same flight number, same times, same route)
            flightsArray.forEach(flight => {
                // Create a key that includes ALL identifying information
                const key = `${flight.airline || ''}|${flight.flight_number || ''}|${flight.departure_airport}|${flight.arrival_airport}|${flight.departure_time}|${flight.arrival_time}|${flight.seat_class || 'ECONOMY'}`;

                if (!uniqueFlights.has(key)) {
                    uniqueFlights.set(key, flight);
                    duplicateGroups.set(key, [flight]);
                } else {
                    // This is an exact duplicate - add to group for counting
                    duplicateGroups.get(key).push(flight);
                }
            });

            // Convert map to array and add duplicate count
            const result = Array.from(uniqueFlights.values()).map(flight => {
                const key = `${flight.airline || ''}|${flight.flight_number || ''}|${flight.departure_airport}|${flight.arrival_airport}|${flight.departure_time}|${flight.arrival_time}|${flight.seat_class || 'ECONOMY'}`;
                const group = duplicateGroups.get(key) || [flight];
                return {
                    ...flight,
                    exactDuplicateCount: group.length,
                    hasExactDuplicates: group.length > 1
                };
            });

            console.log(`Original flights: ${flightsArray.length}, After exact duplicate removal: ${result.length}`);
            return result;
        }

        // Legacy Implementation
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

                originalFlights = flightData.flights || [];

                // FIXED: Only remove EXACT duplicates
                originalFlights = removeExactDuplicateFlights(originalFlights);

                flights = [...originalFlights];
                filteredFlights = [...originalFlights];

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
                    initializeFilters();
                    displayFlightsList();
                    document.getElementById('selectionForm').style.display = 'block';
                }
            } catch (error) {
                console.error('Error loading flights:', error);
                showError('Error loading flights: ' + error.message);
            }
        }

        // Modern Implementation
        function initializeModern() {
            const flightsData = sessionStorage.getItem('flights') || sessionStorage.getItem('flightResults');
            const searchData = sessionStorage.getItem('searchCriteria');

            if (!flightsData || !searchData) {
                showAlert('error', 'No flights found. Please search again.');
                setTimeout(() => window.location.href = '/', 2000);
                return;
            }

            try {
                let parsedData = JSON.parse(flightsData);
                originalFlights = Array.isArray(parsedData) ? parsedData : (parsedData.flights || []);

                // FIXED: Only remove EXACT duplicates
                originalFlights = removeExactDuplicateFlights(originalFlights);

                flights = [...originalFlights];
                filteredFlights = [...originalFlights];
            } catch (e) {
                console.error('Error parsing flight data:', e);
                originalFlights = [];
                flights = [];
                filteredFlights = [];
            }

            const searchCriteria = JSON.parse(searchData);

            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('contentContainer').style.display = 'block';

            displaySearchSummary(searchCriteria);
            document.getElementById('searchSummary').style.display = 'block';
            document.getElementById('searchCriteria').style.display = 'none';

            if (flights.length > 0) {
                initializeFilters();
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

        // Initialize filter options based on available flights
        function initializeFilters() {
            // Update filter counts and options
            updateFilterCounts();
            populateAirlinesFilter();
            updatePriceRange();
            updateResultsCount();
        }

        // Update filter counts
        function updateFilterCounts() {
            const nonStopCount = originalFlights.filter(f => f.stops === 0).length;
            const oneStopCount = originalFlights.filter(f => f.stops === 1).length;
            const twoPlusStopCount = originalFlights.filter(f => f.stops >= 2).length;

            document.getElementById('nonStopCount').textContent = `(${nonStopCount})`;
            document.getElementById('oneStopCount').textContent = `(${oneStopCount})`;
            document.getElementById('twoPlusStopCount').textContent = `(${twoPlusStopCount})`;
        }

        // Populate airlines filter
        function populateAirlinesFilter() {
            const airlines = {};
            originalFlights.forEach(flight => {
                airlines[flight.airline] = (airlines[flight.airline] || 0) + 1;
            });

            const container = document.getElementById('airlinesFilter');
            container.innerHTML = '';

            Object.keys(airlines).sort().forEach(airline => {
                const label = document.createElement('label');
                label.className = 'filter-checkbox';
                label.innerHTML = `
                    <input type="checkbox" name="airline" value="${escapeHtml(airline)}">
                    <span>${escapeHtml(airline)}</span>
                    <span class="filter-count">(${airlines[airline]})</span>
                `;

                const checkbox = label.querySelector('input');
                checkbox.addEventListener('change', applyFilters);

                container.appendChild(label);
            });
        }

        // Update price range display
        function updatePriceRange() {
            if (originalFlights.length === 0) return;

            const prices = originalFlights.map(f => parseFloat(f.price));
            const minPrice = Math.min(...prices);
            const maxPrice = Math.max(...prices);

            document.getElementById('minPrice').placeholder = `Min $${Math.floor(minPrice)}`;
            document.getElementById('maxPrice').placeholder = `Max $${Math.ceil(maxPrice)}`;
        }

        // Apply price filter
        function applyPriceFilter() {
            const minPrice = parseFloat(document.getElementById('minPrice').value);
            const maxPrice = parseFloat(document.getElementById('maxPrice').value);

            if (!isNaN(minPrice)) activeFilters.minPrice = minPrice;
            if (!isNaN(maxPrice)) activeFilters.maxPrice = maxPrice;

            applyFilters();
        }

        // Apply all active filters
        function applyFilters() {
            // Update active filters
            activeFilters.stops = Array.from(document.querySelectorAll('input[name="stops"]:checked')).map(cb => cb.value);
            activeFilters.airlines = Array.from(document.querySelectorAll('input[name="airline"]:checked')).map(cb => cb.value);
            activeFilters.departureTimes = Array.from(document.querySelectorAll('input[name="departure_time"]:checked')).map(cb => cb.value);
            activeFilters.seatClasses = Array.from(document.querySelectorAll('input[name="seat_class"]:checked')).map(cb => cb.value);

            // Apply filters
            filteredFlights = originalFlights.filter(flight => {
                // Stops filter
                if (activeFilters.stops.length > 0) {
                    const flightStops = flight.stops >= 2 ? '2' : flight.stops.toString();
                    if (!activeFilters.stops.includes(flightStops) &&
                        !(flight.stops >= 2 && activeFilters.stops.includes('2'))) {
                        return false;
                    }
                }

                // Airlines filter
                if (activeFilters.airlines.length > 0 && !activeFilters.airlines.includes(flight.airline)) {
                    return false;
                }

                // Price filter
                const price = parseFloat(flight.price);
                if (activeFilters.minPrice !== null && price < activeFilters.minPrice) return false;
                if (activeFilters.maxPrice !== null && price > activeFilters.maxPrice) return false;

                // Departure time filter
                if (activeFilters.departureTimes.length > 0) {
                    const hour = parseInt(flight.departure_time.split(':')[0]);
                    let timeSlot;
                    if (hour >= 6 && hour < 12) timeSlot = 'morning';
                    else if (hour >= 12 && hour < 18) timeSlot = 'afternoon';
                    else if (hour >= 18 && hour < 24) timeSlot = 'evening';
                    else timeSlot = 'night';

                    if (!activeFilters.departureTimes.includes(timeSlot)) return false;
                }

                // Seat class filter
                if (activeFilters.seatClasses.length > 0) {
                    const seatClass = (flight.seat_class || 'ECONOMY').toUpperCase();
                    if (!activeFilters.seatClasses.includes(seatClass)) return false;
                }

                return true;
            });

            // Apply sorting
            applySort();

            // Update active filters display
            updateActiveFiltersDisplay();

            // Update results
            displayFilteredResults();
        }

        // Apply sorting
        function applySort() {
            const sortBy = document.getElementById('sortBy').value;

            switch (sortBy) {
                case 'price_low':
                    filteredFlights.sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
                    break;
                case 'price_high':
                    filteredFlights.sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
                    break;
                case 'duration':
                    filteredFlights.sort((a, b) => {
                        const durationA = parseDuration(a.duration);
                        const durationB = parseDuration(b.duration);
                        return durationA - durationB;
                    });
                    break;
                case 'departure':
                    filteredFlights.sort((a, b) => a.departure_time.localeCompare(b.departure_time));
                    break;
                case 'arrival':
                    filteredFlights.sort((a, b) => a.arrival_time.localeCompare(b.arrival_time));
                    break;
                case 'stops':
                    filteredFlights.sort((a, b) => (a.stops || 0) - (b.stops || 0));
                    break;
            }

            displayFilteredResults();
        }

        // Parse duration string to minutes
        function parseDuration(duration) {
            if (!duration) return 0;
            const match = duration.match(/PT(?:(\d+)H)?(?:(\d+)M)?/);
            const hours = parseInt(match[1] || 0);
            const minutes = parseInt(match[2] || 0);
            return hours * 60 + minutes;
        }

        // Update active filters display
        function updateActiveFiltersDisplay() {
            const container = document.getElementById('activeFilters');
            let filtersHtml = [];

            if (activeFilters.stops.length > 0) {
                const stopsText = activeFilters.stops.map(s => s === '0' ? 'Non-stop' : s === '1' ? '1 Stop' : '2+ Stops').join(', ');
                filtersHtml.push(createFilterTag('Stops', stopsText, 'stops'));
            }

            if (activeFilters.airlines.length > 0) {
                filtersHtml.push(createFilterTag('Airlines', activeFilters.airlines.join(', '), 'airlines'));
            }

            if (activeFilters.minPrice !== null || activeFilters.maxPrice !== null) {
                const priceText = `${activeFilters.minPrice !== null ? '$' + activeFilters.minPrice : '$0'} - ${activeFilters.maxPrice !== null ? '$' + activeFilters.maxPrice : 'Any'}`;
                filtersHtml.push(createFilterTag('Price', priceText, 'price'));
            }

            if (activeFilters.departureTimes.length > 0) {
                filtersHtml.push(createFilterTag('Departure', activeFilters.departureTimes.join(', '), 'departure_times'));
            }

            if (activeFilters.seatClasses.length > 0) {
                filtersHtml.push(createFilterTag('Cabin', activeFilters.seatClasses.join(', '), 'seat_classes'));
            }

            if (filtersHtml.length > 0) {
                container.innerHTML = filtersHtml.join('');
                container.style.display = 'flex';
            } else {
                container.style.display = 'none';
            }
        }

        function createFilterTag(label, value, filterType) {
            return `
                <div class="filter-tag">
                    <span><strong>${label}:</strong> ${value}</span>
                    <i class="fas fa-times" onclick="removeFilter('${filterType}')"></i>
                </div>
            `;
        }

        // Remove specific filter
        function removeFilter(filterType) {
            switch (filterType) {
                case 'stops':
                    document.querySelectorAll('input[name="stops"]').forEach(cb => cb.checked = false);
                    activeFilters.stops = [];
                    break;
                case 'airlines':
                    document.querySelectorAll('input[name="airline"]').forEach(cb => cb.checked = false);
                    activeFilters.airlines = [];
                    break;
                case 'price':
                    document.getElementById('minPrice').value = '';
                    document.getElementById('maxPrice').value = '';
                    activeFilters.minPrice = null;
                    activeFilters.maxPrice = null;
                    break;
                case 'departure_times':
                    document.querySelectorAll('input[name="departure_time"]').forEach(cb => cb.checked = false);
                    activeFilters.departureTimes = [];
                    break;
                case 'seat_classes':
                    document.querySelectorAll('input[name="seat_class"]').forEach(cb => cb.checked = false);
                    activeFilters.seatClasses = [];
                    break;
            }
            applyFilters();
        }

        // Clear all filters
        function clearAllFilters() {
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';

            activeFilters = {
                stops: [],
                airlines: [],
                minPrice: null,
                maxPrice: null,
                departureTimes: [],
                seatClasses: []
            };

            applyFilters();
        }

        // Display filtered results
        function displayFilteredResults() {
            const container = document.getElementById('flightsList');
            container.innerHTML = '';

            if (filteredFlights.length === 0) {
                document.getElementById('noResults').style.display = 'block';
                document.getElementById('flightsList').style.display = 'none';
                updateResultsCount();
                return;
            }

            document.getElementById('noResults').style.display = 'none';
            document.getElementById('flightsList').style.display = 'block';

            // Display all filtered flights (no grouping - show all unique flights)
            filteredFlights.forEach((flight, index) => {
                const isModern = document.getElementById('searchSummary').style.display === 'block';
                const card = createFlightCard(flight, index, isModern);

                // Add badge for exact duplicates only (same flight, same time, same route)
                if (flight.hasExactDuplicates) {
                    const badge = document.createElement('div');
                    badge.className = 'identical-flight-badge';
                    badge.innerHTML = `<i class="fas fa-copy"></i> ${flight.exactDuplicateCount} identical options`;
                    card.appendChild(badge);
                }

                container.appendChild(card);
            });

            updateResultsCount();
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
            filteredFlights = [...originalFlights];
            displayFilteredResults();
        }

        function displayFlightsModern() {
            filteredFlights = [...originalFlights];
            displayFilteredResults();
        }

        function updateResultsCount() {
            const displayedCount = filteredFlights.length;
            document.getElementById('displayedFlightsCount').textContent = displayedCount;

            const totalText = displayedCount === originalFlights.length ?
                `of ${originalFlights.length} flights` :
                `of ${originalFlights.length} flights (${originalFlights.length - displayedCount} filtered)`;
            document.getElementById('totalFlightsCount').textContent = totalText;
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

            // Update hidden inputs
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