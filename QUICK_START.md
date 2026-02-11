# Exploreease Flight Booking System - Quick Start Guide

## ⚡ 5-Minute Setup

### 1️⃣ Get Amadeus API Credentials (5 minutes)

1. Visit https://developers.amadeus.com/
2. Sign up for a free account
3. Go to **Developers** → **My Workspace**
4. Get your **Client ID** and **Client Secret** (Sandbox environment)

### 2️⃣ Configure Environment Variables

**Copy the template:**

```bash
cp .env.example .env
```

**Edit `.env` file:**

```env
AMADEUS_CLIENT_ID=your_client_id_here
AMADEUS_CLIENT_SECRET=your_client_secret_here
AMADEUS_ENV=test
SMTP_HOST=smtp.gmail.com
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password
```

### 3️⃣ Setup Email (Gmail Example)

#### If using Gmail:

1. Enable 2-Step Verification: https://myaccount.google.com/security
2. Generate App Password: https://myaccount.google.com/apppasswords
3. Use the 16-character password in your `.env` file

#### If using other SMTP:

Update `php/core/EmailService.php` with your SMTP details

### 4️⃣ Start Development Server

```bash
# Navigate to project directory
cd "c:/Users/HP/Documents/Offce Project/Exploreease/exploreease"

# Start PHP server
php -S localhost:8000
```

### 5️⃣ Test the System

1. Open browser: `http://localhost:8000/pages/search.php`
2. Try a search:
   - From: **JFK** (New York)
   - To: **LHR** (London)
   - Date: **Any future date**
   - Passengers: **1**
3. Follow the booking flow
4. Check your email for confirmation

---

## 🎯 Booking Flow Overview

```
[Search Page] → [Results Page] → [Personal Details] → [Payment] → [Confirmation]
    ↓              ↓                  ↓                   ↓            ↓
 Airport        Select         Name, Email,         Card Details  Send Emails
 & Dates        Flight         Phone, Passport      (Masked)      & Confirm
```

### Files by Step:

| Step | File                         | Handler                             | Session Storage |
| ---- | ---------------------------- | ----------------------------------- | --------------- |
| 1    | `pages/search.php`           | `php/handlers/search.php`           | Search criteria |
| 2    | `pages/results.php`          | `php/handlers/select-flight.php`    | Flight details  |
| 3    | `pages/personal-details.php` | `php/handlers/personal-details.php` | Personal info   |
| 4    | `pages/payment-details.php`  | `php/handlers/payment-details.php`  | Masked card     |
| 5    | `pages/confirmation.php`     | `php/handlers/confirm-booking.php`  | Send emails     |

---

## 🔐 Security Highlights

### What's Secured:

- ✅ **CSRF Tokens** on all forms
- ✅ **Input Validation** on every field
- ✅ **Card Masking** (never stores full number)
- ✅ **Session Security** (30-min timeout, httponly)
- ✅ **POST-Only** transmission (no GET with sensitive data)
- ✅ **Injection Prevention** (SQL, XSS checks)
- ✅ **Email Encryption** (via SMTP TLS)

### What's NOT Stored:

- ❌ Full card numbers
- ❌ CVV codes
- ❌ Sensitive logs
- ❌ User data (session-only, cleared after booking)

### What IS Stored (Masked):

- ✅ \***\* \*\*** \*\*\*\* 1234 (last 4 digits only)
- ✅ Expiry date (MM/YY format)
- ✅ Cardholder name

---

## 🧪 Test Scenarios

### Scenario 1: Successful Booking

```
Search: JFK → LHR, 2026-06-01, 1 passenger
Flight: BA112, $750
Name: John Doe
Email: john@example.com
Phone: +1-555-123-4567
Card: 4532015112830366 (Valid test card)
Expiry: 12/25
CVV: 123
Result: ✅ Confirmation email sent
```

### Scenario 2: Invalid Card

```
Card: 1234567890123456 (Invalid Luhn)
Result: ❌ Validation error: "Invalid card number"
```

### Scenario 3: Expired Card

```
Card: 4532015112830366
Expiry: 12/24 (past date)
Result: ❌ Validation error: "Card has expired"
```

### Scenario 4: CSRF Attack Prevention

```
Missing/Invalid CSRF Token
Result: ❌ 403 Forbidden
```

---

## 🛠️ Development Tips

### View Session Data (Debug)

Add to any page:

