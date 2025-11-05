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
        <h1 class="display-4 fw-bold mb-3 text-white">Nutrition Tracking</h1>
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
                            <div style="position: relative;">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="query" 
                                    id="foodQuery"
                                    placeholder="e.g., chicken breast, rice, apple"
                                    autocomplete="off"
                                >
                                <div id="foodSuggestions" class="list-group" style="position: absolute; z-index: 9999; width: 100%; background: #1a1a1a; border: 2px solid #0d6efd; max-height: 400px; overflow-y: auto; display: none;"></div>
                            </div>
                            <small class="form-text text-muted">
                                Search and add foods one at a time
                            </small>
                        </div>
                        
                        <!-- Foods Added to Current Meal -->
                        <div id="mealItems" class="mb-3" style="display: none;">
                            <label class="form-label text-white">Foods in this meal:</label>
                            <div id="mealItemsList" class="list-group mb-2"></div>
                            <div class="text-end">
                                <small class="text-muted">Total: <span id="mealTotal">0</span> cal</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Meal Type</label>
                            <select class="form-select" name="meal_type" id="mealType">
                                <option value="breakfast">Breakfast</option>
                                <option value="lunch">Lunch</option>
                                <option value="dinner">Dinner</option>
                                <option value="snack" selected>Snack</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100" id="logMealBtn" disabled>
                            <i class="bi bi-check-circle"></i> Log Meal
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
                                    <h5 class="mb-1">Cal</h5>
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

#foodSuggestions .list-group-item small {
    color: #adb5bd !important;
}

.form-select option {
    background-color: #212529;
    color: #ffffff;
}
</style>

<script>
// Multiple foods per meal tracking
let mealItems = [];
let searchTimeout;
const foodInput = document.getElementById('foodQuery');
const suggestionsDiv = document.getElementById('foodSuggestions');
const mealItemsDiv = document.getElementById('mealItems');
const mealItemsList = document.getElementById('mealItemsList');
const mealTotalSpan = document.getElementById('mealTotal');
const logMealBtn = document.getElementById('logMealBtn');

// Initialize suggestions div as hidden
suggestionsDiv.style.display = 'none';

// Update meal totals and display
function updateMealDisplay() {
    if (mealItems.length === 0) {
        mealItemsDiv.style.display = 'none';
        logMealBtn.disabled = true;
        return;
    }
    
    mealItemsDiv.style.display = 'block';
    logMealBtn.disabled = false;
    
    const totalCalories = mealItems.reduce((sum, item) => sum + item.calories, 0);
    mealTotalSpan.textContent = totalCalories;
    
    mealItemsList.innerHTML = mealItems.map((item, index) => `
        <div class="list-group-item bg-secondary text-white d-flex justify-content-between align-items-center">
            <div>
                <strong>${item.food_name}</strong>
                <br>
                <small class="text-muted">
                    ${item.serving_size} • ${item.calories} cal • 
                    P: ${item.protein_g}g C: ${item.carbs_g}g F: ${item.fat_g}g
                </small>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeFood(${index})">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `).join('');
}

// Remove food from meal
window.removeFood = function(index) {
    mealItems.splice(index, 1);
    updateMealDisplay();
}

// Add food to meal
async function addFoodToMeal(foodName, servingSize = '1 serving') {
    try {
        const response = await fetch('../handlers/getFoodNutrition.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ food_name: foodName, serving_size: servingSize })
        });
        
        const data = await response.json();
        
        if (data.success) {
            mealItems.push(data.nutrition);
            updateMealDisplay();
            foodInput.value = '';
            document.getElementById('logResult').innerHTML = '';
        } else {
            document.getElementById('logResult').innerHTML = `
                <div class="alert alert-warning">${data.message}</div>
            `;
        }
    } catch (error) {
        console.error('Error adding food:', error);
    }
}

