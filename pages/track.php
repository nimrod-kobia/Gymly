<?php
// track.php — Fitness Tracking Page for Gymly (Dark Theme + Future Python Integration)

// Include autoload and session management
require_once "../autoload.php";

/**
 * BACKEND NOTES FOR ENGINEERS (IMPORTANT)
 *
 * - Do NOT keep static numbers in the template. All UI numbers/rows MUST be sourced from:
 *     * The application's database (preferred for user-specific data) OR
 *     * An internal API endpoint that returns JSON (use curl / Guzzle / file_get_contents with proper error handling)
 *
 * - Recommended flow:
 *     1) Authenticate user and get $userId via SessionManager or $_SESSION before rendering the page.
 *     2) Open a single DB connection per request:
 *           $db = (new Database())->connect();
 *     3) Use prepared statements for any user-supplied values:
 *           $stmt = $db->prepare('SELECT ... FROM table WHERE user_id = :uid');
 *           $stmt->execute([':uid' => $userId]);
 *     4) Format timestamps in SQL to include hours:minutes:seconds:
 *           DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at
 *     5) Assign data to descriptive variables that the template uses, e.g.:
 *           $caloriesConsumed = (int)$row['consumed'] ?? 0;
 *           $workoutMinutes   = (int)$row['burned'] ?? 0;
 *           $dailyGoalPercent = (int)($caloriesConsumed ? ($burned / $caloriesConsumed) * 100 : 0);
 *           $mealBreakdown    = $stmtMeals->fetchAll(PDO::FETCH_ASSOC);
 *           $recentActivities = $stmtActivities->fetchAll(PDO::FETCH_ASSOC);
 *     6) Sanitize all output in the view:
 *           echo htmlspecialchars($caloriesConsumed, ENT_QUOTES, 'UTF-8');
 *
 * - Timezone: Ensure consistent timezone handling. Either:
 *       date_default_timezone_set('Africa/Nairobi');
 *   Or convert timestamps in SQL with CONVERT_TZ(...).
 *
 * - Performance:
 *     * Add indexes on columns used in WHERE / ORDER BY (e.g. users.id, activities.user_id, created_at).
 *     * Cache heavy aggregations (Redis / APCu) if traffic is high.
 *
 * - Error handling:
 *     * Provide sensible defaults to the template if DB/API is unavailable.
 *     * Log exceptions server-side (error_log / Monolog) and do not expose DB errors to the client.
 *
 * - Example (pseudo-code, place before include '../template/layout.php' or in a dedicated controller):
 *     if (SessionManager::isLoggedIn()) {
 *         $userId = SessionManager::getUserId();
 *         $db = (new Database())->connect();
 *         $stmt = $db->prepare('SELECT ... FROM ... WHERE user_id = :uid');
 *         $stmt->execute([':uid' => $userId]);
 *         $data = $stmt->fetch(PDO::FETCH_ASSOC);
 *         $caloriesConsumed = (int)$data['calories'] ?? 0;
 *         $workoutMinutes = (int)$data['workout_minutes'] ?? 0;
 *         // ...
 *     }
 *
 * - When returning lists (recentActivities / mealBreakdown), include created_at with seconds for frontend:
 *     DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at
 *
 * - If you prefer an API approach: create endpoints under handlers/ (e.g., handlers/getSummary.php)
 *   that return JSON { consumed, burned, remaining, meals: [...], activities: [...] } and fetch via AJAX.
 */

// Refresh session if logged in
if (SessionManager::isLoggedIn()) {
    SessionManager::updateActivity();
}

// OPTIONAL: Backend may populate these variables before rendering.
// Example placeholders (replace with DB/API values):
// $caloriesConsumed = $caloriesConsumed ?? 1245;
// $workoutMinutes   = $workoutMinutes   ?? 42;
// $dailyGoalPercent = $dailyGoalPercent ?? 78;
// $mealBreakdown    = $mealBreakdown    ?? [];
// $recentActivities = $recentActivities ?? [];


// Set page title for layout
$pageTitle = "Track Progress | Gymly";
include '../template/layout.php';
?>

