<?php
require_once "../autoload.php";

// Update activity if user is logged in (prevents timeout)
if (SessionManager::isLoggedIn()) {
    SessionManager::updateActivity();
}

$pageTitle = "Gymly - Your Fitness Journey";
include '../template/layout.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <?php if (SessionManager::isLoggedIn()): ?>
                    <!-- Show welcome message for logged-in users -->
                    <h1 class="display-4 fw-bold mb-4">Welcome back, <?php echo htmlspecialchars(SessionManager::getUsername()); ?>!</h1>
                    <p class="lead mb-4">Continue your fitness journey. Track your progress, join classes, and achieve your goals.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="tracking.php" class="btn btn-light btn-lg">Continue Tracking</a>
                        <a href="classes.php" class="btn btn-outline-light btn-lg">Browse Classes</a>
                    </div>
                <?php else: ?>
                    <!-- Show generic message for guests -->
                    <h1 class="display-4 fw-bold mb-4">Start Your Fitness Journey</h1>
                    <p class="lead mb-4">Track your progress, join classes, and achieve your fitness goals with Gymly's comprehensive platform.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="signUpPage.php" class="btn btn-light btn-lg">Get Started</a>
                        <a href="pricing.php" class="btn btn-outline-light btn-lg">View Plans</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-visual" style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 3rem; backdrop-filter: blur(10px);">
                    <i class="bi bi-graph-up-arrow" style="font-size: 8rem; color: white;"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rest of your home page content remains the same -->
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
            <!-- ... rest of your stats ... -->
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
                    <?php if (SessionManager::isLoggedIn()): ?>
                        <a href="tracking.php" class="btn btn-outline-primary btn-sm">View Progress</a>
                    <?php else: ?>
                        <a href="signUpPage.php" class="btn btn-outline-primary btn-sm">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ... rest of your features ... -->
        </div>
    </div>
</section>

<!-- Recent Activity Section - Only show if logged in -->
<?php if (SessionManager::isLoggedIn()): ?>
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
                        <!-- ... rest of activity ... -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include '../template/footer.php'; ?>