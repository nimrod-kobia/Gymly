<?php
require_once "../conf/conf.php";
require_once "../classes/database.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$paymentMethod = $data['paymentMethod'] ?? '';
$phoneNumber = $data['phoneNumber'] ?? '';
$amount = floatval($data['amount'] ?? 0);
$items = $data['items'] ?? [];
$customerName = $data['customerName'] ?? '';
$customerEmail = $data['customerEmail'] ?? '';

// Validate required fields
if (empty($paymentMethod) || $amount <= 0 || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate phone number for M-Pesa
if ($paymentMethod === 'mpesa') {
    if (empty($phoneNumber)) {
        echo json_encode(['success' => false, 'message' => 'Phone number is required for M-Pesa payment']);
        exit;
    }
    
    // Format phone number (remove spaces, hyphens, and ensure it starts with 254)
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
    if (substr($phoneNumber, 0, 1) === '0') {
        $phoneNumber = '254' . substr($phoneNumber, 1);
    } elseif (substr($phoneNumber, 0, 3) !== '254') {
        $phoneNumber = '254' . $phoneNumber;
    }
    
    if (!preg_match('/^254[17]\d{8}$/', $phoneNumber)) {
        echo json_encode(['success' => false, 'message' => 'Invalid Kenyan phone number format']);
        exit;
    }
}

try {
    if ($paymentMethod === 'mpesa') {
        $result = processMpesaPayment($phoneNumber, $amount, $items, $customerName, $customerEmail);
        echo json_encode($result);
    } elseif ($paymentMethod === 'card') {
        // For now, we'll simulate card payment
        // In production, integrate with a payment gateway like Stripe or Flutterwave
        $result = processCardPayment($amount, $items, $customerName, $customerEmail);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
    }
} catch (Exception $e) {
    error_log("Payment processing error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Payment processing failed. Please try again.']);
}

function processMpesaPayment($phoneNumber, $amount, $items, $customerName, $customerEmail) {
    // Get M-Pesa credentials from environment
    $consumerKey = $_ENV['MPESA_CONSUMER_KEY'] ?? '';
    $consumerSecret = $_ENV['MPESA_CONSUMER_SECRET'] ?? '';
    $shortcode = $_ENV['MPESA_SHORTCODE'] ?? '174379';
    $passkey = $_ENV['MPESA_PASSKEY'] ?? 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $callbackUrl = $_ENV['MPESA_CALLBACK_URL'] ?? '';
    $environment = $_ENV['MPESA_ENVIRONMENT'] ?? 'sandbox';
    
    if (empty($consumerKey) || empty($consumerSecret)) {
        throw new Exception('M-Pesa credentials not configured');
    }
    
    // Determine API URLs based on environment
    $authUrl = $environment === 'production' 
        ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    
    $stkPushUrl = $environment === 'production'
        ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
        : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    
    // Step 1: Get access token
    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
    
    $ch = curl_init($authUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("M-Pesa auth failed: " . $response);
        throw new Exception('Failed to authenticate with M-Pesa');
    }
    
    $authResult = json_decode($response);
    $accessToken = $authResult->access_token ?? '';
    
    if (empty($accessToken)) {
        throw new Exception('Failed to get M-Pesa access token');
    }
    
    // Step 2: Initiate STK Push
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode . $passkey . $timestamp);
    $transactionDesc = 'Payment for Gymly Shop Items';
    
    $stkPushData = [
        'BusinessShortCode' => $shortcode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => round($amount),
        'PartyA' => $phoneNumber,
        'PartyB' => $shortcode,
        'PhoneNumber' => $phoneNumber,
        'CallBackURL' => $callbackUrl,
        'AccountReference' => 'Gymly-' . time(),
        'TransactionDesc' => $transactionDesc
    ];
    
    $ch = curl_init($stkPushUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stkPushData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    error_log("M-Pesa STK Push Response: " . $response);
    
    if ($httpCode !== 200) {
        throw new Exception('Failed to initiate M-Pesa payment');
    }
    
    $result = json_decode($response, true);
    
    if (isset($result['ResponseCode']) && $result['ResponseCode'] === '0') {
        // Save order to database with pending status
        $orderId = saveOrder(
            $customerName,
            $customerEmail,
            $phoneNumber,
            $amount,
            $items,
            'pending',
            $result['CheckoutRequestID'] ?? ''
        );
        
        return [
            'success' => true,
            'message' => 'Payment request sent. Please check your phone to complete the payment.',
            'checkoutRequestId' => $result['CheckoutRequestID'] ?? '',
            'orderId' => $orderId
        ];
    } else {
        // Handle specific error codes
        $errorMessage = $result['errorMessage'] ?? $result['CustomerMessage'] ?? 'Failed to initiate payment';
        $responseCode = $result['ResponseCode'] ?? '';
        
        // Map common error codes to user-friendly messages
        if ($responseCode === '1') {
            $errorMessage = 'Insufficient funds in your M-Pesa account. Please top up and try again.';
        } elseif ($responseCode === '2001') {
            $errorMessage = 'Invalid M-Pesa PIN. Please check and try again.';
        } elseif ($responseCode === '1032') {
            $errorMessage = 'Transaction cancelled by user.';
        } elseif ($responseCode === '1037') {
            $errorMessage = 'Transaction timeout. Please try again.';
        } elseif (stripos($errorMessage, 'insufficient') !== false) {
            $errorMessage = 'Insufficient funds in your M-Pesa account. Please top up and try again.';
        }
        
        error_log("M-Pesa payment failed: Code $responseCode - $errorMessage");
        throw new Exception($errorMessage);
    }
}

function processCardPayment($amount, $items, $customerName, $customerEmail) {
    // This is a placeholder for card payment integration
    // In production, integrate with Stripe, Flutterwave, or another payment gateway
    
    // For now, we'll simulate a successful payment
    $orderId = saveOrder(
        $customerName,
        $customerEmail,
        '',
        $amount,
        $items,
        'pending',
        'CARD-' . time()
    );
    
    return [
        'success' => true,
        'message' => 'Card payment processing... (Demo mode)',
        'orderId' => $orderId,
        'note' => 'This is a demo. Integrate with Stripe or Flutterwave for real card payments.'
    ];
}

function saveOrder($customerName, $customerEmail, $phoneNumber, $amount, $items, $status, $transactionRef) {
    $db = new Database();
    $conn = $db->connect();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Create orders table if it doesn't exist
    createOrdersTableIfNotExists($conn);
    
    try {
        $conn->beginTransaction();
        
        // Insert order
        $stmt = $conn->prepare("
            INSERT INTO orders (customer_name, customer_email, phone_number, total_amount, status, transaction_ref, items, created_at)
            VALUES (:customer_name, :customer_email, :phone_number, :total_amount, :status, :transaction_ref, :items, NOW())
            RETURNING id
        ");
        
        $stmt->execute([
            ':customer_name' => $customerName,
            ':customer_email' => $customerEmail,
            ':phone_number' => $phoneNumber,
            ':total_amount' => $amount,
            ':status' => $status,
            ':transaction_ref' => $transactionRef,
            ':items' => json_encode($items)
        ]);
        
        $orderId = $stmt->fetchColumn();
        
        $conn->commit();
        
        return $orderId;
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Failed to save order: " . $e->getMessage());
        throw new Exception('Failed to save order');
    }
}

function createOrdersTableIfNotExists($conn) {
    $sql = "
    CREATE TABLE IF NOT EXISTS orders (
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
    )";
    
    $conn->exec($sql);
}
