# Food & Exercise Tracking Setup

## Files Created

### Database
- `database/migrations/2024_11_05_000001_create_food_tracking_tables.php` - Migration for tracking tables

### Services
- `services/NutritionService.php` - Nutritionix API wrapper

### Handlers
- `handlers/logFood.php` - Log food intake
- `handlers/logExercise.php` - Log exercise activity  
- `handlers/getDailySummary.php` - Get daily calorie/macro summary

### Configuration
- `conf/conf.php` - Updated with Nutritionix API credentials

### Tests
- `tests/test_nutrition_api.html` - Test page for API integration

## Setup Steps

### 1. Run Database Migration

```bash
cd c:\Apache24\htdocs\Gymly
php scripts/migrate.php
```

This creates these tables:
- `food_cache` - Caches API food lookups
- `user_meals` - Logs user meal entries
- `exercise_cache` - Caches API exercise lookups
- `user_exercises` - Logs user exercise entries
- `user_daily_summary` - Aggregated daily stats

### 2. Test API Integration

Open in browser:
```
http://localhost/Gymly/tests/test_nutrition_api.html
```

Test queries:
- Food: "1 cup of rice", "chicken breast 150g", "2 eggs"
- Exercise: "ran 30 minutes", "lifted weights 45 minutes"

### 3. Add User Stats (Optional but Recommended)

For accurate exercise calorie calculations, add these columns to users table:

```sql
ALTER TABLE users 
ADD COLUMN gender VARCHAR(10) DEFAULT 'male',
ADD COLUMN weight_kg DECIMAL(5,2) DEFAULT 70,
ADD COLUMN height_cm DECIMAL(5,2) DEFAULT 170,
ADD COLUMN age INT DEFAULT 30;
```

## API Usage Examples

### Log Food (JavaScript)

```javascript
const formData = new FormData();
formData.append('query', '1 cup of rice');
formData.append('meal_type', 'lunch'); // breakfast/lunch/dinner/snack

fetch('/handlers/logFood.php', {
    method: 'POST',
    body: formData
})
.then(r => r.json())
.then(data => {
    console.log(data.food); // {food_name, calories, protein_g, carbs_g, fat_g}
});
```

### Log Exercise (JavaScript)

```javascript
const formData = new FormData();
formData.append('query', 'ran 3 miles');

fetch('/handlers/logExercise.php', {
    method: 'POST',
    body: formData
})
.then(r => r.json())
.then(data => {
    console.log(data.exercise); // {exercise_name, duration_minutes, calories_burned}
});
```

### Get Daily Summary (JavaScript)

```javascript
fetch('/handlers/getDailySummary.php?date=2024-11-05')
.then(r => r.json())
.then(data => {
    console.log(data.summary); 
    // {calories_consumed, calories_burned, net_calories, protein_g, carbs_g, fat_g}
    console.log(data.meals);    // Array of meal entries
    console.log(data.exercises); // Array of exercise entries
});
```

## API Rate Limits

- **Free Tier**: 500 requests/day
- **Caching**: Results cached for 7 days to reduce API calls
- **Estimated Usage**: ~15 users logging 3x/day = 45 requests/day

## Integration with Track Page

Add to `pages/track.php`:

```php
// Fetch today's summary
fetch('/handlers/getDailySummary.php')
    .then(r => r.json())
    .then(data => {
        document.getElementById('caloriesConsumed').textContent = data.summary.calories_consumed;
        document.getElementById('caloriesBurned').textContent = data.summary.calories_burned;
        // ... render meals and exercises
    });
```

## Troubleshooting

### Error: "Food not found or API error"
- Check API credentials in `conf/conf.php`
- Verify query format (e.g., "1 cup rice" not just "rice")
- Check Apache error log: `C:\Apache24\logs\error.log`

### Error: "Table doesn't exist"
- Run migration: `php scripts/migrate.php`
- Check database connection in `conf/conf.php`

### API Rate Limit Exceeded
- Caching reduces calls automatically
- Check `food_cache` and `exercise_cache` tables
- Consider upgrading Nutritionix plan if needed

## Next Steps

1. Update `pages/track.php` with food/exercise logging UI
2. Add meal type selection (breakfast/lunch/dinner/snack)
3. Create dashboard widgets showing daily progress
4. Add weekly/monthly summary charts
5. Implement goals and recommendations based on calorie balance
