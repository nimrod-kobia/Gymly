<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: signInPage.php");
    exit();
}

$pageTitle = "Dashboard - Welcome to Gymly";
include '../template/layout.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to Your Fitness Journey</h1>
                <p class="lead mb-4">Track your progress, join classes, and achieve your fitness goals with Gymly's comprehensive platform.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="tracking.php" class="btn btn-light btn-lg">Start Tracking</a>
                    <a href="pricing.php" class="btn btn-outline-light btn-lg">View Plans</a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-visual" style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 3rem; backdrop-filter: blur(10px);">
                    <i class="bi bi-graph-up-arrow" style="font-size: 8rem; color: white;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3 class="h4 mt-3">5</h3>
                    <p class="text-muted">Workouts This Week</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <h3 class="h4 mt-3">12</h3>
                    <p class="text-muted">Goals Achieved</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <h3 class="h4 mt-3">85%</h3>
                    <p class="text-muted">Consistency Rate</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="text-center">
                    <div class="feature-icon mx-auto">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="h4 mt-3">3</h3>
                    <p class="text-muted">Active Challenges</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Your Fitness Dashboard</h2>
            <p class="text-muted">Everything you need to succeed in one place</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-activity"></i>
                    </div>
                    <h4>Progress Tracking</h4>
                    <p class="text-muted">Monitor your workouts, track your progress, and see real-time analytics of your fitness journey.</p>
                    <a href="tracking.php" class="btn btn-outline-primary btn-sm">View Progress</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <h4>Class Schedule</h4>
                    <p class="text-muted">Book and manage your fitness classes. Never miss a session with smart reminders.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View Schedule</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-nutrition"></i>
                    </div>
                    <h4>Nutrition Plans</h4>
                    <p class="text-muted">Access personalized meal plans and track your nutrition to complement your workouts.</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">View Plans</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Activity Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h3 class="fw-bold mb-4">Recent Activity</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="feature-icon me-3" style="width: 50px; height: 50px;">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Morning Yoga Session</h5>
                                <p class="text-muted mb-0">Completed today at 8:30 AM</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="feature-icon me-3" style="width: 50px; height: 50px;">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Evening Strength Training</h5>
                                <p class="text-muted mb-0">Scheduled for 6:00 PM today</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3" style="width: 50px; height: 50px;">
                                <i class="bi bi-trophy"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Weekly Goal Achieved</h5>
                                <p class="text-muted mb-0">Completed 5 workouts this week</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include '../template/footer.php'; ?>