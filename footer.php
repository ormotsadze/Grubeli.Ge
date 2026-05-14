<?php
[$mLat, $mLon] = resolve_coordinates($lat ?? null, $lon ?? null);
$histUrl = 'historical-weather.php?lat=' . rawurlencode($mLat) . '&lon=' . rawurlencode($mLon);
?>

<footer id="site-footer" class="shadow-lg">
    <div class="footer-inner">
        <a href="index.php" class="footer-item">
            <i class="fa-solid fa-house"></i>
            <span>მთავარი</span>
        </a>
       <a href="<?php echo htmlspecialchars($histUrl, ENT_QUOTES, 'UTF-8'); ?>" class="footer-item">
      
           <i class="fa-solid fa-cloud-arrow-down"></i>
            <span>არქივი</span>
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
    <span>მენიუ</span>
</a>
<a href="contact.php" class="footer-item">
<i class="fa-regular fa-envelope"></i>
    <span>კონტაქტი</span>
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
                GRUBELI.GE 
                <span class="version-badge">BETA</span>
            </span>
            <span class="brand-tagline">ამინდი მარტივად</span>
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
                <span>მთავარი გვერდი</span>
            </a>

           <a href="<?php echo htmlspecialchars($histUrl, ENT_QUOTES, 'UTF-8'); ?>" class="drawer-link hist-link">
                <div class="link-icon">
                 <i class="fa-solid fa-cloud-arrow-down"></i>
                </div>
                <span>ამინდის არქივი</span>
            </a>
            
            <a href="global-time.php" class="drawer-link">
                <div class="link-icon">
                <i class="fa-regular fa-clock"></i>
                </div>
                <span>მსოფლიო დრო</span>
            </a>
         
  <a href="holidays.php" class="drawer-link">
                <div class="link-icon">
              <i class="fa-solid fa-champagne-glasses"></i>
                </div>
                <span>დღესასწაულები</span>
            </a>
 <a href="app-release.apk" id="appDownloadLink" class="drawer-link getapp">
    <div class="link-icon app-icon">
        <i class="fa-brands fa-google-play"></i>
    </div>
    <div class="app-text-wrapper">
        <span class="app-title">ჩამოტვირთე აპი</span>
        <span class="app-subtitle">Android ვერსია</span>
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
        
        <a href="https://instagram.com/grubeli.ge" target="_blank" class="social-btn ig">
            <i class="fa-brands fa-instagram"></i>
        </a>
        
        <a href="https://tiktok.com/@grubeli.ge" target="_blank" class="social-btn tk">
            <i class="fa-brands fa-tiktok"></i>
        </a>
         <a href="https://github.com/ormotsadze/Grubeli.Ge" target="_blank" class="social-btn git">
          <i class="fa-brands fa-github"></i>
        </a>
    </div>


</div>
<center>
<span class="footer-credit"> 2026 <a href="https://grubeli.ge">Grubeli.Ge</a> - ყველა უფლება დაცულია. </span>

</center>



    </div>

  <div class="drawer-footer">
    <div class="footer-links-grid">
        <a href="privacy.php" class="f-link">წესები</a>
        <a href="about.php" class="f-link">ჩვენ შესახებ</a>
        <a href="jobs.php" class="f-link">ვაკანსია</a>
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