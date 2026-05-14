<?php
// 1. მივიერთოთ ჰელპერი (იგივე პაპკაშია)
require_once __DIR__ . '/fcm_helper.php';

// 2. მისამართი: __DIR__ არის /notifications, ამიტომ პირდაპირ შიგნით შევდივართ
$credentials_path = __DIR__ . '/firebase/firebase-credentials.json';

if (!file_exists($credentials_path)) {
    die("FCM Error: Credentials file not found at " . $credentials_path);
}

$json_data = json_decode(file_get_contents($credentials_path), true);
$project_id = $json_data['project_id'];

date_default_timezone_set('Asia/Tbilisi');
$hour = (int)date('H');

if ($hour < 12) {
    $title = "დილა მშვიდობისა! 🌤️";
    $body = "გაინტერესებთ როგორი ამინდი იქნება დღეს? შემოიხედეთ Grubeli.ge-ზე.";
} else {
    $title = "საღამო მშვიდობისა! 🌙";
    $body = "დაგეგმე საღამო ამინდის შესაბამისად! შემოიხედეთ Grubeli.ge-ზე.";
}

$response = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $project_id);
print_r($response);