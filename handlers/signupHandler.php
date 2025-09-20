<?php
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["signUp"])){
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    include_once "../classes/SignupController.php";
    $signup = new signUpController($fullname, $username, $email, $password, $cpassword);
    
    //validate inputs
    if($signup->validateInputs()){
        header("Location: ../pages/signUpPage.php?success=1");
        exit();
    }else{
        //validation failed - FIXED THIS PART
        $errors = $signup->getErrors();
        $errorString = "";
        
        if(!empty($errors)){
            // Extract just the error messages from the array
            $errorMessages = array_values($errors);
            $errorString = implode("|", $errorMessages);
        }
        
        header("Location: ../pages/signUpPage.php?error=". urlencode($errorString));
        exit();
    }
}