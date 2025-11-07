# 🚀 Quick Start - M-Pesa Payment Integration

## ✅ What's Already Done
- ✅ Payment handler created with your M-Pesa credentials
- ✅ M-Pesa callback handler set up
- ✅ Payment modal added to shop page
- ✅ Cart checkout integration complete
- ✅ Your Consumer Key & Secret configured
- ✅ Database auto-creates orders table

## ⚡ Quick Setup (3 Steps)

### Step 1: Get Your Passkey (2 minutes)
1. Go to: https://developer.safaricom.co.ke/
2. Login → My Apps → Click "Gymly"
3. Find "Lipa Na M-Pesa Online" section
4. Copy the **Passkey**
5. Open `C:\Apache24\htdocs\Gymly\.env`
6. Replace this line:
   ```
   MPESA_PASSKEY=bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
   ```
   With your actual passkey from Daraja

### Step 2: Set Up ngrok (5 minutes)
1. Download: https://ngrok.com/download (Windows version)
2. Extract the zip file
3. Open PowerShell in that folder
4. Run:
   ```powershell
   .\ngrok.exe http 80
   ```
5. Copy the URL shown (e.g., `https://abc123.ngrok.io`)
6. Update `.env` file:
   ```
   MPESA_CALLBACK_URL=https://abc123.ngrok.io/Gymly/handlers/mpesaCallback.php
   ```

### Step 3: Update Daraja Callback (2 minutes)
1. Go back to https://developer.safaricom.co.ke/
2. My Apps → Gymly
3. Under "Lipa Na M-Pesa Online"
4. Update Callback URL to: `https://your-ngrok-url.ngrok.io/Gymly/handlers/mpesaCallback.php`
5. Click Save

## 🎯 Test Payment

1. Open: http://localhost/Gymly/pages/shop.php
2. Add products to cart
3. Click "Cart" → "Proceed to Checkout"
4. Enter:
   - Name: Your Name
   - Phone: `0708374149` (sandbox test number)
   - Or use your real number if it's registered
5. Click "Confirm Payment"
6. **Check your phone** - you'll get M-Pesa prompt!
7. Enter PIN to complete

## 📱 Sandbox Test Numbers
- Primary: `254708374149` or `0708374149`
- Works with any amount in sandbox mode

## 🔍 Troubleshooting

### Not receiving M-Pesa prompt?
```powershell
# Check Apache logs
Get-Content C:\Apache24\logs\error.log -Tail 20
```

### Verify ngrok is running
- Keep the ngrok terminal window open
- Should show incoming requests
- URL changes each time you restart ngrok (update .env when it does)

### Phone number format
- ✅ Correct: `254712345678`, `0712345678`
- ❌ Wrong: `+254712345678`, `712345678`

## 📂 Files Created

| File | Purpose |
|------|---------|
| `handlers/processPayment.php` | Processes M-Pesa & card payments |
| `handlers/mpesaCallback.php` | Receives M-Pesa payment confirmations |
| `.env` | Your M-Pesa credentials (already configured) |
| `.env.example` | Template for others |
| `MPESA_SETUP_GUIDE.md` | Full documentation |

## 🎨 Payment Features

✅ Real M-Pesa STK Push (phone prompt)
✅ Customer receives actual M-Pesa SMS
✅ Orders saved to database
✅ Payment status tracking
✅ M-Pesa receipt number stored
✅ Phone number validation
✅ Beautiful payment modal
✅ Cart clears after successful payment

## 💳 Card Payments (Coming Soon)

Currently shows placeholder. To add:
- **Flutterwave**: https://flutterwave.com/
- **Stripe**: https://stripe.com/

## 🚀 Production Deployment

When ready for live payments:
1. Get production credentials from Daraja
2. Update `.env` with production keys
3. Change `MPESA_ENVIRONMENT=production`
4. Deploy to server with HTTPS
5. Update callback URL to your domain

## 📞 Support

- Daraja Docs: https://developer.safaricom.co.ke/Documentation
- Test Credentials: https://developer.safaricom.co.ke/test_credentials
- ngrok Docs: https://ngrok.com/docs

---

**That's it! You're ready to accept real M-Pesa payments! 💰📱**
