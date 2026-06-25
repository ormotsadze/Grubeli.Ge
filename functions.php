<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. URL-ში მითითებულ ენას აქვს უმაღლესი პრიორიტეტი
if (isset($_GET['lang'])) {
    $requested_lang = $_GET['lang'];
    if (in_array($requested_lang, ['ka', 'en'])) {
        setcookie('lang', $requested_lang, time() + (86400 * 365), "/", "", false, false);
        $_SESSION['lang'] = $requested_lang;
        $current_lang = $requested_lang;
    }
}

if (!isset($current_lang)) {
    $lang_val = $_COOKIE['lang'] ?? $_SESSION['lang'] ?? null;
    $current_lang = $lang_val ? trim($lang_val) : null;
}

if (!$current_lang) {
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'ka', 0, 2);
    $current_lang = ($browser_lang === 'ka') ? 'ka' : 'en';
}

if (!$current_lang) {
    $current_lang = 'ka';
}

if (!isset($_COOKIE['lang']) || $_COOKIE['lang'] !== $current_lang) {
    setcookie('lang', $current_lang, time() + (86400 * 365), "/", "", false, false);
    $_SESSION['lang'] = $current_lang;
}
setcookie('lang_client', $current_lang, time() + (86400 * 365), "/", "", false, false);

function get_current_lang() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['ka', 'en'])) {
        return $_GET['lang'];
    }
    return $_COOKIE['lang'] ?? $_SESSION['lang'] ?? 'ka';
}

$lang_file = __DIR__ . "/lang/{$current_lang}.php";
$lang = file_exists($lang_file) ? require $lang_file : [];

function __($key) {
    global $lang;
    return $lang[$key] ?? $key;
}

// ─── CONSTANTS ────────────────────────────────────────────────────────────

define('USER_AGENT', 'GrubeliApp/1.0 (contact@grubeli.ge)');
define('CACHE_TTL_WEATHER', 600);      // 10 min
define('CACHE_TTL_AIR', 600);           // 10 min
define('CACHE_TTL_EARTHQUAKE', 900);    // 15 min
define('CACHE_TTL_FIRE', 3600);         // 1 hour
define('CACHE_TTL_GEOCODE', 86400);     // 24 hours
define('CACHE_TTL_HISTORICAL', 86400);  // 24 hours
define('HISTORICAL_API_DELAY_DAYS', 5); // Open-Meteo archive needs ~5 days delay

// ─── COORDINATE HELPERS ─────────────────────────────────────────────────

function resolve_coordinates($getLat = null, $getLon = null) {
    $default_lat = 41.7151;
    $default_lon = 44.8271;

    if ($getLat !== null && $getLon !== null && is_numeric($getLat) && is_numeric($getLon)) {
        $lat = floatval($getLat);
        $lon = floatval($getLon);
    } elseif (isset($_COOKIE['user_lat']) && isset($_COOKIE['user_lon'])) {
        $lat = floatval($_COOKIE['user_lat']);
        $lon = floatval($_COOKIE['user_lon']);
    } else {
        $lat = $default_lat;
        $lon = $default_lon;
    }

    if (!is_in_georgia($lat, $lon)) {
        $lat = $default_lat;
        $lon = $default_lon;
    }

    return [$lat, $lon];
}

// ─── GEO VALIDATION ─────────────────────────────────────────────────────

function is_in_georgia($lat, $lon) {
    $minLat = 40.0; $maxLat = 44.0;
    $minLon = 39.0; $maxLon = 47.0;
    return $lat >= $minLat && $lat <= $maxLat && $lon >= $minLon && $lon <= $maxLon;
}

// ─── CACHE LAYER (UNIFIED) ──────────────────────────────────────────────

