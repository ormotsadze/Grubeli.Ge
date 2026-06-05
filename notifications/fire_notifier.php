<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/fcm_helper.php';

// 1. ვიღებთ გასაღებს .env ფაილიდან (შეცვალე 'NASA_MAP_KEY' იმ სახელით, რაც .env-ში გიწერია)
$nasa_map_key = $_ENV['NASA_MAP_KEY'] ?? $_SERVER['NASA_MAP_KEY'] ?? (function_exists('env') ? env('NASA_MAP_KEY') : getenv('NASA_MAP_KEY')); 

// 2. გადავცემთ გასაღებს ფუნქციას
$fire_data = checkFireRisk($nasa_map_key); 

// 3. ვამოწმებთ, არის თუ არა საერთოდ აქტიური ხანძარი ქვეყანაში
if ($fire_data && $fire_data['active'] && !empty($fire_data['points'])) {
    
    // ვიღებთ ყველაზე ბოლო აღმოჩენილ ხანძრის წერტილს (პირველს მასივიდან)
    $latest_fire = $fire_data['points'][0];
    
    $last_fire_file = __DIR__ . '/last_fire_id.txt';
    $last_fire_val = file_exists($last_fire_file) ? file_get_contents($last_fire_file) : '';

    // სპამის თავიდან ასაცილებლად ვქმნით უნიკალურ ხელმოწერას: რეგიონი + დრო
    $current_fire_signature = $latest_fire['region'] . '_' . $latest_fire['time'];

    if ($last_fire_val !== $current_fire_signature) {
        
        $region_en = $latest_fire['region']; 
        
        $title = "Fire Alert! 🔥";
        $body = "Active wildfire detected in Georgia. Region: " . $region_en . ". Stay safe!";
        
        $credentials_path = __DIR__ . '/firebase/firebase-credentials.json';
        
        if (file_exists($credentials_path)) {
            $json_data = json_decode(file_get_contents($credentials_path), true);
            
            // ვაგზავნით აპლიკაციის მუშა არხზე: 'urgent_alerts'
            $res = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $json_data['project_id']);
            
            if ($res['success']) {
                file_put_contents($last_fire_file, $current_fire_signature);
                error_log("Fire notification sent successfully.");
            } else {
                error_log("Fire notification failed: " . json_encode($res));
            }
        } else {
            error_log("Firebase credentials file not found.");
        }
    }
}
?>