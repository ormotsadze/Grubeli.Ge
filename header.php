<?php
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="ka">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
 <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . " | " : ""; echo __('app_title'); ?></title>
<meta name="description" content="<?php echo isset($pageDesc) ? htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') : ''; ?>">
<meta property="og:title" content="<?php echo isset($pageOgTitle) ? htmlspecialchars($pageOgTitle, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta property="og:description" content="<?php echo isset($pageDesc) ? htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta property="og:image" content="https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'grubeli.ge', ENT_QUOTES, 'UTF-8'); ?>/images/og-preview.png">
    <meta property="og:url" content="https://<?php echo htmlspecialchars(($_SERVER['HTTP_HOST'] ?? 'grubeli.ge') . ($_SERVER['REQUEST_URI'] ?? '/'), ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ka_GE">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($pageTwTitle) ? htmlspecialchars($pageTwTitle, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta name="twitter:description" content="<?php echo isset($pageTwDesc) ? htmlspecialchars($pageTwDesc, ENT_QUOTES, 'UTF-8') : ''; ?>">
    <meta name="twitter:image" content="https://<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'grubeli.ge', ENT_QUOTES, 'UTF-8'); ?>/images/og-preview.png">
    <link rel="canonical" href="https://<?php echo htmlspecialchars(($_SERVER['HTTP_HOST'] ?? 'grubeli.ge') . strtok($_SERVER['REQUEST_URI'] ?? '/', '?'), ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="preload" as="image" href="/<?php echo ltrim(htmlspecialchars($current_icon ?? 'icons/sun.svg', ENT_QUOTES, 'UTF-8'), '/'); ?>">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/app.css?ver=1.1.0" rel="stylesheet">
    <link href="icons/fontawesome/css/all.min.css?v-1.1.0" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link rel="icon" type="image/png" href="images/favicon.png"/>
    <script>
      (function () {
    const lat = localStorage.getItem("lat");
    const lon = localStorage.getItem("lon");

    if (lat && lon) {
        const url = new URL(window.location.href);
        // თუ URL-ში ლოკაცია არ გვაქვს, დავამატოთ (შენახული query params-ის ჩათვლით!)
        if (!url.searchParams.get("lat")) {
            url.searchParams.set("lat", lat);
            url.searchParams.set("lon", lon);
            window.location.replace(url.toString());
        }
    }
})();
    </script>
    
    <!-- LANGUAGE FIX for shared hosting: ensure lang param survives via cookie -->
    <script>
    (function() {
        // On shared hosting, append ?lang= from cookie if missing in URL
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
  <body class="d-flex flex-column min-vh-100 premium-bg">
  <div id="loading-bar" style="position: fixed; top: 0; left: 0; width: 0%; height: 3px; background: linear-gradient(to right, #0dcaf0, #0d6efd); box-shadow: 0 0 10px rgba(13, 202, 240, 0.7); z-index: 9999; transition: width 0.3s ease;"></div>
