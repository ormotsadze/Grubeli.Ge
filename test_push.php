<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. განსაზღვრე გზა htdocs-ის გარეთ
// თუ შენი htdocs არის /home/user/public_html/htdocs/
// მაშინ დონით ასვლა ხდება dirname-ით
$credentials_path = dirname(__DIR__, 1) . '/firebase/firebase-credentials.json';

echo "🔍 ვეძებ ფაილს მისამართზე: " . $credentials_path . "<br>";

if (!file_exists($credentials_path)) {
    echo "❌ შეცდომა: ფაილი ფიზიკურად არ არსებობს ამ მისამართზე.<br>";
    
    // ვნახოთ საერთოდ რა ფოლდერებია იმ გარემოში
    echo "საქაღალდის შიგთავსი: <pre>";
    print_r(scandir(dirname($credentials_path)));
    echo "</pre>";
    die();
} else {
    echo "✅ ფაილი ნაპოვნია!<br>";
}

if (!is_readable($credentials_path)) {
    die("❌ შეცდომა: ფაილი არსებობს, მაგრამ PHP-ს არ აქვს მისი წაკითხვის უფლება (Permissions).");
}

require_once __DIR__ . '/fcm_helper.php';

// აქ უკვე შენი პროექტის ID ჩაწერე
$project_id = 'facelist-d53a0'; 

$result = send_fcm_topic(
    'urgent_alerts',
    'ტესტი',
    'ფაილი ვიპოვე და ვაგზავნი!',
    $credentials_path,
    $project_id
);

echo "🚀 შედეგი: <pre>";
print_r($result);
echo "</pre>";