function cache_dir() {
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

function get_cache_key($prefix, ...$parts) {
    $key_parts = [$prefix];
    foreach ($parts as $p) {
        if ($p !== null && $p !== '') {
            $key_parts[] = is_numeric($p) ? intval($p * 1000) : preg_replace('/[^a-zA-Z0-9_-]/', '_', $p);
        }
    }
    return implode('_', $key_parts) . '.json';
}

function cache_get($key, $ttl_seconds = 600) {
    $file = cache_dir() . DIRECTORY_SEPARATOR . $key;
    if (!file_exists($file)) return null;
    $data = json_decode(file_get_contents($file), true);
    if (!$data || !isset($data['fetched_at'])) return null;
    if (time() - $data['fetched_at'] > $ttl_seconds) return null;
    // If payload is explicitly null, still respect cache to throttle retries
    return $data['payload'];
}

function cache_set($key, $payload) {
    $file = cache_dir() . DIRECTORY_SEPARATOR . $key;
    $data = ['fetched_at' => time(), 'payload' => $payload];
    $tmp = $file . '.tmp.' . getmypid();
    file_put_contents($tmp, json_encode($data, JSON_UNESCAPED_UNICODE));
    rename($tmp, $file); // atomic write
}

// ─── PARALLEL HTTP HELPER (curl_multi) ─────────────────────────────────

/**
 * Execute multiple HTTP GET requests in parallel using curl_multi.
 * Returns array of [url_key => body_or_null].
 */
function multi_http_get($urls, $timeout = 10, $ssl_verify = false) {
    if (empty($urls)) return [];

    $results = [];
    $mh = curl_multi_init();
    $handles = [];

    foreach ($urls as $key => $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => $ssl_verify,
            CURLOPT_SSL_VERIFYHOST => $ssl_verify ? 2 : 0,
            CURLOPT_USERAGENT => USER_AGENT,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_DNS_CACHE_TIMEOUT => 120,
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$key] = $ch;
    }

    $running = null;
    do {
        $exec = curl_multi_exec($mh, $running);
        if ($exec !== CURLM_OK) break;
        curl_multi_select($mh, 0.2);
    } while ($running > 0);

    // Collect results and check for errors
    foreach ($handles as $key => $ch) {
        $body = curl_multi_getcontent($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        if ($error || $httpCode >= 400 || $body === false || $body === '') {
            $results[$key] = null;
        } else {
            $results[$key] = $body;
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
    return $results;
}

// ─── WEATHER DATA (PARALLEL ENABLED) ───────────────────────────────────

function fetch_weather_and_air($lat, $lon) {
    $weatherKey = get_cache_key('weather', $lat, $lon);
    $airKey = get_cache_key('air', $lat, $lon);

    $weatherCached = cache_get($weatherKey, CACHE_TTL_WEATHER);
    $airCached = cache_get($airKey, CACHE_TTL_AIR);

    if ($weatherCached && $airCached) {
        return ['weather' => $weatherCached, 'air_quality' => $airCached];
    }

    $urls = [];
    $needsWeather = !$weatherCached;
    $needsAir = !$airCached;

    if ($needsWeather) {
        $start = new DateTime('now', new DateTimeZone('UTC'));
        $end = (new DateTime('now', new DateTimeZone('UTC')))->modify('+11 days');
        $params = http_build_query([
            'latitude' => $lat,
            'longitude' => $lon,
            'hourly' => 'temperature_2m,apparent_temperature,precipitation,precipitation_probability,weathercode,windspeed_10m,winddirection_10m,windgusts_10m,relativehumidity_2m,dewpoint_2m,visibility,uv_index',
            'daily' => 'temperature_2m_max,temperature_2m_min,weathercode,sunrise,sunset,uv_index_max',
            'current_weather' => 'true',
            'timezone' => 'auto',
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d')
        ]);
        $urls['weather'] = 'https://api.open-meteo.com/v1/forecast?' . $params;
    }

    if ($needsAir) {
        $airParams = http_build_query([
            'latitude' => $lat,
            'longitude' => $lon,
            'hourly' => 'pm2_5,pm10,us_aqi',
            'timezone' => 'auto'
        ]);
        $urls['air'] = 'https://air-quality-api.open-meteo.com/v1/air-quality?' . $airParams;
    }

    $responses = multi_http_get($urls, 10, false);

    if ($needsWeather) {
        $weatherData = null;
        if (isset($responses['weather'])) {
            $weatherData = json_decode($responses['weather'], true);
            if ($weatherData && isset($weatherData['hourly'])) {
                $weatherData = enrich_weather_data($weatherData, $lat, $lon);
                cache_set($weatherKey, $weatherData);
            }
        }
        $weatherResult = $weatherData ?: $weatherCached;
    } else {
        $weatherResult = $weatherCached;
    }

    if ($needsAir) {
        $airData = null;
        if (isset($responses['air'])) {
            $airData = json_decode($responses['air'], true);
            if ($airData && isset($airData['hourly'])) {
                cache_set($airKey, $airData);
            }
        }
        $airResult = $airData ?: $airCached;
    } else {
        $airResult = $airCached;
    }

    return ['weather' => $weatherResult, 'air_quality' => $airResult];
}

/**
 * Enrich raw Open-Meteo weather data with Georgian descriptions and icons.
 */
function enrich_weather_data($data, $lat, $lon) {
    if (!$data) return null;

    $codes = weather_code_to_geo();
    $now = new DateTime('now', new DateTimeZone($data['timezone'] ?? 'UTC'));
    $is_day = true;
    if (isset($data['current_weather']['is_day'])) $is_day = $data['current_weather']['is_day'] == 1;

    // Current weather
    $current_code = $data['current_weather']['weathercode'] ?? 0;
    $data['current_weather']['description_geo'] = $codes[$current_code] ?? 'უცნობი';
    $data['current_weather']['icon'] = weather_code_to_icon($current_code, $is_day);

    // Daily enrichment
    if (isset($data['daily']['weathercode'])) {
        $daily_times = $data['daily']['time'] ?? [];
        foreach ($data['daily']['weathercode'] as $i => $c) {
            $date = $daily_times[$i] ?? null;
            $desc = $codes[$c] ?? 'უცნობი';
            $icon = weather_code_to_icon($c, true);

            if ($date && isset($data['hourly']['time'])) {
                $max_precip = 0;
                $snow_count = 0;
                $rain_count = 0;
                $min_temp = null;

                foreach ($data['hourly']['time'] as $hi => $htime) {
                    if (strpos($htime, $date) !== 0) continue;
                    if (isset($data['hourly']['precipitation_probability'][$hi])) {
                        $pp = intval($data['hourly']['precipitation_probability'][$hi]);
                        if ($pp > $max_precip) $max_precip = $pp;
                    }
                    if (isset($data['hourly']['temperature_2m'][$hi])) {
                        $t = floatval($data['hourly']['temperature_2m'][$hi]);
                        if ($min_temp === null || $t < $min_temp) $min_temp = $t;
                    }
                    $hwc = $data['hourly']['weathercode'][$hi] ?? null;
                    if (in_array($hwc, [71,73,75,77,85,86], true)) $snow_count++;
                    if (in_array($hwc, [51,53,55,61,63,65,80,81,82], true)) $rain_count++;
                }

                if (!($max_precip >= 20)) {
                    $icon = weather_code_to_icon($c, true);
                } else {
                    if ($snow_count > $rain_count || ($min_temp !== null && $min_temp <= 1.0)) {
                        $icon = weather_code_to_icon(71, true);
                        $desc = $codes[71] ?? $desc;
                    } else {
                        $icon = weather_code_to_icon(61, true);
                        $desc = $codes[61] ?? $desc;
                    }
                }
            }

            $data['daily']['description_geo'][$i] = $desc;
            $data['daily']['icon'][$i] = $icon;
        }
    }

    // Hourly enrichment
    if (isset($data['hourly']['weathercode']) && isset($data['hourly']['time'])) {
        foreach ($data['hourly']['weathercode'] as $i => $c) {
            $time = new DateTime($data['hourly']['time'][$i], new DateTimeZone($data['timezone'] ?? 'UTC'));
            $hour = intval($time->format('H'));
            $is_day_hour = $hour >= 6 && $hour < 20;
            $data['hourly']['description_geo'][$i] = $codes[$c] ?? 'უცნობი';
            $data['hourly']['icon'][$i] = weather_code_to_icon($c, $is_day_hour);
            $data['hourly']['current_visibility'][$i] = $data['hourly']['visibility'][$i] ?? 0;
        }
    }

    return $data;
}

// Backward compatibility wrappers
function fetch_weather($lat, $lon) {
    $result = fetch_weather_and_air($lat, $lon);
    return $result['weather'];
}

function fetch_air_quality($lat, $lon) {
    $result = fetch_weather_and_air($lat, $lon);
    return $result['air_quality'];
}

// ─── HISTORICAL DATA ───────────────────────────────────────────────────

/**
 * Unified fetch_historical using curl_multi for efficiency.
 * Uses -5 days as max archival date (Open-Meteo needs ~3-5 days delay).
 */
function fetch_historical($lat, $lon, $start_date, $end_date) {
    $lat = round(floatval($lat), 4);
    $lon = round(floatval($lon), 4);

    $key = get_cache_key('historical', $lat, $lon, $start_date, $end_date);
    $cached = cache_get($key, CACHE_TTL_HISTORICAL);
    if ($cached) return $cached;

    $params = http_build_query([
        'latitude'   => $lat,
        'longitude'  => $lon,
        'daily'      => 'temperature_2m_max,temperature_2m_min,weathercode',
        'timezone'   => 'auto',
        'start_date' => $start_date,
        'end_date'   => $end_date
    ]);

    $url = 'https://archive-api.open-meteo.com/v1/archive?' . $params;
    $responses = multi_http_get(['data' => $url], 20, false);

    if (!isset($responses['data'])) {
        // Cache null to prevent retry spam (shorter TTL: 5 minutes)
        cache_set($key . '_null', ['temp' => null]);
        return null;
    }

    $data = json_decode($responses['data'], true);
    if (!$data || !isset($data['daily'])) return null;

    $codes = weather_code_to_geo();
    if (isset($data['daily']['weathercode'])) {
        foreach ($data['daily']['weathercode'] as $i => $c) {
            $desc = $codes[$c] ?? 'უცნობი';
            $data['daily']['description_geo'][$i] = $desc;
            $data['daily']['icon'][$i] = weather_code_to_icon($c, true);
        }
    }

    cache_set($key, $data);
    return $data;
}
function get_last_year_temp($lat, $lon) {
    $lat = round(floatval($lat), 4);
    $lon = round(floatval($lon), 4);
    
    $last_year_date = date('Y-m-d', strtotime('-1 year'));
    // ვიყენებთ შენსავე get_cache_key ფუნქციას, რომ სახელი არ დამახინჯდეს
    $cacheKey = get_cache_key('hist_oyd', $lat, $lon, $last_year_date);
    
    $cached = cache_get($cacheKey, 86400); // 24 საათი
    if ($cached !== null) {
        return $cached;
    }

    $url = "https://archive-api.open-meteo.com/v1/archive?latitude={$lat}&longitude={$lon}&start_date={$last_year_date}&end_date={$last_year_date}&hourly=temperature_2m";
    
    $responses = multi_http_get(['hist' => $url], 5, false);
    $temp = null;

    if (isset($responses['hist'])) {
        $data = json_decode($responses['hist'], true);
        $hour = (int)gmdate('H'); // უმნიშვნელოვანესია: Open-Meteo არქივისთვის ვიღებთ UTC საათს!
        
        if (isset($data['hourly']['temperature_2m'])) {
            $temps = $data['hourly']['temperature_2m'];
            if (isset($temps[$hour]) && $temps[$hour] !== null) {
                $temp = $temps[$hour];
            } else {
                // თუ მიმდინარე საათის მონაცემი ჯერ არ არის, ავიღოთ უახლოესი ხელმისაწვდომი
                foreach ($temps as $t) {
                    if ($t !== null) { $temp = $t; break; }
                }
            }
        }
    }

    $result = ['temp' => $temp];
    // ვინახავთ კეშში მხოლოდ მაშინ, თუ მონაცემი ვალიდურია
    if ($temp !== null) {
        cache_set($cacheKey, $result);
    }
    return $result;
}
// ─── REVERSE GEOCODE (WITH 24H CACHE) ──────────────────────────────────

function get_location_name($lat, $lon) {
    $lat = round(floatval($lat), 4);
    $lon = round(floatval($lon), 4);

    $cacheKey = get_cache_key('geo', $lat, $lon);
    $cached = cache_get($cacheKey, CACHE_TTL_GEOCODE);
    if ($cached) return $cached;

    $placeName = 'საქართველო';

    $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$lat}&lon={$lon}&accept-language=ka&addressdetails=1";
    
    $responses = multi_http_get(['geo' => $url], 5, false);

    if (isset($responses['geo'])) {
        $json = json_decode($responses['geo'], true);
        if (isset($json['address'])) {
            $addr = $json['address'];
            $placeName = $addr['city']
                ?? $addr['town']
                ?? $addr['village']
                ?? $addr['suburb']
                ?? $addr['county']
                ?? 'საქართველო';
        }
    }

    cache_set($cacheKey, $placeName);
    return $placeName;
}

// ─── WEATHER CODE MAPS ──────────────────────────────────────────────────

function weather_code_to_geo() {
    return [
        0 => 'სუფთა ცა',
        1 => 'ნაწილობრივ ღრუბლიანი',
        2 => 'მცირე ღრუბელი',
        3 => 'ღრუბლიანი',
        45 => 'ნისლი',
        48 => 'ნისლი და ყინვა',
        51 => 'მცირედ წვიმს',
        53 => 'წვიმა',
        55 => 'ძლიერი წვიმა',
        56 => 'ყინვიანი წვიმა',
        57 => 'ძლიერი ყინვიანი წვიმა',
        61 => 'მცირე წვიმა',
        63 => 'წვიმა',
        65 => 'ძლიერი წვიმა',
        66 => 'ყინვიანი წვიმა',
        67 => 'ძლიერი ყინვიანი წვიმა',
        71 => 'მცირე თოვა',
        73 => 'თოვა',
        75 => 'ძლიერი თოვა',
        77 => 'სეტყვა',
        80 => 'წვიმა',
        81 => 'წვიმა',
        82 => 'ძლიერი წვიმა',
        85 => 'ხანმოკლე თოვა',
        86 => 'ხანმოკლე თოვა',
        95 => 'ქუხილი',
        96 => 'ქუხილი და სეტყვა',
        99 => 'ქუხილი და სეტყვა'
    ];
}

function get_weather_description_by_text($geo_text) {
    if (get_current_lang() === 'ka') {
        return $geo_text;
    }

    $translation_map = [
        'სუფთა ცა'                => 'Clear sky',
        'მოწმენდილი ცა'           => 'Clear sky',
        'ნაწილობრივ ღრუბლიანი'    => 'Partly cloudy',
        'მცირე ღრუბელი'          => 'Mainly clear',
        'ღრუბლიანი'               => 'Cloudy',
        'მოღრუბლული'              => 'Overcast',
        'ნისლი'                   => 'Fog',
        'ნისლი და ყინვა'          => 'Depositing rime fog',
        'მცირედ წვიმს'            => 'Light drizzle',
        'წვიმა'                   => 'Rain',
        'ძლიერი წვიმა'            => 'Heavy rain',
        'ყინვიანი წვიმა'          => 'Freezing rain',
        'ძლიერი ყინვიანი წვიმა'   => 'Heavy freezing rain',
        'მცირე წვიმა'             => 'Light rain',
        'მცირე თოვა'             => 'Light snow fall',
        'თოვა'                    => 'Snow fall',
        'ძლიერი თოვა'             => 'Heavy snow fall',
        'სეტყვა'                  => 'Hail',
        'ხანმოკლე თოვა'           => 'Snow showers',
        'ქუხილი'                  => 'Thunderstorm',
        'ქუხილი და სეტყვა'        => 'Thunderstorm with hail'
    ];

    $clean_text = trim($geo_text);
    if (array_key_exists($clean_text, $translation_map)) {
        return $translation_map[$clean_text];
    }

    return ucwords(transliterate_georgian($geo_text));
}

function weather_code_to_icon($code, $is_day = true) {
    $iconsDir = __DIR__ . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;

    $map = [
        0 => [$is_day ? 'clear-day.svg' : 'clear-night.svg'],
        1 => [$is_day ? 'partly-cloudy-day.svg' : 'partly-cloudy-night.svg'],
        2 => [$is_day ? 'partly-cloudy-day.svg' : 'partly-cloudy-night.svg'],
        3 => [$is_day ? 'overcast-day.svg' : 'overcast-night.svg'],
        45 => [$is_day ? 'fog-day.svg' : 'fog-night.svg'],
        48 => [$is_day ? 'fog-day.svg' : 'fog-night.svg'],
        51 => ['drizzle.svg'],
        53 => ['rain.svg'],
        55 => [$is_day ? 'thunderstorms-day.svg' : 'thunderstorms-night.svg'],
        56 => ['sleet.svg'],
        57 => ['sleet.svg'],
        61 => ['rain.svg'],
        63 => ['rain.svg'],
        65 => ['rain.svg'],
        66 => ['sleet.svg'],
        67 => ['sleet.svg'],
        71 => ['snow.svg'],
        73 => ['snow.svg'],
        75 => ['snow.svg'],
        77 => ['snowflake.svg'],
        80 => ['rain.svg'],
        81 => ['rain.svg'],
        82 => ['rain.svg'],
        85 => ['snow.svg'],
        86 => ['snow.svg'],
        95 => [$is_day ? 'thunderstorms-day.svg' : 'thunderstorms-night.svg'],
        96 => [$is_day ? 'thunderstorms-day-rain.svg' : 'thunderstorms-night-rain.svg'],
        99 => [$is_day ? 'thunderstorms-day-rain.svg' : 'thunderstorms-night-rain.svg']
    ];

    $candidates = $map[$code] ?? ['not-available.svg'];
    foreach ($candidates as $f) {
        if (file_exists($iconsDir . $f)) return $f;
    }

    if (file_exists($iconsDir . 'not-available.svg')) return 'not-available.svg';
    if ($is_day && file_exists($iconsDir . 'clear-day.svg')) return 'clear-day.svg';
    if (!$is_day && file_exists($iconsDir . 'clear-night.svg')) return 'clear-night.svg';

    return 'not-available.svg';
}

function icon_url($filename, $is_day = true) {
    if (!$filename) return 'icons/sun.svg';
    
    $iconsDir = __DIR__ . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
    $candidate = $iconsDir . $filename;
    if (file_exists($candidate)) return 'icons/' . $filename;

    $subs = [
        'fog.svg' => 'cloudy.svg',
        'partly-cloudy.svg' => 'cloudy.svg',
        'drizzle.svg' => 'rain.svg',
        'sleet.svg' => 'hail.svg',
        'thunder.svg' => 'storm.svg',
        'thunder-sleet.svg' => 'storm.svg',
        'unknown.svg' => 'sun.svg'
    ];
    if (isset($subs[$filename]) && file_exists($iconsDir . $subs[$filename])) {
        return 'icons/' . $subs[$filename];
    }

    if (!$is_day) {
        if (file_exists($iconsDir . 'clear-night.svg')) return 'icons/clear-night.svg';
        if (file_exists($iconsDir . 'starry-night.svg')) return 'icons/starry-night.svg';
    }

    if (file_exists($iconsDir . 'cloudy.svg')) return 'icons/cloudy.svg';
    if (file_exists($iconsDir . 'clear-day.svg')) return 'icons/clear-day.svg';
    return 'icons/not-available.svg';
}

// ─── DATE FORMATTING ────────────────────────────────────────────────────

function format_georgian_datetime(DateTime $dt) {
    $days = [
        'კვირა','ორშაბათი','სამშაბათი','ოთხშაბათი','ხუთშაბათი','პარასკევი','შაბათი'
    ];
    $months_short = ['იანვ.','თებ.','მარტ.','აპრ.','მაი.','ივნ.','ივლ.','აგვ.','სექტ.','ოქტ.','ნოემ.','დეკ.'];
    $dayName = $days[intval($dt->format('w'))];
    $month = $months_short[intval($dt->format('n')) - 1];
    return $dt->format('j') . ' ' . $month . ' ' . mb_substr($dayName,0,3) . '. ' . $dt->format('H:i');
}

function format_custom_datetime($datetime) {
    $date = ($datetime instanceof DateTime) ? $datetime : new DateTime($datetime);
    
    if (get_current_lang() === 'en') {
        return $date->format('M d, Y - H:i');
    }
    
    return format_georgian_datetime($date);
}

// ─── HOLIDAYS ───────────────────────────────────────────────────────────

function getGeorgianHolidays() {
    $currentYear = date('Y');
    $cacheDir = cache_dir();
    $cacheFile = $cacheDir . "/holidays_{$currentYear}.json";

    $needsUpdate = !file_exists($cacheFile);

    if (!$needsUpdate && date('m-d') === '12-31') {
        if (time() - filemtime($cacheFile) > 86400) {
            $needsUpdate = true;
        }
    }

    if ($needsUpdate) {
        $url = "https://date.nager.at/api/v3/PublicHolidays/{$currentYear}/GE";
        
        $responses = multi_http_get(['holidays' => $url], 10, false);

        if (isset($responses['holidays'])) {
            $decoded = json_decode($responses['holidays'], true);
            if (is_array($decoded)) {
                file_put_contents($cacheFile, $responses['holidays']);
                return $decoded;
            }
        }

        if (file_exists($cacheFile)) {
            return json_decode(file_get_contents($cacheFile), true) ?? [];
        }
        return [];
    }

    return json_decode(file_get_contents($cacheFile), true) ?? [];
}

$allHolidays = getGeorgianHolidays();
$todayDate = date('Y-m-d');
$todayHoliday = null;

foreach ($allHolidays as $h) {
    if (isset($h['date']) && $h['date'] === $todayDate) {
        if ($current_lang === 'ka') {
            $todayHoliday = !empty($h['localName']) ? $h['localName'] : ($h['name'] ?? null);
        } else {
            $todayHoliday = !empty($h['name']) ? $h['name'] : ($h['localName'] ?? null);
        }
        break;
    }
}

// ─── EARTHQUAKES ────────────────────────────────────────────────────────
function checkEarthquakeRisk() {
    $cacheFile = cache_dir() . '/earthquake_alert.json';
    
    // თუ ქეში არსებობს და 30 წუთზე ახალია, ვიყენებთ ქეშს
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 1800)) {
        return json_decode(file_get_contents($cacheFile), true);
    }

    $alertData = [
        'active' => false,
        'mag'    => 0,
        'place'  => '',
        'time'   => ''
    ];

    $today = date('Y-m-d');
    
    // სამუშაო რეჟიმისთვის ვაყენებთ 4.0 მაგნიტუდას
    $minMagnitude = "4.0"; 
    
    $url = "https://earthquake.usgs.gov/fdsnws/event/1/query?" . http_build_query([
        'format'       => 'geojson',
        'starttime'    => $today,
        'minmagnitude' => $minMagnitude,
        'minlatitude'  => '40.0',
        'maxlatitude'  => '44.5',
        'minlongitude' => '39.0',
        'maxlongitude' => '47.0'
    ]);

    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n",
            "timeout" => 15
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['features']) && !empty($data['features'])) {
            // ავიღოთ ბოლო მომხდარი მიწისძვრა
            $event = $data['features'][0]; 
            $props = $event['properties'];

            $alertData = [
                'active' => true,
                'mag'    => $props['mag'],
                'place'  => $props['place'] ?? '',
                'time'   => date('H:i', intval($props['time'] / 1000))
            ];
        }
    }

    // ვინახავთ პასუხს ქეშში
    file_put_contents($cacheFile, json_encode($alertData));
    return $alertData;
}

