# M-Pesa Payment Integration Setup Guide

## Overview
Your Gymly shop now has **real M-Pesa payment integration** using the Safaricom Daraja API. When customers pay, they will receive an actual M-Pesa payment prompt on their phone.

## Your Daraja Credentials
✅ **Consumer Key**: `YbDKywXLimqWX53n8xSsIfGTqbsLTNWvUIu2R4WL34h3t8yE`
✅ **Consumer Secret**: `m24pxjuXIybmqBLqkD9yjTA6nVLlAUa8bbDWDZvSkHEBpq78j0OEQ0WYacqe5twq`

---

## Step-by-Step Setup

### Step 1: Create Your .env File
1. Copy `.env.example` to `.env` in your root directory:
   ```powershell
   cd C:\Apache24\htdocs\Gymly
   Copy-Item .env.example .env
   ```

2. Open `.env` and update these values:
   ```env
   # Your Database Configuration (keep your existing values)
   PGHOST=your-neon-host.neon.tech
   PGPOOLHOST=your-pooled-host.neon.tech
   PGDATABASE=gymly
   PGUSER=your-username
   PGPASSWORD=your-password
   
   # M-Pesa Configuration (ALREADY FILLED IN)
   MPESA_ENVIRONMENT=sandbox
   MPESA_CONSUMER_KEY=YbDKywXLimqWX53n8xSsIfGTqbsLTNWvUIu2R4WL34h3t8yE
   MPESA_CONSUMER_SECRET=m24pxjuXIybmqBLqkD9yjTA6nVLlAUa8bbDWDZvSkHEBpq78j0OEQ0WYacqe5twq
   MPESA_SHORTCODE=174379
   MPESA_PASSKEY=bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
   MPESA_CALLBACK_URL=https://your-ngrok-url.ngrok.io/Gymly/handlers/mpesaCallback.php
   ```

### Step 2: Get Your Passkey from Daraja
1. Go to https://developer.safaricom.co.ke/
2. Log in to your account
3. Go to "My Apps" → Click on "Gymly"
4. Under "Lipa Na M-Pesa Online", find your **Passkey**
5. Copy the Passkey and replace it in your `.env` file:
   ```env
   MPESA_PASSKEY=your-actual-passkey-here
   ```

### Step 3: Set Up ngrok for Testing (Local Development)
M-Pesa needs a public URL to send payment confirmations. ngrok creates a tunnel to your localhost.

1. **Download ngrok**:
   - Go to https://ngrok.com/download
   - Download the Windows version
   - Extract the zip file

2. **Run ngrok**:
   ```powershell
   # Navigate to ngrok folder
   cd C:\path\to\ngrok
   
   # Start tunnel to port 80 (Apache)
   .\ngrok.exe http 80
   ```

3. **Copy the URL**:
   - ngrok will show a URL like: `https://abc123.ngrok.io`
   - Copy this URL

4. **Update your .env file**:
   ```env
   MPESA_CALLBACK_URL=https://abc123.ngrok.io/Gymly/handlers/mpesaCallback.php
   ```

5. **Update Daraja Portal**:
   - Go back to https://developer.safaricom.co.ke/
   - Go to "My Apps" → "Gymly"
   - Under "Lipa Na M-Pesa Online" → Update the Callback URL
   - Save changes

---

## Testing the Payment

### Test Phone Numbers (Sandbox)
The Daraja sandbox only accepts specific test numbers:
- **Test Phone**: `254708374149` (or `0708374149`)
- Any amount works in sandbox mode

### How to Test:
1. Open your shop: http://localhost/Gymly/pages/shop.php
2. Add items to cart
3. Click "Cart" in navbar
4. Click "Proceed to Checkout"
5. Fill in:
   - Name: Your name
   - Phone: `0708374149` (or any of your real numbers)
   - Payment Method: M-Pesa
6. Click "Confirm Payment"
7. **Check your phone** - you'll receive an M-Pesa prompt
8. Enter your M-Pesa PIN to complete payment

---

## What Happens When Customer Pays?

### 1. Customer Flow:
   - Adds items to cart
   - Clicks checkout
   - Enters phone number
   - Receives M-Pesa prompt on phone
   - Enters PIN to pay
   - Gets M-Pesa confirmation SMS

### 2. Backend Flow:
   - Order saved to database with "pending" status
   - M-Pesa STK Push sent to customer's phone
   - Customer completes payment
   - M-Pesa sends callback to your server
   - Order status updated to "completed"
   - M-Pesa receipt number saved

---

## Database Tables

The system automatically creates an `orders` table:
```sql
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255),
    phone_number VARCHAR(20),
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    transaction_ref VARCHAR(255),
    mpesa_receipt VARCHAR(255),
    items TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Troubleshooting

### Payment not working?
1. **Check Apache error logs**:
   ```powershell
   Get-Content C:\Apache24\logs\error.log -Tail 50
   ```

2. **Verify .env file exists**: Make sure `.env` is in `C:\Apache24\htdocs\Gymly\`

3. **Check ngrok is running**: The ngrok window should show requests coming in

4. **Verify Callback URL**: Make sure it's updated in both `.env` and Daraja portal

### Phone not receiving prompt?
- Use test number: `254708374149` in sandbox
- Check phone number format: `254712345678` (no spaces)
- Verify your Consumer Key and Secret are correct

### Database errors?
- Make sure your Neon PostgreSQL credentials are in `.env`
- The orders table will be created automatically on first payment

---

## Moving to Production

When you're ready for real payments:

1. **In Daraja Portal**:
   - Switch app to "Production" mode
   - Get production credentials
   - Get production shortcode and passkey

2. **Update .env**:
   ```env
   MPESA_ENVIRONMENT=production
   MPESA_CONSUMER_KEY=your-production-key
   MPESA_CONSUMER_SECRET=your-production-secret
   MPESA_SHORTCODE=your-production-shortcode
   MPESA_PASSKEY=your-production-passkey
   ```

3. **Deploy to server**:
   - Upload to a server with public domain
   - Update MPESA_CALLBACK_URL to your domain
   - SSL certificate required (HTTPS)

---

## Card Payment Integration (Future)

Currently, card payment shows a placeholder. To add real card payments:

### Option 1: Flutterwave
- Sign up at https://flutterwave.com/
- Get API keys
- Integrate Flutterwave inline payment

### Option 2: Stripe
- Sign up at https://stripe.com/
- Get API keys
- Add Stripe.js integration

---

## Support & Resources

- **Daraja API Docs**: https://developer.safaricom.co.ke/Documentation
- **ngrok Docs**: https://ngrok.com/docs
- **Test Credentials**: https://developer.safaricom.co.ke/test_credentials

---

## Summary Checklist

- [x] Payment handler created (`handlers/processPayment.php`)
- [x] Callback handler created (`handlers/mpesaCallback.php`)
- [x] Payment modal added to shop page
- [x] Cart integration complete
- [ ] Create `.env` file from `.env.example`
- [ ] Get Passkey from Daraja portal
- [ ] Install and run ngrok
- [ ] Update callback URL in `.env` and Daraja
- [ ] Test payment with sandbox number

**Once you complete the checklist above, you'll be ready to accept real M-Pesa payments!** 🎉
