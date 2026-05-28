<?php
require_once __DIR__ . '/functions.php';

// Determine current lang for HTML lang attribute
$current_lang_attr = get_current_lang() === 'en' ? 'en' : 'ka';
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$current_path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$query_params = $_GET;
unset($query_params['lang']); // Remove lang param for alternate URLs

// Build alternate URLs
$ka_query = $query_params;
$ka_query['lang'] = 'ka';
$en_query = $query_params;
$en_query['lang'] = 'en';

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
$ka_url = $base_url . $current_path . '?' . http_build_query($ka_query);
$en_url = $base_url . $current_path . '?' . http_build_query($en_query);
?>
<!doctype html>
<html lang="<?php echo $current_lang_attr; ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . " | " : ""; echo __('app_title'); ?></title>
    <meta name="description" content="<?php echo isset($pageDesc) ? htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') : ''; ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($pageOgTitle) ? htmlspecialchars($pageOgTitle, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta property="og:description" content="<?php echo isset($pageDesc) ? htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta property="og:image" content="https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'grubeli.ge', ENT_QUOTES, 'UTF-8'); ?>/images/og-preview.png">
    <meta property="og:url" content="<?php echo htmlspecialchars($current_url, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="<?php echo $current_lang_attr === 'ka' ? 'ka_GE' : 'en_US'; ?>">
    <meta property="og:site_name" content="Grubeli.ge">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($pageTwTitle) ? htmlspecialchars($pageTwTitle, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta name="twitter:description" content="<?php echo isset($pageTwDesc) ? htmlspecialchars($pageTwDesc, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta name="twitter:image" content="https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'grubeli.ge', ENT_QUOTES, 'UTF-8'); ?>/images/og-preview.png">
    
    <!-- Hreflang for multilingual SEO -->
    <link rel="alternate" hreflang="ka" href="<?php echo htmlspecialchars($ka_url, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="alternate" hreflang="en" href="<?php echo htmlspecialchars($en_url, ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($ka_url, ENT_QUOTES, 'UTF-8'); ?>">
    
    <!-- Canonical URL (without query params that don't affect content) -->
    <link rel="canonical" href="<?php echo htmlspecialchars($current_url, ENT_QUOTES, 'UTF-8'); ?>">
    
    <!-- Preload critical resources -->
    <link rel="preload" as="image" href="/<?php echo ltrim(htmlspecialchars($current_icon ?? 'icons/sun.svg', ENT_QUOTES, 'UTF-8'), '/'); ?>">
    
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/app.css?ver=1.1.2" rel="stylesheet">
    <link href="icons/fontawesome/css/all.min.css?v-1.1.0" rel="stylesheet" media="print" onload="this.media='all'">
    
    <!-- Google Fonts: only load the weight we need -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    
    <link rel="icon" type="image/png" href="images/favicon.png"/>
    
    <!-- JSON-LD Structured Data (WeatherForecast for main page, WebPage for others) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Grubeli.ge",
        "url": "https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'grubeli.ge', ENT_QUOTES, 'UTF-8'); ?>",
        "description": "<?php echo __('app_description'); ?>",
        "inLanguage": "<?php echo $current_lang_attr === 'ka' ? 'ka-GE' : 'en-US'; ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'grubeli.ge', ENT_QUOTES, 'UTF-8'); ?>/?s={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <script>
    (function () {
        // Only redirect if URL doesn't already have lat/lon params
        var url = new URL(window.location.href);
        if (!url.searchParams.get("lat") && !url.searchParams.get("lon")) {
            var lat = localStorage.getItem("lat");
            var lon = localStorage.getItem("lon");
            if (lat && lon) {
                url.searchParams.set("lat", lat);
                url.searchParams.set("lon", lon);
                window.location.replace(url.toString());
            }
        }
    })();
    
    /* Geolocation: only run once, set cookie and reload ONCE */
    (function() {
        // Check if we already have geolocation done (cookie set by previous visit)
        var hasGeo = document.cookie.indexOf('user_lat=') !== -1;
        if (!hasGeo && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;
                    document.cookie = 'user_lat=' + lat + '; path=/; max-age=86400; SameSite=Lax';
                    document.cookie = 'user_lon=' + lon + '; path=/; max-age=86400; SameSite=Lax';
                    // Only reload if we have no lat/lon in URL and no localStorage
                    var url = new URL(window.location.href);
                    if (!url.searchParams.get('lat') && !localStorage.getItem('lat')) {
                        window.location.reload();
                    }
                },
                function(error) {
                    console.log('Geolocation not available:', error.message);
                },
                { timeout: 5000, enableHighAccuracy: false }
            );
        }
    })();
    </script>
    
    <!-- LANGUAGE FIX for shared hosting: ensure lang param survives via cookie -->
    <script>
    (function() {
        var url = new URL(window.location.href);
        if (!url.searchParams.get('lang')) {
            var langCookie = (function(name) {
                var m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
                return m ? decodeURIComponent(m[1]) : null;
            })('lang_client');
            if (langCookie && langCookie !== 'ka') {
                url.searchParams.set('lang', langCookie);
                window.location.replace(url.toString());
            }
        }
    })();
    </script>
  </head>

