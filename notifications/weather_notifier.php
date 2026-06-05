<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/fcm_helper.php';

// 1. ვიღებთ რეალურ ამინდის მონაცემებს Open-Meteo API-დან
$weather = get_weather_data(); 
$alert = get_weather_alert($weather);

if ($alert) {
    $last_alert_file = __DIR__ . '/last_weather_alert.txt';
    $last_alert_type = file_exists($last_alert_file) ? file_get_contents($last_alert_file) : '';

    // ვაგზავნით მხოლოდ მაშინ, თუ საფრთხე ახალია (სპამის თავიდან ასაცილებლად)
    if ($last_alert_type !== $alert['title']) {
        
        // ნოტიფიკაციის სათაური ინგლისურად
        $title = ($alert['type'] == 'danger') ? "Severe Weather Alert! ⚠️" : "Weather Warning 🌤️";
        
        // სტატუსის თარგმნა ინგლისურად
        $status_en = ($alert['status'] === 'მიმდინარე საფრთხე') ? 'Current threat' : 'Expected within 12 hours';
        
        // საფრთხის დასახელების თარგმნა ინგლისურად
        $title_en = 'Weather Hazard';
        if ($alert['title'] === 'ძლიერი შტორმი') $title_en = 'Severe Storm';
        elseif ($alert['title'] === 'ძლიერი ქარი') $title_en = 'Severe Wind';
        elseif ($alert['title'] === 'მოსალოდნელია შტორმი') $title_en = 'Storm Expected';
        elseif ($alert['title'] === 'მოსალოდნელია ძლიერი ქარი') $title_en = 'Severe Wind Expected';

        // ნოტიფიკაციის შინაარსი სრულად ინგლისურად
        $body = $status_en . ": " . $title_en . ". Wind speed: " . $alert['wind'] . " km/h.";
        
        $credentials_path = __DIR__ . '/firebase/firebase-credentials.json';
        
        if (file_exists($credentials_path)) {
            $json_data = json_decode(file_get_contents($credentials_path), true);
            
            // ვიყენებთ მუშა ტოპიკს: 'urgent_alerts'
            $res = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $json_data['project_id']);
            
            // თუ წარმატებით გაიგზავნა, ვიმახსოვრებთ ფაილში, რომ დუბლიკატები არ გაიგზავნოს
            if ($res['success']) {
                file_put_contents($last_alert_file, $alert['title']);
            }
        }
    }
}
?>