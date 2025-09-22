<?php
session_start();
require_once "../autoload.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify'])) {
    $username = $_POST['username'] ?? '';
    $code = $_POST['code'] ?? '';

    $verify = new VerifyController($username, $code);

    if ($verify->validateInputs() && $verify->checkCode()) {
        if ($verify->markVerified()) {
            header("Location: ../pages/signInPage.php?success=" . urlencode("Account verified successfully! You can now sign in."));
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
?>
