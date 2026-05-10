<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
// 1. სესია და აუცილებელი ფუნქციები
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/functions.php';

// 2. კოორდინატების განსაზღვრა (ერთიანი helper)
[$lat, $lon] = resolve_coordinates($_GET['lat'] ?? null, $_GET['lon'] ?? null);

// 3. მონაცემების წამოღება (პარალელურად weather + air)
$weatherData = fetch_weather_and_air($lat, $lon);
$weather = $weatherData['weather'];
$air_quality = $weatherData['air_quality'];

// 4. IP-სგან დამოუკიდებელი ქალაქის სახელი (cached Nominatim)
$placeName = get_location_name($lat, $lon);

// 5. სესიის და SEO-სთვის საჭირო მონაცემები
$city_name = $placeName;
$_SESSION['city_name'] = $city_name;
$_SESSION['weather_cache'] = $weather;
$_SESSION['is_day'] = $weather['current_weather']['is_day'] ?? 1;

$pageTitle = "ამინდი " . $city_name;
$pageDesc = "ზუსტი ამინდის პროგნოზი " . $city_name . "-ში. ტემპერატურა, ტენიანობა და ჰაერის ხარისხი რეალურ დროში Grubeli.ge-ზე.";

// 6. Header და დანარჩენი ფაილები
include 'header.php';
require_once __DIR__ . '/ai/ai_helper.php'; 
require_once __DIR__ . '/ai/ai-suggest.php';

// 7. მონაცემების დამუშავება UI-სთვის
$tz = $weather['timezone'] ?? 'UTC';
$now = new DateTime('now', new DateTimeZone($tz));

$current = $weather['current_weather'] ?? null;
$current_temp = $current['temperature'] ?? '--';
$current_desc = $current['description_geo'] ?? '';
$is_day = ($current['is_day'] ?? 1) == 1;
$current_icon = isset($current['icon']) ? icon_url($current['icon'], $is_day) : 'icons/sun.svg';

// საათობრივი პროგნოზი
$hourly = $weather['hourly'] ?? null;
$hourly_items = [];
if ($hourly && isset($hourly['time'])) {
    $target = clone $now; $target->modify('+2 hour');
    $firstIndex = null;
    foreach ($hourly['time'] as $i => $t) {
        if (strtotime($t) >= $target->getTimestamp()) { $firstIndex = $i; break; }
    }
    if ($firstIndex !== null) {
        for ($k = 0; $k < 12; $k++) {
            $idx = $firstIndex + $k;
            if (!isset($hourly['time'][$idx])) break;
            $h_time = new DateTime($hourly['time'][$idx], new DateTimeZone($tz));
            $is_day_h = (intval($h_time->format('H')) >= 6 && intval($h_time->format('H')) < 20);
            $hourly_items[] = [
                'label' => $h_time->format('H:i'),
                'temp' => $hourly['temperature_2m'][$idx] ?? '--',
                'icon' => isset($hourly['icon'][$idx]) ? icon_url($hourly['icon'][$idx], $is_day_h) : 'icons/sun.svg',
                'desc' => $hourly['description_geo'][$idx] ?? ''
            ];
        }
    }
    
    // UV და AQI-სთვის ინდექსის პოვნა
    $currentIndex = null;
    $nowTs = $now->getTimestamp();
    $closest = null; $closestDiff = PHP_INT_MAX;
    foreach ($hourly['time'] as $i => $t) {
        $diff = abs(strtotime($t) - $nowTs);
        if ($diff < $closestDiff) { $closestDiff = $diff; $closest = $i; }
    }
    if ($closest !== null && $closestDiff <= 90 * 60) $currentIndex = $closest;
}

// დღიური პროგნოზი
$daily = $weather['daily'] ?? null;
$daily_items = [];
if ($daily && isset($daily['time'])) {
    $total = count($daily['time']);
    $startIndex = ($total > 1) ? 1 : 0;
    for ($k = 0; $k < min(10, $total - $startIndex); $k++) {
        $i = $startIndex + $k;
        $dt = new DateTime($daily['time'][$i], new DateTimeZone($tz));
        $daily_items[] = [
            'date' => $dt,
            'icon' => isset($daily['icon'][$i]) ? icon_url($daily['icon'][$i], true) : 'icons/sun.svg',
            'temp_max' => $daily['temperature_2m_max'][$i] ?? '--',
            'temp_min' => $daily['temperature_2m_min'][$i] ?? '--',
            'desc' => $daily['description_geo'][$i] ?? ''
        ];
    }
}

// --- UV ინდექსის გამოთვლა ---
$uv_value = null;
if (isset($hourly['uv_index'][$currentIndex])) {
    $uv_value = round($hourly['uv_index'][$currentIndex], 1);
}

