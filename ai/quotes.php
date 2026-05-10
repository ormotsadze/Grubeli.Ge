<?php
function get_random_weather_quote() {
    // რადგან ეს ფაილი ai/ საქაღალდეშია, 
    // ერთი დონით მაღლა უნდა ავიდეთ (..), რომ ვიპოვოთ quote.txt
    $file = __DIR__ . '/quotes.txt'; 
    
    // თუ ფაილი მაინც სხვაგან გაქვს (მაგალითად ai/ საქაღალდეშივე), 
    // მაშინ გამოიყენე: $file = __DIR__ . '/quote.txt';

    if (file_exists($file)) {
        $quotes = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!empty($quotes)) {
            return htmlspecialchars($quotes[array_rand($quotes)], ENT_QUOTES, 'UTF-8');
        }
    }
    
    // ეს ტექსტი მხოლოდ მაშინ გამოჩნდება, თუ ფაილი საერთოდ ვერ იპოვა
    return "ფაილი ვერ მოიძებნა: " . $file; 
}
?>