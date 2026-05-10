<?php
require_once 'config.php';

function askAI($userQuestion, $systemInstruction) {
    // ყურადღება: აქ შევცვალე AI_API_KEY -> GROQ_API_KEY-ით, როგორც config.php-ში გვაქვს
    $apiKey = defined('GROQ_API_KEY') ? GROQ_API_KEY : null;
    
    if (!$apiKey) {
        return "კონფიგურაციის შეცდომა: API გასაღები არ არის მითითებული (GROQ_API_KEY ვერ მოიძებნა).";
    }

    $url = "https://api.groq.com/openai/v1/chat/completions";
    
    $payload = [
        "model" => "llama-3.3-70b-versatile",
        "messages" => [
            [
                "role" => "system", 
                "content" => $systemInstruction
            ],
            [
                "role" => "user", 
                "content" => $userQuestion
            ]
        ],
        "temperature" => 0.65,
        "max_tokens" => 250
    ];
    
    $jsonPayload = json_encode($payload);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonPayload,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        // უსაფრთხოების მიზნით ჩავრთოთ SSL-ის შემოწმება (CURLOPT_SSL_VERIFYPEER => true)
        CURLOPT_SSL_VERIFYPEER => true, 
        CURLOPT_TIMEOUT => 25
    ]);

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "CURL შეცდომა: " . $error_msg;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? "ბოდიში, პასუხის მიღება ვერ მოხერხდა.";
    }

    error_log("Groq API error | HTTP $httpCode | " . $response);
    return "ბოდიში, AI სერვისი დროებით მიუწვდომელია.";
}