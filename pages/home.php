<?php
// home.php — Main landing page for Gymly (dark theme with video background)

// Include autoload and session management utilities
require_once "../autoload.php";

// If user is logged in, refresh session activity
if (SessionManager::isLoggedIn()) {
    SessionManager::updateActivity();
}

// Set page title for layout.php
$pageTitle = "Gymly - Your Fitness Journey";
include '../template/layout.php';
?>

<!-- HERO SECTION -->
<section class="hero-section position-relative text-white">
    <!-- Background video (autoplay muted for UX) -->
    <video autoplay muted loop playsinline id="heroVideo" class="hero-video">
        <source src="../assets/videos/gym_video.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Static fallback image for when video fails or ends -->
    <img src="../assets/images/gym image stock.jpeg" alt="Gym Background" id="fallbackImage" class="hero-image d-none">

    <div class="container position-relative z-1 text-start">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <?php if (SessionManager::isLoggedIn()): ?>
                    <!-- Dynamic Welcome Message -->
                    <h1 class="display-4 fw-bold mb-4 text-gradient">
                        Welcome back, <?php echo htmlspecialchars(SessionManager::getUsername()); ?>!
                    </h1>
                    <p class="lead mb-4 text-light">
                        Continue your fitness journey. Track your progress, join classes, and achieve your goals.
                    </p>

                    <!-- Navigation Buttons -->
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="tracking.php" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-graph-up-arrow me-2"></i> Continue Tracking
                        </a>
                        <a href="classes.php" class="btn btn-outline-light btn-lg fw-semibold">
                            <i class="bi bi-calendar-check me-2"></i> Browse Classes
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Default (unauthenticated) view -->
                    <h1 class="display-4 fw-bold mb-4 text-gradient">Start Your Fitness Journey</h1>
                    <p class="lead mb-4 text-light">
                        Track your progress, join classes, and achieve your fitness goals with Gymly’s all-in-one fitness platform.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="signUpPage.php" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-person-plus me-2"></i> Get Started
                        </a>
                        <a href="pricing.php" class="btn btn-outline-light btn-lg fw-semibold">
                            <i class="bi bi-tags me-2"></i> View Plans
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- STATS SECTION -->
<section class="py-5 bg-black text-white border-top border-secondary">
    <div class="container">
        <div class="row g-4 text-center">
            <?php
            /**
             * TODO (Backend group members):
             * Replace static values below with data from `user_stats` table
             * Columns may include: workouts_this_week, total_training_time, goal_completion, achievements_earned
             * Fetch using prepared statements or ORM model
             */
            ?>
            <div class="col-md-3 col-6">
                <i class="bi bi-calendar-check fs-1 text-primary"></i>
                <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($workoutsThisWeek ?? '5'); ?></h3>
                <p class="text-light">Workouts This Week</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bi bi-stopwatch fs-1 text-primary"></i>
                <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($totalTrainingTime ?? '12 hrs'); ?></h3>
                <p class="text-light">Total Training Time</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bi bi-heart-pulse fs-1 text-primary"></i>
                <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($goalCompletion ?? '87%'); ?></h3>
                <p class="text-light">Goal Completion</p>
            </div>
            <div class="col-md-3 col-6">
                <i class="bi bi-trophy fs-1 text-primary"></i>
                <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($achievementsEarned ?? '3'); ?></h3>
                <p class="text-light">Achievements Earned</p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-gradient">Your Fitness Dashboard</h2>
            <p class="text-light">Everything you need to succeed — all in one place</p>
        </div>
        <div class="row g-4">
            <?php
            /**
             * TODO (Backend group members):
             * Dynamically populate these cards using `features` table (id, title, description, icon, link)
             * Use foreach loop to render each feature
             */
            ?>
            <div class="col-md-4">
                <div class="feature-card bg-black p-4 rounded-4 border border-secondary">
                    <i class="bi bi-graph-up text-primary fs-2 mb-3"></i>
                    <h4 class="fw-semibold text-white">Progress Tracking</h4>
                    <p class="text-light">Monitor your workouts and real-time stats.</p>
                    <a href="<?php echo SessionManager::isLoggedIn() ? 'tracking.php' : 'signUpPage.php'; ?>" 
                       class="btn btn-outline-light btn-sm">View Progress</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card bg-black p-4 rounded-4 border border-secondary">
                    <i class="bi bi-people text-primary fs-2 mb-3"></i>
                    <h4 class="fw-semibold text-white">Community</h4>
                    <p class="text-light">Join others and stay motivated together.</p>
                    <a href="community.php" class="btn btn-outline-light btn-sm">Join Now</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card bg-black p-4 rounded-4 border border-secondary">
                    <i class="bi bi-shop text-primary fs-2 mb-3"></i>
                    <h4 class="fw-semibold text-white">Gym Store</h4>
                    <p class="text-light">Get premium fitness gear and supplements.</p>
                    <a href="shop.php" class="btn btn-outline-light btn-sm">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- RECENT ACTIVITY (USER-ONLY)  -->
<?php if (SessionManager::isLoggedIn()): ?>
<section class="py-5 bg-black text-white border-top border-secondary">
    <div class="container">
        <div class="col-lg-8 mx-auto">
            <h3 class="fw-bold mb-4 text-gradient">Recent Activity</h3>

            <?php
            /**
             * TODO (Backend group members):
             * Fetch recent user actions from `activity_logs` table
             * Expected columns: id, user_id, action, timestamp, category
             * Display 3–5 most recent activities
             */
            ?>
            <div class="card bg-dark border-secondary rounded-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-check-circle text-success fs-2 me-3"></i>
                        <div>
                            <h5 class="text-white mb-1">Morning Yoga Session</h5>
                            <p class="text-light mb-0">Completed today at 8:30 AM</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-lightning-charge text-warning fs-2 me-3"></i>
                        <div>
                            <h5 class="text-white mb-1">Cardio Challenge</h5>
                            <p class="text-light mb-0">New challenge unlocked</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include '../template/footer.php'; ?>

<!--  INLINE STYLES (Hero + Text Gradient)  -->
<style>
.hero-video, .hero-image {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
}
.hero-section {
    position: relative;
    overflow: hidden;
}
.text-gradient {
    background: linear-gradient(90deg, #0D6EFD, #10B981);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>

<!--  JS: Switch from Video to Image -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const video = document.getElementById("heroVideo");
    const image = document.getElementById("fallbackImage");

    if (video) {
        video.addEventListener("ended", () => {
            video.classList.add("d-none");
            image.classList.remove("d-none");
        });
    }
});
</script>