```php
<?php
session_start();
echo '<pre>';
echo 'Session: ';
print_r($_SESSION['booking'] ?? []);
echo '</pre>';
?>
```

### Check PHP Configuration

```bash
# Display PHP info
php -i

# Check PHP modules
php -m

# Verify cURL is enabled
php -m | grep curl
```

### Monitor Error Logs

```bash
# Watch error log in real-time
tail -f /var/log/php_errors.log

# Or check PHP error log location:
php -i | grep error_log
```

### Test Email Locally (No SMTP)

Replace `EmailService::sendCustomerConfirmation()` with:

```php
public function sendCustomerConfirmation($bookingSummary)
{
    // Mock email - just log it
    $filename = 'emails/' . date('Y-m-d_H-i-s') . '.txt';
    file_put_contents($filename, json_encode($bookingSummary, JSON_PRETTY_PRINT));
    return true;
}
```

---

## 📦 File Structure Reference

```
php/
├── core/                    # Reusable classes
│   ├── Security.php        # CSRF, sanitization, masking
│   ├── Validator.php       # Input validation rules
│   ├── BookingSession.php  # Session management
│   └── EmailService.php    # Email sending
├── config/
│   └── amadeus-config.php  # API credentials
├── api/
│   └── AmadeusService.php  # API integration
└── handlers/               # Request processors
    ├── search.php          # Fetch flights
    ├── select-flight.php   # Store flight
    ├── personal-details.php # Store personal info
    ├── payment-details.php  # Store payment (masked)
    └── confirm-booking.php  # Send emails
```

---

## 🐛 Common Issues & Solutions

### Issue: "Can't connect to API"

**Solution:**

- Check internet connection
- Verify API credentials
- Check if using Sandbox (test) vs Production
- Verify cURL is enabled: `php -m | grep curl`

### Issue: "Emails not sending"

**Solution:**

- Verify SMTP credentials in `.env`
- Check if using Gmail App Password (not regular password)
- Allow "Less secure apps" if not using App Password
- Check firewall (port 587 open)

### Issue: "Session expired error"

**Solution:**

- Session timeout is 30 minutes
- Increase in `BookingSession.php`: `isSessionExpired(60)` for 60 minutes
- Clear browser cookies and restart

### Issue: "CSRF token mismatch"

**Solution:**

- Clear browser session cookies
- Verify JavaScript is enabled
- Don't have multiple windows with same booking flow

---

## 🚀 Production Checklist

Before deploying to production:

- [ ] Enable HTTPS on server
- [ ] Generate self-signed or Let's Encrypt SSL certificate
- [ ] Update `.env` with production credentials
- [ ] Switch Amadeus to `AMADEUS_ENV=prod`
- [ ] Configure proper database (if needed)
- [ ] Set up error logging to file (not display)
- [ ] Configure email with your domain SMTP
- [ ] Update admin email address
- [ ] Test with real test card from payment provider
- [ ] Set up monitoring/alerting
- [ ] Enable PHP opcache for performance
- [ ] Configure proper file permissions (644 files, 755 dirs)
- [ ] Remove debug/test code
- [ ] Test all error scenarios

---

## 📚 API Documentation Links

- **Amadeus API**: https://developers.amadeus.com/
- **PHPMailer**: https://phpmailer.world/
- **PHP Sessions**: https://www.php.net/manual/en/book.session.php
- **OWASP Security**: https://owasp.org/www-community/

---

## 💡 Tips for Customization

### Change colors/styling:

Edit `pages/*.php` → `<style>` sections

### Add more flight filters:

Modify `results.php` and `AmadeusService.php`

### Store data in database:

Create `php/core/Database.php` class and replace session calls

### Add real payment processing:

Integrate Stripe/PayPal API in `payment-details.php`

### Change email templates:

Edit template methods in `EmailService.php`

---

## 📞 Getting Help

1. **Check logs:**

   ```bash
   tail -f /var/log/apache2/error.log  # Apache
   tail -f /var/log/nginx/error.log     # Nginx
   ```

2. **Test API connectivity:**

   ```bash
   curl -X GET "https://test.api.amadeus.com/v2/shopping/flight-offers?originLocationCode=JFK&destinationLocationCode=LHR&departureDate=2026-06-01&adults=1" -H "Authorization: Bearer YOUR_TOKEN"
   ```

3. **Debug PHP:**
   ```bash
   php -d display_errors=1 your-script.php
   ```

---

**Ready to go! 🚀**

Start with `http://localhost:8000/pages/search.php`
