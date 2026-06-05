<?php
require_once __DIR__ . '/fcm_helper.php';
file_put_contents(__DIR__ . '/cron_log.txt', "Cron ran at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

$credentials_path = __DIR__ . '/firebase/firebase-credentials.json';

if (!file_exists($credentials_path)) {
    die("FCM Error: Credentials file not found.");
}

$json_data = json_decode(file_get_contents($credentials_path), true);
$project_id = $json_data['project_id'];

date_default_timezone_set('Asia/Tbilisi');
$hour = (int)date('H');

// დილის 9 საათი
if ($hour == 9) {
   $title = "Morning Update 🌤️";
   $body = "Check the weather forecast for today on Grubeli.ge";
} 
// საღამოს 19 საათი
elseif ($hour == 19) {
   $title = "Evening Update 🌙";
    $body = "Plan your evening with the latest weather at Grubeli.ge";
} else {
    // თუ კრონი შეცდომით სხვა დროს გაეშვა, არ გავაგზავნოთ არაფერი
    exit("Not the right time for notification.");
}

$response = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $project_id);
print_r($response);
?>