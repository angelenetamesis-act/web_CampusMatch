<?php
// src/Helpers/ModerationHelper.php

class ModerationHelper {
    public static function checkMessage($text) {
        // 1. LOCAL FILTER (Immediate check for local slang/student slurs)
        // Add more local terms here as needed
        $badWords = ['bitch', 'gago', 'puta', 'tarantado', 'hayop', 'bilat', 'piste', 'suicide']; 
        foreach ($badWords as $word) {
            if (stripos($text, $word) !== false) {
                return true; // Violation found locally
            }
        }

        // 2. API FILTER (For complex English profanity and variations)
        $apiKey = 'qj7TGtoO6Wy4diIEblfrcaarWNfvnIBCWlAQ1swp'; 
        $url = "https://api.api-ninjas.com/v1/profanityfilter?text=" . urlencode($text);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Api-Key: ' . $apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // If the API call is successful (HTTP 200)
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            return $result['has_profanity'] ?? false;
        }

        // Default to false if API is unreachable to prevent locking the app,
        // but the Local Filter above will have already caught the basics.
        return false; 
    }
}