<?php
// layout.php — used as the main layout template for all Gymly pages

session_start();
require_once "../classes/SessionManager.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gymly - <?php echo $pageTitle ?? 'Fitness Management'; ?></title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563EB; --primary-dark: #1D4ED8; --primary-light: #EBF4FF;
            --accent: #10B981; --text-dark: #1E2A38; --text-light: #64748B;
            --border: #E2E8F0; --white: #FFFFFF; --gray-50: #F9FAFB;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-50);
            color: var(--text-dark);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content { flex: 1; }

        /* Navbar */
        .navbar {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 1rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }
        .navbar-nav .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .navbar-nav .nav-link:hover {
            background: var(--primary-light);
            color: var(--primary) !important;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            transition: all 0.2s ease;
        }
        .btn-primary { background: var(--primary); border: none; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-outline-primary { border-color: var(--primary); color: var(--primary); }
        .btn-outline-primary:hover { background: var(--primary); color: var(--white); transform: translateY(-1px); }
        .btn-logout { background: #DC2626; border: none; color: var(--white); }
        .btn-logout:hover { background: #B91C1C; transform: translateY(-1px); }

        /* Auth Pages */
        .auth-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .auth-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
        }
        .logo-container { text-align: center; margin-bottom: 2rem; }
        .logo { max-height: 60px; margin-bottom: 1rem; }
        .auth-title { font-size: 1.875rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; }
        .auth-subtitle { color: var(--text-light); margin-bottom: 2rem; }

        /* Forms */
        .form-label { font-weight: 500; color: var(--text-dark); margin-bottom: 0.5rem; }
        .form-control {
            padding: 0.875rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Footer */
        .footer {
            background: var(--text-dark);
            color: var(--white);
            padding: 3rem 0 1rem;
            margin-top: auto;
        }
        .footer h5 { color: var(--white); font-weight: 600; margin-bottom: 1rem; }
        .footer p, .footer li { color: #CBD5E1; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 0.5rem; }
        .footer-links a { color: #CBD5E1; text-decoration: none; transition: color 0.2s ease; }
        .footer-links a:hover { color: var(--white); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }

        /* Alerts */
        .alert { border-radius: 8px; margin-bottom: 1.5rem; }
        .alert-danger { background: #FEF2F2; border-color: #FECACA; color: #B91C1C; }
        .alert-success { background: #F0FDF4; border-color: #BBF7D0; color: #15803D; }

        /* Homepage */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 6rem 0;
            border-radius: 0 0 24px 24px;
        }
        .feature-card {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
            height: 100%;
        }
        .feature-card:hover { transform: translateY(-4px); }
        .feature-icon {
            background: var(--primary-light);
            color: var(--primary);
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="../pages/home.php">
                <i class="bi bi-activity"></i> Gymly
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="../pages/home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pages/tracking.php">Tracking</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pages/shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pages/about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="../pages/contact.php">Contact</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <?php if (SessionManager::isLoggedIn()): ?>
                        <span class="me-2 text-secondary">
                            Hi, <?php echo htmlspecialchars(SessionManager::getUsername()); ?>
                        </span>
                        <a href="../pages/profile.php" class="btn btn-outline-primary">
                            <i class="bi bi-person-circle"></i> Profile
                        </a>
                        <a href="../handlers/logoutHandler.php" class="btn btn-logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="../pages/signInPage.php" class="btn btn-outline-primary">Sign In</a>
                        <a href="../pages/signUpPage.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
