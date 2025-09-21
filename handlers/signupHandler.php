<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../autoload.php";
use Services\MailService;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signUp"])) {
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    $signup = new signupcontroller($fullname, $username, $email, $password, $cpassword);

    if ($signup->validateInputs()) {
        $signup->checkUserExists();
        $errors = $signup->getErrors();

        if (empty($errors)) {
            if ($signup->createUser()) {
                session_start();
                $verificationCode = rand(100000, 999999);
                $_SESSION['verification_code'] = $verificationCode;
                $_SESSION['user_email'] = $email;

                $mailer = new MailService();
                $mailer->sendVerification($email, $verificationCode);

                header("Location: ../pages/verify.php");
                exit();
            } else {
                $errors = $signup->getErrors();
                $errorString = !empty($errors) ? implode("|", array_values($errors)) : "Registration failed. Please try again.";
                header("Location: ../pages/signUpPage.php?error=" . urlencode($errorString));
                exit();
            }
        } else {
            $errorString = implode("|", array_values($errors));
            header("Location: ../pages/signUpPage.php?error=" . urlencode($errorString));
            exit();
        }
    } else {
        $errors = $signup->getErrors();
        $errorString = !empty($errors) ? implode("|", array_values($errors)) : "Validation failed";
        header("Location: ../pages/signUpPage.php?error=" . urlencode($errorString));
        exit();
    }
} else {
    header("Location: ../pages/signUpPage.php?error=Invalid+request");
    exit();
}