// ─── FIRE RISK ──────────────────────────────────────────────────────────
function checkFireRisk($map_key) {
    $cacheFile = cache_dir() . '/fire_alerts.json';

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < CACHE_TTL_FIRE)) {
        return json_decode(file_get_contents($cacheFile), true);
    }

    if (empty($map_key)) {
        return ['active' => false, 'count' => 0, 'points' => [], 'last_update' => date('H:i')];
    }

    $west = 39.0; $south = 41.0; $east = 47.0; $north = 44.0;
    $url = "https://firms.modaps.eosdis.nasa.gov/api/area/csv/" . $map_key . "/VIIRS_SNPP_NRT/" . $west . "," . $south . "," . $east . "," . $north . "/1";

    $responses = multi_http_get(['fire' => $url], 15, false);
    $firePoints = [];

    if (isset($responses['fire']) && strlen($responses['fire']) > 50) {
        $lines = explode("\n", trim($responses['fire']));
        $headers = str_getcsv(array_shift($lines), ',', '"', '\\');

        foreach ($lines as $line) {
            $data = str_getcsv($line, ',', '"', '\\');
            if (count($data) === count($headers)) {
                $row = array_combine($headers, $data);
                
                $confidenceRaw = strtolower($row['confidence']);
                $brightnessK = floatval($row['bright_ti4']);

                if (($confidenceRaw === 'h' || $confidenceRaw === 'n') && $brightnessK > 300) {
                    
                    $lat_fire = floatval($row['latitude']);
                    $lng_fire = floatval($row['longitude']);

                    // რეგიონები ინგლისურად
                    if ($lng_fire < 41.5) { $region = "Western Georgia"; }
                    elseif ($lng_fire > 44.5) { $region = "Kakheti region"; }
                    elseif ($lat_fire < 41.8) { $region = "Southern Georgia"; }
                    else { $region = "Shida Kartli / Mtskheta-Mtianeti"; }

                    $firePoints[] = [
                        'lat'    => $lat_fire,
                        'lng'    => $lng_fire,
                        'temp'   => round($brightnessK - 273.15),
                        'conf'   => ($confidenceRaw === 'h' ? 'High' : 'Nominal'),
                        'region' => $region,
                        'time'   => $row['acq_time']
                    ];
                }
            }
        }
    }

    $result = [
        'active' => !empty($firePoints),
        'count'  => count($firePoints),
        'points' => $firePoints,
        'last_update' => date('H:i')
    ];

    file_put_contents($cacheFile, json_encode($result));
    return $result;
}
// ─── WEATHER ALERTS ─────────────────────────────────────────────────────
// ვამატებთ $lat და $lon პარამეტრებს, სადაც თბილისი იქნება სტანდარტული (fallback)
function get_weather_data($lat = '41.7151', $lon = '44.8271') {
    
    // დინამიური URL კოორდინატების მიხედვით
    $url = "https://api.open-meteo.com/v1/forecast?latitude=" . urlencode($lat) . "&longitude=" . urlencode($lon) . "&current=weather_code,wind_speed_10m&hourly=weather_code,wind_speed_10m";
    
    $response = @file_get_contents($url);
    if ($response) {
        return json_decode($response, true);
    }
    return null;
}

