<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $fullname = $_POST["fullname"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];

    include_once "../classes/SignupController.php";
    $signup = new signUpController($fullname, $username, $email, $password, $cpassword);
}