$uv_label = ($uv_value !== null) ? $uv_value : '--';
$uv_class = 'uv-low';
$uv_text = '---';

if ($uv_value !== null) {
    if ($uv_value < 3) { $uv_class = 'uv-low'; $uv_text = 'დაბალი'; }
    elseif ($uv_value < 6) { $uv_class = 'uv-moderate'; $uv_text = 'საშუალო'; }
    elseif ($uv_value < 8) { $uv_class = 'uv-high'; $uv_text = 'მაღალი'; }
    elseif ($uv_value < 11) { $uv_class = 'uv-very-high'; $uv_text = 'ძალიან მაღალი'; }
    else { $uv_class = 'uv-extreme'; $uv_text = 'ექსტრემალური'; }
}
// AQI
$aq_label = '--'; $aq_class = '';
if ($air_quality && isset($air_quality['hourly']['time']) && isset($currentIndex)) {
    $aq_v = intval($air_quality['hourly']['us_aqi'][$currentIndex] ?? 0);
    if ($aq_v > 0) {
        if ($aq_v <= 50) { $aq_label = $aq_v . ' (კარგი)'; $aq_class='aq-good'; }
        elseif ($aq_v <= 100) { $aq_label = $aq_v . ' (ზომიერი)'; $aq_class='aq-moderate'; }
        else { $aq_label = $aq_v . ' (ყურადღება)'; $aq_class='aq-unhealthy'; }
    }
}

$sunrise_label = '--:--';
$sunset_label = '--:--';
$day_length = '--:--';

if (isset($weather['daily']['sunrise'][0]) && isset($weather['daily']['sunset'][0])) {
    $sr = new DateTime($weather['daily']['sunrise'][0], new DateTimeZone($tz));
    $ss = new DateTime($weather['daily']['sunset'][0], new DateTimeZone($tz));
    
    $sunrise_label = $sr->format('H:i');
    $sunset_label = $ss->format('H:i');
    
    // დღის ხანგრძლივობის გამოთვლა
    $diff = $sr->diff($ss);
    $day_length = $diff->format('%h სთ %i წთ');
}
?>

<div class="container justify-content-center mt-3">
    <?php 
$fireData = checkFireRisk(defined('NASA_MAP_KEY') ? NASA_MAP_KEY : '');

if (!empty($fireData['active']) && isset($fireData['points'][0])): 
    $p = $fireData['points'][0]; 
    $mapUrl = "https://firms.modaps.eosdis.nasa.gov/map/#d:24hrs;l:viirs_snpp_nrt;@" . $p['lat'] . "," . $p['lng'] . ",12z";
?>
<div class="alert-card shadow-sm mb-4" style="background: rgba(255, 68, 68, 0.12); border-left: 4px solid #ff4444; border-radius: 12px; backdrop-filter: blur(10px);">
    <div class="card-body p-2 px-3 text-white">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center text-truncate">
                <i class="fa-solid fa-fire-flame-curved text-danger me-2 pulse-animation" style="font-size: 1.3rem;"></i>
                <div class="text-truncate">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="m-0 fw-bold text-truncate" style="font-size: 0.9rem;">
                            ხანძარი: <?php echo $p['region'] ?? 'საქართველო'; ?>
                        </h6>
                    </div>
                    <small class="text-white-50" style="font-size: 0.7rem;">
                        სტატუსი: <span class="text-white"><?php echo $p['conf'] ?? 'საშუალო'; ?></span> | 
                        ტემპ: <span class="text-white"><?php echo $p['temp'] ?? round(($p['bright'] ?? 273.15) - 273.15); ?>°C</span>
                    </small>
                </div>
            </div>

            <a href="<?php echo $mapUrl; ?>" target="_blank" class="btn btn-danger py-1 px-2 ms-2" style="font-size: 0.65rem; border-radius: 8px; font-weight: 600; white-space: nowrap;">
                <i class="fa-solid fa-location-dot"></i> რუკა
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if ($eqAlert['active']): ?>
<div class="alert-earthquake premium-glass p-3 mb-4 mt-2 reveal-up" 
     style="border-left: 5px solid #ff4757; background: rgba(255, 71, 87, 0.15);">
    
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0 me-3">
            <i class="fa-solid fa-house-chimney-crack text-danger pulse-animation" style="font-size: 2rem;"></i>
        </div>
        <div>
            <h5 class="m-0 text-white fw-bold" style="font-family: 'BPG NinoMtavruli';">
                ყურადღება: მიწისძვრა!
            </h5>
            <p class="m-0 text-white-50" style="font-size: 0.9rem;">
                დაფიქსირდა <strong><?php echo htmlspecialchars((string)$eqAlert['mag'], ENT_QUOTES, 'UTF-8'); ?></strong>
                მაგნიტუდის ბიძგები: <?php echo htmlspecialchars($georgianPlace, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($eqAlert['time'], ENT_QUOTES, 'UTF-8'); ?>)
            </p>
        </div>
    </div>
