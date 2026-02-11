# Quick Reference Guide

## 🎯 File Quick Links

### 📚 Documentation (Start Here)

1. **[BUILD_SUMMARY.md](BUILD_SUMMARY.md)** - Overview of everything created
2. **[BOOKING_SYSTEM_README.md](BOOKING_SYSTEM_README.md)** - Complete technical documentation
3. **[QUICK_START.md](QUICK_START.md)** - 5-minute setup guide
4. **[EMAIL_SETUP_GUIDE.md](EMAIL_SETUP_GUIDE.md)** - Email/SMTP configuration
5. **[API_HANDLERS_REFERENCE.md](API_HANDLERS_REFERENCE.md)** - API endpoint documentation
6. **[DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)** - Pre-launch checklist

### ⚙️ Configuration Files

- **[.env.example](.env.example)** - Environment variable template (copy to `.env`)

### 🌐 User-Facing Pages

| File                                                         | Purpose            | Step |
| ------------------------------------------------------------ | ------------------ | ---- |
| **[pages/search.php](pages/search.php)**                     | Flight search form | 1    |
| **[pages/results.php](pages/results.php)**                   | Flight selection   | 2    |
| **[pages/personal-details.php](pages/personal-details.php)** | Personal info form | 3    |
| **[pages/payment-details.php](pages/payment-details.php)**   | Card details form  | 4    |
| **[pages/confirmation.php](pages/confirmation.php)**         | Review & confirm   | 5    |

### 🔧 Backend - Core Classes

| File                                                           | Purpose                     | #Lines |
| -------------------------------------------------------------- | --------------------------- | ------ |
| **[php/core/Security.php](php/core/Security.php)**             | CSRF, sanitization, masking | 266    |
| **[php/core/Validator.php](php/core/Validator.php)**           | Input validation rules      | 315    |
| **[php/core/BookingSession.php](php/core/BookingSession.php)** | Session management          | 195    |
| **[php/core/EmailService.php](php/core/EmailService.php)**     | Email sending via PHPMailer | 285    |

### 🌐 Backend - API Integration

| File                                                               | Purpose             | #Lines |
| ------------------------------------------------------------------ | ------------------- | ------ |
| **[php/api/AmadeusService.php](php/api/AmadeusService.php)**       | Amadeus API wrapper | 195    |
| **[php/config/amadeus-config.php](php/config/amadeus-config.php)** | API credentials     | 10     |

### 📨 Backend - Request Handlers

| File                                                                       | Purpose                | Step | #Lines |
| -------------------------------------------------------------------------- | ---------------------- | ---- | ------ |
| **[php/handlers/search.php](php/handlers/search.php)**                     | Process flight search  | 1    | 70     |
| **[php/handlers/select-flight.php](php/handlers/select-flight.php)**       | Store flight selection | 2    | 95     |
| **[php/handlers/personal-details.php](php/handlers/personal-details.php)** | Store passenger info   | 3    | 85     |
| **[php/handlers/payment-details.php](php/handlers/payment-details.php)**   | Store payment info     | 4    | 105    |
| **[php/handlers/confirm-booking.php](php/handlers/confirm-booking.php)**   | Send emails & confirm  | 5    | 95     |

---

## 🚀 Setup (5 Minutes)

```bash
# 1. Create environment file
cp .env.example .env

# 2. Edit .env with your credentials
# - Amadeus API credentials
# - SMTP settings (Gmail, etc)

# 3. Start server
php -S localhost:8000

# 4. Visit the application
# http://localhost:8000/pages/search.php
```

**See:** [QUICK_START.md](QUICK_START.md) for detailed setup

---

## 📋 Booking Flow

```
[pages/search.php]
    ↓ POST to php/handlers/search.php
[pages/results.php]
    ↓ POST to php/handlers/select-flight.php
[pages/personal-details.php]
    ↓ POST to php/handlers/personal-details.php
[pages/payment-details.php]
    ↓ POST to php/handlers/payment-details.php
[pages/confirmation.php]
    ↓ POST to php/handlers/confirm-booking.php
[✅ Booking Confirmed - Emails Sent]
```

---

## 🔐 Security Features

| Feature             | Location           | Purpose                    |
| ------------------- | ------------------ | -------------------------- |
| CSRF Tokens         | Security.php       | Prevent cross-site forgery |
| Input Validation    | Validator.php      | Ensure data integrity      |
| Card Masking        | Security.php       | Hide sensitive data        |
| Session Security    | BookingSession.php | Secure data storage        |
| Injection Detection | Security.php       | Prevent SQL/XSS attacks    |
| Output Encoding     | Security.php       | Safe HTML display          |