<!-- INTEGRATED HERO SECTION WITH DUAL COLUMN CAROUSEL -->
<section class="hero-section position-relative text-white overflow-hidden">
    <div class="container position-relative z-1 py-5">
        <div class="row align-items-center">
            <!-- Left Column - Content -->
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3 text-gradient">Science-backed nutrition tracking at your fingertips</h1>
                <p class="lead text-white mb-4">
                    From macros to micros, Gymly gives you personalized insight into your diet, exercise, and health data so you can make more informed decisions about your health.
                </p>
                
                <!-- Progress Stats (if logged in) -->
                <?php if (SessionManager::isLoggedIn()): ?>
                    <div class="mb-4">
                        <div class="card bg-dark bg-opacity-50 border-secondary p-3 rounded-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="fw-bold text-primary mb-1">
                                        <!-- BACKEND: Replace static value with sanitized variable, e.g.
                                             <?php echo htmlspecialchars($caloriesConsumed ?? '1,245', ENT_QUOTES, 'UTF-8'); ?> -->
                                        1,245
                                    </h4>
                                    <small class="text-white">Calories Today</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold text-success mb-1">
                                        <!-- BACKEND: Replace with <?php echo (int)($workoutMinutes ?? 0); ?> -->
                                        42
                                    </h4>
                                    <small class="text-white">Workout Minutes</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold text-info mb-1">
                                        <!-- BACKEND: Replace with computed percent, escaped -->
                                        78%
                                    </h4>
                                    <small class="text-white">Daily Goal</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#tracker-sections" class="btn btn-primary btn-lg fw-semibold me-3">
                        <i class="bi bi-activity me-2"></i> Continue Tracking
                    </a>
                    <a href="#" class="btn btn-outline-light btn-lg fw-semibold">
                        <i class="bi bi-graph-up me-2"></i> View Progress
                    </a>
                <?php else: ?>
                    <a href="signInPage.php" class="btn btn-primary btn-lg fw-semibold me-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Log In
                    </a>
                    <a href="signUpPage.php" class="btn btn-outline-light btn-lg fw-semibold">
                        <i class="bi bi-person-plus me-2"></i> Sign Up
                    </a>
                <?php endif; ?>
            </div>

            <!-- Right Column - Dual Carousel -->
            <div class="col-lg-6 position-relative">
                <div class="dual-carousel-container position-relative" style="height: 500px;">
                    <!-- Left Column Carousel (Moving Down) -->
                    <div class="carousel-column position-absolute" style="left: 0; width: 48%;">
                        <div class="vertical-carousel-down" style="height: 100%; overflow: visible;">
                            <div class="carousel-track-down" style="animation: scrollDown 20s linear infinite;">
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_image.jpeg" class="w-100 rounded-3" alt="Gym Image" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_image.jpeg" alt="Gym Image" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_nutrition.jpeg" class="w-100 rounded-3" alt="Gym Nutrition" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_nutrition.jpeg" alt="Gym Nutrition" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_image.jpeg" class="w-100 rounded-3" alt="Gym Image" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_image.jpeg" alt="Gym Image" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_nutrition.jpeg" class="w-100 rounded-3" alt="Gym Nutrition" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_nutrition.jpeg" alt="Gym Nutrition" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <!-- Duplicate for seamless loop -->
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_image.jpeg" class="w-100 rounded-3" alt="Gym Image" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_image.jpeg" alt="Gym Image" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_nutrition.jpeg" class="w-100 rounded-3" alt="Gym Nutrition" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_nutrition.jpeg" alt="Gym Nutrition" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column Carousel (Moving Up) -->
                    <div class="carousel-column position-absolute" style="right: 0; width: 48%;">
                        <div class="vertical-carousel-up" style="height: 100%; overflow: visible;">
                            <div class="carousel-track-up" style="animation: scrollUp 20s linear infinite;">
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_nutrition.jpeg" class="w-100 rounded-3" alt="Gym Nutrition" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_nutrition.jpeg" alt="Gym Nutrition" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_image.jpeg" class="w-100 rounded-3" alt="Gym Image" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_image.jpeg" alt="Gym Image" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_nutrition.jpeg" class="w-100 rounded-3" alt="Gym Nutrition" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_nutrition.jpeg" alt="Gym Nutrition" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_image.jpeg" class="w-100 rounded-3" alt="Gym Image" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_image.jpeg" alt="Gym Image" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <!-- Duplicate for seamless loop -->
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_nutrition.jpeg" class="w-100 rounded-3" alt="Gym Nutrition" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_nutrition.jpeg" alt="Gym Nutrition" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                                <div class="carousel-slide-vertical mb-3">
-                                    <img src="../assets/images/gym_image.jpeg" class="w-100 rounded-3" alt="Gym Image" style="height: 150px; object-fit: cover;">
+                                    <img src="../assets/images/gym_image.jpeg" alt="Gym Image" style="width:100%; height:auto; object-fit:cover; display:block; border-radius:0;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overlay Gradient -->
                    <div class="position-absolute w-100 h-100" style="background: linear-gradient(90deg, rgba(13,13,13,0.9) 0%, rgba(13,13,13,0.7) 30%, rgba(13,13,13,0) 100%); pointer-events: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Gradient -->
    <div class="position-absolute w-100 h-100" style="top: 0; left: 0; background: radial-gradient(circle at left, #0D0D0D, #000); z-index: -1;"></div>
