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
                $database = new Database();
                $pdo = $database->connect();
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

                // Send verification email
                $mailer = new MailService();
                $mailer->sendVerification($email, $username, $verificationCode);

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
