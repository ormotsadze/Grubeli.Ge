<?php
[$mLat, $mLon] = resolve_coordinates();
$histUrl = 'historical-weather.php?lat=' . rawurlencode($mLat) . '&lon=' . rawurlencode($mLon);
?>

<footer id="site-footer" class="shadow-lg">
    <div class="footer-inner">
        <a href="index.php" class="footer-item">
            <i class="fa-solid fa-house"></i>
            <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('menu_home'); ?></span>
        </a>
       <a href="<?php echo htmlspecialchars($histUrl, ENT_QUOTES, 'UTF-8'); ?>" class="footer-item">
      
           <i class="fa-solid fa-cloud-arrow-down"></i>
            <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('menu_archives'); ?></span>
        </a>

     <?php 
$current_page = basename($_SERVER['PHP_SELF']); 
if ($current_page == 'index.php' || $current_page == 'historical-weather.php') : 
?>
    <a href="javascript:void(0)" id="btn-location" class="fixed-location-btn">
        <i class="fa-solid fa-location-crosshairs"></i>
    </a>
<?php endif; ?>
         <a class="footer-item" href="javascript:void(0)" onclick="toggleMyMenu()" role="button">
    <i class="fa-solid fa-bars-staggered"></i>
    <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('menu_menu'); ?></span>
</a>
<a href="contact.php" class="footer-item">
<i class="fa-regular fa-envelope"></i>
    <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('menu_contact'); ?></span>
</a>
       
    </div>
</footer>
<div id="custom-drawer-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; display: none;"></div>

<div id="custom-drawer">
   <div class="drawer-header">
    <div class="brand-wrapper">
        <img src="images/logo/logo.png" alt="Grubeli.ge - logo" class="header-logo">
        <div class="brand-info">
            <span class="brand-name">
            <?php echo __('app_title'); ?>
                <span class="version-badge">BETA</span>
            </span>
            <span class="brand-tagline"><?php echo __('app_title_sub'); ?></span>
        </div>
    </div>
    <button class="close-btn" onclick="toggleMyMenu()" aria-label="მენიუს დახურვა">&times;</button>
</div>
    
    <div class="drawer-body">

    
        <div class="nav-links">
            <a href="index.php" class="drawer-link">
                <div class="link-icon">
                    <i class="fa-solid fa-house"></i>
                </div>
                <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_home'); ?></span>
            </a>

           <a href="onthisday.php" class="drawer-link hist-link">
                <div class="link-icon">
              
                  <i class="fa-solid fa-calendar-day"></i>
                </div>
                <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_onthisday'); ?></span>
            </a>
            
            <a href="global-time.php" class="drawer-link">
                <div class="link-icon">
                <i class="fa-regular fa-clock"></i>
                </div>
                <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_worldtime'); ?></span>
            </a>
         
  <a href="holidays.php" class="drawer-link">
                <div class="link-icon">
              <i class="fa-solid fa-champagne-glasses"></i>
                </div>
                <span style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_holidays'); ?></span>
            </a>
 <a href="getapp.php" id="appDownloadLink" class="drawer-link getapp">
    <div class="link-icon app-icon">
        <i class="fa-brands fa-google-play"></i>
    </div>
    <div class="app-text-wrapper">
        <span class="app-title" style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_getapp'); ?></span>
        <span class="app-subtitle"><?php echo __('drawermenu_getappversion'); ?></span>
    </div>

</a>
        </div>



<div class="social-block">
   
    <div class="social-icons">
       
        
        <a href="https://facebook.com/grubeli.ge" target="_blank" class="social-btn fb">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
         <a href="https://m.me/grubeli.ge" target="_blank" class="social-btn msg">
            <i class="fa-brands fa-facebook-messenger"></i>
        </a>
        
      
      
         <a href="https://github.com/ormotsadze/Grubeli.Ge" target="_blank" class="social-btn git">
          <i class="fa-brands fa-github"></i>
        </a>
    </div>