</div>

<style>
.pulse-animation {
    animation: dangerPulse 1.5s infinite;
}
@keyframes dangerPulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
<?php endif; ?>

<div class="search-section">
    <div class="search-wrapper">
        <span class="search-icon">🔍</span>
        <input type="text" 
               id="citySearch" 
               class="search-input" 
               placeholder="ქალაქი საქართველოში..." 
               autocomplete="off"
               role="combobox" 
               aria-expanded="false" 
               aria-controls="suggestions" 
               aria-autocomplete="list">
    </div>
    <div id="suggestions" class="suggestions-list" role="listbox"></div>
</div>


<?php
// 1. დროის განსაზღვრა (06:00-დან 20:00-მდე დღეა, სხვა დროს ღამე)
date_default_timezone_set('Asia/Tbilisi');
$hour = (int)date('H');

if ($hour >= 6 && $hour < 20) {
    $card_bg = 'images/widget_bg_day_image_v2.png';
} else {
    $card_bg = 'images/widget_bg_night_image_v2.png';
}
?>

<div id="main-weather-card" class="text-center position-relative overflow-hidden mt-2 mb-4 p-4 p-md-5" 
     style="background-image: url('<?php echo $card_bg; ?>'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            border-radius: 24px; 
            min-height: 380px;
            background-attachment: scroll;">
  
  <!-- მხოლოდ ერთი დაბნელების ფენა, ამბიენტური გლოუების გარეშე -->
  <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: 1; border-radius: 24px;"></div>

  <div class="card-body position-relative" style="z-index: 3; display: flex; flex-direction: column; justify-content: center; min-height: 320px;">
    
    <div class="location-header mb-2">
      <h2 class="location-title m-0" style="font-family: 'BPG NinoMtavruli'; font-size: 1.5rem; font-weight: 600; text-shadow: 0 2px 4px rgba(0,0,0,0.4);">
        <i class="fa-solid fa-location-dot text-info me-2"></i><?php echo htmlspecialchars($placeName, ENT_QUOTES, 'UTF-8'); ?>
      </h2>
    </div>
    
    <p class="daytime mb-3" style="color: rgba(255,255,255,0.8); font-size: 0.9rem; letter-spacing: 0.5px; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
      <i class="fa-regular fa-calendar-days me-1"></i> <?php echo htmlspecialchars(format_georgian_datetime($now), ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
      <div class="debug-badge mb-3" style="font-size:11px; color:#ffb74d; background: rgba(0,0,0,0.4); padding: 4px 8px; border-radius: 4px; display: inline-block;">
        DEBUG: lat=<?php echo htmlspecialchars($lat); ?> lon=<?php echo htmlspecialchars($lon); ?>
      </div>
    <?php endif; ?>

    <div class="weather-core d-flex flex-column align-items-center justify-content-center my-2">
      <div class="main-icon-wrapper mb-2 position-relative">
        <img class="main-icon-weather float-icon" 
             style="width: 110px; height: 110px; object-fit: contain; filter: drop-shadow(0 8px 12px rgba(0,0,0,0.15));"
             width="110" height="110" 
             src="<?php echo htmlspecialchars($current_icon, ENT_QUOTES, 'UTF-8'); ?>" 
             alt="ამინდის მთავარი აიკონი" />
      </div>
      
      <div class="weather-text d-flex align-items-start justify-content-center">
        <span class="fw-bold text-white" style="font-size: 3.5rem; line-height: 1; text-shadow: 0 4px 12px rgba(0,0,0,0.3);">
          <?php echo (is_numeric($current_temp) ? round($current_temp) : '--'); ?>
        </span>
        <span class="unit ms-1 text-white-50" style="font-size: 1.5rem; font-weight: 300; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">&deg;C</span>
      </div>
    </div> 

    <div class="weather-details-footer mt-3 pt-2" style="border-top: 1px solid rgba(255,255,255,0.15);">
      <h3 class="weather-description mb-1" style="font-family: 'BPG NinoMtavruli'; font-size: 1.1rem; font-weight: 500; color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
        <?php echo htmlspecialchars($current_desc, ENT_QUOTES, 'UTF-8'); ?>
      </h3>
      
      <p class="feels-like m-0" style="font-size: 0.85rem; color: rgba(255,255,255,0.7); text-shadow: 0 1px 2px rgba(0,0,0,0.3);">
        <i class="fa-solid fa-temperature-half me-1"></i> შეგრძნებით: 
        <strong class="text-white">
          <?php
            $feels = ($hourly && isset($currentIndex) && isset($hourly['apparent_temperature'][$currentIndex])) 
              ? $hourly['apparent_temperature'][$currentIndex] 
              : (($hourly && isset($firstIndex) && isset($hourly['apparent_temperature'][$firstIndex])) 
                ? $hourly['apparent_temperature'][$firstIndex] 
                : ($current['temperature'] ?? '--'));
            echo htmlspecialchars(is_numeric($feels) ? round($feels) : '--', ENT_QUOTES, 'UTF-8'); 
          ?>&deg;C
        </strong>
      </p>
    </div>

  </div> 
</div>

<?php if ($todayHoliday): ?>
 <div id="main-weather-card" class="premium-glass main-hero-card text-center position-relative overflow-hidden reveal-up mt-4 mb-4 p-4 p-md-3">
  
  <div class="ambient-glow glow-1" style="top: -50px; left: -50px; background: #0dcaf0; opacity: 0.2;"></div>
  <div class="ambient-glow glow-2" style="bottom: -50px; right: -50px; background: #8a2be2; opacity: 0.15;"></div>

    <div class="d-flex align-items-center">
        <div class="holiday-icon-box me-3">
            <i class="fa-regular fa-calendar-days" style="font-size: 2.2rem;"></i>
        </div>
        <div>
            <small class="text-white-50 d-block mb-1 text-start" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                დღეს უქმე დღეა
            </small>
            <h5 class="m-0 text-white fw-bold" style="font-family: 'BPG NinoMtavruli'; font-size: 1.15rem;">
                <?php echo htmlspecialchars($todayHoliday, ENT_QUOTES, 'UTF-8'); ?>
            </h5>
        </div>
    </div>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/ai/quotes.php'; ?>
 <div class="quote-container shadow-sm">
        <p class="quote-text">
            <?php echo get_random_weather_quote(); ?>
        </p>
    </div>
<style>
.pulse-animation {
    animation: fire-pulse 1.5s infinite;
}

@keyframes fire-pulse {
    0% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(255, 68, 68, 0.7)); }
    50% { transform: scale(1.1); filter: drop-shadow(0 0 10px rgba(255, 68, 68, 0.9)); }
    100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(255, 68, 68, 0.7)); }
}

