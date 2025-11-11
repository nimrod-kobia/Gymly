<?php
require_once "../conf/conf.php";
require_once "../autoload.php";

use Services\MailService;

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
        // Send email notification to admin
        try {
            $mailService = new MailService();
            
            // Email to admin
            $adminEmail = 'info@gymly.com'; // Change this to your admin email
            $adminSubject = "New Contact Form Submission: $subject";
            $adminMessage = "
                <h2>New Contact Form Submission</h2>
                <p><strong>From:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong></p>
                <p>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</p>
                <p><strong>Received:</strong> $timestamp</p>
                <hr>
                <p><em>Reply to this message by responding to: $email</em></p>
            ";
            
            $mailService->sendMail($adminEmail, $adminSubject, $adminMessage);
            
            // Optional: Send confirmation email to user
            $userSubject = "We received your message - Gymly Support";
            $userMessage = "
                <h2>Thank you for contacting Gymly!</h2>
                <p>Hi $name,</p>
                <p>We've received your message and will get back to you within 24-48 hours.</p>
                <p><strong>Your message:</strong></p>
                <p>" . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . "</p>
                <hr>
                <p>Best regards,<br>The Gymly Team</p>
                <p style='color: #666; font-size: 12px;'>This is an automated confirmation. Please do not reply to this email.</p>
            ";
            
            $mailService->sendMail($email, $userSubject, $userMessage);
            
        } catch (Exception $emailError) {
            // Log email error but don't fail the contact form submission
            error_log("Email notification failed: " . $emailError->getMessage());
        }
        
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
