<?php
require_once 'functions.php';

// ვიღებთ თარგმანებს 'index_' პრეფიქსით
$pageTitle   = __('privacy_title');
$pageDesc    = __('privacy_desc');
$pageOgTitle = __('privacy_og_title');
$pageTwTitle = __('privacy_tw_title');
$pageTwDesc  = __('privacy_tw_desc');
include 'header.php';
?>


<main class="container-xl py-4 flex-grow-1">

  <div class="premium-glass p-5 shadow-lg mb-4 mt-4 position-relative overflow-hidden reveal-up hero-accent">
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    <div class="row position-relative z-index-1">
      <div class="col-lg-12">
        <div class="mb-4">
          <i class="fas fa-shield-halved text-info float-icon flex-shrink-0" style="font-size:clamp(2rem,5vw,3rem);"></i>
          <h1 class="display-8 fw-bolder text-gradient-premium m-0"><?php echo __('privacy_title_page'); ?></h1>
        </div>
        <p class="lead text-white-80 fw-light">
          <?php echo __('privacy_desc_page'); ?>
        </p>
         <div class="status-pill d-inline-flex align-items-center">
          <i class="far fa-clock me-2 text-primary"></i>
          <span class="fw-bold"> 03.06.2026</span>
        </div>
      </div>
     
    </div>
  </div>



  <div class="row g-4">

    <div class="col-lg-6 reveal-up delay-1">
      <div class="premium-glass p-5 h-100 card-accent-cyan interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-cyan me-3"><i class="fas fa-database"></i></div>
          1. <?php echo __('privacy_data_collection'); ?>
        </h3>
        <p class="text-white-60 mb-4"><?php echo __('privacy_data_collection_desc'); ?></p>
        <div class="data-item">
          <i class="fas fa-map-marker-alt text-danger"></i>
          <div>
            <h6 class="text-white mb-1"><?php echo __('privacy_data_gps'); ?></h6>
            <p class="small text-white-50 mb-0"><?php echo __('privacy_data_gps_desc'); ?></p>
          </div>
        </div>
        <div class="data-item mt-2">
          <i class="fas fa-bell text-warning"></i>
          <div>
            <h6 class="text-white mb-1"><?php echo __('privacy_data_push_notifications'); ?></h6>
            <p class="small text-white-50 mb-0"><?php echo __('privacy_data_push_notifications_desc'); ?></p>
          </div>
        </div>
        <div class="data-item mt-2">
          <i class="fas fa-microchip text-info"></i>
          <div>
            <h6 class="text-white mb-1"><?php echo __('privacy_data_technical'); ?></h6>
            <p class="small text-white-50 mb-0"><?php echo __('privacy_data_technical_desc'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-2">
      <div class="premium-glass p-5 h-100 card-accent-purple interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-purple me-3"><i class="fas fa-network-wired"></i></div>
          2. <?php echo __('privacy_third_party'); ?>
        </h3>
        <p class="text-white-60 mb-3"><?php echo __('privacy_third_party_desc'); ?></p>
        <div class="service-box mb-3 p-3 border-cyan-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-info m-0"><i class="fas fa-cloud-sun me-2"></i>Open-Meteo</h6>
            <span class="badge bg-info bg-opacity-25 text-info"><?php echo __('privacy_third_weather'); ?></span>
          </div>
          <p class="small text-white-50 m-0"><?php echo __('privacy_third_weather_desc'); ?></p>
        </div>
        <div class="service-box mb-3 p-3 border-purple-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="color-purple m-0"><i class="fas fa-robot me-2"></i>GPT 5 / GPT‑4o</h6>
            <span class="badge color-purple" style="background:rgba(138,43,226,0.2);">AI</span>
          </div>
          <p class="small text-white-50 m-0"><?php echo __('privacy_third_ai_desc'); ?></p>
        </div>
       
       
        <div class="service-box mb-3 p-3 border-amber-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-warning m-0"><i class="fas fa-house-chimney-crack me-2"></i>USGS Earthquake API</h6>
            <span class="badge bg-warning bg-opacity-25 text-warning"><?php echo __('privacy_third_earthquake'); ?></span>
          </div>
          <p class="small text-white-50 m-0"><?php echo __('privacy_third_earthquake_desc'); ?></p>
        </div>
        <div class="service-box mb-3 p-3 border-emerald-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="color-emerald m-0"><i class="fas fa-map-pin me-2"></i>OSM Nominatim</h6>
            <span class="badge color-emerald" style="background:rgba(32,201,151,0.2);"><?php echo __('privacy_third_geo'); ?></span>
          </div>
          <p class="small text-white-50 m-0"><?php echo __('privacy_third_geo_desc'); ?></p>
        </div>
        <div class="service-box mb-3 p-3 border-danger-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-danger m-0"><i class="fas fa-fire-flame-curved me-2"></i>NASA FIRMS</h6>
            <span class="badge bg-danger bg-opacity-25 text-danger"><?php echo __('privacy_third_nasa'); ?></span>
          </div>
          <p class="small text-white-50 m-0"><?php echo __('privacy_third_nasa_desc'); ?></p>
        </div>
        <div class="service-box p-3 border-rose-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="color-rose m-0"><i class="fas fa-calendar-check me-2"></i>Nager.at</h6>
            <span class="badge color-rose" style="background:rgba(232,62,140,0.2);"><?php echo __('privacy_third_nager'); ?></span>
          </div>
          <p class="small text-white-50 m-0"><?php echo __('privacy_third_nager_desc'); ?></p>
        </div>
      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-3">
      <div class="premium-glass p-5 h-100 card-accent-emerald interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-emerald me-3"><i class="fas fa-shield-alt"></i></div>
          3. <?php echo __('privacy_securityandsave'); ?>
        </h3>
        <ul class="premium-list">
          <li>
            <i class="fas fa-check-circle color-emerald"></i>
            <span><?php echo __('privacy_securityandsave_location'); ?></span>
          </li>
          <li>
            <i class="fas fa-lock color-emerald"></i>
            <span><?php echo __('privacy_securityandsave_ssl'); ?></span>
          </li>
          <li>
            <i class="fas fa-filter color-emerald"></i>
            <span><?php echo __('privacy_securityandsave_ai'); ?></span>
          </li>
          <li>
            <i class="fas fa-map-location-dot color-emerald"></i>
            <span><?php echo __('privacy_securityandsave_location_desc'); ?></span>
          </li>
          <li>
            <i class="fas fa-cookie-bite color-emerald"></i>
            <span><?php echo __('privacy_securityandsave_cookies'); ?></span>
          </li>
        </ul>
      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-3">
      <div class="premium-glass p-5 h-100 card-accent-orange interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-orange me-3"><i class="fas fa-user-check"></i></div>
          4. <?php echo __('privacy_securityandsave_user'); ?>
        </h3>
        <p class="text-white-80 mb-4"><?php echo __('privacy_securityandsave_user_desc'); ?></p>
        <div class="right-item">
          <div class="right-icon" style="background:rgba(220,53,69,0.12);color:#dc3545;">
            <i class="fas fa-location-arrow"></i>
          </div>
          <div>
            <h6 class="text-white mb-1"><?php echo __('privacy_securityandsave_location_off'); ?></h6>
            <p class="small text-white-50 mb-0"><?php echo __('privacy_securityandsave_location_off_desc'); ?></p>
          </div>
        </div>
        <div class="right-item mt-2">
          <div class="right-icon" style="background:rgba(255,193,7,0.12);color:#ffc107;">
            <i class="fas fa-bell-slash"></i>
          </div>
          <div>
            <h6 class="text-white mb-1"><?php echo __('privacy_securityandsave_notif_off'); ?></h6>
            <p class="small text-white-50 mb-0"><?php echo __('privacy_securityandsave_notif_off_desc'); ?></p>
          </div>
        </div>
        <div class="right-item mt-2">
          <div class="right-icon" style="background:rgba(13,202,240,0.12);color:#0dcaf0;">
            <i class="fas fa-trash-alt"></i>
          </div>
          <div>
            <h6 class="text-white mb-1"><?php echo __('privacy_securityandsave_cookies_off'); ?></h6>
            <p class="small text-white-50 mb-0"><?php echo __('privacy_securityandsave_cookies_off_desc'); ?></p>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<style>
.data-item{display:flex;align-items:flex-start;padding:14px;background:rgba(0,0,0,0.2);border-radius:14px;border:1px solid transparent;transition:0.3s;}
.data-item:hover{border-color:rgba(255,255,255,0.1);transform:translateX(4px);}
.data-item i{font-size:1.3rem;margin-right:14px;margin-top:2px;flex-shrink:0;}
.data-item+.data-item{margin-top:10px;}
.service-box{background:rgba(0,0,0,0.22);border-radius:14px;transition:all 0.3s ease;}
.service-box:hover{background:rgba(0,0,0,0.35);transform:translateX(4px);}
.border-cyan-soft{border-left:3px solid rgba(13,202,240,0.5);}
.border-purple-soft{border-left:3px solid rgba(138,43,226,0.5);}
.border-blue-soft{border-left:3px solid rgba(13, 110, 253, 0.5);}
.border-teal-soft{border-left:3px solid rgba(32, 201, 151, 0.5);}
.border-amber-soft{border-left:3px solid rgba(245,183,39,0.5);}
.border-emerald-soft{border-left:3px solid rgba(32,201,151,0.5);}
.border-danger-soft{border-left:3px solid rgba(255,68,68,0.5);}
.border-rose-soft{border-left:3px solid rgba(232,62,140,0.5);}
.color-teal{color: #20c997;}
.color-emerald{color: #20c997;}
.color-rose{color: #e83e8c;}
.premium-list{list-style:none;padding:0;margin:0;}
.premium-list li{display:flex;align-items:flex-start;padding:11px 0;border-bottom:1px solid rgba(255,255,255,0.05);color:rgba(255,255,255,0.75);transition:0.3s;}
.premium-list li:hover{color:rgba(255,255,255,0.95);padding-left:4px;}
.premium-list li i{font-size:0.9rem;margin-right:12px;margin-top:3px;flex-shrink:0;}
.premium-list li:last-child{border-bottom:none;}
.right-item{display:flex;align-items:flex-start;gap:14px;padding:14px;background:rgba(0,0,0,0.2);border-radius:14px;border:1px solid transparent;transition:0.3s;}
.right-item:hover{border-color:rgba(255,255,255,0.1);transform:translateX(4px);}
.right-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}

/* Stat Cards */
.stat-card{background:rgba(255,255,255,0.04);border-radius:16px;border:1px solid rgba(255,255,255,0.06);transition:all 0.35s ease;}
.stat-card:hover{background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.12);transform:translateY(-3px);}
.stat-icon i{font-size:1.4rem;}
.stat-value{font-size:1.3rem;color:#fff;}

/* Color helpers */
.text-cyan{color:#0dcaf0;}
.text-purple{color:#9b51e0;}
.text-emerald{color:#20c997;}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>