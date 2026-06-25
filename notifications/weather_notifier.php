<?php
// 1. საჭირო ფაილების შემოტანა
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/fcm_helper.php';

// 2. ამინდის მონაცემების და ალერტის მიღება
$weather = get_weather_data(); 
$alert = get_weather_alert($weather);

// 3. ლოკალური ფაილის მისამართი (ბოლო გაგზავნილი შეტყობინების დასამახსოვრებლად)
$last_alert_file = __DIR__ . '/last_weather_alert.txt';

if ($alert) {
    // ვკითხულობთ, რა საფრთხე გავაგზავნეთ ბოლოს
    $last_alert_type = file_exists($last_alert_file) ? file_get_contents($last_alert_file) : '';

    // ვაგზავნით მხოლოდ მაშინ, თუ საფრთხე ახალია (სპამის თავიდან ასაცილებლად)
    if ($last_alert_type !== $alert['title']) {
        
        // --- თარგმანების ბლოკი ინგლისური ვერსიისთვის ---
        $title = ($alert['type'] == 'danger') ? "Severe Weather Alert! ⚠️" : "Weather Warning 🌤️";
        $status_en = ($alert['status'] === 'მიმდინარე საფრთხე') ? 'Current threat' : 'Expected within 12 hours';
        
        $title_en = 'Weather Hazard';
        if ($alert['title'] === 'ძლიერი შტორმი') $title_en = 'Severe Storm';
        elseif ($alert['title'] === 'ძლიერი ქარი') $title_en = 'Severe Wind';
        elseif ($alert['title'] === 'მოსალოდნელია შტორმი') $title_en = 'Storm Expected';
        elseif ($alert['title'] === 'მოსალოდნელია ძლიერი ქარი') $title_en = 'Severe Wind Expected';

        $body = $status_en . ": " . $title_en . ". Wind speed: " . $alert['wind'] . " km/h.";
        // ----------------------------------------------
        
        // Firebase კრედენციალების წაკითხვა
        $credentials_path = __DIR__ . '/firebase/firebase-credentials.json';
        
        if (file_exists($credentials_path)) {
            $json_data = json_decode(file_get_contents($credentials_path), true);
            
            // შეტყობინების გაგზავნა ტოპიკზე
            $res = send_fcm_topic('urgent_alerts', $title, $body, $credentials_path, $json_data['project_id']);
            
            // თუ წარმატებით გაიგზავნა, ვიმახსოვრებთ ფაილში
            if ($res['success']) {
                file_put_contents($last_alert_file, $alert['title']);
            }
        }
    }
} else {
    // 4. თუ ამინდი გამოსწორდა, ვასუფთავებთ ფაილს, რომ შემდეგი საფრთხე არ დაიბლოკოს
    if (file_exists($last_alert_file) && file_get_contents($last_alert_file) !== '') {
        file_put_contents($last_alert_file, '');
    }
}
?>