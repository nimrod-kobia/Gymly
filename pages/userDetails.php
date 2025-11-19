<?php
require_once "../autoload.php";

// Require admin access
SessionManager::requireAdmin();

// Get user ID from query parameter
$viewUserId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$viewUserId) {
    header("Location: users.php");
    exit();
}

$pdo = (new Database())->connect();

// Fetch user details
$stmt = $pdo->prepare("SELECT id, full_name, username, email, role, is_verified, created_at, updated_at FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$viewUserId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: users.php?error=User+not+found");
    exit();
}

// Fetch latest health metrics
$stmtHealth = $pdo->prepare("
    SELECT * FROM user_health_metrics 
    WHERE user_id = ? 
    ORDER BY recorded_at DESC 
    LIMIT 1
");
$stmtHealth->execute([$viewUserId]);
$latestHealth = $stmtHealth->fetch(PDO::FETCH_ASSOC);

// Fetch health history (last 30 days)
$stmtHealthHistory = $pdo->prepare("
    SELECT 
        DATE(recorded_at) as date,
        weight_kg,
        bmi,
        water_intake_ml,
        hours_slept,
        steps_count
    FROM user_health_metrics 
    WHERE user_id = ? 
    AND recorded_at >= NOW() - INTERVAL '30 days'
    ORDER BY recorded_at DESC
    LIMIT 10
");
$stmtHealthHistory->execute([$viewUserId]);
$healthHistory = $stmtHealthHistory->fetchAll(PDO::FETCH_ASSOC);

// Fetch active workout split
$stmtActiveSplit = $pdo->prepare("
    SELECT ws.*, 
           (SELECT COUNT(*) FROM split_days WHERE workout_split_id = ws.id) as total_days
    FROM workout_splits ws
    WHERE ws.user_id = ? AND ws.is_active = TRUE
    LIMIT 1
");
$stmtActiveSplit->execute([$viewUserId]);
$activeSplit = $stmtActiveSplit->fetch(PDO::FETCH_ASSOC);

// Fetch all workout splits
$stmtAllSplits = $pdo->prepare("
    SELECT ws.*, 
           (SELECT COUNT(*) FROM split_days WHERE workout_split_id = ws.id) as total_days
    FROM workout_splits ws
    WHERE ws.user_id = ?
    ORDER BY ws.is_active DESC, ws.created_at DESC
    LIMIT 5
");
$stmtAllSplits->execute([$viewUserId]);
$allSplits = $stmtAllSplits->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent workout completions
$stmtWorkouts = $pdo->prepare("
    SELECT 
        e.name as exercise_name,
        e.category,
        sde.sets,
        sde.reps,
        sde.weight_kg,
        sde.rest_seconds,
        ec.completed_at,
        ec.notes
    FROM exercise_completions ec
    JOIN split_day_exercises sde ON ec.split_day_exercise_id = sde.id
    JOIN exercises e ON sde.exercise_id = e.id
    WHERE ec.user_id = ?
    ORDER BY ec.completed_at DESC
    LIMIT 20
");
$stmtWorkouts->execute([$viewUserId]);
$recentWorkouts = $stmtWorkouts->fetchAll(PDO::FETCH_ASSOC);

// Fetch nutrition summary (last 7 days)
$stmtNutrition = $pdo->prepare("
    SELECT 
        summary_date,
        calories_consumed,
        protein_g,
        carbs_g,
        fat_g,
        meals_count
    FROM user_daily_summary
    WHERE user_id = ?
    ORDER BY summary_date DESC
    LIMIT 7
");
$stmtNutrition->execute([$viewUserId]);
$nutritionHistory = $stmtNutrition->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "User Details - " . htmlspecialchars($user['full_name']);
include "../template/layout.php";
?>

<style>
    body {
        background-color: #0D0D0D;
        color: #E5E7EB;
    }

    .user-detail-card {
        background: linear-gradient(145deg, #1a1a1a, #2d2d2d);
        border: 1px solid #3d3d3d;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .stat-card {
        background: rgba(102, 126, 234, 0.1);
        border: 1px solid rgba(102, 126, 234, 0.3);
        border-radius: 10px;
        padding: 1rem;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #667eea;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }

    .section-title {
        color: #667eea;
        font-weight: 600;
        margin-bottom: 1rem;
        border-bottom: 2px solid #667eea;
        padding-bottom: 0.5rem;
    }

    .workout-item {
        background: rgba(255, 255, 255, 0.03);
        border-left: 3px solid #667eea;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 5px;
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
    }

    .table-dark {
        background-color: #1a1a1a;
    }

    .table-dark th {
        background-color: #2d2d2d;
        color: #667eea;
        border-color: #3d3d3d;
    }

    .table-dark td {
        border-color: #3d3d3d;
        color: #e5e7eb;
    }

    .back-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
        transition: transform 0.2s;
    }

    .back-btn:hover {
        transform: translateY(-2px);
        color: white;
    }
</style>

<div class="container mt-5 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-light fw-bold mb-0">
            <i class="bi bi-person-badge text-warning"></i> 
            User Details: <?= htmlspecialchars($user['full_name']) ?>
        </h2>
        <a href="users.php" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>

    <!-- User Info Card -->
    <div class="user-detail-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="text-white mb-3">
                    <i class="bi bi-person-circle"></i> Account Information
                </h4>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Username:</strong> @<?= htmlspecialchars($user['username']) ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>User ID:</strong> <?= $user['id'] ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Role:</strong> 
                        <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Status:</strong> 
                        <?php if ($user['is_verified']): ?>
                            <span class="badge bg-success">Verified</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Not Verified</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Member Since:</strong> <?= date('M d, Y', strtotime($user['created_at'])) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="display-1 text-primary">
                    <i class="bi bi-person-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Health Stats Overview -->
    <?php if ($latestHealth): ?>
    <div class="user-detail-card">
        <h4 class="section-title">
            <i class="bi bi-heart-pulse"></i> Latest Health Metrics
        </h4>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($latestHealth['weight_kg'], 1) ?></div>
                    <div class="stat-label">Weight (kg)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= $latestHealth['bmi'] ? number_format($latestHealth['bmi'], 1) : '--' ?></div>
                    <div class="stat-label">BMI</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= $latestHealth['steps_count'] ?? 0 ?></div>
                    <div class="stat-label">Steps</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?= $latestHealth['hours_slept'] ? number_format($latestHealth['hours_slept'], 1) : '--' ?></div>
                    <div class="stat-label">Sleep (hrs)</div>
                </div>
            </div>
        </div>
        <div class="mt-3 text-muted small">
            <i class="bi bi-clock"></i> Last updated: <?= date('M d, Y g:i A', strtotime($latestHealth['recorded_at'])) ?>
        </div>
    </div>
    <?php else: ?>
    <div class="user-detail-card text-center text-muted">
        <i class="bi bi-exclamation-circle display-4 mb-3"></i>
        <p>No health metrics recorded yet</p>
    </div>
    <?php endif; ?>

    <!-- Health History -->
    <?php if (!empty($healthHistory)): ?>
    <div class="user-detail-card">
        <h4 class="section-title">
            <i class="bi bi-graph-up"></i> Health History (Last 30 Days)
        </h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Weight (kg)</th>
                        <th>BMI</th>
                        <th>Water (ml)</th>
                        <th>Sleep (hrs)</th>
                        <th>Steps</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($healthHistory as $record): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($record['date'])) ?></td>
                        <td><?= number_format($record['weight_kg'], 1) ?></td>
                        <td><?= $record['bmi'] ? number_format($record['bmi'], 1) : '--' ?></td>
                        <td><?= $record['water_intake_ml'] ?? '--' ?></td>
                        <td><?= $record['hours_slept'] ? number_format($record['hours_slept'], 1) : '--' ?></td>
                        <td><?= $record['steps_count'] ?? '--' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Nutrition Summary -->
    <?php if (!empty($nutritionHistory)): ?>
    <div class="user-detail-card">
        <h4 class="section-title">
            <i class="bi bi-egg-fried"></i> Nutrition Summary (Last 7 Days)
        </h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Calories</th>
                        <th>Protein (g)</th>
                        <th>Carbs (g)</th>
                        <th>Fat (g)</th>
                        <th>Meals</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nutritionHistory as $nutrition): ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($nutrition['summary_date'])) ?></td>
                        <td><?= number_format($nutrition['calories_consumed']) ?></td>
                        <td><?= round($nutrition['protein_g'], 1) ?></td>
                        <td><?= round($nutrition['carbs_g'], 1) ?></td>
                        <td><?= round($nutrition['fat_g'], 1) ?></td>
                        <td><?= $nutrition['meals_count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Workout Splits -->
    <div class="user-detail-card">
        <h4 class="section-title">
            <i class="bi bi-calendar-week"></i> Workout Splits
        </h4>
        <?php if (!empty($allSplits)): ?>
            <?php foreach ($allSplits as $split): ?>
            <div class="workout-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-white">
                            <?= htmlspecialchars($split['name']) ?>
                            <?php if ($split['is_active']): ?>
                                <span class="badge bg-success ms-2">Active</span>
                            <?php endif; ?>
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-calendar3"></i> <?= $split['total_days'] ?> days
                            | Created: <?= date('M d, Y', strtotime($split['created_at'])) ?>
                        </small>
                    </div>
                </div>
                <?php if ($split['description']): ?>
                <p class="mb-0 mt-2 small text-muted"><?= htmlspecialchars($split['description']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted text-center py-3">
                <i class="bi bi-exclamation-circle"></i> No workout splits created yet
            </p>
        <?php endif; ?>
    </div>

    <!-- Recent Workout Completions -->
    <?php if (!empty($recentWorkouts)): ?>
    <div class="user-detail-card">
        <h4 class="section-title">
            <i class="bi bi-check-circle"></i> Recent Workout Completions (Last 20)
        </h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Exercise</th>
                        <th>Category</th>
                        <th>Sets × Reps</th>
                        <th>Weight (kg)</th>
                        <th>Rest (sec)</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentWorkouts as $workout): ?>
                    <tr>
                        <td><?= date('M d, g:i A', strtotime($workout['completed_at'])) ?></td>
                        <td><?= htmlspecialchars($workout['exercise_name']) ?></td>
                        <td>
                            <span class="badge badge-custom" style="background-color: #667eea;">
                                <?= htmlspecialchars($workout['category']) ?>
                            </span>
                        </td>
                        <td><?= $workout['sets'] ?> × <?= $workout['reps'] ?></td>
                        <td><?= $workout['weight_kg'] ?? '--' ?></td>
                        <td><?= $workout['rest_seconds'] ?? '--' ?></td>
                        <td class="small"><?= htmlspecialchars($workout['notes'] ?? '--') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="user-detail-card text-center text-muted">
        <i class="bi bi-clipboard-x display-4 mb-3"></i>
        <p>No workout completions recorded yet</p>
    </div>
    <?php endif; ?>
</div>

<?php include "../template/footer.php"; ?>
