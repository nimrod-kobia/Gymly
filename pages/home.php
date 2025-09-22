<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signInPage.php");
    exit();
}

include '../template/layout.php';
?>

<style>
    body, html {
        height: 100%;
        margin: 0;
        font-family: Arial, sans-serif;
    }
    .bg-container {
        /* Full-page background */
        background-image: url('../assets/images/website-is-under-construction-developers-fixing-web-system-and-updating-the-server-website-maintenance-coding-and-programming-software-development-concept-landing-page-ui-web-banner-mobile-app-vector.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
    }
    .bg-container h1 {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    .bg-container p {
        font-size: 1.5rem;
    }
</style>

<div class="bg-container">
    <div>
        <h1>Welcome to Gymly</h1>
        <p>We're currently working on this page. Stay tuned!</p>
    </div>
</div>
