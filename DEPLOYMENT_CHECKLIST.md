# Production Deployment Checklist

Complete checklist for deploying the Exploreease booking system to production.

---

## 🔒 Security Configuration

### HTTPS/SSL

- [ ] SSL certificate installed (Let's Encrypt or purchased)
- [ ] HTTPS enabled and enforced
- [ ] HTTP redirects to HTTPS
- [ ] Security headers configured:
  ```apache
  # .htaccess or web server config
  Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
  Header set X-Content-Type-Options "nosniff"
  Header set X-Frame-Options "SAMEORIGIN"
  Header set X-XSS-Protection "1; mode=block"
  Header set Referrer-Policy "strict-origin-when-cross-origin"
  ```

### PHP Security

- [ ] `display_errors = Off` in php.ini
- [ ] `log_errors = On` in php.ini
- [ ] Error logs sent to file, not displayed
- [ ] `expose_php = Off` in php.ini
- [ ] `disable_functions` configured for unused functions
- [ ] `open_basedir` restricted to web root
- [ ] `session.cookie_httponly = On`
- [ ] `session.cookie_secure = On`
- [ ] `session.cookie_samesite = Strict`

### File Permissions

```bash
# Web root
chmod 755 /var/www/exploreease

# PHP files
find . -name "*.php" -type f -exec chmod 644 {} \;

# Directories
find . -name "*" -type d -exec chmod 755 {} \;

# Writable directories (logs, sessions)
chmod 777 /var/www/exploreease/logs
chmod 777 /var/www/exploreease/sessions
```

### .env Protection

- [ ] `.env` file exists with production credentials
- [ ] `.env` NOT in version control
- [ ] `.env` readable only by web server user
- [ ] `.env.example` provided as template
- [ ] Sensitive paths restricted in web server config

---

## 🔑 API & Service Configuration

### Amadeus API

- [ ] Production API credentials obtained (not sandbox)
- [ ] `AMADEUS_ENV=prod` in `.env`
- [ ] `AMADEUS_CLIENT_ID` and `AMADEUS_CLIENT_SECRET` set
- [ ] API rate limits understood (e.g., 10 requests/second)
- [ ] Error handling for API failures
- [ ] API response logging configured

### Email Configuration

- [ ] SMTP server configured
- [ ] `SMTP_HOST`, `SMTP_USER`, `SMTP_PASSWORD` in `.env`
- [ ] `SMTP_PORT` set correctly (usually 587 for TLS)
- [ ] Sender email address valid and deliverable
- [ ] Admin email configured
- [ ] Test email sent successfully
- [ ] SPF/DKIM records configured in DNS
- [ ] Email logging configured (without sensitive data)

---

## 📊 Database (If Added Later)

- [ ] Database created and configured
- [ ] Connection string in `.env`
- [ ] Database user has minimal required permissions
- [ ] Database backups scheduled
- [ ] Database password changed from default
- [ ] SSL connection to database working
- [ ] Prepared statements used (no SQL injection)

---

## 🚀 Application Configuration

### PHP Settings

- [ ] PHP 7.4+ (or 8.0+) running
- [ ] cURL extension enabled
- [ ] Session extension enabled
- [ ] OpenSSL extension enabled
- [ ] JSON extension enabled
- [ ] Multipart form data limits appropriate
- [ ] `post_max_size` >= expected booking data
- [ ] `upload_max_filesize` appropriate
- [ ] `max_execution_time` adequate for API calls
- [ ] OPcache enabled for performance

### Application Settings

- [ ] Session timeout set appropriately (30 minutes)
- [ ] CSRF token length configured (32 bytes)
- [ ] Booking reference format appropriate
- [ ] Email template styling tested
- [ ] Timezone configured correctly
- [ ] Locale settings correct for number formatting

---

## 📋 Testing

### Functional Testing

- [ ] Complete booking flow tested (search → confirmation)
- [ ] All validation working (invalid inputs rejected)
- [ ] Card masking verified (only last 4 digits shown)
- [ ] CVV never logged or displayed
- [ ] Emails sent successfully to customer and admin
- [ ] Session timeout working
- [ ] CSRF protection working
- [ ] Back button behavior correct
- [ ] Error messages friendly and helpful
- [ ] Mobile responsiveness tested

### Security Testing

- [ ] SQL injection attempts blocked
- [ ] XSS attempts blocked
- [ ] CSRF token validation working
- [ ] Invalid HTTPS requests rejected
- [ ] POST-only enforcement working
- [ ] Sensitive data not in logs
- [ ] Error messages don't expose paths
- [ ] Input validation comprehensive
- [ ] Output properly escaped

### Performance Testing

- [ ] Page load time < 3 seconds
- [ ] API response time acceptable
- [ ] No memory leaks in PHP scripts
- [ ] Session reads/writes efficient
- [ ] Email sending doesn't timeout
- [ ] Concurrent bookings handled

---

## 🔍 Monitoring & Logging

### Logging

- [ ] Error logs monitored
- [ ] Application logs configured
- [ ] No sensitive data in logs
- [ ] Log rotation configured
- [ ] Log retention policy set
- [ ] Log access restricted

### Monitoring

- [ ] Uptime monitoring enabled
- [ ] Error rate alerts configured
- [ ] API failure alerts set up
- [ ] Email delivery monitoring enabled
- [ ] Server resource monitoring configured
- [ ] SSL certificate expiry alerts set

### Backups

- [ ] Regular backup schedule set
- [ ] Backups tested for restore
- [ ] Backup storage secured
- [ ] Disaster recovery plan documented

---

## 📝 Documentation

- [ ] README updated with production URLs
- [ ] API documentation current
- [ ] Security policies documented
- [ ] Data retention policy defined
- [ ] Privacy policy created
- [ ] Terms of service available
- [ ] SLA documented
- [ ] Contact information current

---

## 👥 User Communication

- [ ] Privacy policy visible
- [ ] Terms & conditions accepted
- [ ] Data handling explained
- [ ] Support contact information displayed
- [ ] Email confirmation text clear
- [ ] Booking reference format explained
- [ ] Refund/cancellation policy clear

---

## 🔐 Compliance

### Privacy/Data Protection

- [ ] GDPR compliance (if applicable)
- [ ] CCPA compliance (if applicable)
- [ ] Data collection minimized
- [ ] User consent obtained
- [ ] Data deletion working
- [ ] Data portability supported

### PCI DSS

- [ ] No full card number storage
- [ ] No CVV storage
- [ ] HTTPS enforced
- [ ] No logging of sensitive data
- [ ] Access controls in place
- [ ] Vulnerability scanning scheduled

### Payment Card Industry

- [ ] Card validation using Luhn
- [ ] Expiry date validation
- [ ] CVV validation (not stored)
- [ ] No unencrypted transmission

---

## 🧹 Cleanup

### Codebase

- [ ] Debug/test code removed
- [ ] Console.log statements removed
- [ ] Comments cleaned up
- [ ] Unused files deleted
- [ ] Dependencies updated
- [ ] Code reviewed

### Configuration

- [ ] Test data removed
- [ ] Development API keys removed
- [ ] Debug flags turned off
- [ ] Sample files removed
- [ ] .env.example provides good template

### Build

- [ ] All files minified
- [ ] Source maps removed from production
- [ ] Cache busting implemented
- [ ] Static assets optimized

---

## 📊 Pre-Launch

### Final Checks

- [ ] Run through entire booking flow
- [ ] Check all emails arrive
- [ ] Verify no errors in logs
- [ ] Test on multiple browsers
- [ ] Test on mobile devices
- [ ] Check loading speeds
- [ ] Verify HTTPS everywhere
- [ ] Check SSL certificate validity

### Staging Environment

- [ ] Exact copy of production
- [ ] Full testing in staging first
- [ ] Staff training on production system
- [ ] Support team ready

### Launch Plan

- [ ] Launch window scheduled
- [ ] Rollback plan documented
- [ ] Team on standby
- [ ] Monitoring ready
- [ ] Support escalation path clear

---

## ✅ Post-Launch

### Day 1

- [ ] Monitor error logs closely
- [ ] Check email delivery
- [ ] Verify API connectivity
- [ ] Monitor server performance
- [ ] Check for user issues

### Week 1

- [ ] Review analytics
- [ ] Check for security issues
- [ ] Verify backups working
- [ ] Update documentation
- [ ] Gather user feedback

### Ongoing

- [ ] Monthly security patches
- [ ] Quarterly penetration testing
- [ ] Regular backup verification
- [ ] Log analysis
- [ ] Performance optimization
- [ ] User support monitoring

---

## 🚨 Emergency Contacts

**Update before launch:**

- [ ] On-call engineer contact
- [ ] On-call manager contact
- [ ] API support phone number
- [ ] Email support queue setup
- [ ] Escalation procedure documented

---

## 📞 Support Infrastructure

- [ ] Support email configured
- [ ] Support hours defined
- [ ] Response time SLA defined
- [ ] Ticketing system ready
- [ ] FAQ documentation prepared
- [ ] Common issues documented
- [ ] Troubleshooting guide ready

---

## 🎯 Success Criteria

- [ ] 100+ bookings completed without errors
- [ ] Zero security incidents
- [ ] Email delivery > 99%
- [ ] Page load time < 3 sec
- [ ] API response time < 2 sec
- [ ] 99.9% uptime achieved
- [ ] User satisfaction > 4.5/5
- [ ] Support tickets < 5% of bookings

---

**Deployment Date:** ******\_\_\_******
**Deployed By:** ******\_\_\_******
**Signed Off:** ******\_\_\_******

---

## Version History

| Version | Date       | Changes                    |
| ------- | ---------- | -------------------------- |
| 1.0.0   | 2026-02-11 | Initial production release |
