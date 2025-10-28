<?php
require_once '../classes/SessionManager.php';
SessionManager::startSession();

// Redirect to sign-in if user not logged in
if (!SessionManager::isLoggedIn()) {
    header("Location: ../handlers/signInHandler.php");
    exit();
}

include '../template/layout.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Gymly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .hero-section {
            background: linear-gradient(to right, #007bff, #6610f2);
            color: white;
            padding: 5rem 2rem;
            text-align: center;
        }
        .section-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .feature-icon {
            font-size: 2rem;
            color: #007bff;
        }
        .team-member img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        footer {
            margin-top: 3rem;
            background: #212529;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>About Gymly</h1>
            <p class="lead">Empowering fitness through technology and community.</p>
        </div>
    </section>

    <!-- Our Story -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Our Story</h2>
            <p class="text-center w-75 mx-auto">Gymly was born from a passion to make fitness management simpler, smarter, and more engaging. We’re a team of developers and fitness enthusiasts who believe that technology can enhance how gyms operate and how members achieve their fitness goals.</p>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Why Choose Gymly?</h2>
            <div class="row text-center mt-4">
                <div class="col-md-4">
                    <i class="bi bi-graph-up feature-icon"></i>
                    <h5 class="mt-3">Smart Analytics</h5>
                    <p>Track gym performance and member engagement effortlessly.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-people feature-icon"></i>
                    <h5 class="mt-3">Community Building</h5>
                    <p>Connect members and trainers through a seamless online ecosystem.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-laptop feature-icon"></i>
                    <h5 class="mt-3">Modern Tech Stack</h5>
                    <p>Built with reliability, scalability, and a smooth user experience in mind.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="section-title">Meet the Team</h2>
            <div class="row justify-content-center mt-4">
                <div class="col-md-3 team-member">
                    <img src="../assets/images/team1.jpg" alt="Team Member 1">
                    <h5 class="mt-3">Alex M.</h5>
                    <p>Lead Developer</p>
                </div>
                <div class="col-md-3 team-member">
                    <img src="../assets/images/team2.jpg" alt="Team Member 2">
                    <h5 class="mt-3">Sophie K.</h5>
                    <p>UI/UX Designer</p>
                </div>
                <div class="col-md-3 team-member">
                    <img src="../assets/images/team3.jpg" alt="Team Member 3">
                    <h5 class="mt-3">Daniel R.</h5>
                    <p>Fitness Consultant</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title">Our Technology</h2>
            <p>Gymly is powered by PHP, MySQL, and Bootstrap — optimized for reliability and performance.</p>
        </div>
    </section>

    <!-- Call To Action -->
    <section class="py-5 text-center">
        <div class="container">
            <h2 class="section-title">Join the Gymly Community</h2>
            <p>Whether you're managing a gym or achieving your fitness goals — Gymly is here to help you every step of the way.</p>
            <a href="signUpPage.php" class="btn btn-primary btn-lg mt-3">Get Started</a>
        </div>
    </section>

    <?php include '../template/footer.php'; ?>

</body>
</html>
