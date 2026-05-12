<?php
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="ka">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo isset($pageTitle) ? $pageTitle . " - Grubeli.ge" : "Grubeli.ge - ამინდი მარტივად"; ?></title>
    
   
   <meta name="description" content="<?php echo isset($pageDesc) ? $pageDesc : 'ამინდის პროგნოზი საქართველოში - Grubeli.ge. გაიგეთ ზუსტი ამინდი თბილისში, ბათუმსა და სხვა ქალაქებში მარტივად.'; ?>">
    
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle : 'Grubeli.ge - ამინდი მარტივად'; ?>">
    <meta property="og:description" content="<?php echo $currentDesc; ?>">
    <meta property="og:image" content="images/og-preview.png">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="website">
  <link rel="preload" as="image" href="/<?php echo ltrim($current_icon, '/'); ?>">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/app.css?ver=1.0.0" rel="stylesheet">
    <link href="icons/fontawesome/css/all.min.css?v-1.0.0" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link rel="icon" type="image/png" href="images/favicon.png"/>
    <script>
      (function () {
    const lat = localStorage.getItem("lat");
    const lon = localStorage.getItem("lon");

    if (lat && lon) {
        const url = new URL(window.location.href);
        // თუ URL-ში ლოკაცია არ გვაქვს, დავამატოთ და გადავტვირთოთ
        if (!url.searchParams.get("lat")) {
            url.searchParams.set("lat", lat);
            url.searchParams.set("lon", lon);
            window.location.replace(url.toString());
        }
    }
})();
    </script>
  </head>
  <body class="d-flex flex-column min-vh-100 premium-bg">
  <div id="loading-bar" style="position: fixed; top: 0; left: 0; width: 0%; height: 3px; background: linear-gradient(to right, #0dcaf0, #0d6efd); box-shadow: 0 0 10px rgba(13, 202, 240, 0.7); z-index: 9999; transition: width 0.3s ease;"></div>
