<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method'
        ]);
        exit;
    }

    // Get and sanitize form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
        exit;
    }

    // Validate name length
    if (strlen($name) > 100) {
        echo json_encode([
            'success' => false,
            'message' => 'Name is too long (max 100 characters)'
        ]);
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address'
        ]);
        exit;
    }

    // Validate subject length
    if (strlen($subject) > 200) {
        echo json_encode([
            'success' => false,
            'message' => 'Subject is too long (max 200 characters)'
        ]);
        exit;
    }

    // Validate message length
    if (strlen($message) < 10) {
        echo json_encode([
            'success' => false,
            'message' => 'Message is too short (minimum 10 characters)'
        ]);
        exit;
    }

    if (strlen($message) > 5000) {
        echo json_encode([
            'success' => false,
            'message' => 'Message is too long (max 5000 characters)'
        ]);
        exit;
    }

    // Connect to database
    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed'
        ]);
        exit;
    }

    // Insert contact message
    $stmt = $db->prepare("
        INSERT INTO contacts (name, email, subject, message, status, created_at, updated_at)
        VALUES (:name, :email, :subject, :message, 'unread', :timestamp, :timestamp)
    ");

    $timestamp = date('Y-m-d H:i:s');
    
    $result = $stmt->execute([
        ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        ':email' => $email,
        ':subject' => htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'),
        ':message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
        ':timestamp' => $timestamp
    ]);

    if ($result) {
        // Optionally send email notification to admin
        // You can add email functionality here using PHPMailer if needed
        
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for contacting us! We\'ll get back to you within 24-48 hours.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send message. Please try again.'
        ]);
    }

} catch (PDOException $e) {
    error_log("contactHandler.php PDOException: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("contactHandler.php Exception: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}
?>