</div>




    </div>

  <div class="drawer-footer">

 <?php global $current_lang; ?>
<div class="lang-switcher-container d-flex justify-content-center align-items-center">
    <div class="lang-pill-box p-1 d-flex align-items-center" style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 30px; backdrop-filter: blur(10px);">
        
        <a href="?lang=ka" class="lang-link <?php echo $current_lang === 'ka' ? 'active-lang' : ''; ?>">GEO</a>
        <a href="?lang=en" class="lang-link <?php echo $current_lang === 'en' ? 'active-lang' : ''; ?>">ENG</a>
        
    </div>
</div>
    <div class="footer-links-grid">
        <a href="privacy.php" class="f-link" style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_privacy'); ?></a>
        <a href="about.php" class="f-link" style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_about'); ?></a>
        <a href="jobs.php" class="f-link" style="font-family: '<?php echo __('font_family'); ?>';"><?php echo __('drawermenu_jobs'); ?></a>
    </div>

    <div class="data-credits mt-2 pt-2">
      
        <div class="d-flex flex-wrap justify-content-center gap-1">
            
            <a href="https://open-meteo.com/" target="_blank" rel="nofollow" style="font-size: 0.7em; opacity: 0.5;">
            Open-Meteo |
            </a>

            
            <a href="https://www.nasa.gov/" target="_blank" rel="nofollow" style="font-size: 0.7em; opacity: 0.5;">
             NASA |
            </a>

            <a href="https://earthquake.usgs.gov/" target="_blank" rel="nofollow" style="font-size: 0.7em; opacity: 0.5;">
             USGS |
            </a>
       
             <a href="https://date.nager.at/" target="_blank" rel="nofollow" style="font-size: 0.7em; opacity: 0.5;">
           Nager Date
            </a>
        </div>
    </div>
</div>
  
</div>
<script>

function toggleMyMenu() {
    const drawer = document.getElementById('custom-drawer');
    const overlay = document.getElementById('custom-drawer-overlay');
    
    if (drawer.classList.contains('active')) {
        drawer.classList.remove('active');
        if (overlay) overlay.style.display = 'none';
    } else {
        drawer.classList.add('active');
        if (overlay) overlay.style.display = 'block';
    }
}


document.addEventListener('click', function(event) {
    const drawer = document.getElementById('custom-drawer');
    const overlay = document.getElementById('custom-drawer-overlay');
    
   
    if (event.target === overlay) {
        toggleMyMenu();
        return;
    }

  
    if (drawer && drawer.classList.contains('active')) {
    
        const isClickInsideDrawer = drawer.contains(event.target);
    
        const isMenuToggleButton = event.target.closest('[onclick*="toggleMyMenu"]');
        
     
        if (!isClickInsideDrawer && !isMenuToggleButton) {
            drawer.classList.remove('active');
            if (overlay) overlay.style.display = 'none';
        }
    }
});
</script>

 <script src="js/bootstrap.bundle.min.js" ></script>
  <script src="js/geolocation.js" ></script>

<script>
/**
 * LANGUAGE PERSISTENCE FOR WEBVIEW
 * Stores language preference in localStorage and ensures all
 * internal navigation links preserve the ?lang= parameter.
 */
