<?php
/**
 * NutritionService - Wrapper for Nutritionix API (Food Tracking Only)
 */

class NutritionService {
    private $appId;
    private $appKey;
    private $baseUrl;
    
    public function __construct() {
        $this->appId = NUTRITIONIX_APP_ID;
        $this->appKey = NUTRITIONIX_APP_KEY;
        $this->baseUrl = NUTRITIONIX_API_URL;
    }
    
    /**
     * Search for food using natural language
     * Example: "1 cup of rice" or "chicken breast 150g"
     */
    public function getNutritionInfo($query) {
        $url = $this->baseUrl . '/natural/nutrients';
        
        $data = json_encode(['query' => $query]);
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'x-app-id: ' . $this->appId,
                'x-app-key: ' . $this->appKey,
                'Content-Type: application/json'
            ],
            // SSL options for development environment
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            error_log("Nutritionix API CURL error: {$curlError}");
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log("Nutritionix API error: HTTP {$httpCode}, Response: {$response}");
            return null;
        }
        
        $result = json_decode($response, true);
        
        if (empty($result['foods'])) {
            return null;
        }
        
        // Return first food item with nutrition data
        $food = $result['foods'][0];
        return [
            'food_name' => $food['food_name'] ?? '',
            'calories' => round($food['nf_calories'] ?? 0),
            'protein_g' => round($food['nf_protein'] ?? 0, 2),
            'carbs_g' => round($food['nf_total_carbohydrate'] ?? 0, 2),
            'fat_g' => round($food['nf_total_fat'] ?? 0, 2),
            'serving_size' => $food['serving_unit'] ?? ''
        ];
    }
    
    /**
     * Search foods (instant search for autocomplete)
     */
    public function searchFoods($query) {
        $url = $this->baseUrl . '/search/instant?query=' . urlencode($query);
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'x-app-id: ' . $this->appId,
                'x-app-key: ' . $this->appKey
            ],
            // SSL options for development environment
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            error_log("Nutritionix Search API CURL error: {$curlError}");
            return [];
        }
        
        $result = json_decode($response, true);
        return $result['common'] ?? [];
    }
}