**See:** [BOOKING_SYSTEM_README.md](BOOKING_SYSTEM_README.md#-security-architecture)

---

## 📧 Email Setup

### Gmail (Easiest)

```env
SMTP_HOST=smtp.gmail.com
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password  # Get from myaccount.google.com/apppasswords
SMTP_PORT=587
```

### Other Providers

- Outlook: `smtp.office365.com`
- SendGrid: `smtp.sendgrid.net`
- Mailgun: `smtp.mailgun.org`

**See:** [EMAIL_SETUP_GUIDE.md](EMAIL_SETUP_GUIDE.md)

---

## 🧪 Testing the System

### Test Search

```
From: JFK (New York)
To: LHR (London)
Date: Any future date
Passengers: 1-9
```

### Test Card Numbers

- Valid: `4532015112830366`
- Invalid: `1234567890123456`
- Expired: Use 12/24 or earlier

**See:** [QUICK_START.md](QUICK_START.md#-test-scenarios)

---

## 🐛 Quick Troubleshooting

### "API Connection Failed"

→ Check Amadeus API credentials in `.env`

### "Emails Not Sending"

→ Check SMTP settings and Gmail 2-Step/App Password

### "Session Expired"

→ Timeout is 30 minutes. Work faster or increase in BookingSession.php

### "CSRF Token Error"

→ Clear browser cookies, restart booking flow

**See:** [QUICK_START.md](QUICK_START.md#-common-issues--solutions)

---

## 📊 Key Endpoints

### Handler Endpoints

```
POST /php/handlers/search.php
POST /php/handlers/select-flight.php
POST /php/handlers/personal-details.php
POST /php/handlers/payment-details.php
POST /php/handlers/confirm-booking.php
```

### All responses are JSON

```json
{
    "success": true/false,
    "message": "Human readable message",
    "data": { /* response data */ },
    "errors": { /* field errors if validation fails */ }
}
```

**See:** [API_HANDLERS_REFERENCE.md](API_HANDLERS_REFERENCE.md)

---

## 🎯 Configuration Checklist

Before launching:

- [ ] Amadeus API credentials obtained
- [ ] `.env` file created with credentials
- [ ] SMTP configured and tested
- [ ] Admin email set
- [ ] HTTPS certificate installed
- [ ] Session timeout configured
- [ ] Error logging configured
- [ ] Backups scheduled
- [ ] Monitoring set up
- [ ] Security audit completed

**See:** [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)

---

## 💻 Development Commands

```bash
# Start development server
php -S localhost:8000

# Check PHP configuration
php -i

# Test SMTP connection
php test-smtp.php

# Check for errors
tail -f /var/log/php_errors.log

# Test a handler with cURL
curl -X POST http://localhost:8000/php/handlers/search.php \
  -d "origin=JFK&destination=LHR&departure_date=2026-06-01&passengers=1"
```

---

## 📚 Class Method Reference

### Security Class

```php
Security::generateCSRFToken()           // Generate token
Security::verifyCSRFToken($token)       // Verify token
Security::sanitizeInput($input)         // Remove HTML tags
Security::escapeHTML($text)             // Escape for display
Security::maskCardNumber($card)         // Show only last 4
Security::validateCardNumber($card)     // Luhn check
```

### Validator Class

```php
$validator = new Validator();
$validator->validateRequired($field, $value, $name);
$validator->validateEmail($field, $email);
$validator->validatePhone($field, $phone);
$validator->validateCardNumber($field, $card);
$validator->validateCardExpiry($field, $expiry);
$validator->hasErrors();                // Has validation errors?
$validator->getErrors();                // Get error messages
```

### BookingSession Class

```php
BookingSession::init();                 // Start session
BookingSession::setSearchCriteria(...); // Store search
BookingSession::setSelectedFlight($f);  // Store flight
BookingSession::setPersonalDetails(...);// Store personal
BookingSession::setPaymentDetails(...); // Store payment
BookingSession::getBookingSummary();    // Get all data
BookingSession::isBookingComplete();    // All steps done?
BookingSession::clearBookingData();     // Wipe booking data
```

### EmailService Class

```php
$email = new EmailService();
$email->sendCustomerConfirmation($booking);
$email->sendAdminNotification($booking, $email);
```

### AmadeusService Class

```php
$amadeus = new AmadeusService();
$flights = $amadeus->searchFlights($origin, $dest, $date, $passengers);
```

---

## 🔗 Important Links

| Resource       | URL                             |
| -------------- | ------------------------------- |
| Amadeus API    | https://developers.amadeus.com/ |
| PHPMailer      | https://phpmailer.world/        |
| OWASP Security | https://owasp.org/              |
| PHP Docs       | https://www.php.net/manual/     |

---

## 💡 Common Modifications

### Add Payment Gateway

→ Edit `pages/payment-details.php` and add real payment processing

### Add Database

→ Create `php/core/Database.php` and replace BookingSession calls

### Add User Accounts

→ Create authentication system with login/register

### Add Admin Dashboard

→ Create new pages folder for admin-specific functionality

### Change Colors

→ Edit `<style>` sections in `pages/*.php` files

**See:** [BOOKING_SYSTEM_README.md](BOOKING_SYSTEM_README.md#customization)

---

## ✅ Pre-Launch Checklist

- [ ] Complete booking flow tested end-to-end
- [ ] All validation working (invalid inputs rejected)
- [ ] Emails sent and received
- [ ] Card masking verified
- [ ] No errors in PHP logs
- [ ] HTTPS working
- [ ] CSRF protection verified
- [ ] Mobile responsiveness checked
- [ ] Performance acceptable
- [ ] Monitoring configured
- [ ] Backups tested
- [ ] Support team trained

**See:** [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)

---

## 📞 Getting Help

1. **Check Documentation**
   - [BOOKING_SYSTEM_README.md](BOOKING_SYSTEM_README.md) - Comprehensive guide
   - [QUICK_START.md](QUICK_START.md) - Quick answers
   - [API_HANDLERS_REFERENCE.md](API_HANDLERS_REFERENCE.md) - API details

2. **Check Inline Comments**
   - All PHP files have detailed comments
   - All classes have docblock comments
   - Code is self-documenting

3. **Enable Debug Mode**

   ```php
   // In handlers, to debug:
   error_log(print_r($_POST, true));      // Log POST data
   error_log(print_r($result, true));     // Log API response
   ```

4. **Check Error Logs**
   ```bash
   tail -f /var/log/php_errors.log
   ```

---

## 🎉 You're All Set!

Everything is ready to use. Start with:

1. **Setup:** [QUICK_START.md](QUICK_START.md) (5 minutes)
2. **Details:** [BOOKING_SYSTEM_README.md](BOOKING_SYSTEM_README.md) (full reference)
3. **Launch:** [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) (pre-flight)

**Let's build something great!** 🚀
