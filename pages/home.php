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
    <video autoplay muted loop playsinline id="heroVideo" class="hero-video" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
        <source src="../assets/videos/gym_video.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Static fallback image for when video fails or ends -->
    <img src="../assets/images/gym_image.jpeg" alt="Gym Background" id="fallbackImage" class="hero-image d-none" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">

    <div class="container position-relative z-1 text-start">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <?php if (SessionManager::isLoggedIn()): ?>
                    <!-- Dynamic Welcome Message -->
                    <h1 class="display-4 fw-bold mb-4 text-gradient">
                        Welcome back, <?php echo htmlspecialchars(SessionManager::getUsername()); ?>!
                    </h1>
                    <p class="lead mb-4 text-light">
                        Continue your fitness journey. Track your progress and achieve your goals.
                    </p>

                    <!-- Navigation Buttons -->
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="track.php" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-graph-up-arrow me-2"></i> Continue Tracking
                        </a>
                        <a href="shop.php" class="btn btn-outline-light btn-lg fw-semibold">
                            <i class="bi bi-cart3 me-2" aria-hidden="true"></i> Shop now
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Default (unauthenticated) view -->
                    <h1 class="display-4 fw-bold mb-4 text-gradient">Start Your Fitness Journey</h1>
                    <p class="lead mb-4 text-light">
                        Track your progress and achieve your fitness goals with Gymly’s all-in-one fitness platform.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="signUpPage.php" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-person-plus me-2"></i> Get Started
                        </a>
                        <a href="plans.php" class="btn btn-outline-light btn-lg fw-semibold">
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
            // Fetch real user stats
            $workoutsThisWeek = 0;
            $totalTrainingTime = '0 hrs';
            $goalCompletion = '0%';
            $achievementsEarned = 0;
            $mealsLoggedToday = 0;
            $caloriesConsumed = 0;
            
            if (SessionManager::isLoggedIn()) {
                $userId = SessionManager::getUserId();
                $db = (new Database())->connect();
                
                // 1. Workouts This Week (from workout_sessions)
                try {
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as count
                        FROM workout_sessions
                        WHERE user_id = ?
                        AND started_at >= DATE_TRUNC('week', CURRENT_DATE)
                    ");
                    $stmt->execute([$userId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $workoutsThisWeek = $result['count'] ?? 0;
                } catch (PDOException $e) {
                    // Table might not exist
                }
                
                // 2. Total Training Time (sum of completed workouts this month)
                try {
                    $stmt = $db->prepare("
                        SELECT SUM(EXTRACT(EPOCH FROM (completed_at - started_at))/3600) as total_hours
                        FROM workout_sessions
                        WHERE user_id = ?
                        AND completed_at IS NOT NULL
                        AND started_at >= DATE_TRUNC('month', CURRENT_DATE)
                    ");
                    $stmt->execute([$userId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $hours = round($result['total_hours'] ?? 0, 1);
                    $totalTrainingTime = $hours > 0 ? $hours . ' hrs' : '0 hrs';
                } catch (PDOException $e) {
                    // Table might not exist
                }
                
                // 3. Nutrition Goal Completion (based on calorie target)
                try {
                    $stmt = $db->prepare("
                        SELECT calories_consumed
                        FROM user_daily_summary
                        WHERE user_id = ?
                        AND summary_date = CURRENT_DATE
                    ");
                    $stmt->execute([$userId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $caloriesConsumed = $result['calories_consumed'] ?? 0;
                    
                    // Assume daily goal of 2000 calories (can be made dynamic later)
                    $calorieGoal = 2000;
                    $completion = $calorieGoal > 0 ? min(100, round(($caloriesConsumed / $calorieGoal) * 100)) : 0;
                    $goalCompletion = $completion . '%';
                } catch (PDOException $e) {
                    // Table might not exist
                }
                
                // 4. Achievements Earned (from user_achievements)
                try {
                    $stmt = $db->prepare("
                        SELECT COUNT(*) as count
                        FROM user_achievements
                        WHERE user_id = ?
                    ");
                    $stmt->execute([$userId]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $achievementsEarned = $result['count'] ?? 0;
                } catch (PDOException $e) {
                    // Table might not exist
                }
                
                // Fallback: If no workouts/achievements tracked, show meals logged today
                if ($workoutsThisWeek == 0 && $achievementsEarned == 0) {
                    try {
                        $stmt = $db->prepare("
                            SELECT COUNT(*) as count
                            FROM user_meals
                            WHERE user_id = ?
                            AND DATE(logged_at) = CURRENT_DATE
                        ");
                        $stmt->execute([$userId]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $mealsLoggedToday = $result['count'] ?? 0;
                    } catch (PDOException $e) {
                        // Table might not exist
                    }
                }
            }
            ?>
            
            <?php if (SessionManager::isLoggedIn()): ?>
                <div class="col-md-3 col-6">
                    <i class="bi bi-calendar-check fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($workoutsThisWeek); ?></h3>
                    <p class="text-light">Workouts This Week</p>
                </div>
                <div class="col-md-3 col-6">
                    <i class="bi bi-stopwatch fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($totalTrainingTime); ?></h3>
                    <p class="text-light">Training This Month</p>
                </div>
                <div class="col-md-3 col-6">
                    <i class="bi bi-heart-pulse fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($goalCompletion); ?></h3>
                    <p class="text-light">Daily Calorie Goal</p>
                </div>
                <div class="col-md-3 col-6">
                    <i class="bi bi-trophy fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold"><?= htmlspecialchars($achievementsEarned); ?></h3>
                    <p class="text-light">Achievements Earned</p>
                </div>
            <?php else: ?>
                <!-- Show generic stats for non-logged in users -->
                <div class="col-md-3 col-6">
                    <i class="bi bi-people fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold">1000+</h3>
                    <p class="text-light">Active Members</p>
                </div>
                <div class="col-md-3 col-6">
                    <i class="bi bi-lightning-charge fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold">50K+</h3>
                    <p class="text-light">Workouts Completed</p>
                </div>
                <div class="col-md-3 col-6">
                    <i class="bi bi-egg-fried fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold">100K+</h3>
                    <p class="text-light">Meals Logged</p>
                </div>
                <div class="col-md-3 col-6">
                    <i class="bi bi-trophy fs-1 text-primary"></i>
                    <h3 class="h4 mt-3 fw-bold">500+</h3>
                    <p class="text-light">Achievements Unlocked</p>
                </div>
            <?php endif; ?>
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
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card bg-black p-4 rounded-4 border border-secondary h-100">
                    <i class="bi bi-egg-fried text-primary fs-2 mb-3"></i>
                    <h4 class="fw-semibold text-white">Nutrition Tracking</h4>
                    <p class="text-light">Track your meals and monitor your daily calories with local Kenyan foods.</p>
                    <a href="<?php echo SessionManager::isLoggedIn() ? 'nutrition.php' : 'signUpPage.php'; ?>" 
                       class="btn btn-outline-light btn-sm">Track Nutrition</a>
                </div>
            </div>
            <?php if (SessionManager::isAdmin()): ?>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card bg-black p-4 rounded-4 border border-secondary h-100">
                    <i class="bi bi-people text-primary fs-2 mb-3"></i>
                    <h4 class="fw-semibold text-white">User Management</h4>
                    <p class="text-light">Manage all users and monitor platform activity.</p>
                    <a href="users.php" class="btn btn-outline-warning btn-sm">Manage Users</a>
                </div>
            </div>
            <?php else: ?>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card bg-black p-4 rounded-4 border border-secondary h-100">
                    <i class="bi bi-calendar-week text-primary fs-2 mb-3"></i>
                    <h4 class="fw-semibold text-white">Workout Splits</h4>
                    <p class="text-light">Create custom workout routines and track your progress.</p>
                    <a href="<?php echo SessionManager::isLoggedIn() ? 'workoutSplits.php' : 'signUpPage.php'; ?>" 
                       class="btn btn-outline-light btn-sm">View Splits</a>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card bg-black p-4 rounded-4 border border-secondary h-100">
                    <i class="bi bi-shop text-primary fs-2 mb-3"></i>
                    <h4 class="fw-semibold text-white">Gym Store</h4>
                    <p class="text-light">Get premium fitness gear and supplements.</p>
                    <a href="shop.php" class="btn btn-outline-light btn-sm" aria-label="Shop now">
                        Shop Now <span class="ms-2" aria-hidden="true"></span>
                    </a>
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
            // Fetch recent nutrition logs
            $userId = SessionManager::getUserId();
            $db = (new Database())->connect();
            
            // Get recent meals (last 5)
            $mealsStmt = $db->prepare("
                SELECT food_name, calories, protein_g, carbs_g, fat_g, meal_type, logged_at
                FROM user_meals
                WHERE user_id = ?
                ORDER BY logged_at DESC
                LIMIT 5
            ");
            $mealsStmt->execute([$userId]);
            $recentMeals = $mealsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get recent workout sessions (if table exists)
            $recentWorkouts = [];
            try {
                $workoutsStmt = $db->prepare("
                    SELECT ws.id, ws.started_at, ws.completed_at, sd.name as workout_name
                    FROM workout_sessions ws
                    JOIN split_days sd ON ws.split_day_id = sd.id
                    WHERE ws.user_id = ?
                    ORDER BY ws.started_at DESC
                    LIMIT 3
                ");
                $workoutsStmt->execute([$userId]);
                $recentWorkouts = $workoutsStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Table might not exist yet
            }
            
            $hasActivity = !empty($recentMeals) || !empty($recentWorkouts);
            ?>

            <?php if ($hasActivity): ?>
                <div class="card bg-dark border-secondary rounded-4 shadow-sm">
                    <div class="card-body">
                        <?php foreach ($recentWorkouts as $workout): ?>
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-secondary">
                                <i class="bi bi-lightning-charge text-warning fs-2 me-3"></i>
                                <div class="flex-grow-1">
                                    <h5 class="text-white mb-1">
                                        <?php echo htmlspecialchars($workout['workout_name']); ?>
                                    </h5>
                                    <p class="text-light mb-0">
                                        <small>
                                            <?php 
                                            $time = new DateTime($workout['started_at']);
                                            echo $time->format('M j') . ' at ' . $time->format('g:i A');
                                            if ($workout['completed_at']) {
                                                echo ' <span class="badge bg-success">Completed</span>';
                                            }
                                            ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php foreach ($recentMeals as $index => $meal): ?>
                            <div class="d-flex align-items-center <?php echo $index < count($recentMeals) - 1 ? 'mb-3 pb-3 border-bottom border-secondary' : ''; ?>">
                                <i class="bi bi-egg-fried text-success fs-2 me-3"></i>
                                <div class="flex-grow-1">
                                    <h5 class="text-white mb-1">
                                        <?php echo htmlspecialchars($meal['food_name']); ?>
                                        <span class="badge bg-primary ms-2"><?php echo ucfirst($meal['meal_type']); ?></span>
                                    </h5>
                                    <p class="text-light mb-0">
                                        <small>
                                            <?php 
                                            $time = new DateTime($meal['logged_at']);
                                            echo $time->format('M j') . ' at ' . $time->format('g:i A');
                                            ?>
                                            • <?php echo $meal['calories']; ?> cal
                                            • P: <?php echo round($meal['protein_g'], 1); ?>g
                                            • C: <?php echo round($meal['carbs_g'], 1); ?>g
                                            • F: <?php echo round($meal['fat_g'], 1); ?>g
                                        </small>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="nutrition.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-egg-fried me-2"></i>Track Food
                    </a>
                    <a href="track.php" class="btn btn-outline-primary">
                        <i class="bi bi-lightning-charge me-2"></i>Track Workout
                    </a>
                </div>
            <?php else: ?>
                <div class="card bg-dark border-secondary rounded-4 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-activity text-muted fs-1 mb-3 d-block"></i>
                        <h5 class="text-white mb-2">No Recent Activity</h5>
                        <p class="text-light mb-4">Start tracking your nutrition and workouts to see your activity here!</p>
                        <div>
                            <a href="nutrition.php" class="btn btn-primary me-2">
                                <i class="bi bi-egg-fried me-2"></i>Track Food
                            </a>
                            <a href="track.php" class="btn btn-outline-primary">
                                <i class="bi bi-lightning-charge me-2"></i>Track Workout
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include '../template/footer.php'; ?>

<!--  INLINE STYLES (Hero + Text Gradient)  -->
<style>
.hero-section {
    position: relative;
    overflow: hidden;
    min-height: 100vh;
    max-height: 100vh;
    height: 100vh;
    width: 100%;
}

.hero-video, .hero-image {
    position: absolute;
    top: 0; 
    left: 0;
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    object-fit: cover;
    object-position: center;
    z-index: 0;
    transition: none !important;
    transform: none !important;
}

.text-gradient {
    background: linear-gradient(90deg, #0D6EFD, #10B981);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>

