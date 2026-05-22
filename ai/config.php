<?php
// ai/config.php

function loadEnv($path) {
    if (!file_exists($path)) {
        // თუ აქ გაჩერდა, ესე იგი გზა .env-მდე არასწორია
        die("კრიტიკული შეცდომა: .env ფაილი ვერ მოიძებნა აქ: " . $path);
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            putenv(trim($name) . "=" . trim($value, '"\' '));
        }
    }
}

// გზის დაზუსტება
$envPath = realpath(dirname(__DIR__) . '/.env');

if (!$envPath) {
    // თუ realpath-მა ვერ იპოვა ფაილი
    die("შეცდომა: .env ფაილის რეალური გზა ვერ დადგინდა. შეამოწმე ფაილის სახელი.");
}

loadEnv($envPath);

$apiKey = getenv('GROQ_API_KEY');

if (!$apiKey) {
    die("შეცდომა: .env ფაილი ნაპოვნია, მაგრამ შიგნით GROQ_API_KEY ცარიელია.");
}

// კონსტანტის განსაზღვრა
if (!defined('GROQ_API_KEY')) {
    define('GROQ_API_KEY', $apiKey);
}


$nasaKey = getenv('NASA_MAP_KEY');
$footballKey = getenv('API_FOOTBALL_KEY');


// კონსტანტის განსაზღვრა NASA-სთვის
if ($nasaKey && !defined('NASA_MAP_KEY')) {
    define('NASA_MAP_KEY', $nasaKey);
}

