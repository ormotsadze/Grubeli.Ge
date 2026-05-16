<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── CONSTANTS ────────────────────────────────────────────────────────────

define('USER_AGENT', 'GrubeliApp/1.0 (contact@grubeli.ge)');
define('CACHE_TTL_WEATHER', 600);      // 10 min
define('CACHE_TTL_AIR', 600);           // 10 min
define('CACHE_TTL_EARTHQUAKE', 900);    // 15 min
define('CACHE_TTL_FIRE', 3600);         // 1 hour
define('CACHE_TTL_GEOCODE', 86400);     // 24 hours — Nominatim cache!

// ─── COORDINATE HELPERS ─────────────────────────────────────────────────

/**
 * Resolve coordinates with priority: GET → Cookie → Default (Tbilisi)
 */
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

    // Validate inside Georgia
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

// ─── CACHE LAYER ────────────────────────────────────────────────────────

function cache_dir() {
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

function get_cache_key($prefix, $lat, $lon, $suffix = '') {
    $key = $prefix . '_' . intval($lat * 1000) . '_' . intval($lon * 1000);
    if ($suffix) $key .= '_' . $suffix;
    return $key . '.json';
}

function cache_get($key, $ttl_seconds = 600) {
    $file = cache_dir() . DIRECTORY_SEPARATOR . $key;
    if (!file_exists($file)) return null;
    $data = json_decode(file_get_contents($file), true);
    if (!$data || !isset($data['fetched_at'])) return null;
    if (time() - $data['fetched_at'] > $ttl_seconds) return null;
    return $data['payload'];
}

function cache_set($key, $payload) {
    $file = cache_dir() . DIRECTORY_SEPARATOR . $key;
    $data = ['fetched_at' => time(), 'payload' => $payload];
    file_put_contents($file, json_encode($data));
}

// ─── REVERSE GEOCODE (WITH 24H CACHE) ──────────────────────────────────

/**
 * Get location name from coordinates via Nominatim.
 * Results are cached for 24 hours (CACHE_TTL_GEOCODE).
 */
function get_location_name($lat, $lon) {
    $lat = round(floatval($lat), 4);
    $lon = round(floatval($lon), 4);

    // Try cache first
    $cacheKey = get_cache_key('geo', $lat, $lon);
    $cached = cache_get($cacheKey, CACHE_TTL_GEOCODE);
    if ($cached) return $cached;

    // Fallback default
    $placeName = 'საქართველო';

  $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$lat}&lon={$lon}&accept-language=ka&addressdetails=1";
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: " . USER_AGENT . "\r\n",
            'timeout' => 5
        ]
    ];
    $response = @file_get_contents($url, false, stream_context_create($opts));

    if ($response) {
        $json = json_decode($response, true);
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

    // Cache for 24 hours
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
    $iconsDir = __DIR__ . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR;
    if (!$filename) return 'icons/sun.svg';
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

// ─── PARALLEL HTTP HELPER ───────────────────────────────────────────────

/**
 * Execute multiple HTTP GET requests in parallel using curl_multi.
 * Returns array of [url => body_or_null].
 */
function multi_http_get($urls, $timeout = 10) {
    $results = [];
    $mh = curl_multi_init();
    $handles = [];

    foreach ($urls as $key => $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => USER_AGENT,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$key] = $ch;
    }

    // Execute all queries simultaneously
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh, 0.2); // Wait briefly for activity
    } while ($running > 0);

    // Collect results
    foreach ($handles as $key => $ch) {
        $body = curl_multi_getcontent($ch);
        $results[$key] = ($body !== false && $body !== '') ? $body : null;
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
    return $results;
}

// ─── WEATHER DATA (PARALLEL ENABLED) ────────────────────────────────────

/**
 * Fetch weather AND air quality in parallel.
 * Returns ['weather' => $weatherData, 'air_quality' => $airData]
 */
