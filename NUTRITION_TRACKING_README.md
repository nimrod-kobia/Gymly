# Nutrition Tracking System - Gymly

## Overview
Complete food tracking system integrated with Nutritionix API for automatic nutrition lookup.

## Features
- 🥗 **Natural Language Food Logging** - Just type "1 cup of rice" or "chicken breast 150g"
- 🔥 **Real-time Calorie Tracking** - Instant daily calorie and macro totals
- 📊 **Daily Summary Dashboard** - View today's meals, calories, protein, carbs, and fat
- 📈 **7-Day Statistics** - Weekly averages displayed on profile page
- ⚡ **Smart Caching** - API responses cached for 7 days to minimize external calls (500/day limit)
- 🔒 **User-specific** - All nutrition data is private and linked to logged-in user

## Database Tables

### `food_cache`
Stores Nutritionix API responses to reduce API calls
- `query` - User's search query (e.g., "1 cup rice")
- `food_name`, `calories`, `protein_g`, `carbs_g`, `fat_g`, `serving_size`
- `cached_at` - Timestamp (auto-expires after 7 days)

### `user_meals`
Logs individual meals for each user
- `user_id` - Foreign key to users table
- `food_name`, `calories`, `protein_g`, `carbs_g`, `fat_g`, `serving_size`
- `meal_type` - breakfast, lunch, dinner, or snack
- `logged_at` - Timestamp

### `user_daily_summary`
Aggregated daily nutrition totals
- `user_id`, `summary_date`
- `calories_consumed`, `protein_g`, `carbs_g`, `fat_g`, `meals_count`
- `updated_at` - Auto-updated when meals are logged

## API Configuration

### Nutritionix API Credentials
Located in `conf/conf.php`:
```php
define('NUTRITIONIX_APP_ID', 'a1516028');
define('NUTRITIONIX_APP_KEY', 'a85ab2f12f36968f9b637d300f308aa9');
define('NUTRITIONIX_API_URL', 'https://trackapi.nutritionix.com/v2');
```

**Rate Limit**: 500 requests/day (free tier)  
**Caching Strategy**: 7-day cache to stay well under limit

## Files Structure

```
handlers/
  logFood.php          - Logs meals, queries API, updates daily summary
  getDailySummary.php  - Returns today's nutrition totals + meal history

services/
  NutritionService.php - API wrapper class

pages/
  nutrition.php        - Main tracking page (log food, view stats)
  profile.php          - Shows nutrition stats widget

database/migrations/
  2024_11_05_000002_create_food_tracking_only.php

scripts/
  migrate_simple.php   - PostgreSQL migration runner
```

## Usage

### 1. Migration (Already Done)
```bash
php scripts/migrate_simple.php
```

### 2. Access Nutrition Tracking
- Navigate to **Tracking → Nutrition** in the navbar
- Or visit: `http://localhost/Gymly/index.php?page=nutrition`

### 3. Log Food
1. Type food query: "2 eggs and toast" or "chicken breast 200g"
2. Select meal type (breakfast, lunch, dinner, snack)
3. Click **Log Food**
4. API fetches nutrition data automatically
5. Meal is saved to database
6. Daily summary updates instantly

### 4. View Stats
- **Today's Summary**: Displayed on nutrition tracking page
- **Profile Stats**: 7-day averages shown on profile page
- **Meal History**: Recent meals listed with timestamps

## API Examples

### Natural Language Queries
- "1 cup of rice"
- "chicken breast 150g"
- "2 eggs and toast"
- "large apple"
- "protein shake with banana"

### Response Format
```json
{
  "food_name": "Rice",
  "calories": 206,
  "protein_g": 4.3,
  "carbs_g": 44.5,
  "fat_g": 0.4,
  "serving_size": "1 cup"
}
```

## Navigation

The nutrition tracking page is accessible via:

1. **Main Navbar** → Tracking dropdown → "🥗 Nutrition"
2. **Profile Page** → "Track Food" button in nutrition stats widget

## Security

- ✅ Session-based authentication required
- ✅ User-specific data (can't view other users' meals)
- ✅ CSRF protection on forms
- ✅ Prepared statements (SQL injection protection)
- ✅ Input validation and sanitization

## Development Notes

### SSL Certificate Handling
Development environment has SSL verification disabled in `NutritionService.php`:
```php
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_SSL_VERIFYHOST => false
```

**⚠️ Production**: Remove these options or use proper SSL certificates

### PostgreSQL-Specific SQL
- Uses `SERIAL` for auto-increment
- `TO_CHAR()` for date formatting
- `CURRENT_TIMESTAMP` for timestamps
- `INTERVAL '7 days'` for date arithmetic

## Future Enhancements

- [ ] Meal templates (save favorite meals for quick logging)
- [ ] Calorie goals (set target calories per day)
- [ ] Weekly/monthly charts (Chart.js visualization)
- [ ] Export data to CSV
- [ ] BMI calculator integration
- [ ] Barcode scanner for packaged foods

## Troubleshooting

### "Not authenticated" error
- Ensure user is logged in
- Check `$_SESSION['user_id']` is set

### "CURL error: SSL certificate problem"
- Already fixed with SSL options in NutritionService.php
- If still occurs, check PHP cURL extension is enabled

### "No foods found" from API
- Check API credentials in conf/conf.php
- Verify query is specific (e.g., "apple" vs "1 apple")
- Check rate limit (500/day)

### Database errors
- Ensure migrations ran successfully
- Check PostgreSQL connection in conf/conf.php
- Verify table names match: `food_cache`, `user_meals`, `user_daily_summary`

## Credits

**Nutrition Data**: Powered by [Nutritionix API](https://www.nutritionix.com/business/api)  
**Database**: PostgreSQL hosted on Neon  
**Framework**: Custom PHP with Bootstrap 5
