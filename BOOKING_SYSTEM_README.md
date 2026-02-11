# Exploreease Flight Booking System - Technical Documentation

## 📋 Overview

This is a secure, production-ready PHP-based flight booking system with multi-step validation, error handling, and encryption-ready architecture. It integrates with the **Amadeus Flight API** for real-time flight data and uses **PHPMailer** for email confirmations.

### 🔑 Key Features

- ✅ 5-step secure booking flow
- ✅ Real-time flight search via Amadeus API
- ✅ Session-based data handling (no database required)
- ✅ CSRF protection on all forms
- ✅ Input validation and sanitization
- ✅ Card number masking and PCI compliance
- ✅ HTML email confirmations with PHPMailer
- ✅ Mobile-responsive design
- ✅ Error handling and user feedback
- ✅ Security best practices implemented

---

## 🏗️ Project Structure

```
exploreease/
├── index.html                          # Main landing page
├── pages/                              # User-facing pages
│   ├── search.php                      # Step 1: Flight search
│   ├── results.php                     # Step 2: Flight selection
│   ├── personal-details.php            # Step 3: Personal information
│   ├── payment-details.php             # Step 4: Payment details
│   └── confirmation.php                # Step 5: Booking confirmation
├── php/
│   ├── core/                           # Core classes
│   │   ├── Security.php                # CSRF, sanitization, validation
│   │   ├── Validator.php               # Input validation rules
│   │   ├── BookingSession.php          # Session management
│   │   └── EmailService.php            # Email sending with PHPMailer
│   ├── config/
│   │   └── amadeus-config.php          # API credentials
│   ├── api/
│   │   └── AmadeusService.php          # Amadeus API integration
│   └── handlers/                       # Request handlers
│       ├── search.php                  # Process flight search
│       ├── select-flight.php           # Store flight selection
│       ├── personal-details.php        # Store personal info
│       ├── payment-details.php         # Store payment info (masked)
│       └── confirm-booking.php         # Send emails & complete booking
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
├── data/
│   └── airports.json
├── PHPMailer/                          # PHPMailer library
│   ├── Exception.php
│   ├── PHPMailer.php
│   └── SMTP.php
└── README.md (this file)
```

---

## 🔐 Security Architecture

### CSRF Protection

Every form includes a unique **CSRF token** generated server-side:

```php
$csrfToken = Security::generateCSRFToken();
```

Tokens are verified before processing:

```php
if (!Security::verifyCSRFToken($csrf)) {
    // Reject request
}
```

### Input Validation & Sanitization

**All user input is:**

1. **Sanitized** - HTML tags removed, special characters encoded
2. **Validated** - Format, length, and business logic checks
3. **Checked for injection** - SQL/XSS patterns detected

```php
$name = Security::sanitizeInput($_POST['name']);
$email = Security::sanitizeEmail($_POST['email']);
```

### Card Data Protection

**CRITICAL: Card numbers are NEVER stored**

1. **Full card number** is validated but NOT stored
2. **Masked version** stored for reference (\***\*-\*\***-\*\*\*\*-1234)
3. **CVV** is validated on submission only, NEVER stored
4. **Expiry date** stored as MM/YY format

Example masking:

```php
$maskedCard = Security::maskCardNumber($cardNumber); // **** **** **** 1234
```

### Session Security

Sessions are configured with strict security:

```php
session_set_cookie_params([
    'lifetime' => 1800,           // 30 minutes
    'secure' => isset($_SERVER['HTTPS']),  // HTTPS only
    'httponly' => true,           // JS cannot access
    'samesite' => 'Strict'        // CSRF protection
]);
```

### POST-Only Data Flow

All sensitive data:

- ✅ Transmitted via **POST** (never GET)
- ✅ Encrypted with **HTTPS** (required in production)
- ✅ Validated server-side
- ✅ Stored in **encrypted sessions**

---

## 📊 Booking Flow

### Step 1: Search (search.php)

- User enters: Origin, Destination, Dates, Passengers
- System validates input
- Calls Amadeus API to fetch flights
- Stores search criteria in session

### Step 2: Flight Selection (results.php)

- Displays available flights in attractive cards
- User selects one flight
- Selected flight details stored in session
- Form generates unique flight ID

### Step 3: Personal Details (personal-details.php)

- User enters: Name, Email, Phone, Passport (optional)
- Name validated (letters, spaces, hyphens only)
- Email format validated
- Phone number validated (E.164 format)
- Data stored in session

