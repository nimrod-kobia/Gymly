<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../autoload.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signIn"])) {
    $usernameOrEmail = $_POST["usernameOrEmail"];
    $password = $_POST["password"];

    $signin = new SignInController($usernameOrEmail, $password);

    if ($signin->validateInputs()) {
        if ($signin->authenticateUser()) {
            // success → start session & redirect
            session_start();
            $_SESSION['user'] = $signin->getUserData();
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            $errors = $signin->getErrors();
            $errorString = !empty($errors) ? implode("|", array_values($errors)) : "Login failed. Please try again.";
            header("Location: ../pages/signInPage.php?error=" . urlencode($errorString));
            exit();
        }
    } else {
        $errors = $signin->getErrors();
        $errorString = !empty($errors) ? implode("|", array_values($errors)) : "Validation failed.";
        header("Location: ../pages/signInPage.php?error=" . urlencode($errorString));
        exit();
    }
} else {
    header("Location: ../pages/signInPage.php?error=Invalid+request");
    exit();
}
