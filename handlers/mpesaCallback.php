<?php
require_once "../conf/conf.php";
require_once "../classes/database.php";

// Log the callback for debugging
$callbackData = file_get_contents('php://input');
error_log("M-Pesa Callback Received: " . $callbackData);

// Parse the callback data
$callback = json_decode($callbackData, true);

if (!$callback) {
    error_log("Invalid M-Pesa callback data");
    http_response_code(400);
    exit;
}

// Extract relevant information
$resultCode = $callback['Body']['stkCallback']['ResultCode'] ?? null;
$resultDesc = $callback['Body']['stkCallback']['ResultDesc'] ?? '';
$checkoutRequestId = $callback['Body']['stkCallback']['CheckoutRequestID'] ?? '';

if ($resultCode === null) {
    error_log("Missing result code in M-Pesa callback");
    http_response_code(400);
    exit;
}

try {
    $db = new Database();
    $conn = $db->connect();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    if ($resultCode === 0) {
        // Payment successful
        $callbackMetadata = $callback['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
        
        $mpesaReceiptNumber = '';
        $amount = 0;
        $phoneNumber = '';
        
        foreach ($callbackMetadata as $item) {
            if ($item['Name'] === 'MpesaReceiptNumber') {
                $mpesaReceiptNumber = $item['Value'];
            } elseif ($item['Name'] === 'Amount') {
                $amount = $item['Value'];
            } elseif ($item['Name'] === 'PhoneNumber') {
                $phoneNumber = $item['Value'];
            }
        }
        
        // Update order status
        $stmt = $conn->prepare("
            UPDATE orders 
            SET status = 'completed', 
                mpesa_receipt = :mpesa_receipt,
                updated_at = NOW()
            WHERE transaction_ref = :transaction_ref
        ");
        
        $stmt->execute([
            ':mpesa_receipt' => $mpesaReceiptNumber,
            ':transaction_ref' => $checkoutRequestId
        ]);
        
        error_log("Order updated successfully. Receipt: $mpesaReceiptNumber");
        
    } else {
        // Payment failed - determine the reason
        $failureReason = $resultDesc;
        
        // Map common result codes to user-friendly messages
        if ($resultCode === 1) {
            $failureReason = 'Insufficient funds';
        } elseif ($resultCode === 2001) {
            $failureReason = 'Invalid PIN';
        } elseif ($resultCode === 1032) {
            $failureReason = 'Cancelled by user';
        } elseif ($resultCode === 1037) {
            $failureReason = 'Transaction timeout';
        } elseif ($resultCode === 17) {
            $failureReason = 'User cancelled';
        }
        
        // Update order status with failure reason
        $stmt = $conn->prepare("
            UPDATE orders 
            SET status = 'failed',
                transaction_ref = CONCAT(transaction_ref, ' - ', :failure_reason),
                updated_at = NOW()
            WHERE transaction_ref = :transaction_ref
        ");
        
        $stmt->execute([
            ':failure_reason' => $failureReason,
            ':transaction_ref' => $checkoutRequestId
        ]);
        
        error_log("Payment failed. Code: $resultCode, Reason: $failureReason");
    }
    
    // Send success response to Safaricom
    http_response_code(200);
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    
} catch (Exception $e) {
    error_log("M-Pesa callback processing error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Internal server error']);
}
