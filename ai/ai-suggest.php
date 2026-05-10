<?php
// AI suggestion loader
// Reads ai-suggest.txt (simple sectioned file) and picks a suggestion

function ai_load_suggestions($path = null) {
    if ($path === null) $path = __DIR__ . DIRECTORY_SEPARATOR . 'ai-suggest.txt';
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $sections = [];
    $cur = 'default';
    foreach ($lines as $raw) {
        $line = trim($raw);
        if ($line === '') continue;
        // header (no starting quote and not a comment)
        if (substr($line,0,1) !== '"' && strpos($line,'"') === false) {
            $cur = strtolower(trim($line));
            if (!isset($sections[$cur])) $sections[$cur] = [];
            continue;
        }
        // quoted string lines - strip leading/trailing quotes and optional trailing comma
        $line = trim($line);
        // remove trailing comma
        if (substr($line, -1) === ',') $line = substr($line, 0, -1);
        // remove surrounding quotes if present
        if ((substr($line,0,1) === '"' && substr($line,-1) === '"') || (substr($line,0,1) === "'" && substr($line,-1) === "'")) {
            $line = substr($line,1,-1);
        }
        $line = trim($line);
        if ($line === '') continue;
        if (!isset($sections[$cur])) $sections[$cur] = [];
        $sections[$cur][] = $line;
    }
    return $sections;
}

function ai_pick_random($arr) {
    if (empty($arr)) return null;
    return $arr[array_rand($arr)];
}

function ai_get_suggestion($current = null, $hourly = null, $currentIndex = null) {
    $sections = ai_load_suggestions();

    // helper to check section existence
    $has = function($k) use ($sections) { return isset($sections[$k]) && !empty($sections[$k]); };

    // derive simple context
    $code = isset($current['weathercode']) ? intval($current['weathercode']) : null;
    $temp = isset($current['temperature']) ? floatval($current['temperature']) : null;
    $windspeed = isset($current['windspeed']) ? floatval($current['windspeed']) : null;

    // priorities: thunder/rain, snow, fog, windy, hot, cold, clear/partlycloudy/cloudy

    // rain/drizzle codes
    $rain_codes = array_merge(range(51,57), [61,63,65,80,81,82]);
    $snow_codes = array_merge(range(71,77), [85,86]);
    $fog_codes = [45,48];

    // determine if precipitation likely (use hourly precip prob if available)
    $precip_prob = null;
    if ($hourly && isset($hourly['precipitation_probability']) && $currentIndex !== null && isset($hourly['precipitation_probability'][$currentIndex])) {
        $precip_prob = intval($hourly['precipitation_probability'][$currentIndex]);
    }

    // decide category
    // 1) snow
    if ($code !== null && in_array($code, $snow_codes, true) && $has('snow')) return ai_pick_random($sections['snow']);
    // 2) fog
    if ($code !== null && in_array($code, $fog_codes, true) && $has('fog')) return ai_pick_random($sections['fog']);
    // 3) rain (or high precip probability)
    if (($code !== null && in_array($code, $rain_codes, true)) || ($precip_prob !== null && $precip_prob >= 30)) {
        if ($has('rain')) return ai_pick_random($sections['rain']);
    }
    // 4) windy
    if ($windspeed !== null && $windspeed >= 15 && $has('windy')) return ai_pick_random($sections['windy']);
    // 5) hot / cold by temperature thresholds
    if ($temp !== null) {
        if ($temp >= 25 && $has('hot')) return ai_pick_random($sections['hot']);
        if ($temp <= 15 && $has('cold')) return ai_pick_random($sections['cold']);
    }
    // 6) clear / partlycloudy / cloudy by code
    if ($code === 0 && $has('clear')) return ai_pick_random($sections['clear']);
    if ($code === 3 && $has('cloudy')) return ai_pick_random($sections['cloudy']);
    if (($code === 1 || $code === 2) && $has('partlycloudy')) return ai_pick_random($sections['partlycloudy']);

    // 7) fallback to default
    if ($has('default')) return ai_pick_random($sections['default']);

    // last resort - pick any available
    foreach ($sections as $arr) { if (!empty($arr)) return ai_pick_random($arr); }
    return '';
}

?>