function get_weather_alert($weather) {
    $current = $weather['current'] ?? null;
    $hourly = $weather['hourly'] ?? null;
    
    if (!$current || !$hourly) return null;

    $current_code = $current['weather_code'] ?? 0;
    $current_wind = $current['wind_speed_10m'] ?? 0;

    $is_storm_now = in_array($current_code, [95, 96, 99]);
    $is_windy_now = $current_wind >= 20; 

    if ($is_storm_now || $is_windy_now) {
        return [
            'type' => 'danger',
            'title' => $is_storm_now ? "ძლიერი შტორმი" : "ძლიერი ქარი",
            'status' => "მიმდინარე საფრთხე",
            'icon' => $is_storm_now ? "fa-bolt-lightning" : "fa-wind",
            'wind' => round($current_wind)
        ];
    }

    for ($i = 1; $i <= 12; $i++) {
        $h_code = $hourly['weather_code'][$i] ?? 0;
        $h_wind = $hourly['wind_speed_10m'][$i] ?? 0;

        if (in_array($h_code, [95, 96, 99]) || $h_wind >= 20) {
            $is_storm_future = in_array($h_code, [95, 96, 99]);
            return [
                'type' => 'warning',
                'title' => $is_storm_future ? "მოსალოდნელია შტორმი" : "მოსალოდნელია ძლიერი ქარი",
                'status' => "მომდევნო 12 სთ-ში",
                'icon' => $is_storm_future ? "fa-cloud-bolt" : "fa-wind",
                'wind' => round($h_wind)
            ];
        }
    }

    return null;
}
// ─── TRANSLITERATION & TRANSLATION ──────────────────────────────────────

