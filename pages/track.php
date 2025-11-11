<?php
require_once "../autoload.php";

if (SessionManager::isLoggedIn()) {
    SessionManager::updateActivity();
}

// Fetch real user data
$caloriesConsumed = 0;
$workoutMinutes = 0;
$dailyGoalPercent = 0;
$caloriesBurned = 0;
$caloriesRemaining = 0;
$mealBreakdown = [];
$recentActivities = [];

if (SessionManager::isLoggedIn()) {
    try {
        $db = (new Database())->connect();
        $userId = SessionManager::getUserId();
        
        // Get today's nutrition data
        $today = date('Y-m-d');
        $stmtNutrition = $db->prepare("
            SELECT 
                COALESCE(SUM(calories), 0) as total_calories,
                COALESCE(SUM(protein_g), 0) as total_protein,
                COALESCE(SUM(carbs_g), 0) as total_carbs,
                COALESCE(SUM(fat_g), 0) as total_fats
            FROM user_meals 
            WHERE user_id = :user_id AND DATE(logged_at) = :today
        ");
        $stmtNutrition->execute([':user_id' => $userId, ':today' => $today]);
        $nutritionData = $stmtNutrition->fetch(PDO::FETCH_ASSOC);
        $caloriesConsumed = round($nutritionData['total_calories'] ?? 0);
        
        // Get meal breakdown by meal type
        $stmtMeals = $db->prepare("
            SELECT 
                meal_type,
                COALESCE(SUM(calories), 0) as calories,
                COALESCE(SUM(protein_g), 0) as protein,
                COALESCE(SUM(carbs_g), 0) as carbs,
                COALESCE(SUM(fat_g), 0) as fats
            FROM user_meals 
            WHERE user_id = :user_id AND DATE(logged_at) = :today
            GROUP BY meal_type
            ORDER BY 
                CASE meal_type
                    WHEN 'breakfast' THEN 1
                    WHEN 'lunch' THEN 2
                    WHEN 'dinner' THEN 3
                    WHEN 'snack' THEN 4
                    ELSE 5
                END
        ");
        $stmtMeals->execute([':user_id' => $userId, ':today' => $today]);
        $mealBreakdown = $stmtMeals->fetchAll(PDO::FETCH_ASSOC);
        
        // Get today's workout data
        $stmtWorkout = $db->prepare("
            SELECT 
                COALESCE(SUM(EXTRACT(EPOCH FROM (completed_at - started_at))/60), 0) as total_minutes
            FROM workout_sessions 
            WHERE user_id = :user_id 
            AND DATE(started_at) = :today 
            AND status = 'completed'
        ");
        $stmtWorkout->execute([':user_id' => $userId, ':today' => $today]);
        $workoutData = $stmtWorkout->fetch(PDO::FETCH_ASSOC);
        $workoutMinutes = round($workoutData['total_minutes'] ?? 0);
        
        // Calculate calories burned (estimate: ~5 calories per minute of workout)
        $caloriesBurned = $workoutMinutes * 5;
        
        // Calculate daily goal (assuming 2000 calorie target)
        $dailyGoal = 2000;
        $caloriesRemaining = $dailyGoal - $caloriesConsumed + $caloriesBurned;
        $dailyGoalPercent = $dailyGoal > 0 ? round(($caloriesConsumed / $dailyGoal) * 100) : 0;
        
        // Get recent workout activities
        $stmtActivities = $db->prepare("
            SELECT 
                ws.started_at,
                ws.completed_at,
                sd.name as workout_name,
                EXTRACT(EPOCH FROM (ws.completed_at - ws.started_at))/60 as duration_minutes
            FROM workout_sessions ws
            LEFT JOIN split_days sd ON ws.split_day_id = sd.id
            WHERE ws.user_id = :user_id 
            AND ws.status = 'completed'
            AND DATE(ws.started_at) = :today
            ORDER BY ws.started_at DESC
            LIMIT 5
        ");
        $stmtActivities->execute([':user_id' => $userId, ':today' => $today]);
        $recentActivities = $stmtActivities->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Error fetching track data: " . $e->getMessage());
    }
}

$pageTitle = "Track Progress | Gymly";
include '../template/layout.php';
?>

<!-- INTEGRATED HERO SECTION WITH DUAL COLUMN CAROUSEL -->
<section class="hero-section position-relative text-white overflow-hidden">
    <div class="container position-relative z-1">
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
                        <div class="card bg-dark border-secondary p-3 rounded-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="fw-bold text-primary mb-1"><?php echo number_format($caloriesConsumed); ?></h4>
                                    <small class="text-white">Calories Today</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold text-success mb-1"><?php echo $workoutMinutes; ?></h4>
                                    <small class="text-white">Workout Minutes</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="fw-bold text-info mb-1"><?php echo $dailyGoalPercent; ?>%</h4>
                                    <small class="text-white">Daily Goal</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="nutrition.php" class="btn btn-primary btn-lg fw-semibold text-white">
                        <i class="bi bi-activity me-2"></i>Continue Tracking
                    </a>
                <?php else: ?>
                    <a href="signInPage.php" class="btn btn-primary btn-lg fw-semibold me-3 text-white">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Log In
                    </a>
                    <a href="signUpPage.php" class="btn btn-outline-light btn-lg fw-semibold text-white">
                        <i class="bi bi-person-plus me-2"></i>Sign Up
                    </a>
                <?php endif; ?>
            </div>

            <!-- Right Column - Dual Carousel -->
            <div class="col-lg-6 position-relative">
                <div class="dual-carousel-container" style="height: 500px;">
                    <!-- Left Column Carousel (Moving Down) -->
                    <div class="carousel-column" style="left: 0; width: 48%;">
                        <div class="vertical-carousel-down">
                            <div class="carousel-track-down">
                                <?php 
                                $images = [
                                    "../assets/images/gym_image.jpeg",
                                    "../assets/images/gym_nutrition.jpeg"
                                ];
                                for ($i = 0; $i < 6; $i++): ?>
                                    <div class="carousel-slide-vertical mb-3">
                                        <img src="<?php echo $images[$i % 2]; ?>" class="w-100 rounded-3" alt="Gym" style="height: 150px; object-fit: cover;">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column Carousel (Moving Up) -->
                    <div class="carousel-column" style="right: 0; width: 48%;">
                        <div class="vertical-carousel-up">
                            <div class="carousel-track-up">
                                <?php 
                                $images = [
                                    "../assets/images/gym_nutrition.jpeg",
                                    "../assets/images/gym_image.jpeg"
                                ];
                                for ($i = 0; $i < 6; $i++): ?>
                                    <div class="carousel-slide-vertical mb-3">
                                        <img src="<?php echo $images[$i % 2]; ?>" class="w-100 rounded-3" alt="Gym" style="height: 150px; object-fit: cover;">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Gradient -->
    <div class="hero-bg"></div>
</section>

<!-- TRACKING SECTIONS -->
<section id="tracker-sections" class="py-5 bg-black text-white border-top border-secondary">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3 text-white">Develop healthy habits</h2>
            <p class="text-white">Monitor all aspects of your fitness journey in one place</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $trackingCards = [
                [
                    'title' => 'Nutrition Tracking',
                    'icon' => 'egg-fried',
                    'color' => 'primary',
                    'desc' => 'Log meals, track macros, and monitor calorie intake with detailed breakdowns.',
                    'link' => 'nutrition.php'
                ],
                [
                    'title' => 'Workout Tracking',
                    'icon' => 'activity',
                    'color' => 'success',
                    'desc' => 'Record exercises, track sets and reps, and monitor calories burned during workouts.',
                    'link' => 'myWorkouts.php'
                ],
                [
                    'title' => 'Health Metrics',
                    'icon' => 'heart-pulse',
                    'color' => 'danger',
                    'desc' => 'Track weight, BMI, body fat, heart rate, sleep, and other vital health indicators.',
                    'link' => 'trackHealth.php'
                ]
            ];
            
            foreach ($trackingCards as $card): ?>
                <div class="col-md-4">
                    <div class="card bg-dark border-secondary p-4 rounded-4 h-100 tracking-card">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-<?php echo $card['color']; ?> bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-<?php echo $card['icon']; ?> text-<?php echo $card['color']; ?> fs-4"></i>
                            </div>
                            <h4 class="fw-semibold mb-0 text-white"><?php echo $card['title']; ?></h4>
                        </div>
                        <p class="mb-4 text-white"><?php echo $card['desc']; ?></p>
                        <a href="<?php echo $card['link']; ?>" class="btn btn-<?php echo $card['color']; ?> w-100 mt-auto text-white">
                            <i class="bi bi-<?php echo $card['icon']; ?> me-2"></i>Track <?php echo explode(' ', $card['title'])[0]; ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
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
                            <?php 
                            $calorieStats = [
                                ['value' => $caloriesConsumed, 'label' => 'Consumed', 'color' => 'primary', 'border' => true],
                                ['value' => $caloriesBurned, 'label' => 'Burned', 'color' => 'success', 'border' => true],
                                ['value' => $caloriesRemaining, 'label' => 'Remaining', 'color' => 'info', 'border' => false]
                            ];
                            foreach ($calorieStats as $stat): ?>
                                <div class="col-4">
                                    <?php if ($stat['border']): ?><div class="border-end border-secondary"><?php endif; ?>
                                        <h4 class="text-<?php echo $stat['color']; ?> fw-bold"><?php echo number_format($stat['value']); ?></h4>
                                        <small class="text-white"><?php echo $stat['label']; ?></small>
                                    <?php if ($stat['border']): ?></div><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Meal Breakdown -->
                <div class="card bg-black border-secondary">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">Meal Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($mealBreakdown)): ?>
                            <?php foreach ($mealBreakdown as $index => $meal): ?>
                                <div class="<?php echo $index < count($mealBreakdown) - 1 ? 'mb-3' : ''; ?>">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold text-white"><?php echo ucfirst($meal['meal_type']); ?></span>
                                        <span class="text-white"><?php echo round($meal['calories']); ?> kcal</span>
                                    </div>
                                    <small class="text-white">
                                        <?php echo round($meal['protein']); ?>g protein, 
                                        <?php echo round($meal['carbs']); ?>g carbs, 
                                        <?php echo round($meal['fats']); ?>g fat
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center mb-0 text-white">No meals logged today. Start tracking your nutrition!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <h3 class="fw-bold mb-4 text-white">Recent Activities</h3>
                
                <div class="card bg-black border-secondary">
                    <div class="card-body">
                        <?php if (!empty($recentActivities)): ?>
                            <?php foreach ($recentActivities as $index => $activity): ?>
                                <div class="<?php echo $index < count($recentActivities) - 1 ? 'mb-3' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold text-white"><?php echo htmlspecialchars($activity['workout_name'] ?? 'Workout'); ?></span>
                                        <span class="badge bg-success"><?php echo round($activity['duration_minutes']); ?> mins</span>
                                    </div>
                                    <small class="text-white"><?php echo date('g:i A', strtotime($activity['started_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center mb-0 text-white">No workouts logged today. Start your training session!</p>
                        <?php endif; ?>
                    </div>
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
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-section {
    background: radial-gradient(circle at left, #0D0D0D, #000);
    padding: 30px 0 50px;
    min-height: auto;
    display: flex;
    align-items: center;
}

.hero-section .container {
    padding-top: 20px;
    padding-bottom: 20px;
}

.hero-bg {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: radial-gradient(circle at left, #0D0D0D, #000);
    z-index: -1;
}

.tracking-card:hover {
    transform: translateY(-6px);
    transition: 0.3s;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.08);
}

.card-header {
    background-color: #1a202c !important;
}

.dual-carousel-container {
    position: relative;
    overflow: visible;
}

.carousel-column {
    position: absolute;
    height: 100%;
}

.vertical-carousel-down,
.vertical-carousel-up {
    position: relative;
    height: 100%;
    overflow: visible;
}

.carousel-track-down,
.carousel-track-up {
    display: flex;
    flex-direction: column;
}

.carousel-track-down {
    animation: scrollDown 20s linear infinite;
}

.carousel-track-up {
    animation: scrollUp 20s linear infinite;
}

.carousel-slide-vertical {
    flex-shrink: 0;
}

@keyframes scrollDown {
    0% { transform: translateY(0); }
    100% { transform: translateY(-50%); }
}

@keyframes scrollUp {
    0% { transform: translateY(-50%); }
    100% { transform: translateY(0); }
}
</style>