<div id="android-lang-modal" class="android-dialog-overlay">
    <div class="android-dialog">
        <div class="android-dialog-glow"></div>
        <div class="android-dialog-icon">
            <i class="fa-solid fa-language"></i>
        </div>
        <h3 class="android-dialog-title">გასაგრძელებლად აირჩიეთ ენა</h3>
        <p class="android-dialog-text">Select your preferred language to continue</p>
        
        <div class="android-lang-list">
            <button class="android-lang-item" onclick="selectLanguage('ka')">
                <span class="android-lang-name">ქართული</span>
                <span class="android-lang-radio"></span>
            </button>
            <button class="android-lang-item" onclick="selectLanguage('en')">
                <span class="android-lang-name">English</span>
                <span class="android-lang-radio"></span>
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // უნიკალური სახელი ქუქისთვის
    var cookieCheckName = "grubeli_lang_first_set"; 
    var isCookieSaved = getAndroidLangCookie(cookieCheckName);

    console.log("=== GRUBELI LANG CHECK ===");
    console.log("ქუქი არსებობს?:", isCookieSaved);

    /* გასწორებული ლოგიკა: თუ მომხმარებელს ქუქი არ აქვს, 
       ფანჯარა აუცილებლად გამოჩნდება! 
       საიტის მიერ ავტომატურად მიწერილი &lang=en მას ვეღარ დაბლოკავს.
    */
    if (!isCookieSaved) {
        setTimeout(function() {
            var modal = document.getElementById("android-lang-modal");
            if (modal) {
                modal.classList.add("show");
                console.log("თხევადი მინის ფანჯარა წარმატებით გამოჩნდა.");
            }
        }, 500);
    } else {
        console.log("ფანჯარა არ გამოიძახა, რადგან ენა უკვე შენახულია ქუქიში.");
    }
});

function selectLanguage(langCode) {
    var cookieCheckName = "grubeli_lang_first_set";

    // 1. მყარად ვწერთ ქუქიებს 365 დღით
    setAndroidLangCookie(cookieCheckName, "true", 365);
    setAndroidLangCookie("lang", langCode, 365);
    setAndroidLangCookie("language", langCode, 365); 

    // 2. ვხურავთ ფანჯარას ანიმაციით
    var modal = document.getElementById("android-lang-modal");
    if (modal) modal.classList.remove("show");

    // 3. ვიღებთ მიმდინარე URL-ს (კოორდინატებიანად) და ვუცვლით მხოლოდ lang პარამეტრს
    var currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('lang', langCode);

    // გადამისამართება იძულებით ახალ მისამართზე
    setTimeout(function() {
        window.location.href = currentUrl.toString();
    }, 200);
}

// დამხმარე ფუნქციები ქუქიებთან მუშაობისთვის
function setAndroidLangCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
}

function getAndroidLangCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}
</script>

  <body class="d-flex flex-column min-vh-100 premium-bg">
  <div id="loading-bar" style="position: fixed; top: 0; left: 0; width: 0%; height: 3px; background: linear-gradient(to right, #0dcaf0, #0d6efd); box-shadow: 0 0 10px rgba(13, 202, 240, 0.7); z-index: 9999; transition: width 0.3s ease;"></div>