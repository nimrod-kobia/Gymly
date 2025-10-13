<?php
// layout.php — Clean dark theme with invisible transparent navbar
session_start();
require_once "../classes/SessionManager.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Gymly - Your Fitness Journey'; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logo_white.png">

    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Global Dark Theme -->
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
</head>

<body class="bg-black text-light">
    <!-- Transparent Navbar (minimalist) -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-transparent border-0 shadow-none py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../pages/home.php">
                <img src="../assets/images/logo.png" alt="Gymly Logo" width="40" height="40" class="d-inline-block align-text-top">
                <span class="fw-bold fs-4">Gymly</span>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list text-white fs-1"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-lg-4">
                    <li class="nav-item"><a class="nav-link text-white" href="../pages/home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="../pages/tracking.php">Tracking</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="../pages/shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="../pages/about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="../pages/contact.php">Contact</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2 ms-lg-4">
                    <?php if (SessionManager::isLoggedIn()): ?>
                        <span class="text-white small me-2">
                            Hi, <?php echo htmlspecialchars(SessionManager::getUsername()); ?>
                        </span>
                        <a href="../pages/profile.php" class="btn btn-outline-light btn-sm px-3">
                            <i class="bi bi-person"></i> Profile
                        </a>
                        <a href="../handlers/logoutHandler.php" class="btn btn-light btn-sm px-3">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="../pages/signInPage.php" class="btn btn-outline-light btn-sm px-3">Sign In</a>
                        <a href="../pages/signUpPage.php" class="btn btn-primary btn-sm px-3">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Page Content -->
    <main class="main-content pt-5">
        <!-- Content injected here -->
