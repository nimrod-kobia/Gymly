<?php
/**
 * Track Health Page - Simple health tracking
 */

require_once '../autoload.php';

SessionManager::requireAuth();
$userId = SessionManager::getUserId();

$pageTitle = "Track Health | Gymly";
include '../template/layout.php';

// Fetch user's latest health metrics
$db = (new Database())->connect();

// Get latest health entry
$stmtLatest = $db->prepare("
    SELECT * FROM user_health_metrics 
    WHERE user_id = ? 
    ORDER BY recorded_at DESC 
    LIMIT 1
");
$stmtLatest->execute([$userId]);
$latestMetrics = $stmtLatest->fetch(PDO::FETCH_ASSOC);

// Get health history for charts (last 30 days)
$stmtHistory = $db->prepare("
    SELECT 
        DATE(recorded_at) as date,
        weight_kg,
        bmi
    FROM user_health_metrics 
    WHERE user_id = ? 
    AND recorded_at >= NOW() - INTERVAL '30 days'
    ORDER BY recorded_at ASC
");
$stmtHistory->execute([$userId]);
$healthHistory = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .health-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 4rem 0 3rem;
        margin-top: 56px;
    }

    .metric-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        transition: all 0.3s;
        height: 100%;
        color: white;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        border-color: rgba(102, 126, 234, 0.5);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .metric-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: #8b9eff;
        margin-bottom: 0.5rem;
    }

    .metric-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
    }

    .chart-container {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        color: white;
    }

    .health-form {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 2rem;
        color: white;
    }

    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #667eea;
        color: white;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }
    
    .form-label {
        color: white;
        font-weight: 500;
    }

    .bmi-indicator {
        height: 10px;
        background: linear-gradient(to right, 
            #3b82f6 0%, #3b82f6 18.5%,
            #22c55e 18.5%, #22c55e 25%,
            #eab308 25%, #eab308 30%,
            #f59e0b 30%, #f59e0b 35%,
            #ef4444 35%, #ef4444 100%
        );
        border-radius: 5px;
        position: relative;
        margin: 1rem 0;
    }

    .bmi-pointer {
        position: absolute;
        top: -5px;
        width: 3px;
        height: 20px;
        background: white;
        border-radius: 2px;
        transition: left 0.5s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }
    
    body {
        background-color: #000;
    }
    
    .container {
        color: white;
    }
    
    h3, h4, h5, h6 {
        color: white !important;
    }
    
    .text-muted {
        color: rgba(255, 255, 255, 0.6) !important;
    }
    
    .alert {
        color: white;
        border: none;
    }
    
    .alert-success {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.3), rgba(34, 197, 94, 0.2));
        border-left: 4px solid #22c55e;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(239, 68, 68, 0.2));
        border-left: 4px solid #ef4444;
    }
    
    .badge {
        color: white !important;
    }
    
    .bmi-indicator + .d-flex span {
        color: rgba(255, 255, 255, 0.7) !important;
    }
</style>

