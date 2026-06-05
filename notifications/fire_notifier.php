<?php
// შეცდომების დამალვა მუშა რეჟიმში
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../functions.php'; 
require_once __DIR__ . '/fcm_helper.php';

// კრონლოგი (მუშაობს!)
file_put_contents(__DIR__ . '/fire_cron_log.txt', "Fire check ran at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// API გასაღები
$map_key = getenv('NASA_MAP_KEY'); 

// თუ getenv ვერ კითხულობს კრონის გამო, პირდაპირ ჩასვი სტრინგად:
// $map_key = "შენი_ნასას_გასაღები";

$fire_data = checkFireRisk($map_key, false);

if ($fire_data['active'] && !empty($fire_data['points'])) {
    $last_fire_file = __DIR__ . '/last_fire_hash.txt';
    $last_fire_hash = file_exists($last_fire_file) ? file_get_contents($last_fire_file) : '';

    // ავიღოთ ყველაზე ახალი ხანძრის მონაცემები
    $latest_fire = $fire_data['points'][0];
    
    // ვქმნით უნიკალურ ჰეშს დროის, რეგიონისა და კოორდინატებისგან
    $current_fire_hash = md5($latest_fire['time'] . $latest_fire['region'] . $latest_fire['lat']);

    // თუ ეს კონკრეტული ხანძარი ჯერ არ გაგვიგზავნია
    if ($current_fire_hash !== $last_fire_hash) {
        
        $region = $latest_fire['region']; 
        $title = "Fire Alert! 🔥";
        $body = "Active fire detected in " . $region . ". Please check the app for map details.";
        
        $credentials_path = __DIR__ . '/firebase/firebase-credentials.json';
        $json_data = json_decode(file_get_contents($credentials_path), true);
        
        // შეტყობინების გაგზავნა
        $res = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $json_data['project_id']);
        
        // თუ წარმატებით გაიგზავნა, დავიმახსოვროთ ჰეში
        if ($res['success']) {
            file_put_contents($last_fire_file, $current_fire_hash);
        }
    }
}
// else ბლოკი ამოღებულია, რადგან ჰეშის შედარებისას ის საჭირო აღარ არის და სპამსაც აგვაცილებს
?>