function fetch_weather_and_air($lat, $lon) {
    $weatherKey = get_cache_key('weather', $lat, $lon);
    $airKey = get_cache_key('air', $lat, $lon);

    $weatherCached = cache_get($weatherKey, CACHE_TTL_WEATHER);
    $airCached = cache_get($airKey, CACHE_TTL_AIR);

    // If both cached, return immediately
    if ($weatherCached && $airCached) {
        return ['weather' => $weatherCached, 'air_quality' => $airCached];
    }

    $urls = [];
    $needsWeather = !$weatherCached;
    $needsAir = !$airCached;

    // Build weather URL
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

    // Build air quality URL
    if ($needsAir) {
        $airParams = http_build_query([
            'latitude' => $lat,
            'longitude' => $lon,
            'hourly' => 'pm2_5,pm10,us_aqi',
            'timezone' => 'auto'
        ]);
        $urls['air'] = 'https://air-quality-api.open-meteo.com/v1/air-quality?' . $airParams;
    }

    // Execute in parallel
    $responses = multi_http_get($urls, 10);

    // Process weather result
    if ($needsWeather) {
        $weatherData = null;
        if (isset($responses['weather'])) {
            $weatherData = json_decode($responses['weather'], true);
            if ($weatherData) {
                $weatherData = enrich_weather_data($weatherData, $lat, $lon);
                cache_set($weatherKey, $weatherData);
            }
        }
        $weatherResult = $weatherData;
    } else {
        $weatherResult = $weatherCached;
    }

    // Process air quality result
    if ($needsAir) {
        $airData = null;
        if (isset($responses['air'])) {
            $airData = json_decode($responses['air'], true);
            if ($airData) {
                cache_set($airKey, $airData);
            }
        }
        $airResult = $airData;
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

// ─── HISTORICAL DATA ────────────────────────────────────────────────────

function get_historical_cache_key($lat, $lon, $start_date, $end_date) {
    return 'historical_' . intval($lat * 1000) . '_' . intval($lon * 1000) . '_' . $start_date . '_' . $end_date . '.json';
}

function historical_cache_get($key) {
    $file = cache_dir() . DIRECTORY_SEPARATOR . $key;
    if (!file_exists($file)) return null;
    $data = json_decode(file_get_contents($file), true);
    if (!$data || !isset($data['payload'])) return null;
    return $data['payload'];
}

function historical_cache_set($key, $payload) {
    $file = cache_dir() . DIRECTORY_SEPARATOR . $key;
    $data = ['fetched_at' => time(), 'payload' => $payload];
    file_put_contents($file, json_encode($data));
}

function fetch_historical($lat, $lon, $start_date, $end_date) {
    $lat = round(floatval($lat), 4);
    $lon = round(floatval($lon), 4);

    $key = get_historical_cache_key($lat, $lon, $start_date, $end_date);
    $cached = historical_cache_get($key);
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

    $opts = [
        'http' => [
            'timeout' => 20,
            'header'  => "User-Agent: " . USER_AGENT . "\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $res = @file_get_contents($url, false, $context);

    if (!$res) return null;
    $data = json_decode($res, true);
    if (!$data || !isset($data['daily'])) return null;

    $codes = weather_code_to_geo();
    if (isset($data['daily']['weathercode'])) {
        foreach ($data['daily']['weathercode'] as $i => $c) {
            $desc = $codes[$c] ?? 'უცნობი';
            $data['daily']['description_geo'][$i] = $desc;
            $data['daily']['icon'][$i] = weather_code_to_icon($c, true);
        }
    }

    historical_cache_set($key, $data);
    return $data;
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
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => USER_AGENT,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response && $httpCode === 200) {
            $decoded = json_decode($response, true);
            if (is_array($decoded)) {
                file_put_contents($cacheFile, $response);
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
        $todayHoliday = !empty($h['localName']) ? $h['localName'] : ($h['name'] ?? null);
        break;
    }
}

// ─── EARTHQUAKES ────────────────────────────────────────────────────────

function checkEarthquakeRisk() {
    $cacheFile = cache_dir() . '/earthquake_alert.json';

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < CACHE_TTL_EARTHQUAKE)) {
        return json_decode(file_get_contents($cacheFile), true) ?? ['active' => false];
    }

    $url = "https://earthquake.usgs.gov/fdsnws/event/1/query?format=geojson"
         . "&starttime=" . date('Y-m-d', strtotime('-1 day'))
         . "&minmagnitude=2.5";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => USER_AGENT,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $alertData = ['active' => false, 'mag' => 0, 'place' => '', 'time' => ''];

    if ($response && $httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['features']) && is_array($data['features'])) {
            foreach ($data['features'] as $event) {
                $props  = $event['properties'];
                $coords = $event['geometry']['coordinates'];

                if ($coords[1] >= 40.0 && $coords[1] <= 44.0 &&
                    $coords[0] >= 39.0 && $coords[0] <= 47.0) {
                    if ($props['mag'] >= 4.0) {
                        $alertData = [
                            'active' => true,
                            'mag'    => $props['mag'],
                            'place'  => $props['place'] ?? '',
                            'time'   => date('H:i', intval($props['time'] / 1000))
                        ];
                        break;
                    }
                }
            }
        }
        file_put_contents($cacheFile, json_encode($alertData));
    }

    return $alertData;
}

$eqAlert = checkEarthquakeRisk();

function translateLocation($place) {
    $search  = ['/\bGeorgia\b/', '/\bof\b\s*/', '/\bEast\b/', '/\bWest\b/',
                '/\bNorth\b/', '/\bSouth\b/', '/\bkm\b/'];
    $replace = ['საქართველო', '', 'აღმოსავლეთ', 'დასავლეთ',
                'ჩრდილოეთ', 'სამხრეთ', 'კმ'];
    return trim(preg_replace($search, $replace, $place));
}