(function() {
    // 1. Detect current language from URL or cookie
    const urlParams = new URLSearchParams(window.location.search);
    const langFromUrl = urlParams.get('lang');
    const currentLang = langFromUrl || getCookie('lang') || 'ka';
    
    // 2. Save to localStorage for cross-page persistence
    if (currentLang) {
        localStorage.setItem('grubeli_lang', currentLang);
    }
    
    // 3. Auto-append ?lang= to all internal links (except lang switcher links)
    const savedLang = localStorage.getItem('grubeli_lang') || currentLang;
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;
        if (!link.href) return;
        if (link.href.startsWith('javascript:')) return;
        if (link.href.startsWith('#')) return;
        if (link.target === '_blank') return;
        
        // Skip lang switcher links (they already have ?lang=)
        if (link.classList.contains('lang-link')) return;
        
        // Only internal links
        const linkUrl = new URL(link.href, window.location.origin);
        if (linkUrl.origin !== window.location.origin) return;
        
        // If lang is not already in the URL, add it
        if (!linkUrl.searchParams.has('lang') && savedLang && savedLang !== 'ka') {
            linkUrl.searchParams.set('lang', savedLang);
            link.href = linkUrl.toString();
        }
    });
    
    // 4. Also fix any existing links on the page
    document.addEventListener('DOMContentLoaded', function() {
        const saved = localStorage.getItem('grubeli_lang');
        if (saved && saved !== 'ka') {
            document.querySelectorAll('a:not(.lang-link)').forEach(function(a) {
                if (!a.href) return;
                if (a.href.startsWith('javascript:')) return;
                try {
                    const url = new URL(a.href, window.location.origin);
                    if (url.origin === window.location.origin && !url.searchParams.has('lang')) {
                        url.searchParams.set('lang', saved);
                        a.href = url.toString();
                    }
                } catch(e) {}
            });
        }
    });
})();

window.addEventListener('load', function() {
    const currentCity = "<?php echo addslashes($city_name ?? ''); ?>";

    if (window.AndroidBridge) {
        if (currentCity !== "" && currentCity !== "საქართველოში" && currentCity !== "საქართველო") {
            AndroidBridge.updateWidgetLocation(currentCity);
        }

        const downloadLink = document.getElementById('appDownloadLink');
        if (downloadLink) {
            downloadLink.style.display = 'none';
        }
    }
});
</script>
<script>
document.getElementById('btn-location').addEventListener('click', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                window.location.href = `index.php?lat=${lat}&lon=${lon}`;
            },
            function(error) {
                console.error("Error Code: " + error.code + " - " + error.message);
                alert("ლოკაციის მიღება ვერ მოხერხდა. გთხოვთ, შეამოწმოთ ნებართვები ბრაუზერში.");
            }
        );
    } else {
        alert("თქვენი ბრაუზერი არ უჭერს მხარს გეოლოკაციას.");
    }
});
</script>

<script>
    window.startLoading = function() {
        const loadingBar = document.getElementById('loading-bar');
        if (!loadingBar) return;

        loadingBar.style.opacity = '1';
        loadingBar.style.width = '30%';
        
        setTimeout(() => {
            loadingBar.style.width = '70%';
        }, 200);
    };

    window.addEventListener('load', () => {
        const loadingBar = document.getElementById('loading-bar');
        if (!loadingBar) return;

        loadingBar.style.width = '100%';
        setTimeout(() => {
            loadingBar.style.opacity = '0';
        }, 300);
    });

    document.addEventListener('click', (e) => {
        const target = e.target.closest('a');

        if (target && 
            target.href && 
            !target.classList.contains('drawer-toggle') && 
            !target.getAttribute('href').startsWith('#') && 
            !target.getAttribute('href').startsWith('javascript') &&
            target.target !== '_blank') {
            
            window.startLoading();
        }
    });

    window.startLoading();
</script>


<script>

(function() {
  const banner = document.getElementById('app-banner');
  if (!banner) return;

  // ✅ WebView-ში საერთოდ არ ჩანს
  if (window.AndroidBridge) return;

  // ✅ თუ მომხმარებელმა უკვე დახურა — არ ვაჩვენებთ
  if (sessionStorage.getItem('appBannerClosed')) return;

  // ✅ ბანერის ჩვენება
  banner.style.display = 'block';

  // ✅ ბანერის სიმაღლით body-ს padding-top
  document.body.style.paddingTop = banner.offsetHeight + 'px';

  // დახურვის ღილაკი
  const closeBtn = document.getElementById('app-banner-close');
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      banner.style.display = 'none';
      document.body.style.paddingTop = '0';
      sessionStorage.setItem('appBannerClosed', '1');
    });
  }
})();

</script>