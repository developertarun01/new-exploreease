# API Handler Documentation

Complete reference for all handler endpoints in the Exploreease flight booking system.

---

## 🔄 Handler Architecture

All handlers follow this pattern:

```
HTTP Request (POST)
    ↓
CSRF Token Validation
    ↓
Input Sanitization
    ↓
Business Logic Processing
    ↓
Session Data Storage
    ↓
JSON Response
```

---

## 1️⃣ Flight Search Handler

**Endpoint:** `/php/handlers/search.php`
**Method:** `POST`
**Session Required:** Yes

### Request

```javascript
const formData = new FormData();
formData.append("csrf_token", csrfToken);
formData.append("origin", "JFK");
formData.append("destination", "LHR");
formData.append("departure_date", "2026-06-01");
formData.append("return_date", "2026-06-08"); // Optional
formData.append("passengers", "1");

fetch("/php/handlers/search.php", {
  method: "POST",
  body: formData,
});
```

### Parameters

| Field          | Type    | Required | Validation                          |
| -------------- | ------- | -------- | ----------------------------------- |
| csrf_token     | string  | Yes      | Must match session token            |
| origin         | string  | Yes      | 3-letter IATA code, uppercase       |
| destination    | string  | Yes      | 3-letter IATA code, uppercase       |
| departure_date | date    | Yes      | YYYY-MM-DD, must be future          |
| return_date    | date    | No       | YYYY-MM-DD, must be after departure |
| passengers     | integer | Yes      | 1-9                                 |

### Response: Success (200 OK)

```json
{
  "success": true,
  "message": "Flights found successfully",
  "data": {
    "flights": [
      {
        "id": "flight_1234567890.5678",
        "api_id": "1",
        "airline": "BA",
        "flight_number": "112",
        "departure_airport": "JFK",
        "arrival_airport": "LHR",
        "departure_time": "15:30",
        "arrival_time": "03:45",
        "duration": "PT7H15M",
        "stops": 0,
        "price": "750.00",
        "currency": "USD",
        "seat_class": "ECONOMY"
      }
    ],
    "search_criteria": {
      "origin": "JFK",
      "destination": "LHR",
      "departure_date": "2026-06-01",
      "return_date": "2026-06-08",
      "passenger_count": 1,
      "created_at": 1707667200
    }
  }
}
```

### Response: Error (400/500)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "origin": "Invalid airport code (must be 3 letters)",
    "passengers": "Number of passengers must be between 1 and 9"
  }
}
```

### Session Storage

```php
$_SESSION['booking']['search'] = [
    'origin' => 'JFK',
    'destination' => 'LHR',
    'departure_date' => '2026-06-01',
    'return_date' => '2026-06-08',
    'passenger_count' => 1,
    'created_at' => 1707667200
];
```

---

## 2️⃣ Flight Selection Handler

**Endpoint:** `/php/handlers/select-flight.php`
**Method:** `POST`
**Session Required:** Yes (Search must be completed)

### Request

```javascript
const flightData = {
  csrf_token: csrfToken,
  id: "flight_1234567890.5678",
  airline: "BA",
  flight_number: "112",
  departure_airport: "JFK",
  arrival_airport: "LHR",
  departure_time: "15:30",
  arrival_time: "03:45",
  duration: "PT7H15M",
  stops: 0,
  price: "750.00",
  currency: "USD",
  seat_class: "ECONOMY",
};

const formData = new FormData();
Object.entries(flightData).forEach(([key, value]) => {
  formData.append(key, value);
});

