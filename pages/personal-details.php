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
    <title>Personal Details - ExploreEase</title>
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

<body>
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

    <footer class="footer">
        <div class="footer-inner container">
            <div class="footer-top">
                <h1 class="center">Subscribe to our <span>Newsletter</span></h1>
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