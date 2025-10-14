<?php
require_once "../autoload.php";
use Services\MailService;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["signUp"])) {

    // Sanitize inputs
    $fullname  = trim($_POST["fullname"]);
    $username  = trim($_POST["username"]);
    $email     = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password  = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    // Initialize SignupController
    $signup = new SignupController($fullname, $username, $email, $password, $cpassword);

    // Check for database connection errors immediately
    $errors = $signup->getErrors();
    if (!empty($errors)) {
        $errorString = implode("|", $errors);
        header("Location: ../pages/signUpPage.php?error=" . urlencode($errorString));
        exit();
    }

    // Validate inputs
    if ($signup->validateInputs()) {
        $signup->checkUserExists();
        $errors = $signup->getErrors();

        if (empty($errors)) {
            // Create user and get the inserted user ID
            $userId = $signup->createUser(); 

            if ($userId) {
                // Generate 6-digit verification code
                $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // Save verification code and expiry in the users table
                try {
                    $database = new Database();
                    $pdo = $database->connect();
                    if ($pdo) {
                        $stmt = $pdo->prepare("
                            UPDATE users
                            SET verification_code = :code,
                                code_expiry = :expires_at,
                                is_verified = FALSE
                            WHERE id = :id
                        ");
                        $stmt->execute([
                            ':code'       => $verificationCode,
                            ':expires_at' => $expiresAt,
                            ':id'         => $userId
                        ]);
                    }
                } catch (PDOException $e) {
                    error_log("Failed to save verification code: " . $e->getMessage());
                }

                // Send verification email (with error handling)
                try {
                    $mailer = new MailService();
                    $mailer->sendVerification($email, $username, $verificationCode);
                } catch (Exception $e) {
                    // Log the error but don't stop the signup process
                    error_log("Email sending failed: " . $e->getMessage());
                    // You could set a flag here to show "Email not sent" message
                }

                // Start session immediately after signup
                SessionManager::startSignupSession($userId, $username, $email);


                // Redirect to verification page
                header("Location: ../pages/verify.php");
                exit();
            } else {
                $errorString = "Signup failed. Please try again.";
                header("Location: ../pages/signUpPage.php?error=" . urlencode($errorString));
                exit();
            }

        } else {
            // Input validation errors
            $errorString = implode("|", $errors);
            header("Location: ../pages/signUpPage.php?error=" . urlencode($errorString));
            exit();
        }

    } else {
        // Validation failed
        $errors = $signup->getErrors();
        $errorString = implode("|", $errors);
        header("Location: ../pages/signUpPage.php?error=" . urlencode($errorString));
        exit();
    }

} else {
    header("Location: ../pages/signUpPage.php?error=Invalid+request");
    exit();
}
?>