fetch("/php/handlers/select-flight.php", {
  method: "POST",
  body: formData,
});
```

### Parameters

| Field             | Type    | Required | Notes                           |
| ----------------- | ------- | -------- | ------------------------------- |
| csrf_token        | string  | Yes      | Session CSRF token              |
| id                | string  | Yes      | Unique flight identifier        |
| airline           | string  | Yes      | Airline code (e.g., BA, AA, LH) |
| flight_number     | string  | Yes      | Flight number                   |
| departure_airport | string  | Yes      | IATA airport code               |
| arrival_airport   | string  | Yes      | IATA airport code               |
| departure_time    | string  | Yes      | HH:MM format                    |
| arrival_time      | string  | Yes      | HH:MM format                    |
| duration          | string  | Yes      | ISO 8601 format (PT7H15M)       |
| stops             | integer | Yes      | Number of stops (0 = direct)    |
| price             | string  | Yes      | Numeric price                   |
| currency          | string  | Yes      | Currency code (USD, EUR, etc)   |
| seat_class        | string  | Yes      | ECONOMY, BUSINESS, FIRST, etc   |

### Response: Success (200 OK)

```json
{
  "success": true,
  "message": "Flight selected successfully",
  "data": {
    "flight": {
      "id": "flight_1234567890.5678",
      "airline": "BA",
      "flight_number": "112",
      "departure_airport": "JFK",
      "arrival_airport": "LHR",
      "departure_time": "15:30",
      "arrival_time": "03:45",
      "duration": "PT7H15M",
      "stops": 0,
      "price": "750.00",
      "currency": "USD",
      "seat_class": "ECONOMY"
    }
  }
}
```

### Session Storage

```php
$_SESSION['booking']['flight'] = [...flight data...];
$_SESSION['booking']['flight_selected_at'] = time();
```

---

## 3️⃣ Personal Details Handler

**Endpoint:** `/php/handlers/personal-details.php`
**Method:** `POST`
**Session Required:** Yes (Search & Flight must be completed)

### Request

```javascript
const formData = new FormData();
formData.append("csrf_token", csrfToken);
formData.append("name", "John Doe");
formData.append("email", "john@example.com");
formData.append("phone", "+1-555-123-4567");
formData.append("passport_number", "A12345678"); // Optional

fetch("/php/handlers/personal-details.php", {
  method: "POST",
  body: formData,
});
```

### Parameters

| Field           | Type   | Required | Validation                                      |
| --------------- | ------ | -------- | ----------------------------------------------- |
| csrf_token      | string | Yes      | Session CSRF token                              |
| name            | string | Yes      | 2-100 chars, letters/spaces/hyphens/apostrophes |
| email           | string | Yes      | Valid email format                              |
| phone           | string | Yes      | E.164 format (+1-555-123-4567)                  |
| passport_number | string | No       | 6-20 alphanumeric characters                    |

### Response: Success (200 OK)

```json
{
  "success": true,
  "message": "Personal details saved successfully",
  "data": {
    "personal": {
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1-555-123-4567",
      "passport_number": "A12345678",
      "updated_at": 1707667300
    }
  }
}
```

### Session Storage

```php
$_SESSION['booking']['personal'] = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1-555-123-4567',
    'passport_number' => 'A12345678',
    'updated_at' => time()
];
```

---

## 4️⃣ Payment Details Handler

**Endpoint:** `/php/handlers/payment-details.php`
**Method:** `POST`
**Session Required:** Yes (All previous steps required)

### Request

```javascript
const formData = new FormData();
formData.append("csrf_token", csrfToken);
formData.append("cardholder_name", "John Doe");
formData.append("card_number", "4532015112830366");
formData.append("expiry_date", "12/25");
formData.append("cvv", "123");

fetch("/php/handlers/payment-details.php", {
  method: "POST",
  body: formData,
});
```

### Parameters

| Field           | Type   | Required | Validation                          |
| --------------- | ------ | -------- | ----------------------------------- |
| csrf_token      | string | Yes      | Session CSRF token                  |
| cardholder_name | string | Yes      | 2-100 chars, letters/spaces/hyphens |
| card_number     | string | Yes      | Valid Luhn (13-19 digits)           |
| expiry_date     | string | Yes      | MM/YY format, must be future        |
| cvv             | string | Yes      | 3-4 digits                          |

### Response: Success (200 OK)

```json
{
  "success": true,
  "message": "Payment details saved successfully",
  "data": {
    "payment_status": "verified",
    "masked_card": "**** **** **** 0366"
  }
}
```

**Important:** Response does NOT include card details. They're only stored internally as masked versions.

### Session Storage

```php
$_SESSION['booking']['payment'] = [
    'cardholder_name' => 'John Doe',
    'card_number_masked' => '**** **** **** 0366',
    'card_last_four' => '0366',
    'expiry_date' => '12/25',  // NEVER full expiry unmasked
    'updated_at' => time()
];
// CVV is NOT stored
```

---

## 5️⃣ Booking Confirmation Handler

**Endpoint:** `/php/handlers/confirm-booking.php`
**Method:** `POST`
**Session Required:** Yes (All steps must be complete)

### Request

```javascript
const formData = new FormData();
formData.append("csrf_token", csrfToken);