### Step 4: Payment Details (payment-details.php)

- User enters: Cardholder, Card Number, Expiry, CVV
- Card number validated using **Luhn algorithm**
- Expiry date checked (must be future)
- CVV validated (3-4 digits)
- Only **masked card** and expiry stored in session
- CVV NOT stored (security best practice)

### Step 5: Confirmation (confirmation.php)

- Review all booking details
- User confirms agreement
- Triggers email sending via PHPMailer
- Emails sent to:
  - **Customer**: Full booking confirmation with masked card
  - **Admin**: Detailed booking notification
- Session cleared (no data persistence)
- Booking reference generated

---

## 🚀 Setup & Installation

### 1. Prerequisites

```bash
# Ensure PHP 7.4+ is installed
php -v

# Ensure cURL is enabled (for API calls)
php -m | grep curl
```

### 2. Configure Amadeus API

Register at https://developers.amadeus.com/ and get credentials:

**File: `php/config/amadeus-config.php`**

```php
return [
    'client_id' => 'YOUR_CLIENT_ID',
    'client_secret' => 'YOUR_CLIENT_SECRET',
    'environment' => 'test', // or 'prod'
];
```

**Using Environment Variables (Recommended):**

Create `.env` file in project root:

```env
AMADEUS_CLIENT_ID=your_client_id
AMADEUS_CLIENT_SECRET=your_client_secret
AMADEUS_ENV=test
SMTP_HOST=smtp.gmail.com
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password
```

### 3. Configure Email Service

**File: `php/core/EmailService.php`**

Update SMTP settings:

```php
// For Gmail:
$this->mail->Host = 'smtp.gmail.com';
$this->mail->Port = 587;
$this->mail->Username = 'your-email@gmail.com';
$this->mail->Password = 'your-app-password'; // Use App Password, not Gmail password

// Change admin email:
$this->adminEmail = 'admin@yourdomain.com';
$this->fromEmail = 'noreply@yourdomain.com';
```

**Gmail Setup:**

1. Enable 2-Step Verification
2. Generate "App Password" at https://myaccount.google.com/apppasswords
3. Use App Password in configuration

### 4. Test the System

Start PHP built-in server:

```bash
php -S localhost:8000
```

Visit: `http://localhost:8000/pages/search.php`

### 5. Production Deployment

- ✅ Enable **HTTPS** (required for payment forms)
- ✅ Use **environment variables** for credentials
- ✅ Set **secure SMTP** with authentication
- ✅ Configure **error logging** (not display)
- ✅ Set appropriate **file permissions** (644 files, 755 directories)
- ✅ Use **PHP 8.0+** if possible
- ✅ Enable **OPcache** for performance

---

## 🔌 API Integration

### Amadeus Flight Search API

**Endpoint:**

```
GET https://api.amadeus.com/v2/shopping/flight-offers
```

**Parameters:**

```php
[
    'originLocationCode' => 'JFK',
    'destinationLocationCode' => 'LHR',
    'departureDate' => '2026-06-01',
    'returnDate' => '2026-06-08', // Optional
    'adults' => 1
]
```

**Response Format:**

```json
{
    "data": [
        {
            "id": "1",
            "source": "GDS",
            "instantTicketingRequired": false,
            "nonHomogeneous": false,
            "oneWay": false,
            "lastTicketingDate": "2026-05-20",
            "numberOfBookableSeats": 9,
            "itineraries": [...],
            "price": {...},
            "pricingOptions": {...},
            "validatingAirlineCodes": [...],
            "travelerPricings": [...]
        }
    ]
}
```

**Error Handling:**

```php
try {
    $flights = $amadeus->searchFlights($origin, $destination, $date, $passengers);
} catch (Exception $e) {
    // Log error, show user-friendly message
    error_log('Flight search failed: ' . $e->getMessage());
}
```

---

## 📧 Email Templates

### Customer Confirmation Email

Includes:

- Flight details (route, times, airline)
- Passenger information
- Masked payment details
- Booking confirmation message
- Support contact info

### Admin Notification Email

Includes:

- Booking timestamp
- Complete flight info
- Passenger details
- Masked payment details
- Customer email for follow-up

**Note:** Full card numbers are NEVER included in emails.

---

## 🧪 Testing

### Test Cases

1. **Search Validation:**
   - Invalid airport codes
   - Past travel dates
   - Invalid passenger count

2. **Flight Selection:**
   - Missing flight data
   - Tampered flight data

