<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include autoloader to handle class loading
// This will automatically load the SignupController class and any other classes needed
require_once "../autoload.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signUp"])) {
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    // Create signup controller (autoloader will handle the class loading)
    $signup = new signupcontroller($fullname, $username, $email, $password, $cpassword);
    
    // Validate inputs
    if ($signup->validateInputs()) {
        // Check if user already exists
        $signup->checkUserExists();
        $errors = $signup->getErrors();
        
        if (empty($errors)) {
            if ($signup->createUser()) {
                header("Location: ../pages/signUpPage.php?success=1");
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
?>
