<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/fcm_helper.php';

// 1. ვიღებთ დღესასწაულების სრულ სიას
$allHolidays = getGeorgianHolidays();

// 2. ვიგებთ ხვალინდელ თარიღს (Y-m-d ფორმატში)
$tomorrowDate = date('Y-m-d', strtotime('+1 day'));
$tomorrowHoliday = null;

// 3. ვეძებთ, არის თუ არა ხვალ რაიმე დღესასწაული
foreach ($allHolidays as $h) {
    if (isset($h['date']) && $h['date'] === $tomorrowDate) {
        // რადგან შეტყობინებები ინგლისურადაა, ვიღებთ საერთაშორისო დასახელებას ('name')
        $tomorrowHoliday = !empty($h['name']) ? $h['name'] : ($h['localName'] ?? null);
        break;
    }
}

// 4. თუ ხვალ დღესასწაულია, ვამზადებთ შეტყობინებას
if ($tomorrowHoliday) {
    $last_alert_file = __DIR__ . '/last_holiday_alert.txt';
    $last_alert_val = file_exists($last_alert_file) ? file_get_contents($last_alert_file) : '';

    // სპამის თავიდან ასაცილებლად, ვინახავთ თარიღისა და დღესასწაულის კომბინაციას
    $current_alert_signature = $tomorrowDate . '_' . $tomorrowHoliday;

    if ($last_alert_val !== $current_alert_signature) {
        
        $title = "Public Holiday Tomorrow! 🗓️";
        $body = "Tomorrow is a public holiday in Georgia: " . $tomorrowHoliday . ". Enjoy your day off!";
        
        $credentials_path = __DIR__ . '/firebase/firebase-credentials.json';
        
        if (file_exists($credentials_path)) {
            $json_data = json_decode(file_get_contents($credentials_path), true);
            
            // ვიყენებთ აპლიკაციის მუშა არხს: 'urgent_alerts'
            $res = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $json_data['project_id']);
            
            if ($res['success']) {
                file_put_contents($last_alert_file, $current_alert_signature);
            }
        }
    }
}
?>