<?php
session_start();
require_once "../autoload.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify'])) {
    $username = $_POST['username'] ?? '';
    $code = $_POST['code'] ?? '';

    $verify = new VerifyController($username, $code);

    if ($verify->validateInputs() && $verify->checkCode()) {
        if ($verify->markVerified()) {
            // Redirect to home page after successful verification
            $_SESSION['username'] = $username; // optional, for display on home page
            header("Location: ../pages/home.php");
            exit();
        } else {
            $errors = $verify->getErrors();
            header("Location: ../pages/verify.php?error=" . urlencode(implode("|", $errors)));
            exit();
        }
    } else {
        $errors = $verify->getErrors();
        header("Location: ../pages/verify.php?error=" . urlencode(implode("|", $errors)));
        exit();
    }
} else {
    header("Location: ../pages/verify.php?error=Invalid+request");
    exit();
}