// Food autocomplete - FASTER (100ms delay)
foodInput.addEventListener('input', (e) => {
    const query = e.target.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    // REDUCED from 300ms to 100ms for faster search
    searchTimeout = setTimeout(async () => {
        try {
            // Show loading
            suggestionsDiv.innerHTML = '<div class="list-group-item bg-dark text-white">Searching...</div>';
            suggestionsDiv.style.display = 'block';
            
            const response = await fetch(`../handlers/searchFood.php?query=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success && data.foods && data.foods.length > 0) {
                suggestionsDiv.innerHTML = data.foods.map(food => `
                    <a href="#" class="list-group-item list-group-item-action bg-dark text-white border-secondary food-suggestion" 
                       data-food="${food.food_name}" data-id="${food.food_id || ''}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${food.food_name}</strong>
                                <br>
                                <small class="text-muted">Click to see serving options</small>
                            </div>
                            <i class="bi bi-plus-circle text-primary"></i>
                        </div>
                    </a>
                `).join('');
                suggestionsDiv.style.display = 'block';
                
                document.querySelectorAll('.food-suggestion').forEach(item => {
                    item.addEventListener('click', async (e) => {
                        e.preventDefault();
                        const foodName = e.currentTarget.dataset.food;
                        const foodId = e.currentTarget.dataset.id;
                        
                        suggestionsDiv.innerHTML = '<div class="list-group-item bg-dark text-white">Loading serving options...</div>';
                        suggestionsDiv.style.display = 'block';
                        
                        if (foodId && foodId !== '') {
                            try {
                                const servingResponse = await fetch(`../handlers/getServingOptions.php?food_id=${foodId}&food_name=${encodeURIComponent(foodName)}`);
                                const servingData = await servingResponse.json();
                                
                                if (servingData.success && servingData.servings && servingData.servings.length > 0) {
                                    showServingOptions(foodName, servingData.servings);
                                } else {
                                    showDefaultServingOptions(foodName);
                                }
                            } catch (error) {
                                console.error('Error fetching servings:', error);
                                showDefaultServingOptions(foodName);
                            }
                        } else {
                            showDefaultServingOptions(foodName);
                        }
                    });
                });
            } else {
                suggestionsDiv.innerHTML = '<div class="list-group-item bg-dark text-white">No results found</div>';
                setTimeout(() => {
                    suggestionsDiv.style.display = 'none';
                }, 2000);
            }
        } catch (error) {
            console.error('Search error:', error);
            suggestionsDiv.innerHTML = '<div class="list-group-item bg-dark text-danger">Search failed</div>';
        }
    }, 100);
});

function showServingOptions(foodName, servings) {
    suggestionsDiv.innerHTML = `
        <div class="list-group-item bg-primary text-white border-secondary">
            <strong>📏 Select serving for ${foodName}:</strong>
        </div>
        ${servings.map((serving, idx) => `
            <a href="#" class="list-group-item list-group-item-action bg-dark border-secondary serving-option"
               data-food="${foodName}" data-index="${idx}" style="color: #ffffff !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${serving.description}</strong>
                        <br>
                        <small style="color: #aaa;">
                            ${serving.calories} cal • P:${serving.protein}g C:${serving.carbs}g F:${serving.fat}g
                        </small>
                    </div>
                    <i class="bi bi-check-circle text-success"></i>
                </div>
            </a>
        `).join('')}
    `;
    
    suggestionsDiv.style.display = 'block';
    suggestionsDiv.style.maxHeight = '400px';
    suggestionsDiv.style.overflowY = 'auto';
    
    window.currentServings = servings;
    
    document.querySelectorAll('.serving-option').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const foodName = e.currentTarget.dataset.food;
            const servingIndex = e.currentTarget.dataset.index;
            const serving = servings[servingIndex];
            
            mealItems.push({
                food_name: foodName,
                serving_size: serving.description,
                calories: serving.calories,
                protein_g: serving.protein,
                carbs_g: serving.carbs,
                fat_g: serving.fat
            });
            
            updateMealDisplay();
            suggestionsDiv.style.display = 'none';
            foodInput.value = '';
        });
    });
}

function showDefaultServingOptions(foodName) {
    const quantities = ['1 serving', '1 cup', '100g', '1 piece', '1 medium', '2 servings'];
    suggestionsDiv.innerHTML = `
        <div class="list-group-item bg-warning text-dark border-secondary">
            <strong>📏 Select quantity for ${foodName}:</strong>
            <br><small>Using default servings (nutrition will be estimated)</small>
        </div>
        ${quantities.map(qty => `
            <a href="#" class="list-group-item list-group-item-action bg-dark border-secondary quantity-option"
               data-quantity="${qty}" data-food="${foodName}" style="color: #ffffff !important;">
                <i class="bi bi-circle me-2"></i><strong>${qty}</strong>
            </a>
        `).join('')}
    `;
    
    suggestionsDiv.style.display = 'block';
    suggestionsDiv.style.maxHeight = '400px';
    suggestionsDiv.style.overflowY = 'auto';
    
    document.querySelectorAll('.quantity-option').forEach(qtyItem => {
        qtyItem.addEventListener('click', (e) => {
            e.preventDefault();
            const quantity = e.currentTarget.dataset.quantity;
            const food = e.currentTarget.dataset.food;
            addFoodToMeal(food, quantity);
            suggestionsDiv.style.display = 'none';
        });
    });
}

// Hide suggestions when clicking outside
document.addEventListener('click', (e) => {
    if (!foodInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
        suggestionsDiv.style.display = 'none';
    }
});

// Log meal (all foods added)
document.getElementById('logFoodForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (mealItems.length === 0) {
        alert('Please add at least one food to the meal');
        return;
    }
    
    const result = document.getElementById('logResult');
    const submitBtn = logMealBtn;
    const mealType = document.getElementById('mealType').value;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging...';
    result.innerHTML = '<div class="alert alert-info">Logging meal...</div>';
    
    try {
        // Log each food item
        for (const item of mealItems) {
            const response = await fetch('../handlers/logFood.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    food_name: item.food_name,
                    calories: item.calories,
                    protein_g: item.protein_g,
                    carbs_g: item.carbs_g,
                    fat_g: item.fat_g,
                    serving_size: item.serving_size,
                    meal_type: mealType
                })
            });
            
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to log food');
            }
        }
        
        const totalCal = mealItems.reduce((sum, item) => sum + item.calories, 0);
        result.innerHTML = `
            <div class="alert alert-success">
                <strong>Logged!</strong> ${mealItems.length} items - ${totalCal} cal total
            </div>
        `;
        
        // Reset meal
        mealItems = [];
        updateMealDisplay();
        loadDailySummary();
        
    } catch (error) {
        result.innerHTML = `
            <div class="alert alert-danger">
                <strong>Error:</strong> ${error.message}
            </div>
        `;
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Log Meal';
    }
});

// Load summary on page load
document.addEventListener('DOMContentLoaded', () => {
    loadDailySummary();
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