</section>

<!-- TRACKING SECTIONS -->
<section id="tracker-sections" class="py-5 bg-black text-white border-top border-secondary">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-white mb-3">Develop healthy habits</h2>
            <p class="text-white">Monitor all aspects of your fitness journey in one place</p>
        </div>
        
        <div class="row g-4">
            <!-- Nutrition Tracking Card -->
            <div class="col-md-4">
                <div class="card bg-dark border-secondary p-4 rounded-4 h-100 tracking-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="bi bi-egg-fried text-primary fs-4"></i>
                        </div>
                        <h4 class="fw-semibold text-white mb-0">Nutrition Tracking</h4>
                    </div>
                    <p class="text-white mb-4">
                        Log meals, track macros, and monitor calorie intake with detailed breakdowns.
                    </p>
                </div>
            </div>

            <!-- Workout Tracking Card -->
            <div class="col-md-4">
                <div class="card bg-dark border-secondary p-4 rounded-4 h-100 tracking-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="bi bi-activity text-success fs-4"></i>
                        </div>
                        <h4 class="fw-semibold text-white mb-0">Workout Tracking</h4>
                    </div>
                    <p class="text-white mb-4">
                        Record exercises, track sets and reps, and monitor calories burned during workouts.
                    </p>
                </div>
            </div>

            <!-- Health Metrics Card -->
            <div class="col-md-4">
                <div class="card bg-dark border-secondary p-4 rounded-4 h-100 tracking-card">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="bi bi-body-text text-danger fs-4"></i>
                        </div>
                        <h4 class="fw-semibold text-white mb-0">Health Metrics</h4>
                    </div>
                    <p class="text-white mb-4">
                        Calculate BMI, track weight changes, and monitor other health indicators over time.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DAILY TRACKING PREVIEW -->
<section class="py-5 bg-dark text-white border-top border-secondary">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <h3 class="fw-bold text-white mb-4">Today's Summary</h3>
                
                <!-- Calories Summary -->
                <div class="card bg-black border-secondary mb-4">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="border-end border-secondary">
                                    <h4 class="text-primary fw-bold">1,245</h4>
                                    <small class="text-white">Consumed</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end border-secondary">
                                    <h4 class="text-success fw-bold">420</h4>
                                    <small class="text-white">Burned</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <h4 class="text-info fw-bold">825</h4>
                                <small class="text-white">Remaining</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meal Breakdown -->
                <div class="card bg-black border-secondary">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="text-white mb-0">Meal Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white fw-semibold">Breakfast</span>
                                <span class="text-white">305 kcal</span>
                            </div>
                            <small class="text-white">9g protein, 36g carbs, 7g fat</small>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white fw-semibold">Lunch</span>
                                <span class="text-white">540 kcal</span>
                            </div>
                            <small class="text-white">24g protein, 65g carbs, 18g fat</small>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white fw-semibold">Dinner</span>
                                <span class="text-white">400 kcal</span>
                            </div>
                            <small class="text-white">22g protein, 45g carbs, 12g fat</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <h3 class="fw-bold text-white mb-4">Recent Activities</h3>
                
                <div class="card bg-black border-secondary">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fw-semibold">Running</span>
                                <span class="badge bg-success">60 mins</span>
                            </div>
                            <small class="text-white">Morning jog - 5.2 km</small>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fw-semibold">Weight Training</span>
                                <span class="badge bg-primary">45 mins</span>
                            </div>
                            <small class="text-white">Upper body workout</small>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white fw-semibold">Yoga</span>
                                <span class="badge bg-info">30 mins</span>
                            </div>
                            <small class="text-white">Evening stretching session</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FUTURE INTEGRATION SECTION -->
