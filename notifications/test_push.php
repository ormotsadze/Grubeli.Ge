<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// სწორი მისამართი: /notifications/firebase/firebase-credentials.json
$credentials_path = __DIR__ . '/firebase/firebase-credentials.json';

echo "🔍 ვეძებ ფაილს: " . $credentials_path . "<br>";

if (!file_exists($credentials_path)) {
    die("❌ შეცდომა: ფაილი ვერ მოიძებნა!");
} else {
    echo "✅ ფაილი ნაპოვნია!<br>";
}

// აუცილებლად დახრილი ხაზით
require_once __DIR__ . '/fcm_helper.php';

// პროექტის ID (ამოვიღოთ პირდაპირ ფაილიდან)
$json_data = json_decode(file_get_contents($credentials_path), true);
$project_id = $json_data['project_id'];

echo "🚀 ვცდილობ ნოტიფიკაციის გაგზავნას (Project ID: $project_id)...<br>";

// ფუნქციის გამოძახება
$result = send_fcm_topic('urgent_alerts', 'ტესტ შეტყობინება', 'ეს არის ტესტი notifications საქაღალდიდან', $credentials_path, $project_id);

echo "<h3>შედეგი:</h3><pre>";
print_r($result);
echo "</pre>";