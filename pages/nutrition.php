<?php
/**
 * Nutrition Tracking Page - Log meals and view daily calorie/macro stats
 */

require_once '../autoload.php';

SessionManager::requireAuth();
$userId = $_SESSION['user_id'];

// Fetch user info for display
$db = (new Database())->connect();
$stmt = $db->prepare("SELECT full_name FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$pageTitle = "Nutrition Tracking | Gymly";
include '../template/layout.php';
?>

<!-- Hero Section -->
<div class="hero-section text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3 text-white">🥗 Nutrition Tracking</h1>
        <p class="lead text-white">Track your meals and monitor your daily calorie intake</p>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Log Food Section -->
        <div class="col-lg-5 mb-4">
            <div class="card bg-dark shadow-lg">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3 class="mb-0"><i class="bi bi-plus-circle"></i> Log Food</h3>
                </div>
                <div class="card-body">
                    <form id="logFoodForm">
                        <div class="mb-3">
                            <label class="form-label text-white">What did you eat?</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                name="query" 
                                id="foodQuery"
                                placeholder="e.g., 1 cup of rice, chicken breast 150g"
                                autocomplete="off"
                                required
                            >
                            <div id="foodSuggestions" class="list-group mt-1" style="position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto; display: none;"></div>
                            <small class="form-text text-muted">
                                Use natural language (e.g., "2 eggs and toast")
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Meal Type</label>
                            <select class="form-select" name="meal_type">
                                <option value="breakfast">🌅 Breakfast</option>
                                <option value="lunch">☀️ Lunch</option>
                                <option value="dinner">🌙 Dinner</option>
                                <option value="snack" selected>🍎 Snack</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Log Food
                        </button>
                    </form>
                    
                    <div id="logResult" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <!-- Daily Summary -->
        <div class="col-lg-7">
            <div class="card bg-dark shadow-lg mb-4">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3 class="mb-0"><i class="bi bi-calendar-check"></i> Today's Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4" id="summaryCards">
                        <div class="col-6 col-md-3">
                            <div class="card bg-secondary text-center">
                                <div class="card-body py-3">
                                    <h5 class="mb-1">🔥</h5>
                                    <h4 class="mb-0 text-white" id="caloriesCount">0</h4>
                                    <small class="text-muted">Calories</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-secondary text-center">
                                <div class="card-body py-3">
                                    <h5 class="mb-1">🥩</h5>
                                    <h4 class="mb-0 text-white" id="proteinCount">0g</h4>
                                    <small class="text-muted">Protein</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-secondary text-center">
                                <div class="card-body py-3">
                                    <h5 class="mb-1">🍞</h5>
                                    <h4 class="mb-0 text-white" id="carbsCount">0g</h4>
                                    <small class="text-muted">Carbs</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card bg-secondary text-center">
                                <div class="card-body py-3">
                                    <h5 class="mb-1">🥑</h5>
                                    <h4 class="mb-0 text-white" id="fatCount">0g</h4>
                                    <small class="text-muted">Fat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Meals List -->
                    <h5 class="mb-3 text-white">Recent Meals</h5>
                    <div id="mealsList" class="meal-list">
                        <p class="text-muted text-center py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No meals logged yet today
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.meal-list {
    max-height: 400px;
    overflow-y: auto;
}

.meal-item {
    border-left: 4px solid #667eea;
    transition: all 0.3s ease;
}

.meal-item:hover {
    border-left-color: #764ba2;
    transform: translateX(5px);
}

.badge-meal {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

#foodSuggestions {
    max-width: 100%;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}

#foodSuggestions .list-group-item:hover {
    background-color: #495057 !important;
    cursor: pointer;
}

#foodSuggestions .list-group-item strong {
    color: #ffffff;
}
</style>

<script>
// Food autocomplete
let searchTimeout;
const foodInput = document.getElementById('foodQuery');
const suggestionsDiv = document.getElementById('foodSuggestions');

