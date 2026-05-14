<?php

// 1. ფუნქცია, რომელიც აგენერირებს დროებით წვდომის ტოკენს (Google OAuth 2.0)
function get_fcm_access_token($service_account_path) {
    if (!file_exists($service_account_path)) {
        error_log("FCM Error: Service account file not found!");
        return false;
    }

    $json = json_decode(file_get_contents($service_account_path), true);
    
    // JWT-სთვის საჭირო ჰედერები და ფეილოუდი
    $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $now = time();
    $payload = json_encode([
        'iss' => $json['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => $json['token_uri'],
        'exp' => $now + 3600, // მოქმედებს 1 საათი
        'iat' => $now
    ]);

    // Base64Url ენკოდინგი
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;

    // ხელმოწერა
    openssl_sign($signatureInput, $signature, $json['private_key'], "SHA256");
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    // საბოლოო JWT
    $jwt = $signatureInput . "." . $base64UrlSignature;

    // ტოკენის მოთხოვნა Google-ისგან
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $json['token_uri']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer&assertion=" . $jwt);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // უსაფრთხოება ჩართულია
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $res = json_decode($response, true);
    return $res['access_token'] ?? false;
}

// 2. ფუნქცია, რომელიც აგზავნის შეტყობინებას კონკრეტულ თემაზე (Topic)
function send_fcm_topic($topic, $title, $body, $credentials_path, $project_id) {
    $access_token = get_fcm_access_token($credentials_path);
    
    if (!$access_token) {
        return ['success' => false, 'error' => 'Failed to get access token'];
    }

    $url = "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send";

    // მესიჯის სტრუქტურა
    $message = [
        'message' => [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'sound' => 'default',
                    // აქ შეგვიძლია დავამატოთ აიკონი ან ფერი მოგვიანებით
                ]
            ],
            // აქ შეგიძლია გაატანო ფარული მონაცემებიც (მაგალითად ლოკაცია)
            'data' => [
                'action' => 'open_app',
                'type' => $topic
            ]
        ]
    ];

    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'success' => ($httpcode == 200),
        'response' => json_decode($result, true)
    ];
}
?>