fetch("/php/handlers/confirm-booking.php", {
  method: "POST",
  body: formData,
});
```

### Parameters

| Field      | Type   | Required | Notes              |
| ---------- | ------ | -------- | ------------------ |
| csrf_token | string | Yes      | Session CSRF token |

### Response: Success (200 OK)

```json
{
  "success": true,
  "message": "Booking confirmed successfully!",
  "data": {
    "booking_reference": "BOOK-AB1C2D",
    "customer_email": "john@example.com",
    "confirmation_message": "A confirmation email has been sent to your email address."
  }
}
```

### Response: Error (400/500)

```json
{
  "success": false,
  "message": "Booking is incomplete. Please complete all steps.",
  "errors": null
}
```

### Side Effects

1. **Send Customer Email** - Confirmation with masked card details
2. **Send Admin Email** - Detailed booking notification
3. **Clear Session** - All booking data is deleted
4. **Regenerate Session ID** - Security best practice

### Session Storage

After confirmation:

```php
unset($_SESSION['booking']);      // All data cleared
unset($_SESSION['booking_start_time']);
session_regenerate_id(true);      // New session ID
```

---

## Error Handling

### HTTP Status Codes

| Code | Meaning            | Example                  |
| ---- | ------------------ | ------------------------ |
| 200  | Success            | Booking step completed   |
| 400  | Bad Request        | Validation failed        |
| 403  | Forbidden          | CSRF token invalid       |
| 405  | Method Not Allowed | Used GET instead of POST |
| 500  | Server Error       | API call failed          |

### Error Response Format

```json
{
  "success": false,
  "message": "Human-readable error message",
  "errors": {
    "field_name": "Specific field error"
  }
}
```

### Common Error Messages

```
"Invalid security token"              // CSRF mismatch
"Validation failed"                   // Input validation failed
"Method not allowed"                  // Not a POST request
"Please search for flights first"     // Session incomplete
"Invalid card number"                 // Card Luhn check failed
"Card has expired"                    // Expiry date is past
"Invalid email address"               // Email format invalid
```

---

## CSRF Token Handling

### Generating Token

```php
// In page PHP:
require_once __DIR__ . '/../php/core/Security.php';
require_once __DIR__ . '/../php/core/BookingSession.php';

BookingSession::init();
$csrfToken = Security::generateCSRFToken();
```

### Including in Form

```html
<!-- In HTML -->
<form method="POST" action="/php/handlers/search.php">
  <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars($csrfToken); ?>"
  />
  <!-- other fields -->
</form>
```

### Verifying Token

```php
// In handler:
if (!Security::verifyCSRFToken($csrf)) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Invalid security token']));
}
```

---

## Session Timeout

- **Default:** 30 minutes
- **Cookie Lifetime:** 1800 seconds
- **Max Booking Time:** 30 minutes from search to confirmation

**To change:**

```php
// In BookingSession::init()
session_set_cookie_params([
    'lifetime' => 3600,  // 1 hour
    ...
]);

// In isSessionExpired()
return $elapsed > (60 * 60);  // 1 hour
```

---

## Rate Limiting Recommendations

Add to production:

```php
// Rate limit API calls
$ipAddress = $_SERVER['REMOTE_ADDR'];
$key = 'rate_limit_' . $ipAddress;
$limit = 10;  // 10 requests
$window = 60; // per 60 seconds

if (isset($_SESSION[$key]) && $_SESSION[$key] > $limit) {
    http_response_code(429);
    die(json_encode(['success' => false, 'message' => 'Too many requests']));
}

$_SESSION[$key] = $_SESSION[$key] ?? 0 + 1;
```

---

## Testing with cURL

### Test Search

```bash
curl -X POST http://localhost:8000/php/handlers/search.php \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "csrf_token=token123&origin=JFK&destination=LHR&departure_date=2026-06-01&passengers=1"
```

### Test Flight Selection

```bash
curl -X POST http://localhost:8000/php/handlers/select-flight.php \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "csrf_token=token123&id=flight_123&airline=BA&flight_number=112&departure_airport=JFK&arrival_airport=LHR&departure_time=15:30&arrival_time=03:45&duration=PT7H15M&stops=0&price=750&currency=USD&seat_class=ECONOMY"
```

---

## Integration Checklist

- [ ] CSRF tokens generated on every page
- [ ] All form submissions via POST
- [ ] Session initialized before any booking data access
- [ ] Error responses parsed and displayed to user
- [ ] Loading states shown during API calls
- [ ] Browser session storage used as backup (flight data)
- [ ] Validation errors parsed and displayed per field
- [ ] Card number never logged or displayed
- [ ] Confirmation message shows booking reference
- [ ] Email sent confirmation visible to user

---

**For more details, see:** BOOKING_SYSTEM_README.md