$georgianPlace = translateLocation($eqAlert['place']);

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

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true, // ✅ FIXED: SSL verification enabled
        CURLOPT_USERAGENT => USER_AGENT,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $firePoints = [];

    if ($response && strlen($response) > 50) {
        $lines = explode("\n", trim($response));
        $headers = str_getcsv(array_shift($lines));

        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) === count($headers)) {
                $row = array_combine($headers, $data);
                $confidenceRaw = strtolower($row['confidence']);
                $brightnessK = floatval($row['bright_ti4']);

                if ($confidenceRaw === 'h' && $brightnessK > 325) {
                    $tempC = round($brightnessK - 273.15);
                    $confText = 'ღია ხანძარი';
                    $lat = floatval($row['latitude']);
                    $lng = floatval($row['longitude']);

                    if ($lng < 41.5) {
                        $region = "დასავლეთ საქართველო";
                    } elseif ($lng > 44.5) {
                        $region = "კახეთის რეგიონი";
                    } elseif ($lat < 41.8) {
                        $region = "სამხრეთ საქართველო";
                    } else {
                        $region = "შიდა ქართლი / მცხეთა-მთიანეთი";
                    }

                    $firePoints[] = [
                        'lat'    => $lat,
                        'lng'    => $lng,
                        'temp'   => $tempC,
                        'conf'   => $confText,
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



function get_last_year_temp($lat, $lon) {
    $last_year_date = date('Y-m-d', strtotime('-1 year'));
    $cache_file = __DIR__ . "/cache/hist_{$lat}_{$lon}_{$last_year_date}.json";

    // Check cache but only if temp is NOT null
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < 86400)) {
        $cached = json_decode(file_get_contents($cache_file), true);
        if ($cached && isset($cached['temp']) && $cached['temp'] !== null) {
            return $cached;
        }
        // If cached temp is null, delete stale cache and retry
        if (isset($cached['temp']) && $cached['temp'] === null && filemtime($cache_file) < time() - 3600) {
            unlink($cache_file);
        }
    }

    // Open-Meteo Historical API
    $url = "https://archive-api.open-meteo.com/v1/archive?latitude={$lat}&longitude={$lon}&start_date={$last_year_date}&end_date={$last_year_date}&hourly=temperature_2m";
    
    $context = stream_context_create(['http' => ['timeout' => 10, 'header' => "User-Agent: " . USER_AGENT . "\r\n"]]);
    $response = @file_get_contents($url, false, $context);
    if ($response) {
        $data = json_decode($response, true);
        $hour = (int)date('H');
        
        // Try current hour first, then fall back to nearest available hour
        $temp = $data['hourly']['temperature_2m'][$hour] ?? null;
        if ($temp === null && isset($data['hourly']['temperature_2m'])) {
            // Find the closest non-null temperature
            $temps = $data['hourly']['temperature_2m'];
            if (!empty($temps)) {
                $closest = null;
                $closestDiff = PHP_INT_MAX;
                foreach ($temps as $h => $t) {
                    if ($t !== null) {
                        $diff = abs($h - $hour);
                        if ($diff < $closestDiff) {
                            $closestDiff = $diff;
                            $closest = $t;
                        }
                    }
                }
                if ($closest !== null) $temp = $closest;
            }
        }
        
        // Only cache if we got a valid temperature
        if ($temp !== null) {
            file_put_contents($cache_file, json_encode(['temp' => $temp, 'fetched_at' => time()]));
            return ['temp' => $temp];
        }
        // Don't cache null values — allow retry on next page load
    }
    return null;
}


/**
 * ამინდის საშიშროების განსაზღვრა
 */
function get_weather_alert($weather) {
    $current = $weather['current'] ?? null;
    $hourly = $weather['hourly'] ?? null;
    
    if (!$current || !$hourly) return null;

    // 1. ჯერ ვამოწმებთ მიმდინარე საფრთხეს (Real-time)
    $is_storm_now = in_array($current['weather_code'], [95, 96, 99]);
    $is_windy_now = $current['wind_speed_10m'] >= 50;

    if ($is_storm_now || $is_windy_now) {
        return [
            'type' => 'danger', // წითელი
            'title' => $is_storm_now ? "ძლიერი შტორმი" : "ძლიერი ქარი",
            'status' => "მიმდინარე საფრთხე",
            'icon' => $is_storm_now ? "fa-bolt-lightning" : "fa-wind",
            'wind' => round($current['wind_speed_10m'])
        ];
    }

    // 2. თუ ახლა სიმშვიდეა, ვამოწმებთ მომდევნო 12 საათს (Forecast)
    for ($i = 1; $i <= 12; $i++) {
        $h_code = $hourly['weather_code'][$i] ?? 0;
        $h_wind = $hourly['wind_speed_10m'][$i] ?? 0;

        if (in_array($h_code, [95, 96, 99]) || $h_wind >= 50) {
            return [
                'type' => 'warning', // ყვითელი/ნარინჯისფერი
                'title' => in_array($h_code, [95, 96, 99]) ? "მოსალოდნელია შტორმი" : "მოსალოდნელია ძლიერი ქარი",
                'status' => "მომდევნო 12 სთ-ში",
                'icon' => in_array($h_code, [95, 96, 99]) ? "fa-cloud-bolt" : "fa-wind",
                'wind' => round($h_wind)
            ];
        }
    }

    return null;
}



?>