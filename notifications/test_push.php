<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/fcm_helper.php';

$credentials_path = __DIR__ . '/firebase/firebase-credentials.json';

if (!file_exists($credentials_path)) {
    die("Error: firebase-credentials.json ვერ მოიძებნა ამ გზაზე: " . $credentials_path);
}

$json_data = json_decode(file_get_contents($credentials_path), true);
$project_id = $json_data['project_id'] ?? null;

if (!$project_id) {
    die("Error: პროექტის ID (project_id) ვერ მოიძებნა JSON ფაილში!");
}

echo "სატესტო შეტყობინების გაგზავნა...<br>";

// პირდაპირ ვაგზავნით სატესტო მესიჯს urgent_alerts თემაზე
$res = send_fcm_topic(
    'urgent_alerts', 
    'ტესტირება 🔥', 
    'თუ ამას ხედავთ, სერვერი და Firebase მუშაობს იდეალურად!', 
    $credentials_path, 
    $project_id
);

echo "<pre>";
print_rules_result($res);
echo "</pre>";

function print_rules_result($res) {
    if ($res['success']) {
        echo "<b style='color:green;'>წარმატებით გაიგზავნა Firebase-ში!</b><br>";
    } else {
        echo "<b style='color:red;'>გაგზავნა ჩაიშალა!</b><br>";
    }
    echo "Firebase-ის პასუხი:<br>";
    print_r($res['response']);
}
?>