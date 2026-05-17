<?php
function get_random_weather_quote($lang = 'ka') {
    // Choose file based on language
    if ($lang === 'en') {
        $file = __DIR__ . '/quotes_en.txt';
    } else {
        $file = __DIR__ . '/quotes.txt';
    }

    if (file_exists($file)) {
        $quotes = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!empty($quotes)) {
            return htmlspecialchars($quotes[array_rand($quotes)], ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Fallback messages per language
    if ($lang === 'en') {
        return "Weather is the art of nature — enjoy every drop and ray! ☀️🌧️";
    }
    return "ამინდი ბუნების ხელოვნებაა — ისიამოვნე ყოველი წვეთით და სხივით! ☀️🌧️"; 
}
?>
