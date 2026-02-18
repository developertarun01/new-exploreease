# Email & SMTP Configuration Guide

This guide covers setting up email functionality for booking confirmations in the Exploreease system.

---

## 📧 Email Flow

```
User Completes Booking
        ↓
BookingSession contains all data
        ↓
confirm-booking.php triggers EmailService
        ↓
Customer Email (with masked card)
Admin Email (detailed notification)
        ↓
Session cleared, no data persists
```

---

## 🔧 Configuration Methods

### Method 1: Environment Variables (Recommended)

**File: `.env`**

```env
SMTP_HOST=smtp.gmail.com
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_PORT=587
ADMIN_EMAIL=admin@exploreease.com
FROM_EMAIL=noreply@exploreease.com
FROM_NAME=Exploreease Bookings
```

**Code reads from `.env`:**

```php
$this->mail->Host = getenv('SMTP_HOST') ?? 'smtp.gmail.com';
```

### Method 2: Direct Configuration (Not Recommended for Production)

**File: `php/core/EmailService.php`**

```php
public function __construct()
{
    $this->mail = new PHPMailer(true);
    $this->mail->isSMTP();
    $this->mail->Host = 'smtp.gmail.com';        // Change this
    $this->mail->Username = 'your-email@gmail.com';  // Change this
    $this->mail->Password = 'your-app-password'; // Change this
}
```

---

## Gmail Setup (Step-by-Step)

### Prerequisites:

- Gmail account (supports @gmail.com and Google Workspace accounts)
- 2-Step Verification enabled

### Steps:

**1. Enable 2-Step Verification**

```
Visit: https://myaccount.google.com/security
→ Scroll to "How you sign in to Google"
→ Click "2-Step Verification"
→ Follow prompts to enable
```

**2. Generate App Password**

```
Visit: https://myaccount.google.com/apppasswords

Ensure 2-Step is enabled first!

→ Select App: Mail
→ Select Device: Windows PC (or your device)
→ Click "Generate"
→ Copy the 16-character password
```

**3. Update Configuration**

```env
SMTP_HOST=smtp.gmail.com
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=xxxx xxxx xxxx xxxx  # Remove spaces
```

**4. Test Connection**
Add to `php/core/EmailService.php`:

```php
// Add to constructor
try {
    $this->mail->SMTPDebug = 2;  // Enable debugging
    $this->mail->connect();
    echo "SMTP Connection successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## Other SMTP Providers

### Outlook/Microsoft 365

```env
SMTP_HOST=smtp.office365.com
SMTP_USER=your-email@outlook.com
SMTP_PASSWORD=your-password
SMTP_PORT=587
```

### SendGrid

```env
SMTP_HOST=smtp.sendgrid.net
SMTP_USER=apikey
SMTP_PASSWORD=SG.your-sendgrid-api-key
SMTP_PORT=587
```

### Mailgun

```env
SMTP_HOST=smtp.mailgun.org
SMTP_USER=postmaster@your-domain.mailgun.org
SMTP_PASSWORD=your-password
SMTP_PORT=587
```

### AWS SES (Simple Email Service)

```env
SMTP_HOST=email-smtp.region.amazonaws.com
SMTP_USER=your-ses-user
SMTP_PASSWORD=your-ses-password
SMTP_PORT=587
```

---

## 🔒 Security Best Practices

### ✅ DO:

- Use environment variables for credentials
- Use SMTP with authentication (username/password)
- Use TLS encryption (port 587) or SSL (port 465)
- Mask sensitive data in emails
- Log transaction IDs, not card numbers
- Use dedicated service account for email
- Enable DKIM/SPF records

### ❌ DON'T:

- Hardcode credentials in PHP files
- Send full card numbers in emails
- Use unencrypted SMTP (port 25)
- Log CVV or full card data
- Use personal email for transactional messages
- Store passwords in version control

---

## Email Templates

### Customer Confirmation Email

**What's Included:**

- Flight details (route, times, airline)
- Passenger info (name, email, phone)
- **Masked card** (last 4 digits only)
- Booking confirmation message
- Support contact information

**What's NOT Included:**

- ❌ Full card number
- ❌ CVV
- ❌ Unmasked payment details

**Example:**

```
Flight: JFK → LHR
Departure: June 1, 2026 at 15:30
Passenger: John Doe
Card: **** **** **** 1234
```

### Admin Notification Email

**What's Included:**

- Complete flight information
- Customer details
- Masked payment information
- Booking timestamp
- Customer email (for follow-up)

**What's NOT Included:**

- ❌ Full card number
- ❌ CVV
- ❌ Unmasked sensitive data

---

## 🧪 Testing Email Configuration

### Test 1: Verify SMTP Connection

Create `test-smtp.php`:

```php
<?php
require_once 'php/core/EmailService.php';