<!-- Hero Section -->
<div class="health-hero text-center text-white">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Health Tracking</h1>
        <p class="lead">Track your weight, BMI, steps, water and sleep</p>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Quick Stats Overview -->
        <div class="col-12 mb-4">
            <h3 class="text-white mb-4"><i class="bi bi-activity"></i> Current Metrics</h3>
            <div class="row g-3">
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="metric-card text-center">
                        <div class="metric-icon mx-auto">
                            <i class="bi bi-heart-pulse text-white"></i>
                        </div>
                        <div class="metric-value">
                            <?= $latestMetrics ? number_format($latestMetrics['weight_kg'], 1) : '--' ?>
                        </div>
                        <div class="metric-label">Weight (kg)</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="metric-card text-center">
                        <div class="metric-icon mx-auto">
                            <i class="bi bi-calculator text-white"></i>
                        </div>
                        <div class="metric-value">
                            <?= $latestMetrics ? number_format($latestMetrics['bmi'], 1) : '--' ?>
                        </div>
                        <div class="metric-label">BMI</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="metric-card text-center">
                        <div class="metric-icon mx-auto">
                            <i class="bi bi-droplet text-white"></i>
                        </div>
                        <div class="metric-value">
                            <?= $latestMetrics && $latestMetrics['water_intake_ml'] ? number_format($latestMetrics['water_intake_ml'] / 1000, 1) : '--' ?>
                        </div>
                        <div class="metric-label">Water (L)</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="metric-card text-center">
                        <div class="metric-icon mx-auto">
                            <i class="bi bi-moon-stars text-white"></i>
                        </div>
                        <div class="metric-value">
                            <?= $latestMetrics && $latestMetrics['hours_slept'] ? number_format($latestMetrics['hours_slept'], 1) : '--' ?>
                        </div>
                        <div class="metric-label">Sleep (hrs)</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="metric-card text-center">
                        <div class="metric-icon mx-auto">
                            <i class="bi bi-speedometer text-white"></i>
                        </div>
                        <div class="metric-value">
                            <?= $latestMetrics && $latestMetrics['steps_count'] ? number_format($latestMetrics['steps_count']) : '--' ?>
                        </div>
                        <div class="metric-label">Steps</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BMI Status Card -->
        <?php if ($latestMetrics && $latestMetrics['bmi']): ?>
        <div class="col-lg-6 mx-auto mb-4">
            <div class="metric-card" id="bmiCard">
                <h5 class="text-white mb-3"><i class="bi bi-speedometer2"></i> BMI Status</h5>
                <div class="text-center mb-3">
                    <div class="metric-value" style="font-size: 3rem;" id="bmiDisplay">
                        <?= number_format($latestMetrics['bmi'], 1) ?>
                    </div>
                    <div class="text-white" id="bmiCategory">
                        <?php
                        $bmi = $latestMetrics['bmi'];
                        if ($bmi < 18.5) {
                            echo '<span class="badge bg-info">Underweight</span>';
                        } elseif ($bmi < 25) {
                            echo '<span class="badge bg-success">Normal</span>';
                        } elseif ($bmi < 30) {
                            echo '<span class="badge bg-warning">Overweight</span>';
                        } else {
                            echo '<span class="badge bg-danger">Obese</span>';
                        }
                        ?>
                    </div>
                </div>
                <div class="bmi-indicator">
                    <div class="bmi-pointer" id="bmiPointer" style="left: <?php 
                        // Proper BMI to percentage mapping for 15-40 scale
                        $bmiClamped = min(40, max(15, $bmi));
                        $percentage = (($bmiClamped - 15) / (40 - 15)) * 100;
                        echo number_format($percentage, 2);
                    ?>%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small">
                    <span>15</span>
                    <span>18.5</span>
                    <span>25</span>
                    <span>30</span>
                    <span>40</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Chart -->
        <div class="col-12 mb-4">
            <h3 class="text-white mb-4"><i class="bi bi-graph-up-arrow"></i> Weight Trend (30 Days)</h3>
            <div class="chart-container">
                <canvas id="weightChart" height="80"></canvas>
            </div>
        </div>

        <!-- Log Health Form -->
        <div class="col-lg-8 mx-auto mb-4">
            <div class="health-form">
                <h4 class="text-white mb-4"><i class="bi bi-plus-circle"></i> Log Health Metrics</h4>
                
                <form id="healthForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white">Weight (kg) *</label>
                            <input type="number" class="form-control" name="weight_kg" step="0.1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Height (cm)</label>
                            <input type="number" class="form-control" name="height_cm" step="0.1" 
                                   value="<?= $latestMetrics['height_cm'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Water Intake (ml)</label>
                            <input type="number" class="form-control" name="water_intake_ml" step="100" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Hours Slept</label>
                            <input type="number" class="form-control" name="hours_slept" step="0.5" min="0" max="24">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Steps Count</label>
                            <input type="number" class="form-control" name="steps_count" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Calories Burned</label>
                            <input type="number" class="form-control" name="calories_burned" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white">Notes (Optional)</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="How are you feeling today?"></textarea>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Save Metrics
                        </button>
                    </div>
                </form>

                <div id="healthAlert" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for chart
    const healthData = <?= json_encode($healthHistory) ?>;
    
    const dates = healthData.map(d => d.date);
    const weights = healthData.map(d => parseFloat(d.weight_kg) || null);

    // Weight Chart
    const weightCtx = document.getElementById('weightChart').getContext('2d');
    new Chart(weightCtx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Weight (kg)',
                    data: weights,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: { color: '#fff' }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                },
                y: {
                    ticks: { color: '#fff' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                }
            }
        }
    });

    // Form submission
    document.getElementById('healthForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        
        // Calculate BMI if height is provided
        if (data.weight_kg && data.height_cm) {
            const heightM = data.height_cm / 100;
            data.bmi = (data.weight_kg / (heightM * heightM)).toFixed(2);
        }
        
        try {
            const response = await fetch('../handlers/logHealth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            const alertDiv = document.getElementById('healthAlert');
            alertDiv.style.display = 'block';
            
            if (result.success) {
                alertDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Health metrics logged successfully!
                    </div>
                `;
                
                // Reset form and reload page after 2 seconds
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alertDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> ${result.error || 'Failed to log metrics'}
                    </div>
                `;
            }
        } catch (error) {
            const alertDiv = document.getElementById('healthAlert');
            alertDiv.style.display = 'block';
            alertDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> Error: ${error.message}
                </div>
            `;
        }
    });

    // Auto-calculate BMI on weight/height change
    const weightInput = document.querySelector('input[name="weight_kg"]');
    const heightInput = document.querySelector('input[name="height_cm"]');
    const bmiDisplay = document.getElementById('bmiDisplay');
    const bmiCategory = document.getElementById('bmiCategory');
    const bmiPointer = document.getElementById('bmiPointer');
    
    function calculateBMI() {
        const weight = parseFloat(weightInput.value);
        const height = parseFloat(heightInput.value);
        
        if (weight && height && height > 0) {
            const heightM = height / 100;
            const bmi = parseFloat((weight / (heightM * heightM)).toFixed(1));
            
            // Update BMI display
            if (bmiDisplay) {
                bmiDisplay.textContent = bmi.toFixed(1);
            }
            
            // Update category badge
            if (bmiCategory) {
                let categoryHTML = '';
                if (bmi < 18.5) {
                    categoryHTML = '<span class="badge bg-info">Underweight</span>';
                } else if (bmi < 25) {
                    categoryHTML = '<span class="badge bg-success">Normal</span>';
                } else if (bmi < 30) {
                    categoryHTML = '<span class="badge bg-warning">Overweight</span>';
                } else {
                    categoryHTML = '<span class="badge bg-danger">Obese</span>';
                }
                bmiCategory.innerHTML = categoryHTML;
            }
            
            // Update pointer position (BMI scale from 15 to 40)
            if (bmiPointer) {
                const bmiClamped = Math.min(40, Math.max(15, bmi));
                const percentage = ((bmiClamped - 15) / (40 - 15)) * 100;
                bmiPointer.style.left = percentage.toFixed(2) + '%';
            }
            
            console.log('Calculated BMI:', bmi);
        }
    }
    
    weightInput?.addEventListener('input', calculateBMI);
    heightInput?.addEventListener('input', calculateBMI);
</script>

<?php include '../template/footer.php'; ?>
