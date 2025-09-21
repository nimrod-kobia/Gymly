<?php
require_once "../autoload.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["signIn"])) {
    $usernameOrEmail = $_POST["usernameOrEmail"];
    $password = $_POST["password"];

    $signin = new SignInController($usernameOrEmail, $password);

    if ($signin->validateInputs()) {
        if ($signin->authenticateUser()) {
            $user = $signin->getUserData();

            session_start();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];

            header("Location: ../pages/dashboard.php?success=1");
            exit();
        } else {
            $errors = $signin->getErrors();
            $errorString = implode("|", $errors);
            header("Location: ../pages/signInPage.php?error=" . urlencode($errorString));
            exit();
        }
    } else {
        $errors = $signin->getErrors();
        $errorString = implode("|", $errors);
        header("Location: ../pages/signInPage.php?error=" . urlencode($errorString));
        exit();
    }
} else {
    header("Location: ../pages/signInPage.php?error=Invalid+request");
    exit();
}
