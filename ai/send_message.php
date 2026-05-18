<?php
session_start();
require_once '../functions.php'; 
require_once 'ai_helper.php';

// 1. Flood Protection (5 წამიანი დაცვა)
$currentTime = time();
if (isset($_SESSION['last_ai_query']) && ($currentTime - $_SESSION['last_ai_query'] < 5)) {
  die(__('ai_wait'));
}
$_SESSION['last_ai_query'] = $currentTime;

// Determine current language from session
$ai_lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'ka';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMsg = mb_substr(trim($_POST['message'] ?? ''), 0, 300);

    if (!empty($userMsg)) {
        $ai_lat = isset($_POST['lat']) && is_numeric($_POST['lat']) ? floatval($_POST['lat']) : null;
        $ai_lon = isset($_POST['lon']) && is_numeric($_POST['lon']) ? floatval($_POST['lon']) : null;
        
        if ($ai_lat !== null && $ai_lon !== null) {
            list($ai_lat, $ai_lon) = resolve_coordinates($ai_lat, $ai_lon);
            $cityName = get_location_name($ai_lat, $ai_lon);
        } else {
            $cityName = $_SESSION['city_name'] ?? 'თბილისი';
            $ai_lat = $_SESSION['lat'] ?? 41.7151;
            $ai_lon = $_SESSION['lon'] ?? 44.8271;
        }
        
        $isDay = $_SESSION['is_day'] ?? 1;
        $dayStatus = $isDay ? __('its_day') : __('its_night');
        
        $weatherData = $_SESSION['weather_cache'] ?? null;

        if ($ai_lat !== null && $ai_lon !== null && 
            (!isset($_SESSION['weather_lat']) || $_SESSION['weather_lat'] != $ai_lat || !isset($_SESSION['weather_lon']) || $_SESSION['weather_lon'] != $ai_lon)) {
            $freshWeather = fetch_weather_and_air($ai_lat, $ai_lon);
            if ($freshWeather['weather']) {
                $weatherData = $freshWeather['weather'];
            }
        }

        $temp = __('its_uknow');
        $desc = __('its_uknow');

        if ($weatherData && isset($weatherData['current_weather'])) {
            $temp = round($weatherData['current_weather']['temperature']);
            $desc = $weatherData['current_weather']['description_geo'];
            // If English mode, use the WMO weather code to get an English description
            if ($ai_lang === 'en' && isset($weatherData['current_weather']['weathercode'])) {
                $code = $weatherData['current_weather']['weathercode'];
                $enDescriptions = [
                    0 => 'Clear sky', 1 => 'Mainly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
                    45 => 'Foggy', 48 => 'Depositing rime fog', 51 => 'Light drizzle', 53 => 'Moderate drizzle',
                    55 => 'Dense drizzle', 56 => 'Light freezing drizzle', 57 => 'Dense freezing drizzle',
                    61 => 'Slight rain', 63 => 'Moderate rain', 65 => 'Heavy rain',
                    66 => 'Light freezing rain', 67 => 'Heavy freezing rain',
                    71 => 'Slight snow', 73 => 'Moderate snow', 75 => 'Heavy snow',
                    77 => 'Snow grains', 80 => 'Slight rain showers', 81 => 'Moderate rain showers',
                    82 => 'Violent rain showers', 85 => 'Slight snow showers', 86 => 'Heavy snow showers',
                    95 => 'Thunderstorm', 96 => 'Thunderstorm with slight hail', 99 => 'Thunderstorm with heavy hail'
                ];
                $desc = $enDescriptions[$code] ?? __('its_uknow');
            }
        }

        // Build system instruction dynamically based on language
        if ($ai_lang === 'en') {
            $systemInstruction = "You are Grubeli.ge's friendly weather assistant. " .
                "Context: {$cityName}, {$dayStatus}, {$temp}°C, {$desc}. " .
                "Be caring and human-like, explain the reason behind your advice. " .
                "IMPORTANT: Always reply in English — warm, natural English. " .
                "Never use robotic phrases like 'As an AI' or 'I cannot'. " .
                "Max 2 full sentences.";
        } else {
            $systemInstruction = "შენ ხარ Grubeli.ge-ს მეგობრული ასისტენტი. " .
                "კონტექსტი: {$cityName}, {$dayStatus}, {$temp}°C, {$desc}. " .
                "პასუხი იყოს მზრუნველი და ადამიანური, მიზეზის ახსნით. " .
                "გამოიყენე თბილი ქართული ენა, აკრძალულია რობოტული ფრაზები. " .
                "პასუხი იყოს მაქსიმუმ 2 ვრცელი წინადადება.";
        }

        try {
            $response = askAI($userMsg, $systemInstruction);
            echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
        } catch (Exception $e) {
            error_log("send_message.php exception: " . $e->getMessage());
            if ($ai_lang === 'en') {
                echo "Sorry, I couldn't prepare a response right now. Please try again!";
            } else {
                echo "ბოდიში, პასუხის მომზადება ვერ მოხერხდა.";
            }
        }
    } else {
        if ($ai_lang === 'en') {
            echo "Message is empty.";
        } else {
            echo "მესიჯი ცარიელია.";
        }
    }
}
