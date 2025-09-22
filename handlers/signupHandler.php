<?php
session_start();
require_once "../autoload.php";
use Services\MailService;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["signUp"])) {
    $fullname = trim($_POST["fullname"]);
    $username = trim($_POST["username"]);
    $email    = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    $signup = new SignupController($fullname, $username, $email, $password, $cpassword);

    if ($signup->validateInputs()) {
        $signup->checkUserExists();
        $errors = $signup->getErrors();

        if (empty($errors)) {
            $userId = $signup->createUser(); // must return new user ID
            if ($userId) {
                // Generate 6-digit verification code
                $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // Save code & expiry directly in users table
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
                    ':code' => $verificationCode,
                    ':expires_at' => $expiresAt,
                    ':id' => $userId
                ]);

                // Send verification email
                $mailer = new MailService();
                $mailer->sendVerification($email, $username, $verificationCode);

                $_SESSION['user_id'] = $userId;
                header("Location: ../pages/verify.php");
                exit();
            }
        } else {
            header("Location: ../pages/signUpPage.php?error=" . urlencode(implode("|", $errors)));
            exit();
        }
    } else {
        $errors = $signup->getErrors();
        header("Location: ../pages/signUpPage.php?error=" . urlencode(implode("|", $errors)));
        exit();
    }
} else {
    header("Location: ../pages/signUpPage.php?error=Invalid+request");
    exit();
}
?>
