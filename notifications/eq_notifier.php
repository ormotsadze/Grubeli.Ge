<?php
error_reporting(E_ALL); // დროებით ჩართე, რომ თუ რამე შეცდომაა, ლოგში დაინახო
ini_set('display_errors', 1);

require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/fcm_helper.php';

// მივიღოთ მონაცემები (სატესტოდ შეგიძლია დაამატო true)
$eq_data = checkEarthquakeRisk(); 

if ($eq_data['active']) {
    $last_eq_file = __DIR__ . '/last_eq_id.txt';
    $last_eq_time = file_exists($last_eq_file) ? file_get_contents($last_eq_file) : '';

    if ($eq_data['time'] !== $last_eq_time) {
        // რადგან ინგლისურზე გადავდივართ, თარგმნის ფუნქცია შესაძლოა საჭირო არ იყოს 
        // ან პირდაპირ USGS-ის Place-ს ვიყენებთ (რომელიც ისედაც ინგლისურია)
        $place = $eq_data['place']; 
        $mag = $eq_data['mag'];
        
        $title = "Earthquake Alert! ⚠️";
        $body = "Magnitude " . $mag . " earthquake detected. Location: " . $place;
        
        $credentials_path = __DIR__ . '/firebase/firebase-credentials.json';
        
        if (file_exists($credentials_path)) {
            $json_data = json_decode(file_get_contents($credentials_path), true);
            
            $res = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $json_data['project_id']);
            
            if ($res['success']) {
                file_put_contents($last_eq_file, $eq_data['time']);
                error_log("Earthquake notification sent successfully.");
            } else {
                error_log("Earthquake notification failed: " . json_encode($res));
            }
        } else {
            error_log("Firebase credentials file not found.");
        }
    }
}
?>