<section class="py-5 bg-black text-center text-white border-top border-secondary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="fw-bold text-white mb-3">Advanced Analytics Powered by Python</h3>
                <p class="text-white mb-4">
                    We're developing powerful Python-based analytics to provide deeper insights into your fitness journey. 
                    Soon you'll have access to:
                </p>
                
                <div class="row text-start mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-graph-up-arrow text-primary fs-5 me-3 mt-1"></i>
                            <div>
                                <h5 class="text-white">Trend Analysis</h5>
                                <p class="text-white small mb-0">Identify patterns in your workout performance and nutrition habits.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-calculator text-success fs-5 me-3 mt-1"></i>
                            <div>
                                <h5 class="text-white">Calorie Forecasting</h5>
                                <p class="text-white small mb-0">Predict future calorie needs based on your activity levels and goals.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-speedometer2 text-warning fs-5 me-3 mt-1"></i>
                            <div>
                                <h5 class="text-white">Performance Metrics</h5>
                                <p class="text-white small mb-0">Track strength gains, endurance improvements, and recovery rates.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <i class="bi bi-bell text-info fs-5 me-3 mt-1"></i>
                            <div>
                                <h5 class="text-white">Smart Notifications</h5>
                                <p class="text-white small mb-0">Get personalized recommendations based on your data trends.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-dark bg-opacity-50 p-4 rounded-4 border border-secondary">
                    <p class="small text-white mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        We're actively developing these features. Stay tuned for updates as we connect live tracking APIs and implement advanced data visualization tools.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../template/footer.php'; ?>

<!-- INLINE STYLES -->
<style>
.text-gradient {
    background: linear-gradient(90deg, #0D6EFD, #10B981);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-section {
    background: radial-gradient(circle at left, #0D0D0D, #000);
    /* top padding set by JS to match the navbar; keeps banner tight under header */
    padding-top: 0;
    padding-bottom: 80px;
    min-height: 80vh;
    display: flex;
    align-items: center;
}

.tracking-card:hover {
    transform: translateY(-6px);
    transition: 0.3s;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.08);
}

.progress {
    background-color: #2d3748;
}

.card-header {
    background-color: #1a202c !important;
}

/* Dual Carousel Styles */
.dual-carousel-container {
    border-radius: 0; /* make edges flush so images feel free-flowing */
    overflow: visible;
}

.carousel-column {
    height: 100%;
}

.vertical-carousel-down,
.vertical-carousel-up {
    position: relative;
}

.carousel-track-down,
.carousel-track-up {
    display: flex;
    flex-direction: column;
}

.carousel-slide-vertical {
    flex-shrink: 0;
}

/* Ensure images flow natural and not boxed */
.carousel-slide-vertical img {
    display: block;
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 0;
}

/* Animations */
@keyframes scrollDown {
    0% {
        transform: translateY(0);
    }
    100% {
        transform: translateY(-50%);
    }
}

@keyframes scrollUp {
    0% {
        transform: translateY(-50%);
    }
    100% {
        transform: translateY(0);
    }
}

/* Pause on hover */
.dual-carousel-container:hover .carousel-track-down,
.dual-carousel-container:hover .carousel-track-up {
    animation-play-state: paused;
}
</style>

<!-- ensure hero sits directly under navbar -->
<script>
(function () {
    function adjustHeroSpacing() {
        var hero = document.querySelector('.hero-section');
        if (!hero) return;
        var navbar = document.querySelector('#mainNavbar') || document.querySelector('nav.navbar') || document.querySelector('.navbar');
        var navHeight = 0;
        if (navbar) {
            var rect = navbar.getBoundingClientRect();
            navHeight = Math.ceil(rect.height) || 0;
            var navbarStyle = window.getComputedStyle(navbar);
            if (navbarStyle.position === 'fixed' || navbarStyle.position === 'sticky') {
                var currentBodyPadding = parseInt(window.getComputedStyle(document.body).paddingTop, 10) || 0;
                if (currentBodyPadding < navHeight) {
                    document.body.style.paddingTop = navHeight + 'px';
                }
            }
        } else {
            navHeight = 56;
        }
        hero.style.paddingTop = (navHeight + 8) + 'px';
        hero.style.minHeight = 'calc(100vh - ' + navHeight + 'px)';
    }
    document.addEventListener('DOMContentLoaded', adjustHeroSpacing);
    window.addEventListener('resize', function () {
        clearTimeout(window._adjustHeroSpacingTimer);
        window._adjustHeroSpacingTimer = setTimeout(adjustHeroSpacing, 120);
    });
    setTimeout(adjustHeroSpacing, 400);
})();
</script>