function transliterate_georgian($text) {
    $geo_chars = ['ა','ბ','გ','დ','ე','ვ','ზ','თ','ი','კ','ლ','მ','ნ','ო','პ','ჟ','რ','ს','ტ','უ','ფ','ქ','ღ','ყ','შ','ჩ','ც','ძ','წ','ჭ','ხ','ჯ','ჰ'];
    $eng_chars = ['a','b','g','d','e','v','z','th','i','k','l','m','n','o','p','zh','r','s','t','u','ph','q','gh','q','sh','ch','ts','dz','ts','ch','kh','j','h'];
    
    $text = str_replace(['რაიონი', 'რაიონის', 'სოფელი'], ['Region', 'Region', 'Village'], $text);
    
    return str_replace($geo_chars, $eng_chars, $text);
}

function translate_place_name($placeName) {
    if (get_current_lang() === 'ka') {
        return $placeName;
    }

    $jsonPath = __DIR__ . '/cities.json';
    
    if (file_exists($jsonPath)) {
        $jsonData = file_get_contents($jsonPath);
        $cities = json_decode($jsonData, true);
        
        if (is_array($cities)) {
            foreach ($cities as $city) {
                if (trim($city['name']) === trim($placeName)) {
                    if (isset($city['name_en']) && !empty($city['name_en'])) {
                        return $city['name_en'];
                    }
                    return ucwords(transliterate_georgian($placeName));
                }
            }
        }
    }

    $regionTranslations = [
        'დასავლეთ საქართველო' => 'West Georgia',
        'აღმოსავლეთ საქართველო' => 'East Georgia',
        'სამხრეთ საქართველო' => 'South Georgia',
        'ჩრდილოეთ საქართველო' => 'North Georgia',
        'შიდა ქართლი / მცხეთა-მთიანეთი' => 'Shida Kartli / Mtskheta-Mtianeti',
        'კახეთის რეგიონი' => 'Kakheti Region',
        'აჭარა' => 'Adjara',
        'იმერეთი' => 'Imereti',
        'კახეთი' => 'Kakheti',
        'ქართლი' => 'Kartli',
        'სამეგრელო' => 'Samegrelo',
        'სვანეთი' => 'Svaneti',
        'გურია' => 'Guria',
        'სამცხე-ჯავახეთი' => 'Samtskhe-Javakheti',
        'მცხეთა-მთიანეთი' => 'Mtskheta-Mtianeti',
        'ქვემო ქართლი' => 'Kvemo Kartli',
        'შიდა ქართლი' => 'Shida Kartli',
        'რაჭა-ლეჩხუმი' => 'Racha-Lechkhumi',
        'სამხრეთ ოსეთი' => 'South Ossetia',
        'საქართველო' => 'Georgia',
    ];
    if (isset($regionTranslations[$placeName])) {
        return $regionTranslations[$placeName];
    }

    return ucwords(transliterate_georgian($placeName));
}


function otd_translate_text($text) {
    if (empty($text) || get_current_lang() === 'en') {
        return $text;
    }

    $cache_dir = __DIR__ . '/translations_cache/';
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }

    $hash = md5($text);
    $cache_file = $cache_dir . $hash . '.txt';

    if (file_exists($cache_file)) {
        return file_get_contents($cache_file);
    }

    $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ka&dt=t&q=" . urlencode($text);
    
    $responses = multi_http_get(['trans' => $url], 3, false);

    if (isset($responses['trans'])) {
        $result = json_decode($responses['trans'], true);
        if (isset($result[0])) {
            $translated_text = "";
            foreach ($result[0] as $line) {
                $translated_text .= $line[0];
            }
            @file_put_contents($cache_file, $translated_text);
            return $translated_text;
        }
    }
    
    return $text;
}
?>