foodInput.addEventListener('input', (e) => {
    const query = e.target.value.trim();
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    // Debounce search
    searchTimeout = setTimeout(async () => {
        try {
            const response = await fetch(`../handlers/searchFood.php?query=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success && data.foods && data.foods.length > 0) {
                suggestionsDiv.innerHTML = data.foods.map(food => `
                    <a href="#" class="list-group-item list-group-item-action bg-dark text-white border-secondary food-suggestion" 
                       data-food="${food.food_name}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${food.food_name}</strong>
                                <br>
                                <small class="text-muted">Suggested: 1 serving, 1 cup, 100g</small>
                            </div>
                            <i class="bi bi-plus-circle text-primary"></i>
                        </div>
                    </a>
                `).join('');
                suggestionsDiv.style.display = 'block';
                
                // Add click handlers to suggestions
                document.querySelectorAll('.food-suggestion').forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        const foodName = e.currentTarget.dataset.food;
                        
                        // Show quantity options
                        const quantities = ['1 serving', '1 cup', '100g', '1 piece', '1 medium'];
                        suggestionsDiv.innerHTML = `
                            <div class="list-group-item bg-dark text-white border-secondary">
                                <strong>Select quantity for ${foodName}:</strong>
                            </div>
                            ${quantities.map(qty => `
                                <a href="#" class="list-group-item list-group-item-action bg-dark text-white border-secondary quantity-option"
                                   data-quantity="${qty}" data-food="${foodName}">
                                    <i class="bi bi-circle me-2"></i>${qty} of ${foodName}
                                </a>
                            `).join('')}
                            <a href="#" class="list-group-item list-group-item-action bg-secondary text-white border-secondary" 
                               id="customQuantity" data-food="${foodName}">
                                <i class="bi bi-pencil me-2"></i>Enter custom quantity
                            </a>
                        `;
                        
                        // Handle quantity selection
                        document.querySelectorAll('.quantity-option').forEach(qtyItem => {
                            qtyItem.addEventListener('click', (e) => {
                                e.preventDefault();
                                const quantity = e.currentTarget.dataset.quantity;
                                const food = e.currentTarget.dataset.food;
                                foodInput.value = `${quantity} of ${food}`;
                                suggestionsDiv.style.display = 'none';
                            });
                        });
                        
                        // Handle custom quantity
                        document.getElementById('customQuantity')?.addEventListener('click', (e) => {
                            e.preventDefault();
                            const food = e.currentTarget.dataset.food;
                            foodInput.value = food;
                            suggestionsDiv.style.display = 'none';
                            foodInput.focus();
                        });
                    });
                });
            } else {
                suggestionsDiv.style.display = 'none';
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }, 300);
});

// Hide suggestions when clicking outside
document.addEventListener('click', (e) => {
    if (!foodInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
        suggestionsDiv.style.display = 'none';
    }
});

// Load summary on page load
document.addEventListener('DOMContentLoaded', () => {
    loadDailySummary();
});

// Log food form submission
document.getElementById('logFoodForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const result = document.getElementById('logResult');
    const submitBtn = e.target.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging...';
    result.innerHTML = '<div class="alert alert-info">Looking up food nutrition...</div>';
    
    try {
        const response = await fetch('../handlers/logFood.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        let data;
        
        try {
            data = JSON.parse(text);
        } catch {
            result.innerHTML = `<div class="alert alert-danger">Server error. Please try again.<pre class="small">${text}</pre></div>`;
            return;
        }
        
        if (data.success) {
            result.innerHTML = `
                <div class="alert alert-success">
                    <strong>✓ Logged!</strong> ${data.food_name} - ${data.calories} cal
                </div>
            `;
            e.target.reset();
            loadDailySummary(); // Refresh summary
        } else {
            result.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
        }
    } catch (error) {
        result.innerHTML = `<div class="alert alert-danger">Request failed: ${error.message}</div>`;
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Log Food';
    }
});

// Load daily summary
async function loadDailySummary() {
    try {
        const response = await fetch('../handlers/getDailySummary.php');
        const data = await response.json();
        
        if (data.success) {
            // Update summary cards
            document.getElementById('caloriesCount').textContent = data.summary.calories_consumed;
            document.getElementById('proteinCount').textContent = data.summary.protein_g + 'g';
            document.getElementById('carbsCount').textContent = data.summary.carbs_g + 'g';
            document.getElementById('fatCount').textContent = data.summary.fat_g + 'g';
            
            // Update meals list
            const mealsList = document.getElementById('mealsList');
            if (data.meals && data.meals.length > 0) {
                mealsList.innerHTML = data.meals.map(meal => `
                    <div class="card bg-secondary mb-2 meal-item">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${meal.food_name}</strong>
                                    <span class="badge bg-primary badge-meal ms-2">${meal.meal_type}</span>
                                    <br>
                                    <small class="text-muted">
                                        ${meal.calories} cal • P: ${meal.protein_g}g • C: ${meal.carbs_g}g • F: ${meal.fat_g}g
                                    </small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">${meal.time || ''}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                mealsList.innerHTML = `
                    <p class="text-muted text-center py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        No meals logged yet today
                    </p>
                `;
            }
        }
    } catch (error) {
        console.error('Failed to load summary:', error);
    }
}
</script>

<?php include '../template/footer.php'; ?>
