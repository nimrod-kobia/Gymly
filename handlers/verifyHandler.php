<?php
require_once "../autoload.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    session_start();
    $inputCode = $_POST['code'] ?? '';

    $verify = new VerifyController($inputCode);

    if ($verify->validateCode() && $verify->checkCode()) {
        $_SESSION['verified'] = true;
        header("Location: ../pages/dashboard.php?verified=1");
        exit();
    } else {
        $errors = $verify->getErrors();
        $errorString = implode("|", $errors);
        header("Location: ../pages/verifyPage.php?error=" . urlencode($errorString));
        exit();
    }
} else {
    header("Location: ../pages/verifyPage.php?error=Invalid+request");
    exit();
}