.border-white-10 {
    border-color: rgba(255, 255, 255, 0.1) !important;
}
</style>


<div class="container-fluid px-0 mt-3 mb-4">
    <div class="premium-glass shadow position-relative overflow-hidden reveal-up" style="border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.08);">
        
        <div class="ambient-glow" style="position: absolute; width: 200px; height: 200px; top: -100px; right: -50px; background: rgba(13, 202, 240, 0.15); filter: blur(80px); pointer-events: none;"></div>

        <div class="p-4">
      <div class="position-relative">

    <!-- LEFT ARROW -->
    <button class="scroll-btn scroll-left d-none d-md-flex align-items-center justify-content-center">
        ‹
    </button>

    <!-- RIGHT ARROW -->
    <button class="scroll-btn scroll-right d-none d-md-flex align-items-center justify-content-center">
        ›
    </button>
</div>

            <div class="hourly-scroll-container" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <div class="d-flex pb-2" style="gap: 12px; width: max-content; min-width: 100%;">
                    <?php if (!empty($hourly_items)): foreach ($hourly_items as $h): ?>
                        <div class="hourly-column text-center p-3" style="width: 100px; background: rgba(255,255,255,0.03); border-radius: 18px; border: 1px solid rgba(255,255,255,0.05);">
                            <div class="small text-white-50 mb-2" style="font-size: 0.75rem;">
                                <?php echo htmlspecialchars($h['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars($h['icon'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="icon" 
                                     style="width: 42px; height: 42px; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));" />
                            </div>
                            <div class="fw-bold text-white mb-1" style="font-size: 1.1rem;">
                                <?php echo htmlspecialchars(is_numeric($h['temp']) ? round($h['temp']) : '--', ENT_QUOTES, 'UTF-8'); ?>&deg;
                            </div>
                           
                        </div>
                    <?php endforeach; else: ?>
                        <div class="text-white-50 p-3">მონაცემები არ არის</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector(".hourly-scroll-container");
    const leftBtn = document.querySelector(".scroll-left");
    const rightBtn = document.querySelector(".scroll-right");

    if (!container) return;

    const scrollAmount = 300;

    function updateArrows() {
        const scrollLeft = container.scrollLeft;
        const maxScroll = container.scrollWidth - container.clientWidth;

        if (scrollLeft <= 5) {
            leftBtn.style.opacity = "0";
            leftBtn.style.pointerEvents = "none";
        } else {
            leftBtn.style.opacity = "1";
            leftBtn.style.pointerEvents = "auto";
        }

        if (scrollLeft >= maxScroll - 5) {
            rightBtn.style.opacity = "0";
            rightBtn.style.pointerEvents = "none";
        } else {
            rightBtn.style.opacity = "1";
            rightBtn.style.pointerEvents = "auto";
        }
    }

    function positionArrows() {
        const rect = container.getBoundingClientRect();
        const parentRect = container.parentElement.getBoundingClientRect();
        const centerY = rect.top - parentRect.top + rect.height / 2 - 30;

        leftBtn.style.top = centerY + "px";
        rightBtn.style.top = centerY + "px";
    }

    positionArrows();
    updateArrows();

    window.addEventListener("resize", () => {
        positionArrows();
        updateArrows();
    });

    container.addEventListener("scroll", updateArrows);

    rightBtn.addEventListener("click", () => {
        container.scrollBy({ left: scrollAmount, behavior: "smooth" });
    });

    leftBtn.addEventListener("click", () => {
        container.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    });
});
</script>




