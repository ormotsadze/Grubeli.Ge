<?php
// save_location.php - კოორდინატების და ამინდის მონაცემების დამუშავება
require_once __DIR__ . '/functions.php';

// Same-origin დაცვა — WebView აგზავნის Origin: null, ეს ნორმალურია
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? '';
$origin = $scheme . '://' . $host;

if (isset($_SERVER['HTTP_ORIGIN'])) {
    $reqOrigin = $_SERVER['HTTP_ORIGIN'];
    if ($reqOrigin !== 'null' && $reqOrigin !== $origin) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'forbidden']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $lat = isset($data['lat']) ? floatval($data['lat']) : null;
    $lon = isset($data['lon']) ? floatval($data['lon']) : null;

    // ვალიდაცია
    if ($lat === null || $lon === null || !is_in_georgia($lat, $lon)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'invalid coordinates']);
        exit;
    }

    // ✅ Cookie — SameSite=None WebView-სთვის, Secure მხოლოდ HTTPS-ზე
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $cookieOpts = [
        'expires'  => time() + 31536000, // 1 წელი
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => false,
        'samesite' => $secure ? 'None' : 'Lax' // ✅ SameSite=None მხოლოდ HTTPS-ზე
    ];

    setcookie('user_lat', (string)$lat, $cookieOpts);
    setcookie('user_lon', (string)$lon, $cookieOpts);

    // ✅ city_name — cached Nominatim helper-ით
    $cityName = get_location_name($lat, $lon);

    // ✅ მოგვაქვს ამინდის მონაცემები შენი ფუნქციით (parallel)
    $weatherData = fetch_weather_and_air($lat, $lon);
    $weather = $weatherData['weather'];

    // ✅ ვაბრუნებთ ერთ, სრულ JSON-ს Android-ისთვის და JS-ისთვის
    header('Content-Type: application/json');
    echo json_encode([
        'ok'        => true,
        'lat'       => $lat,
        'lon'       => $lon,
        'city_name' => $cityName,
        'temp'      => isset($weather['current_weather']['temperature']) ? round($weather['current_weather']['temperature']) : null,
        'desc'      => $weather['current_weather']['description_geo'] ?? '',
        'code'      => $weather['current_weather']['weathercode'] ?? -1,
        'is_day'    => isset($weather['current_weather']['is_day']) ? ($weather['current_weather']['is_day'] == 1) : true
    ]);
    exit;
}

// თუ რექვესთი POST არ არის:
http_response_code(405);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
exit;