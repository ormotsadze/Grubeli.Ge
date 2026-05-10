<?php
session_start();
require_once '../functions.php'; 
require_once 'ai_helper.php';

// 1. Flood Protection (5 წამიანი დაცვა)
$currentTime = time();
if (isset($_SESSION['last_ai_query']) && ($currentTime - $_SESSION['last_ai_query'] < 5)) {
    die("გთხოვთ, დაიცადოთ რამდენიმე წამი კითხვებს შორის.");
}
$_SESSION['last_ai_query'] = $currentTime;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ FIX 1: სიგრძის შეზღუდვა — მაქს 300 სიმბოლო
    $userMsg = mb_substr(trim($_POST['message'] ?? ''), 0, 300);

    if (!empty($userMsg)) {
        $cityName = $_SESSION['city_name'] ?? 'თბილისი'; 
        $isDay = $_SESSION['is_day'] ?? 1;
        $dayStatus = ($isDay == 1) ? "დღეა" : "ღამეა";
        
        // ✅ FIX 2: weather_cache არის array (index.php-ში json_encode გარეშე შენახული)
        $weatherData = $_SESSION['weather_cache'] ?? null;

        $temp = 'უცნობია';
        $desc = 'უცნობი პირობები';

        if ($weatherData && isset($weatherData['current_weather'])) {
            $temp = round($weatherData['current_weather']['temperature']);
            $desc = $weatherData['current_weather']['description_geo'];
        }

        // ✅ FIX 3: $userMsg სისტემურ ინსტრუქციაში არ ხვდება — მხოლოდ კონტექსტი
        $systemInstruction = "შენ ხარ Grubeli.ge-ს მეგობრული ასისტენტი. " .
            "კონტექსტი: {$cityName}, {$dayStatus}, {$temp}°C, {$desc}. " .
            "პასუხი იყოს მზრუნველი და ადამიანური, მიზეზის ახსნით. " .
            "გამოიყენე თბილი ქართული ენა, აკრძალულია რობოტული ფრაზები. " .
            "პასუხი იყოს მაქსიმუმ 2 ვრცელი წინადადება.";

        try {
            // $userMsg გადადის user role-ში askAI()-ს შიგნით — სწორია
            $response = askAI($userMsg, $systemInstruction);
            echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
        } catch (Exception $e) {
            error_log("send_message.php exception: " . $e->getMessage());
            echo "ბოდიში, პასუხის მომზადება ვერ მოხერხდა.";
        }
    } else {
        echo "მესიჯი ცარიელია.";
    }
}