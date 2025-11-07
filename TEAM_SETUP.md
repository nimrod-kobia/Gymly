# Gymly - Team Setup Guide

## For Teammates Who Want to Contribute

### Quick Setup (5 minutes)

1. **Clone the repository**
   ```bash
   git clone https://github.com/nimrod-kobia/Gymly.git
   cd Gymly
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Get the .env file**
   - Ask the project owner for the `.env` file
   - Place it in the root directory: `C:\Apache24\htdocs\Gymly\.env`
   - This contains the M-Pesa credentials and database config

4. **Database Setup**
   - Make sure you have access to the PostgreSQL database
   - Or update `.env` with your own Neon database credentials

5. **Start Apache**
   - Make sure Apache is running
   - Open: http://localhost/Gymly/pages/shop.php

6. **Test Payment**
   - Use your own M-Pesa number to test
   - Sandbox test number: 0708374149
   - You'll receive real M-Pesa prompts!

---

## For Testing Without Code Access

Just open the shop and use it like a normal customer:
- Visit: http://localhost/Gymly/pages/shop.php (if running locally)
- Or the deployed URL
- Use your M-Pesa number to pay
- Complete payment on your phone

---

## What You Need

### Required:
- ✅ PHP 8.0+
- ✅ Composer
- ✅ Apache Web Server
- ✅ PostgreSQL database access
- ✅ `.env` file (ask project owner)

### Optional (for M-Pesa callback testing):
- ngrok (for local development)
- See: MPESA_SETUP_GUIDE.md

---

## Important Files

- `.env` - Configuration (credentials) - **DO NOT COMMIT TO GIT**
- `composer.json` - PHP dependencies
- `handlers/processPayment.php` - Payment processor
- `pages/shop.php` - Main shop page

---

## Questions?

Check the documentation:
- `README_PAYMENT.md` - Payment system overview
- `QUICK_START.md` - Quick setup guide
- `MPESA_SETUP_GUIDE.md` - Detailed M-Pesa setup

---

## Security Note

⚠️ **Never commit the `.env` file to Git!**
- It contains sensitive credentials
- Each developer should have their own copy
- Already added to `.gitignore`
