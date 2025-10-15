<?php
require_once "../autoload.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["signIn"])) {
    $usernameOrEmail = $_POST["usernameOrEmail"];
    $password = $_POST["password"];

    $signin = new SignInController($usernameOrEmail, $password);

    if ($signin->validateInputs()) {
        if ($signin->authenticateUser()) {
            $user = $signin->getUserData();

            // Check if user is verified
            if (!$user['is_verified']) {
                header("Location: ../pages/signInPage.php?error=Account+not+verified.+Please+check+your+email.");
                exit();
            }

            // Start login session
            SessionManager::startLoginSession($user);

            // 🔥 CORRECTION: Redirect to home.php instead of dashboard.php
            header("Location: ../pages/home.php?success=1");
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
?>
