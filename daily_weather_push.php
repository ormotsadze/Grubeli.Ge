<?php
require_once __DIR__ . '/fcm_helper.php';

$credentials_path = dirname(__DIR__) . '/firebase/firebase-credentials.json';
$json_data = json_decode(file_get_contents($credentials_path), true);
$project_id = $json_data['project_id'];

// განვსაზღვროთ ტექსტი დროის მიხედვით
date_default_timezone_set('Asia/Tbilisi');
$hour = (int)date('H');

if ($hour < 12) {
    $title = "დილა მშვიდობისა! 🌤️";
    $body = "გაინტერესებთ როგორი ამინდი იქნება დღეს? შემოიხედეთ Grubeli.ge-ზე.";
} else {
    $title = "საღამო მშვიდობისა! 🌙";
    $body = "დაგეგმე საღამო ამინდის შესაბამისად! შემოიხედეთ Grubeli.ge-ზე.";
}

// აგზავნის ნოტიფიკაციას. Topic: urgent_alerts (ან daily_weather, თუ ანდროიდში დაამატე)
send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $project_id);