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
    <title>Flight Results - ExploreEase</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../assets/images/logo.png" type="image/pngs">

    <!-- Google Font (For Font Family) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome (For Icons)-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Minimal custom CSS - only what Bootstrap doesn't provide */
        .flight-card {
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .flight-card:hover {
            border-color: #0d6efd !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }

        .flight-card.selected {
            border-color: #198754 !important;
            background-color: #f0fdf4;
        }

        .filters-sidebar {
            position: sticky;
            top: 20px;
        }

        .filter-tag i:hover {
            color: #dc3545;
        }

        .identical-flight-badge {
            top: -10px;
            right: 10px;
            z-index: 1;
        }

        .best-deal-badge {
            top: -10px;
            left: 20px;
            z-index: 1;
        }

        .flight-continue-btn {
            position: absolute;
            bottom: 15px;
            right: 40px;
            z-index: 2;
        }

        #mobileFilters {
            transition: all 0.3s ease;
        }

        p {
            margin: 0;
        }

        /* summary, */
        h1,
        h2,
        h3,
        b {
            font-family: "Montserrat", sans-serif;
            font-weight: 600 !important;
            line-height: 1.2;
        }

        .btn {
            color: white !important;
        }

        .footer-call-right h1 {
            font-size: 27px;
        }

        .small {
            font-size: 11.5px !important;
            font-weight: 500 !important;
        }

        a {
            color: inherit !important;
            text-decoration: none !important;
            font-weight: 600;
        }

        .form-check-input[type=checkbox] {
            border-radius: .25em !important;
            height: 13px !important;
            width: 13px !important;
        }

        span {
            color: #3d3d3d;
        }

        a span {
            color: var(--button);
        }

        .nav-inner {
            width: 100% !important;
            margin: 0 auto !important;
            padding: 10px 20px !important;
            justify-content: space-between !important;
            transition: transform 0.9s ease !important;
            max-width: 1200px !important;
        }

        h2 {
            font-size: 22px !important;
            margin-bottom: 0 !important;
        }

        .container {
            padding: 20px;
        }

        ol,
        ul {
            padding-left: 0 !important;
            margin-bottom: 0 !important;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 23px !important;
            }

            h2 {
                font-size: 18px !important;
            }

            .container {
                padding: 20px 10px !important;
            }

            .nav-inner {
                padding: 20px !important;
                height: 60px;
                overflow: hidden;
                align-items: start;
                justify-content: start;
            }

            .filters-sidebar {
                position: static;
            }

            .flight-continue-btn,
            .identical-flight-badge {
                right: 15px;
            }

        }
    </style>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-337119917">
    </script>

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-337119917');
    </script>

    <script>
        gtag('config', 'AW-337119917/DPZ7CK2Uy7UaEK2V4KAB', {
            'phone_conversion_number': '+1-888-811-3446'
        });
    </script>

    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-W6WFZBQX');
    </script>
    <!-- End Google Tag Manager -->
</head>

