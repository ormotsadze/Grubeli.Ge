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
        // ✅ FIX: Use coordinates from POST if provided (mobile/WebView), fall back to session
        $ai_lat = isset($_POST['lat']) && is_numeric($_POST['lat']) ? floatval($_POST['lat']) : null;
        $ai_lon = isset($_POST['lon']) && is_numeric($_POST['lon']) ? floatval($_POST['lon']) : null;
        
        // Resolve coordinates with priority: POST > Session > Default
        if ($ai_lat !== null && $ai_lon !== null) {
            list($ai_lat, $ai_lon) = resolve_coordinates($ai_lat, $ai_lon);
            $cityName = get_location_name($ai_lat, $ai_lon);
        } else {
            $cityName = $_SESSION['city_name'] ?? 'თბილისი';
            $ai_lat = $_SESSION['lat'] ?? 41.7151;
            $ai_lon = $_SESSION['lon'] ?? 44.8271;
        }
        
        $isDay = $_SESSION['is_day'] ?? 1;
        $dayStatus = ($isDay == 1) ? "დღეა" : "ღამეა";
        
        // ✅ FIX 2: weather_cache არის array (index.php-ში json_encode გარეშე შენახული)
        $weatherData = $_SESSION['weather_cache'] ?? null;

        // If we have real coordinates and session cache might be stale, fetch fresh weather
        if ($ai_lat !== null && $ai_lon !== null && 
            (!isset($_SESSION['weather_lat']) || $_SESSION['weather_lat'] != $ai_lat || !isset($_SESSION['weather_lon']) || $_SESSION['weather_lon'] != $ai_lon)) {
            $freshWeather = fetch_weather_and_air($ai_lat, $ai_lon);
            if ($freshWeather['weather']) {
                $weatherData = $freshWeather['weather'];
            }
        }

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