3. **Personal Details:**
   - Invalid names (numbers, special chars)
   - Invalid email format
   - Invalid phone format

4. **Payment Details:**
   - Invalid card numbers (Luhn check)
   - Expired cards
   - Invalid CVV
   - Injection attempts

### Test Flight Data (Amadeus Sandbox)

```
Origin: JFK (New York)
Destination: LHR (London)
Date: 2026-06-01
Passengers: 1-9
```

---

## 📱 Mobile Responsiveness

All pages are **fully responsive**:

- CSS Grid for flexible layouts
- Flexbox for button groups
- Meta viewport for mobile browsers
- Touch-friendly input sizes

Test with:

```bash
# Chrome DevTools → Device Toolbar
# Or: http://localhost:8000?debug=1
```

---

## 🐛 Troubleshooting

### "Invalid CSRF Token"

- **Cause:** Session expired or token mismatch
- **Solution:** Clear browser cookies, restart booking

### "Authentication Failed" (Amadeus)

- **Cause:** Invalid credentials or API limit exceeded
- **Solution:** Check API keys, verify Sandbox vs Production

### "Email Not Sending"

- **Cause:** SMTP configuration or credentials incorrect
- **Solution:**
  1. Verify SMTP settings
  2. Check firewall/port 587
  3. Use "App Password" for Gmail
  4. Check error logs

### "Session Expired"

- **Cause:** Booking took longer than 30 minutes
- **Solution:** Increase timeout in BookingSession.php (isSessionExpired method)

---

## 📋 Compliance & Standards

### PCI DSS Compliance

- ✅ No storage of full PANs (card numbers)
- ✅ No transmission of CVV
- ✅ Encrypted transmission (HTTPS required)
- ✅ No logging of sensitive data

### OWASP Top 10 Protection

- ✅ Injection: Input validation & parameterization
- ✅ Broken Auth: Session security & CSRF tokens
- ✅ XSS: Output encoding & CSP headers
- ✅ CSRF: Token-based protection

### GDPR Compliance

- ✅ Data collected only for booking
- ✅ Data not stored permanently
- ✅ User can request data deletion
- ✅ Clear privacy notices

---

## 🔄 Session Management

### Session Initialization

```php
BookingSession::init();  // Start secure session
```

### Storing Data

```php
BookingSession::setSearchCriteria($origin, $destination, $date, $passengers);
BookingSession::setSelectedFlight($flightData);
BookingSession::setPersonalDetails($name, $email, $phone);
BookingSession::setPaymentDetails($cardholder, $maskedCard, $expiry, $lastFour);
```

### Retrieving Data

```php
$search = BookingSession::getSearchCriteria();
$flight = BookingSession::getSelectedFlight();
$personal = BookingSession::getPersonalDetails();
$payment = BookingSession::getPaymentDetails();
$complete = BookingSession::getBookingSummary();
```

### Clearing Data (After Confirmation)

```php
BookingSession::clearBookingData();  // Clear all booking session data
BookingSession::regenerateSessionId();  // Generate new session ID for security
```

---

## 🚨 Important Notes

### ⚠️ Payment Processing

**This system does NOT process actual payments.** It:

- Collects and masks card details
- Validates card format
- Stores masked information for confirmation emails
- Should only be used for booking confirmation, NOT real transactions

To enable real payment processing:

1. Integrate with **Stripe**, **PayPal**, or **2Checkout** API
2. Use tokenization (send token, not card number)
3. Implement webhook handlers for payment confirmation
4. Ensure PCI DSS Level 1 compliance

### 🔗 HTTPS Requirement

In production, **HTTPS MUST be enabled** for all pages, especially:

- Payment details page
- Confirmation page
- All form submissions

Configure in your web server or use Let's Encrypt (free SSL).

### 📝 Logging & Monitoring

Never log:

- Full card numbers
- CVV codes
- Expiry dates
- Passwords

Safe to log:

- Transaction IDs
- Masked card numbers (\***\* \*\*** \*\*\*\* 1234)
- Error messages
- API responses (without sensitive data)

---

## 📞 Support & Contact

**For issues or questions:**

- Email: support@exploreease.com
- Documentation: See inline code comments
- Amadeus API Docs: https://developers.amadeus.com/
- PHPMailer Docs: https://phpmailer.world/

---

## 📄 License

This project is provided as-is for educational and commercial use.

---

**Last Updated:** February 11, 2026
**Version:** 1.0.0
**Status:** Production Ready ✅