<body class="bg-light">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W6WFZBQX"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <nav class="nav flex-center">
        <div class="flex nav-inner container">
            <div class="nav-left flex-center">
                <!-- <a href="/">
                    <span><i class="fa-solid fa-hand-holding-heart fa-2xl"></i></span>
                </a> -->
                <a href="/">
                    <h2>Explore<span>Ease</span></h2>
                </a>
            </div>
            <div class="nav-center ">
                <ul class="flex-center">
                    <li><a href="#">Flights</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <a href="tel:8888113446" class="flex-center">
                        <div class="nav-center-left">
                            <i class="fa-solid fa-phone-volume fa-shake"></i>
                        </div>
                        <div class="nav-center-right">
                            <h2><span>(888) 811-3446</span></h2>
                            <p class="small">CALL 24/7 FOR OUR BEST DEALS</p>
                        </div>
                    </a>
                </ul>
            </div>
            <div class="nav-end">

                <div class="hamburger-open flex">
                    <a href="tel:8888113446">
                        <p class=""><b>(888) 811-3446</b></p>
                    </a>
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="hamburger-close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
            </div>
        </div>
        </div>

    </nav>
    <div class="container">
        <div class="bg-white rounded-3 shadow-sm p-3 p-md-4">
            <!-- Step Indicator (Bootstrap progress steps) -->
            <div class="d-none d-md-flex justify-content-between mb-4">
                <div class="flex-fill text-center p-2 bg-success text-white rounded-2 me-1 fw-bold">1. Search</div>
                <div class="flex-fill text-center p-2 bg-primary text-white rounded-2 me-1 fw-bold">2. Select Flight</div>
                <div class="flex-fill text-center p-2 bg-light text-dark rounded-2 me-1">3. Personal Info</div>
                <div class="flex-fill text-center p-2 bg-light text-dark rounded-2 me-1">4. Payment</div>
                <div class="flex-fill text-center p-2 bg-light text-dark rounded-2">5. Confirm</div>
            </div>

            <h1 class="h2 mb-4">Select Your Flight</h1>

            <!-- Error/Alert Container -->
            <div id="errorContainer"></div>
            <div id="alertContainer"></div>

            <!-- Loading Container -->
            <div id="loadingContainer" class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x text-secondary mb-3"></i>
                <p class="text-muted">Loading your flights...</p>
            </div>

            <!-- Main Content Container -->
            <div id="contentContainer" style="display: none;">
                <!-- Search Criteria/Summary -->
                <div id="searchCriteria"></div>
                <div id="searchSummary" class="bg-light p-3 rounded-3 mb-4 small" style="display: none;"></div>

                <!-- Active Filters -->
                <div id="activeFilters" class="flex-wrap gap-2 mb-4 p-3 bg-light rounded-3" style="display: none !important;"></div>

                <div class="row g-4">
                    <!-- Mobile Filter Button -->
                    <div class="d-lg-none">
                        <button class="btn  w-100"
                            data-bs-toggle="collapse"
                            data-bs-target="#mobileFilters">
                            <i class="fas fa-filter me-2"></i> Show Filters
                        </button>
                    </div>

                    <!-- Filters Sidebar -->
                    <div class="col-lg-3 col-md-4">
                        <div id="mobileFilters" class="collapse d-lg-block">
                            <div class="filters-sidebar bg-white border rounded-3 p-3 p-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <h3 class="h5 mb-0 text-dark">
                                        <i class="fas fa-filter me-2 text-primary"></i> Filters
                                    </h3>
                                    <button id="clearAllFilters" class="btn  p-1 px-2 text-decoration-none small">Clear all</button>
                                </div>

                                <!-- Stops Filter -->
                                <div class="mb-4 pb-2 border-bottom">
                                    <div class="fw-semibold text-dark mb-3">
                                        <i class="fas fa-plane me-2 text-secondary"></i> Stops
                                    </div>
                                    <div class="d-flex flex-column gap-2" id="stopsFilter">
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="stops" value="0" class="form-check-input">
                                            <span>Non-stop</span>
                                            <span class="text-secondary ms-auto" id="nonStopCount">0</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="stops" value="1" class="form-check-input">
                                            <span>1 Stop</span>
                                            <span class="text-secondary ms-auto" id="oneStopCount">0</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="stops" value="2" class="form-check-input">
                                            <span>2+ Stops</span>
                                            <span class="text-secondary ms-auto" id="twoPlusStopCount">0</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Airlines Filter -->
                                <div class="mb-4 pb-2 border-bottom">
                                    <div class="fw-semibold text-dark mb-3">
                                        <i class="fas fa-building me-2 text-secondary"></i> Airlines
                                    </div>
                                    <div class="d-flex flex-column gap-2" id="airlinesFilter">
                                        <!-- Dynamically populated -->
                                    </div>
                                </div>

                                <!-- Price Range Filter -->
                                <div class="mb-4 pb-2 border-bottom">
                                    <div class="fw-semibold text-dark mb-3">
                                        <i class="fas fa-dollar-sign me-2 text-secondary"></i> Price Range
                                    </div>
                                    <div class="price-range">
                                        <div id="priceSlider" class="my-2"></div>
                                        <div class="d-flex gap-2">
                                            <input type="number" id="minPrice" class="form-control form-control-sm" placeholder="Min" step="10">
                                            <input type="number" id="maxPrice" class="form-control form-control-sm" placeholder="Max" step="10">
                                        </div>
                                        <button id="applyPriceFilter" class="btn  btn-sm w-100 mt-3">Apply Price</button>
                                    </div>
                                </div>

                                <!-- Departure Time Filter -->
                                <div class="mb-4 pb-2 border-bottom">
                                    <div class="fw-semibold text-dark mb-3">
                                        <i class="fas fa-clock me-2 text-secondary"></i> Departure Time
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="departure_time" value="morning" class="form-check-input">
                                            <span>Morning (6AM - 12PM)</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="departure_time" value="afternoon" class="form-check-input">
                                            <span>Afternoon (12PM - 6PM)</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="departure_time" value="evening" class="form-check-input">
                                            <span>Evening (6PM - 12AM)</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="departure_time" value="night" class="form-check-input">
                                            <span>Night (12AM - 6AM)</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Seat Class Filter -->
                                <div class="mb-4">
                                    <div class="fw-semibold text-dark mb-3">
                                        <i class="fas fa-chair me-2 text-secondary"></i> Cabin Class
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="seat_class" value="ECONOMY" class="form-check-input">
                                            <span>Economy</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="seat_class" value="PREMIUM_ECONOMY" class="form-check-input">
                                            <span>Premium Economy</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="seat_class" value="BUSINESS" class="form-check-input">
                                            <span>Business</span>
                                        </label>
                                        <label class="d-flex align-items-center gap-2 small">
                                            <input type="checkbox" name="seat_class" value="FIRST" class="form-check-input">
                                            <span>First Class</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Area -->
                    <div class="col-lg-9 col-md-8">
                        <!-- Results Header -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-secondary">
                                <strong id="displayedFlightsCount" class="fs-5 text-dark">0</strong> <span id="totalFlightsCount"></span>
                            </div>
                            <select id="sortBy" class="form-select w-auto">
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="duration">Duration</option>
                                <option value="departure">Earliest Departure</option>
                                <option value="arrival">Earliest Arrival</option>
                                <option value="stops">Least Stops</option>
                            </select>
                        </div>

                        <!-- Flights List -->
                        <div id="flightsList" class="d-flex flex-column gap-3"></div>
                        <div id="flightsContainer" style="display: none;"></div>

                        <!-- Load More Button -->
                        <div id="loadMoreContainer" class="text-center mt-4" style="display: none;">
                            <button id="loadMoreBtn" class="btn  px-4 py-2">
                                <i class="fas fa-plus-circle me-2"></i>10 More Flights
                            </button>
                        </div>

                        <!-- No Results -->
                        <div id="noResults" class="text-center py-5" style="display: none;">
                            <div class="text-secondary mb-3">
                                <i class="fas fa-inbox fa-4x"></i>
                            </div>
                            <h3 class="h5">No flights found</h3>
                            <p class="text-muted">Try adjusting your filters or search criteria</p>
                            <button class="btn " onclick="window.location.href='/';" style="max-width: 300px;">New Search</button>
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
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-inner container">
            <div class="footer-top">
                <h1 class="center">Subscribe to our Newsletter</h1>
                <h3 class="mt-10 center">Get latest offers from Exploreease</h3>
                <form class="footer-form mt-20 grid-4">
                    <input type="text" name="name" id="name" placeholder="Enter Name" required>
                    <input type="email" name="email" id="email" placeholder="your-email@example.com" required>
                    <input type="text" name="mobile" id="mobile" placeholder="Mobile Number" required>
                    <button class="btn">Subscribe</button>
                </form>
                <p class="mt-20 center small">I would like to receive SMS and email from exploreease.online with the
                    latest
                    offers and
                    promotions. I have read and agree to the <a href="#"><span>Terms and conditions</span></a> and <a
                        href="#"><span>privacy policy</span></a> .</p>
            </div>

            <div class="footer-center grid-4 mt-40">
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul class="mt-20 flex-col">
                        <a href="#">
                            <li>About Us</li>
                        </a>
                        <a href="#">
                            <li>Contact Us</li>
                        </a>
                        <a href="#">
                            <li>Taxes & Fees</li>
                        </a>
                        <a href="#">
                            <li>FAQs</li>
                        </a>
                        <a href="#">
                            <li>Sitemap</li>
                        </a>
                    </ul>
                </div>
                <div class="footer-links">
                    <h3>Helpful Links</h3>
                    <ul class="mt-20 flex-col">
                        <a href="#">
                            <li>Security</li>
                        </a>
                        <a href="#">
                            <li>Privacy Policy</li>
                        </a>
                        <a href="#">
                            <li>Baggage Fees</li>
                        </a>
                        <a href="#">
                            <li>Terms & Conditions</li>
                        </a>
                        <a href="#">
                            <li>Cancellation Policy</li>
                        </a>
                    </ul>
                </div>
                <div class="footer-links">
                    <h3>Travel Deals</h3>
                    <ul class="mt-20 flex-col">
                        <a href="#">
                            <li>Top Airlines Deals</li>
                        </a>
                        <a href="#">
                            <li>Last Minute Flights</li>
                        </a>
                        <a href="#">
                            <li>One Way Flights</li>
                        </a>
                        <a href="#">
                            <li>Round Trip Flights</li>
                        </a>
                        <a href="#">
                            <li>Cheap International Flights</li>
                        </a>
                    </ul>
                </div>
                <div class="footer-links">
                    <h3>Top Destinations</h3>
                    <ul class="mt-20 flex-col">
                        <a href="#">
                            <li>Flights to Miami</li>
                        </a>
                        <a href="#">
                            <li>Flights to Las Vegas</li>
                        </a>
                        <a href="#">
                            <li>Flights to Los Angeles</li>
                        </a>
                        <a href="#">
                            <li>Flights to Orlando</li>
                        </a>
                        <a href="#">
                            <li>Flights to New York</li>
                        </a>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom mt-40 flex-col-center-center">
                <div class="footer-certi grid-4">
                    <div><img src="../assets/images/arc.png" alt="Arc Image"></div>
                    <div><img src="../assets/images/IATA.webp" alt="IATA Image"></div>
                    <div><img src="../assets/images/digicert.webp" alt="Digicert Image"></div>
                    <div><img src="../assets/images/cloudflare.webp" alt="Cloudflare Image"></div>
                </div>
                <p class="small center"><b>DISCLAIMER:</b> exploreease.online is an independent travel portal. Its
                    parent
                    company is LBF
                    AMERICAS LLC. The information shown on this website is for general purposes. All necessary measures
                    have been taken to ensure that the information displayed on the website is accurate and up to date;
                    However, under no circumstances we do not offer any type of guarantee or representation, whether
                    implicit or express, regarding the accuracy, completeness or reliability of the information shown on
                    this website. If you need to answer any questions, you can write to <a
                        href="mailto:contact@exploreease.online"><span>contact@exploreease.online</span></a></p>
            </div>
        </div>
    </footer>

    <a href="tel:8888113446">
        <div class="footer-call-button flex w-full">
            <div class="footer-call-left">
                <i class="fa-solid fa-phone-volume fa-shake fa-2xl"></i>
            </div>
            <div class="footer-call-right flex-col-center-center">
                <p class="small">Call & Get Unpublished Flight Deals!</p>
                <h1>+1-888-811-3446</h1>
            </div>
        </div>
    </a>

    <div class="footer-copyright">
        <p class="small center">© 2021 - 2026 LBF AMERICAS LLC. All Rights Reserved. Use of this website signifies your
            agreement to the <a href="#"><span>Terms of Use</span></a></p>
    </div>

    <script src="../assets/js/script.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const btn = document.getElementById("filterToggleBtn");
        const collapse = document.getElementById("mobileFilters");

        collapse.addEventListener("show.bs.collapse", () => {
            btn.innerHTML = '<i class="fas fa-times me-2"></i> Close Filters';
        });

        collapse.addEventListener("hide.bs.collapse", () => {
            btn.innerHTML = '<i class="fas fa-filter me-2"></i> Show Filters';
        });

        // Consolidated JavaScript (unchanged functionality, only styling classes updated)
        let flights = [];
        let originalFlights = [];
        let filteredFlights = [];
        let selectedFlightId = null;
        let selectedFlightIndex = null;
        let useModernImplementation = <?php echo $useModernVersion ? 'true' : 'false'; ?>;

        // Pagination variables
        let currentDisplayCount = 0;
        const FLIGHTS_PER_PAGE = 10;

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
            initFilterListeners();
        });

        // Initialize filter event listeners
        function initFilterListeners() {
            document.querySelectorAll('input[name="stops"]').forEach(checkbox => {
                checkbox.addEventListener('change', applyFilters);
            });
            document.getElementById('clearAllFilters').addEventListener('click', clearAllFilters);
            document.getElementById('applyPriceFilter').addEventListener('click', applyPriceFilter);
            document.getElementById('sortBy').addEventListener('change', applySort);
            document.querySelectorAll('input[name="departure_time"]').forEach(checkbox => {
                checkbox.addEventListener('change', applyFilters);
            });
            document.querySelectorAll('input[name="seat_class"]').forEach(checkbox => {
                checkbox.addEventListener('change', applyFilters);
            });
            document.getElementById('loadMoreBtn').addEventListener('click', loadMoreFlights);
        }

        /**
         * FIXED: Only remove EXACT duplicate flights (same flight number, same times, same route)
         */
        function removeExactDuplicateFlights(flightsArray) {
            const uniqueFlights = new Map();
            const duplicateGroups = new Map();

            flightsArray.forEach(flight => {
                const key = `${flight.airline || ''}|${flight.flight_number || ''}|${flight.departure_airport}|${flight.arrival_airport}|${flight.departure_time}|${flight.arrival_time}|${flight.seat_class || 'ECONOMY'}`;

                if (!uniqueFlights.has(key)) {
                    uniqueFlights.set(key, flight);
                    duplicateGroups.set(key, [flight]);
                } else {
                    duplicateGroups.get(key).push(flight);
                }
            });

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
                showAlert('danger', 'No flights found. Please search again.');
                setTimeout(() => window.location.href = '/', 2000);
                return;
            }

            try {
                let parsedData = JSON.parse(flightsData);
                originalFlights = Array.isArray(parsedData) ? parsedData : (parsedData.flights || []);
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
                document.getElementById('flightsList').style.display = 'flex';
                document.getElementById('flightsContainer').style.display = 'none';
            } else {
                document.getElementById('flightsList').innerHTML =
                    '<p class="text-center text-danger">No flights found. Please search again.</p>';
                document.getElementById('noResults').style.display = 'block';
            }
        }

        function initializeFilters() {
            updateFilterCounts();
            populateAirlinesFilter();
            updatePriceRange();
            updateResultsCount();
        }

        function updateFilterCounts() {
            const nonStopCount = originalFlights.filter(f => f.stops === 0).length;
            const oneStopCount = originalFlights.filter(f => f.stops === 1).length;
            const twoPlusStopCount = originalFlights.filter(f => f.stops >= 2).length;

            document.getElementById('nonStopCount').textContent = `(${nonStopCount})`;
            document.getElementById('oneStopCount').textContent = `(${oneStopCount})`;
            document.getElementById('twoPlusStopCount').textContent = `(${twoPlusStopCount})`;
        }

        function populateAirlinesFilter() {
            const airlines = {};
            originalFlights.forEach(flight => {
                airlines[flight.airline] = (airlines[flight.airline] || 0) + 1;
            });

            const container = document.getElementById('airlinesFilter');
            container.innerHTML = '';

            Object.keys(airlines).sort().forEach(airline => {
                const label = document.createElement('label');
                label.className = 'd-flex align-items-center gap-2 small';
                label.innerHTML = `
                    <input type="checkbox" name="airline" value="${escapeHtml(airline)}" class="form-check-input">
                    <span>${escapeHtml(airline)}</span>
                    <span class="text-secondary ms-auto">(${airlines[airline]})</span>
                `;

                const checkbox = label.querySelector('input');
                checkbox.addEventListener('change', applyFilters);
                container.appendChild(label);
            });
        }

        function updatePriceRange() {
            if (originalFlights.length === 0) return;

            const prices = originalFlights.map(f => parseFloat(f.price));
            const minPrice = Math.min(...prices);
            const maxPrice = Math.max(...prices);

            document.getElementById('minPrice').placeholder = `Min $${Math.floor(minPrice)}`;
            document.getElementById('maxPrice').placeholder = `Max $${Math.ceil(maxPrice)}`;
        }

        function applyPriceFilter() {
            const minPrice = parseFloat(document.getElementById('minPrice').value);
            const maxPrice = parseFloat(document.getElementById('maxPrice').value);

            if (!isNaN(minPrice)) activeFilters.minPrice = minPrice;
            if (!isNaN(maxPrice)) activeFilters.maxPrice = maxPrice;

            applyFilters();
        }

        function applyFilters() {
            activeFilters.stops = Array.from(document.querySelectorAll('input[name="stops"]:checked')).map(cb => cb.value);
            activeFilters.airlines = Array.from(document.querySelectorAll('input[name="airline"]:checked')).map(cb => cb.value);
            activeFilters.departureTimes = Array.from(document.querySelectorAll('input[name="departure_time"]:checked')).map(cb => cb.value);
            activeFilters.seatClasses = Array.from(document.querySelectorAll('input[name="seat_class"]:checked')).map(cb => cb.value);

            filteredFlights = originalFlights.filter(flight => {
                if (activeFilters.stops.length > 0) {
                    const flightStops = flight.stops >= 2 ? '2' : flight.stops.toString();
                    if (!activeFilters.stops.includes(flightStops) &&
                        !(flight.stops >= 2 && activeFilters.stops.includes('2'))) {
                        return false;
                    }
                }

                if (activeFilters.airlines.length > 0 && !activeFilters.airlines.includes(flight.airline)) {
                    return false;
                }

                const price = parseFloat(flight.price);
                if (activeFilters.minPrice !== null && price < activeFilters.minPrice) return false;
                if (activeFilters.maxPrice !== null && price > activeFilters.maxPrice) return false;

                if (activeFilters.departureTimes.length > 0) {
                    const hour = parseInt(flight.departure_time.split(':')[0]);
                    let timeSlot;
                    if (hour >= 6 && hour < 12) timeSlot = 'morning';
                    else if (hour >= 12 && hour < 18) timeSlot = 'afternoon';
                    else if (hour >= 18 && hour < 24) timeSlot = 'evening';
                    else timeSlot = 'night';

                    if (!activeFilters.departureTimes.includes(timeSlot)) return false;
                }

                if (activeFilters.seatClasses.length > 0) {
                    const seatClass = (flight.seat_class || 'ECONOMY').toUpperCase();
                    if (!activeFilters.seatClasses.includes(seatClass)) return false;
                }

                return true;
            });

            applySort();
            updateActiveFiltersDisplay();
            displayFilteredResults();
        }

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

        function parseDuration(duration) {
            if (!duration) return 0;
            const match = duration.match(/PT(?:(\d+)H)?(?:(\d+)M)?/);
            const hours = parseInt(match[1] || 0);
            const minutes = parseInt(match[2] || 0);
            return hours * 60 + minutes;
        }

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
                <div class="filter-tag d-inline-flex align-items-center gap-2 px-3 py-1 bg-white border border-primary rounded-pill small text-dark">
                    <span><strong>${label}:</strong> ${value}</span>
                    <i class="fas fa-times text-secondary" style="cursor: pointer;" onclick="removeFilter('${filterType}')"></i>
                </div>
            `;
        }

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

        function clearAllFilters() {
            document.getElementById('activeFilters').style.display = 'none !important'
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

        function displayFilteredResults() {
            // Reset pagination when filters change
            currentDisplayCount = 0;
            const container = document.getElementById('flightsList');
            container.innerHTML = '';

            if (filteredFlights.length === 0) {
                document.getElementById('noResults').style.display = 'block';
                document.getElementById('flightsList').style.display = 'none';
                document.getElementById('loadMoreContainer').style.display = 'none';
                updateResultsCount();
                return;
            }

            document.getElementById('noResults').style.display = 'none';
            document.getElementById('flightsList').style.display = 'flex';

            // Show first batch of flights
            loadMoreFlights();
            updateResultsCount();
        }

        function loadMoreFlights() {
            const container = document.getElementById('flightsList');
            const isModern = document.getElementById('searchSummary').style.display === 'block';

            const nextBatch = filteredFlights.slice(currentDisplayCount, currentDisplayCount + FLIGHTS_PER_PAGE);

            nextBatch.forEach((flight, batchIndex) => {
                const absoluteIndex = currentDisplayCount + batchIndex;
                const card = createFlightCard(flight, absoluteIndex, isModern);

                if (flight.hasExactDuplicates) {
                    const badge = document.createElement('div');
                    badge.className = 'identical-flight-badge position-absolute bg-warning text-white px-3 py-1 rounded-pill small fw-semibold';
                    badge.innerHTML = `<i class="fas fa-copy me-1"></i> ${flight.exactDuplicateCount} identical options`;
                    card.appendChild(badge);
                    card.classList.add('position-relative');
                }

                container.appendChild(card);
            });

            currentDisplayCount += nextBatch.length;

            // Show/hide load more button
            if (currentDisplayCount < filteredFlights.length) {
                document.getElementById('loadMoreContainer').style.display = 'block';
            } else {
                document.getElementById('loadMoreContainer').style.display = 'none';
            }

            updateResultsCount();
        }

        function displaySearchCriteria(data) {
            const html = `
                <div class="criteria-item">
                    <div class="small text-secondary fw-semibold text-uppercase">From</div>
                    <div class="fw-bold fs-6 text-dark">${escapeHtml(data.origin)}</div>
                </div>
                <div class="criteria-item">
                    <div class="small text-secondary fw-semibold text-uppercase">To</div>
                    <div class="fw-bold fs-6 text-dark">${escapeHtml(data.destination)}</div>
                </div>
                <div class="criteria-item">
                    <div class="small text-secondary fw-semibold text-uppercase">Depart</div>
                    <div class="fw-bold fs-6 text-dark">${formatDate(data.departure_date)}</div>
                </div>
                ${data.return_date ? `
                    <div class="criteria-item">
                        <div class="small text-secondary fw-semibold text-uppercase">Return</div>
                        <div class="fw-bold fs-6 text-dark">${formatDate(data.return_date)}</div>
                    </div>
                ` : ''}
                <div class="criteria-item">
                    <div class="small text-secondary fw-semibold text-uppercase">Passengers</div>
                    <div class="fw-bold fs-6 text-dark">${data.passengers || data.passenger_count || 1}</div>
                </div>
            `;
            document.getElementById('searchCriteria').innerHTML = html;
        }

        function displaySearchSummary(criteria) {
            const summary = document.getElementById('searchSummary');
            summary.innerHTML = `
                <strong class="text-dark">${criteria.origin || 'N/A'} → ${criteria.destination || 'N/A'}</strong> - 
                ${formatDate(criteria.departure_date)} (${criteria.passengers || criteria.passenger_count || 1} passenger${(criteria.passengers || criteria.passenger_count || 1) > 1 ? 's' : ''})
                ${criteria.return_date ? `<span class="ms-2">• Return: ${formatDate(criteria.return_date)}</span>` : '<span class="ms-2">• One-way</span>'}
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
            const displayedCount = Math.min(currentDisplayCount, filteredFlights.length);
            document.getElementById('displayedFlightsCount').textContent = displayedCount;

            const totalText = displayedCount === originalFlights.length ?
                `of ${originalFlights.length} flights` :
                `of ${originalFlights.length} flights (${originalFlights.length - filteredFlights.length} filtered)`;
            document.getElementById('totalFlightsCount').textContent = totalText;
        }

        function createFlightCard(flight, index, isModern = false) {
            const card = document.createElement('div');
            card.className = 'flight-card border rounded-3 p-3 bg-white';
            card.dataset.index = index;
            card.dataset.flightId = flight.id || `flight-${index}`;

            const stopsText = flight.stops === 0 ? 'Direct' : `${flight.stops} stop${flight.stops > 1 ? 's' : ''}`;
            const duration = formatDuration(flight.duration);
            const price = parseFloat(flight.price).toFixed(2);
            const currency = flight.currency || 'USD';

            if (isModern) {
                card.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div class="flex-grow-1">
                            <div class="fw-bold fs-5 mb-2">${escapeHtml(flight.departure_airport)} → ${escapeHtml(flight.arrival_airport)}</div>
                            <div class="row g-3 small mb-2">
                                <div class="col-6 col-md-3">
                                    <span class="text-secondary fw-semibold">Depart:</span>
                                    <span class="text-dark">${escapeHtml(flight.departure_time)}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-secondary fw-semibold">Arrive:</span>
                                    <span class="text-dark">${escapeHtml(flight.arrival_time)}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-secondary fw-semibold">Duration:</span>
                                    <span class="text-dark">${duration}</span>
                                </div>
                                <div class="col-6 col-md-3">
                                    <span class="text-secondary fw-semibold">Stops:</span>
                                    <span class="text-dark">${stopsText}</span>
                                </div>
                            </div>
                            <div class="small text-secondary">
                                ${escapeHtml(flight.airline)} • ${escapeHtml(flight.seat_class || 'ECONOMY')} • Flight ${escapeHtml(flight.flight_number)}
                            </div>
                        </div>
                        <div class="text-end ms-md-3">
                            <div class="text-success fs-3 fw-bold">${currency} ${price}</div>
                            <!-- <div class="small text-secondary">${currency}</div> -->
                        </div>
                    </div>
                    <div class="flight-continue-btn">
                        <button class="btn  btn-sm continue-btn" onclick="event.stopPropagation(); selectAndContinue(${index})">
                            <i class="fas fa-arrow-right me-1"></i>Continue
                        </button>
                    </div>
                `;
            } else {
                card.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex gap-3">
                            <div class="fw-bold text-primary fs-5">${escapeHtml(flight.airline)}</div>
                            <div>
                                <div class="fw-semibold">#${escapeHtml(flight.flight_number)}</div>
                                <div class="small text-secondary">${escapeHtml(flight.seat_class || 'ECONOMY')}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-success fs-3 fw-bold">${currency} ${price}</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center my-3 flex-wrap">
                        <div class="text-center">
                            <div class="fs-4 fw-bold text-dark">${escapeHtml(flight.departure_time)}</div>
                            <div class="small text-secondary">${escapeHtml(flight.departure_airport)}</div>
                        </div>
                        <div class="text-center px-3">
                            <div><i class="fas fa-arrow-right text-secondary"></i></div>
                            <div class="small text-secondary mt-1">${stopsText}</div>
                        </div>
                        <div class="text-center">
                            <div class="fs-4 fw-bold text-dark">${escapeHtml(flight.arrival_time)}</div>
                            <div class="small text-secondary">${escapeHtml(flight.arrival_airport)}</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 p-3 p-md-4 small">
                        <div>
                            <span class="text-secondary fw-semibold">Duration:</span>
                            <span class="text-dark">${duration}</span>
                        </div>
                        <div>
                            <span class="text-secondary fw-semibold">Stops:</span>
                            <span class="text-dark">${stopsText}</span>
                        </div>
                    </div>
                    <div class="flight-continue-btn">
                        <button class="btn  btn-sm continue-btn" onclick="event.stopPropagation(); selectAndContinue(${index})">
                            <i class="fas fa-arrow-right me-1"></i>Continue
                        </button>
                    </div>
                `;
            }

            card.addEventListener('click', () => selectFlight(flight, index, card));
            return card;
        }

        function selectAndContinue(index) {
            const flight = filteredFlights[index];
            if (!flight) return;

            // Select the flight first
            const card = document.querySelector(`[data-index="${index}"]`);
            selectFlight(flight, index, card);

            // Then submit the form
            setTimeout(() => {
                submitFlightSelection();
            }, 100);
        }

        function selectFlight(flight, index, cardElement) {
            if (selectedFlightId || selectedFlightIndex !== null) {
                const prevCard = document.querySelector(`[data-flight-id="${selectedFlightId}"]`) ||
                    document.querySelector(`[data-index="${selectedFlightIndex}"]`);
                if (prevCard) {
                    prevCard.classList.remove('selected');
                }
            }

            selectedFlightId = flight.id || index;
            selectedFlightIndex = index;
            cardElement.classList.add('selected');

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

            sessionStorage.setItem('selectedFlight', JSON.stringify(flight));
        }

        function submitFlightSelection() {
            if (selectedFlightId === null && selectedFlightIndex === null) {
                showAlert('danger', 'Please select a flight');
                return;
            }

            // Get the selected flight from sessionStorage
            const selectedFlight = JSON.parse(sessionStorage.getItem('selectedFlight'));

            if (!selectedFlight) {
                showAlert('danger', 'Flight data not found. Please select again.');
                return;
            }

            // Prepare form data with flight information
            const form = document.getElementById('selectionForm');
            const formData = new FormData(form);

            // Send flight selection to server to store in session
            fetch('../php/handlers/select-flight.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Store in sessionStorage for reference
                        sessionStorage.setItem('booking_selected_flight', JSON.stringify(selectedFlight));

                        // Redirect to personal details page
                        window.location.href = 'personal-details.php';
                    } else {
                        showAlert('danger', result.message || 'Failed to save flight selection. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'An error occurred. Please try again.');
                });
        }

        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            errorContainer.innerHTML = `<div class="alert alert-danger">${escapeHtml(message)}</div>`;
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