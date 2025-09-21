<?php
session_start();
require_once "SignInHandler.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $handler = new SignInHandler();

    if ($handler->authenticate($email, $password)) {
        // Store user session
        $_SESSION['user_email'] = $email;
        header("Location: home.php"); 
        exit;
    } else {
        $_SESSION['signin_error'] = "Invalid email or password.";
        header("Location: SignInPage.php");
        exit;
    }
} else {
    header("Location: SignInPage.php");
    exit;
}