<style>
    .premium-card {
       background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        transition: transform 0.3s ease;
    }
    .premium-card:hover {
        transform: translateY(-3px);
          background: rgba(255, 255, 255, 0.03);
    }
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.5);
        display: block;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #fff;
    }
    .icon-box {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        margin-bottom: 12px;
        background: rgba(255, 255, 255, 0.1);
    }
</style>
<div class="weather-details-grid mt-3 mb-3">
    <div class="detail-item item-aqi shadow-sm">
        <div class="detail-header">
            <p class="detail-label"><i class="fa-solid fa-aquarius"></i> ჰაერი</p>
            <a data-bs-toggle="modal" data-bs-target="#modalaqv" class="detail-info-btn">
                <i class="fa-regular fa-circle-question quess-icon"></i>
            </a>
        </div>
        <h5 class="detail-value"><?php echo htmlspecialchars($aq_label, ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>

    <div class="detail-item item-prec shadow-sm">
        <p class="detail-label"><i class="fa-solid fa-cloud-showers-heavy"></i> ალბათობა</p>
        <h5 class="detail-value">
            <?php 
            $prec = ($hourly && isset($currentIndex) && isset($hourly['precipitation_probability'][$currentIndex])) ? $hourly['precipitation_probability'][$currentIndex] . '%' : (($hourly && isset($firstIndex) && isset($hourly['precipitation_probability'][$firstIndex])) ? $hourly['precipitation_probability'][$firstIndex] . '%' : '--');
            echo htmlspecialchars($prec, ENT_QUOTES, 'UTF-8'); 
            ?>
        </h5>
    </div>

    <div class="detail-item item-wind shadow-sm">
        <p class="detail-label"><i class="fa-solid fa-wind"></i> ქარი</p>
        <h5 class="detail-value">
            <?php
            $wind = $current['windspeed'] ?? (($hourly && isset($currentIndex) && isset($hourly['windspeed_10m'][$currentIndex])) ? $hourly['windspeed_10m'][$currentIndex] : (($hourly && isset($firstIndex) && isset($hourly['windspeed_10m'][$firstIndex])) ? $hourly['windspeed_10m'][$firstIndex] : '--'));
            echo htmlspecialchars($wind, ENT_QUOTES, 'UTF-8'); 
            ?> <small style="font-size: 10px;">კმ/სთ</small>
        </h5>
    </div>

    <div class="detail-item item-hum shadow-sm">
        <p class="detail-label"><i class="fa-solid fa-droplet"></i> ტენიანობა</p>
        <h5 class="detail-value">
            <?php
            $hum = ($hourly && isset($currentIndex) && isset($hourly['relativehumidity_2m'][$currentIndex])) ? $hourly['relativehumidity_2m'][$currentIndex] . '%' : (($hourly && isset($firstIndex) && isset($hourly['relativehumidity_2m'][$firstIndex])) ? $hourly['relativehumidity_2m'][$firstIndex] : '--');
            echo htmlspecialchars($hum, ENT_QUOTES, 'UTF-8'); 
            ?>
        </h5>
    </div>
    
</div>
<div class="container mt-4 mb-4">
    <div class="row row-cols-2 row-cols-lg-4 g-3">
        
        <div class="col">
            <div class="card premium-card h-100 border-0 shadow">
                <div class="card-body p-3">
                    <div class="icon-box">
                        <i class="fa-solid fa-sun fa-spin" style="--fa-animation-duration: 15s; color:#F5B727"></i>
                    </div>
                    <span class="info-label"> მზის ამოსვლა</span>
                    <div class="info-value">
                        <?php echo htmlspecialchars($sunrise_label, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card premium-card h-100 border-0 shadow">
                <div class="card-body p-3">
                    <div class="icon-box">
                        <i class="fa-solid fa-moon fa-fade"    style="--fa-animation-duration: 4s; color:#4fc3f7"></i>
                     
                    </div>
                    <span class="info-label">მზის ჩასვლა</span>
                    <div class="info-value">
                        <?php echo htmlspecialchars($sunset_label, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card premium-card h-100 border-0 shadow">
                <div class="card-body p-3">
                    <div class="icon-box">
                        <i class="fa-regular fa-clock fa-shake" style="--fa-animation-duration: 4s; color:#C46A58"></i>
                    </div>
                    <span class="info-label">ხანგრძლივობა</span>
                    <div class="info-value">
                        <?php echo htmlspecialchars($day_length ?? '--:--', ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card premium-card h-100 border-0 shadow">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="icon-box">
                            <i class="fa-solid fa-sun-plant-wilt fa-fade" style="--fa-animation-duration: 4s; color:#6BC282"></i>
                        </div>
                        <a data-bs-toggle="modal" data-bs-target="#modaluv" class="text-white-50">
                            <i class="fa-regular fa-circle-question quess-icon" style="cursor: pointer;font-size: 16px"></i>
                        </a>
                    </div>
                    <span class="info-label">UV ინდექსი</span>
                    <div class="info-value">
                        <?php if ($uv_value === null): ?>
                            --:--
                        <?php else: ?>
                            <?php echo htmlspecialchars($uv_label, ENT_QUOTES,'UTF-8'); ?> 
                            <small class="fw-normal text-white-50 ms-1" style="font-size: 0.8rem;">
                                <?php echo htmlspecialchars($uv_text, ENT_QUOTES,'UTF-8'); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
 <!-- Modal uv-->
<div class="modal fade" id="modaluv" tabindex="-1" aria-labelledby="modaluvlLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white ai-border-only">
      
     <div class="modal-body text-start">
         <a class='ai-button'>✨ AI განმარტება</a>
 <br><br>

   UV ინდექსი: მზის რადიაციის სიმძლავრის საზომი. რაც უფრო მაღალია ციფრი, მით უფრო სწრაფად ზიანდება კანი.

          <br>  <br>
<b><span style="color: #34EDA0">0-2</span> (დაბალი):</b> საფრთხე არ არსებობს.<br>
<b><span style="color: #EDAC34">3-5</span> (საშუალო):</b> გამოიყენეთ დამცავი საშუალებები.<br>
<b><span style="color: #EDAC34">6-7</span> (მაღალი):</b> მოერიდეთ მზეს 11-დან 16 საათამდე.<br>
<b><span style="color: #F74040">8-10</span> (ძალიან მაღალი):</b> აუცილებელია ჩრდილი და დამცავი.<br>
<b><span style="color: #F74040">11+</span> (ექსტრემალური):</b> მაქსიმალური სიფრთხილე!<br>


      </div>
    </div>
  </div>
</div>



<div class="ai-suggest-container mb-3 mb-sm-0 shadow-sm">
    <div id="ai-response-container" style="display: none; margin-bottom: 20px; padding: 15px; background: rgba(255, 255, 255, 0.05); border-radius: 18px; position: relative;">
        
        <button onclick="closeAIResponse()" class="close-ai-btn" style="position: absolute; top: 12px; right: 12px; background: none; border: none; color: white; cursor: pointer;">
            ✖
        </button>

        <div class="ai-badge-wrapper" style="margin-bottom: 10px;">
            <span class="ai-badge">✨ AI ასისტენტი</span>
        </div>

        <p id="ai-text-content" class="ai-status-text" style="margin: 0; padding-right: 30px; line-height: 1.5; color: #e0e6ed;"></p>
    </div>

 
    <div class="suggestion-grid">
        <button class="suggest-btn" onclick="askQuickAI('დამჭირდება დღეს ქოლგა?')">
            <span class="btn-icon">🌂</span>
            <span class="btn-text">ქოლგა?</span>
        </button>
        <button class="suggest-btn" onclick="askQuickAI('როგორ ჩავიცვა დღეს?')">
            <span class="btn-icon">🧥</span>
            <span class="btn-text">რა ჩავიცვა?</span>
        </button>
        <button class="suggest-btn" onclick="askQuickAI('მზის სათვალე დამჭირდება?')">
            <span class="btn-icon">😎</span>
            <span class="btn-text">მზის სათვალე?</span>
        </button>
        <button class="suggest-btn" onclick="askQuickAI('გამოდგება ამინდი სეირნობისთვის?')">
            <span class="btn-icon">🚶</span>
            <span class="btn-text">სეირნობა</span>
        </button>
        <button class="suggest-btn" onclick="askQuickAI('გამოდგება ამინდი მანქანის გასარეცხად?')">
            <span class="btn-icon">🚗 </span>
            <span class="btn-text">გარეცხვა</span>
        </button>
        <button class="suggest-btn" onclick="askQuickAI('შეიძლება დღეს გარეთ სირბილი ან ვარჯიში?')">
            <span class="btn-icon">🏃</span>
            <span class="btn-text">ვარჯიში</span>
        </button>
        <button class="suggest-btn" onclick="askQuickAI('გაშრება სარეცხი გარეთ სწრაფად?')">
            <span class="btn-icon">🧺</span>
            <span class="btn-text">სარეცხის გაფენა</span>
        </button>
        <button class="suggest-btn" onclick="askQuickAI('ველოსიპედით სასეირნოდ კარგი ამინდია?')">
            <span class="btn-icon">🚲</span>
            <span class="btn-text">ველო</span>
        </button>
    </div>
</div>

<script>
function closeAIResponse() {
    const responseContainer = document.getElementById('ai-response-container');
    if(responseContainer) {
        responseContainer.style.display = "none";
    }
}

function askQuickAI(question) {
    const textContainer = document.getElementById('ai-text-content');
    const responseContainer = document.getElementById('ai-response-container'); 
    
    if(!textContainer) {
        console.error("ID 'ai-text-content' ვერ მოიძებნა!");
        return;
    }

    if(responseContainer) {
        responseContainer.style.display = "block"; 
        responseContainer.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
    }

    textContainer.style.opacity = "0.5";
    textContainer.innerHTML = " ✨ ვფიქრობ...";

    const params = new URLSearchParams();
    params.append('message', question);

    fetch('ai/send_message.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params
    })
    .then(res => res.text())
    .then(data => {
        textContainer.style.opacity = "1";
        textContainer.innerHTML = data;
    })
    .catch(err => {
        console.error(err);
        textContainer.style.opacity = "1";
        textContainer.innerText = "შეცდომაა კავშირისას.";
    });
}
</script>

<div class="modal fade" id="modalaqv" tabindex="-1" aria-labelledby="modalaqvLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white ai-border-only" style="border-radius: 20px; border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-body text-start">
                <a class='ai-button' style="margin-bottom: 15px; display: inline-block;">✨ AI განმარტება</a>
                <br>
                <div style="font-size: 14px; line-height: 1.6;">
                    <b><span style="color: #34EDA0">0-50</span> (კარგი):</b> ჰაერი იდეალურად სუფთაა.<br>
                    <b><span style="color: #34EDA0">51-100</span> (ზომიერი):</b> ხარისხი მისაღებია...<br>
                    <b><span style="color: #EDAC34">101-150</span> (საფრთხილო):</b> მგრძნობიარე ჯგუფებისთვის არაჯანსაღია.<br>
                    <b><span style="color: #F74040">151-200</span> (არაჯანსაღი):</b> ჰაერი დაბინძურებულია.<br>
                    <b><span style="color: #F74040">200+</span> (საშიში):</b> საგანგაშო მდგომარეობა.
                </div>
            </div>
        </div>
    </div>
</div>


</div>



<div class="container mt-3 mb-3">
  
  <div class="forecast-grid">
    <?php if (!empty($daily_items)): 
      $days_short = ['კვი','ორშ','სამ','ოთხ','ხუთ','პარ','შაბ'];
      foreach ($daily_items as $d): ?>
      <div class="forecast-card">
        <div class="forecast-date"><?php echo htmlspecialchars($days_short[intval($d['date']->format('w'))] . ' ' . $d['date']->format('d.m'), ENT_QUOTES, 'UTF-8'); ?></div>
       <img src="<?php echo htmlspecialchars($d['icon'], ENT_QUOTES, 'UTF-8'); ?>" 
     alt="ამინდი - <?php echo htmlspecialchars($d['desc'], ENT_QUOTES, 'UTF-8'); ?>" 
     class="secondary-icon-weather" 
     width="40" height="40" 
     loading="lazy" />
        <div class="forecast-temp"><?php echo htmlspecialchars(is_numeric($d['temp_max']) ? round($d['temp_max']) : '--', ENT_QUOTES, 'UTF-8'); ?><span class="unit">&deg;C</span> / <?php echo htmlspecialchars(is_numeric($d['temp_min']) ? round($d['temp_min']) : '--', ENT_QUOTES, 'UTF-8'); ?><span class="unit">&deg;C</span></div>
        <div class="forecast-desc"><?php echo htmlspecialchars($d['desc'], ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
    <?php endforeach; else: ?>
      <div>მონაცემი არ არის</div>
    <?php endif; ?>
  </div>

</div>
<script>
    let cities = [];
    const searchInput = document.getElementById('citySearch');
    const suggestionBox = document.getElementById('suggestions');

    function latinToGeorgian(text) {
        let str = text.toLowerCase();
        const digraphs = {
            'sh': 'შ',
            'ch': 'ჩ',
            'ts': 'ც',
            'dz': 'ძ',
            'gh': 'ღ',
            'kh': 'ხ',
            'th': 'თ',
            'ph': 'ფ',
            'zh': 'ჟ'
        };
        const single = {
            'a': 'ა', 'b': 'ბ', 'c': 'ც', 'd': 'დ', 'e': 'ე',
            'f': 'ფ', 'g': 'გ', 'h': 'ჰ', 'i': 'ი', 'j': 'ჯ',
            'k': 'კ', 'l': 'ლ', 'm': 'მ', 'n': 'ნ', 'o': 'ო',
            'p': 'პ', 'q': 'ქ', 'r': 'რ', 's': 'ს', 't': 'თ',
            'u': 'უ', 'v': 'ვ', 'w': 'ჭ', 'x': 'ხ', 'y': 'ყ', 'z': 'ზ'
        };
        let result = '';
        let i = 0;
        while (i < str.length) {
            let matched = false;
            for (let dlen = 2; dlen >= 2; dlen--) {
                if (i + dlen <= str.length) {
                    const sub = str.substr(i, dlen);
                    if (digraphs[sub]) {
                        result += digraphs[sub];
                        i += dlen;
                        matched = true;
                        break;
                    }
                }
            }
            if (!matched) {
                const ch = str[i];
                result += single[ch] || ch;
                i++;
            }
        }
        return result;
    }

    fetch('cities.json')
        .then(response => {
            if (!response.ok) throw new Error("ფაილი ვერ ჩაიტვირთა");
            return response.json();
        })
        .then(data => {
            cities = data;
        })
        .catch(error => {
            console.error('შეცდომა cities.json-ის ჩატვირთვისას:', error);
        });

    // Debounced search
    let searchTimer = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const val = this.value.trim().toLowerCase();
            suggestionBox.innerHTML = '';
            
            if (cities.length === 0) return;
            if (val.length < 2) return;

            let valGeo = latinToGeorgian(val);
            
            const matches = cities.filter(city => {
                const cityNameLower = city.name.toLowerCase();
                return cityNameLower.includes(val) || cityNameLower.includes(valGeo);
            }).slice(0, 5);

            if (matches.length > 0) {
                matches.forEach(city => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item';
                    div.innerHTML = `<strong>${city.name}</strong> <small style="color: #aaa;">| ${city.region}</small>`;
                    div.onclick = () => {
                        if (typeof window.startLoading === 'function') {
                            window.startLoading();
                        }
                        localStorage.setItem('lat', city.lat);
                        localStorage.setItem('lon', city.lon);
                        fetch('save_location.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ lat: city.lat, lon: city.lon })
                        })
                        .then(response => response.json())
                        .then(data => {
                            window.location.href = `index.php?lat=${city.lat}&lon=${city.lon}`;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.location.href = `index.php?lat=${city.lat}&lon=${city.lon}`;
                        });
                    };
                    suggestionBox.appendChild(div);
                });
            } else {
                const div = document.createElement('div');
                div.className = 'list-group-item';
                div.style.color = '#777';
                div.style.cursor = 'default';
                div.innerText = 'ქალაქი ვერ მოიძებნა. მალე დავამატებთ ✌️';
                suggestionBox.appendChild(div);
            }
        }, 200);
    });

    document.addEventListener('click', (e) => {
        if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
            suggestionBox.innerHTML = '';
        }
    });
</script>

<script>
    window.GRUBELI_AUTO_PROMPT = <?php echo $autoPrompt ? 'true' : 'false'; ?>;
</script>
<script>
function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

if (!getCookie('user_lat')) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                document.cookie = `user_lat=${lat}; path=/; max-age=86400; SameSite=Lax`;
                document.cookie = `user_lon=${lon}; path=/; max-age=86400; SameSite=Lax`;
                window.location.reload();
            },
            (error) => {
                console.log("მომხმარებელმა უარი თქვა ლოკაციაზე ან შეცდომაა:", error.message);
            }
        );
    }
}
</script>
<?php require_once __DIR__ . '/footer.php'; ?>


<script>
(function() {
    const lat = localStorage.getItem('lat');
    const lon = localStorage.getItem('lon');
    if (lat && lon) {
        const histLinks = document.querySelectorAll('.hist-link');
        histLinks.forEach(link => {
            const url = new URL(link.href, window.location.origin);
            url.searchParams.set('lat', lat);
            url.searchParams.set('lon', lon);
            link.href = url.toString();
        });
    }
})();
</script>
   
  </body>
</html>