try {
    $email = new EmailService();
    echo "✅ SMTP connection successful!";
} catch (Exception $e) {
    echo "❌ SMTP connection failed: " . $e->getMessage();
}
?>
```

Run: `php test-smtp.php`

### Test 2: Send Test Email

Create `send-test-email.php`:

```php
<?php
require_once 'php/core/EmailService.php';

try {
    $email = new EmailService();

    $testBooking = [
        'search' => [
            'origin' => 'JFK',
            'destination' => 'LHR',
            'departure_date' => '2026-06-01'
        ],
        'flight' => [
            'airline' => 'BA',
            'flight_number' => '112',
            'departure_time' => '15:30',
            'arrival_time' => '03:45',
            'price' => 750,
            'currency' => 'USD'
        ],
        'personal' => [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'phone' => '+1-555-123-4567'
        ],
        'payment' => [
            'cardholder_name' => 'John Doe',
            'card_number_masked' => '**** **** **** 1234',
            'expiry_date' => '12/25'
        ]
    ];

    // Send to your test email
    $testBooking['personal']['email'] = 'your-test-email@example.com';
    $email->sendCustomerConfirmation($testBooking);

    echo "✅ Test email sent successfully!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

---

## Troubleshooting

### ❌ "SMTP Connect Failed"

**Check:**

1. SMTP credentials are correct
2. 2-Step Verification is enabled (Gmail)
3. App Password is used (Gmail), not regular password
4. Firewall allows outbound port 587
5. Internet connection is active

**Test:**

```bash
# Test port connectivity (macOS/Linux)
telnet smtp.gmail.com 587

# Or using nc
nc -zv smtp.gmail.com 587

# Windows
Invoke-WebRequest -Method Get -Uri "https://smtp.gmail.com:587" -Verbose
```

### ❌ "Authentication Failed"

**Check:**

1. Username is correct (full email address)
2. Password is correct (App Password for Gmail)
3. Account is active and can receive emails
4. 2-Step is actually enabled

**Fix:**

1. Regenerate App Password (Gmail)
2. Re-enter credentials in `.env`

### ❌ "Connection Timeout"

**Check:**

1. Firewall is blocking port 587
2. SMTP host is reachable
3. Network is connected
4. ISP isn't blocking SMTP

**Alternative:**
Try port 465 with SSL:

```env
SMTP_PORT=465
# And change in EmailService.php:
$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
```

### ❌ "Email Sent but Not Received"

**Check:**

1. Email went to spam folder
2. Recipient spam filter is blocking
3. Email hasn't arrived yet (wait 5 minutes)
4. Check confirmation was actually sent

**Verify:**

1. Check `getLastError()` in EmailService
2. Add error logging: `$this->mail->SMTPDebug = 2;`
3. Check sender address (From field) is valid

### ✅ Common Solutions

**Gmail not accepting login:**
→ Use App Password (16 chars), not your Gmail password

**All emails going to spam:**
→ Your domain needs SPF/DKIM records configured

**Port 587 blocked by ISP:**
→ Try port 465 with SSL/TLS

**Need to use corporate SMTP:**
→ Contact IT for SMTP host, username, password, port

---

## SPF & DKIM Records (Advanced)

For production emails to avoid spam folder:

### SPF Record

```
v=spf1 include:sendmail-smtp.example.com ~all
```

### DKIM Record

Generated by your email service provider

Add to your domain's DNS records. See your SMTP provider's documentation.

---

## Email Monitoring

### Log All Sent Emails

Modify `EmailService.php`:

```php
private function logEmail($recipient, $subject, $success)
{
    $log = date('Y-m-d H:i:s') . " | " .
           $recipient . " | " .
           $subject . " | " .
           ($success ? 'SUCCESS' : 'FAILED') . "\n";

    file_put_contents('logs/emails.log', $log, FILE_APPEND);
}
```

Call in `sendCustomerConfirmation()`:

```php
$this->logEmail($bookingSummary['personal']['email'], 'Booking Confirmation', true);
```

---

## Next Steps

1. ✅ Choose SMTP provider
2. ✅ Get SMTP credentials
3. ✅ Update `.env` file
4. ✅ Test with `test-smtp.php`
5. ✅ Send test email
6. ✅ Verify emails arrive in inbox
7. ✅ Check spam folder
8. ✅ Go live!

---

**Questions?** Check error_log or enable SMTPDebug in EmailService.php for detailed messages.
