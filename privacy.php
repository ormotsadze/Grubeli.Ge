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
      <div class="col-lg-8">
        <div class="mb-4">
          <i class="fas fa-fingerprint me-3 text-info float-icon" style="font-size:clamp(2rem,5vw,3rem);"></i>
          <h1 class="display-5 fw-bolder text-gradient-premium m-0">პრივატულობა და დაცვა</h1>
        </div>
        <p class="lead text-white-80 fw-light">
          კეთილი იყოს თქვენი მობრძანება <strong class="text-white">Grubeli.ge</strong>-ზე. ჩვენთვის მნიშვნელოვანია თქვენი მონაცემების დაცვა. აქ განმარტებულია, თუ რა ინფორმაციას ვაგროვებთ და როგორ ვიყენებთ მას.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
        <div class="status-pill d-inline-flex align-items-center">
          <i class="far fa-clock me-2 text-primary"></i>
          <span class="fw-bold">განახლდა: 5 აპრილი, 2026</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">

    <div class="col-lg-6 reveal-up delay-1">
      <div class="premium-glass p-5 h-100 card-accent-cyan interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-cyan me-3"><i class="fas fa-database"></i></div>
          1. მონაცემთა შეგროვება
        </h3>
        <p class="text-white-60 mb-4">ვაგროვებთ მხოლოდ იმ მინიმალურ ინფორმაციას, რომელიც აუცილებელია სერვისის გამართული მუშაობისთვის:</p>
        <div class="data-item">
          <i class="fas fa-map-marker-alt text-danger"></i>
          <div>
            <h6 class="text-white mb-1">ადგილმდებარეობა (GPS)</h6>
            <p class="small text-white-50 mb-0">ვებ და Android-ზე ვითხოვთ ლოკაციას ზუსტი პროგნოზისთვის. კოორდინატები ინახება cookie-ში 30 დღით — სერვერზე მუდმივად <strong class="text-white">არ ინახება</strong>. მხოლოდ საქართველოს კოორდინატები მიიღება.</p>
          </div>
        </div>
        <div class="data-item mt-2">
          <i class="fas fa-bell text-warning"></i>
          <div>
            <h6 class="text-white mb-1">შეტყობინებები (Android)</h6>
            <p class="small text-white-50 mb-0">Android-ზე ვითხოვთ push notifications-ის უფლებას ამინდის მკვეთრი ცვლილებებისა და სხვა სასარგებლო გაფრთხილებებისთვის.</p>
          </div>
        </div>
        <div class="data-item mt-2">
          <i class="fas fa-microchip text-info"></i>
          <div>
            <h6 class="text-white mb-1">ტექნიკური მონაცემები</h6>
            <p class="small text-white-50 mb-0">სტატისტიკისა და უსაფრთხოებისთვის სერვერის log-ებში ფიქსირდება IP მისამართი და ბრაუზერის ტიპი.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-2">
      <div class="premium-glass p-5 h-100 card-accent-purple interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-purple me-3"><i class="fas fa-network-wired"></i></div>
          2. მესამე მხარის სერვისები
        </h3>
        <p class="text-white-60 mb-3">სერვისი იყენებს შემდეგ გარე API-ებს:</p>
        <div class="service-box mb-3 p-3 border-cyan-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-info m-0"><i class="fas fa-cloud-sun me-2"></i>Open-Meteo</h6>
            <span class="badge bg-info bg-opacity-25 text-info">ამინდი</span>
          </div>
          <p class="small text-white-50 m-0">კოორდინატები გადაეცემა მხოლოდ პროგნოზის გამოსათვლელად. პირადი მონაცემები არ გაიგზავნება.</p>
        </div>
        <div class="service-box mb-3 p-3 border-purple-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="color-purple m-0"><i class="fas fa-robot me-2"></i>Groq API (Llama 3.3)</h6>
            <span class="badge color-purple" style="background:rgba(138,43,226,0.2);">AI</span>
          </div>
          <p class="small text-white-50 m-0">AI ასისტენტს გადაეცემა: ქალაქი, ტემპ., ამინდის სახეობა და მომხმარებლის კითხვა (მაქს. 300 სიმბოლო). სახელი, იმეილი ან სხვა პირადი მონაცემი <strong class="text-white">არ გაიგზავნება</strong>.</p>
        </div>
        <div class="service-box mb-3 p-3 border-amber-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-warning m-0"><i class="fas fa-house-chimney-crack me-2"></i>USGS Earthquake API</h6>
            <span class="badge bg-warning bg-opacity-25 text-warning">მიწისძვრა</span>
          </div>
          <p class="small text-white-50 m-0">გამოიყენება მხოლოდ ზოგადი გეოგრაფიული ფილტრით (საქართველოს bbox). პირადი მონაცემები არ გაიგზავნება.</p>
        </div>
        <div class="service-box p-3 border-emerald-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="color-emerald m-0"><i class="fas fa-map-pin me-2"></i>OSM Nominatim</h6>
            <span class="badge color-emerald" style="background:rgba(32,201,151,0.2);">გეო</span>
          </div>
          <p class="small text-white-50 m-0">კოორდინატები გადაეცემა ქალაქის სახელის ქართულად დასადგენად. OpenStreetMap-ის პოლიტიკის შესაბამისად.</p>
        </div>
      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-3">
      <div class="premium-glass p-5 h-100 card-accent-emerald interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-emerald me-3"><i class="fas fa-shield-alt"></i></div>
          3. შენახვა და უსაფრთხოება
        </h3>
        <ul class="premium-list">
          <li>
            <i class="fas fa-check-circle color-emerald"></i>
            <span>ლოკაციის მონაცემები მუშავდება რეალურ დროში და <strong>არ ინახება</strong> სერვერზე მუდმივად — მხოლოდ browser cookie-ში.</span>
          </li>
          <li>
            <i class="fas fa-lock color-emerald"></i>
            <span>გამოიყენება <strong>SSL/HTTPS</strong> დაშიფვრა ყველა მონაცემის გადაცემისთვის.</span>
          </li>
          <li>
            <i class="fas fa-filter color-emerald"></i>
            <span>AI-ს მოთხოვნები შეზღუდულია — <strong>მაქს. 300 სიმბოლო</strong> და 5-წამიანი flood protection.</span>
          </li>
          <li>
            <i class="fas fa-map-location-dot color-emerald"></i>
            <span>სისტემა <strong>მხოლოდ საქართველოს კოორდინატებს</strong> ამუშავებს — სხვა ქვეყნის ლოკაცია უარყოფილია.</span>
          </li>
          <li>
            <i class="fas fa-cookie-bite color-emerald"></i>
            <span>Cookie-ები გამოიყენება ლოკაციის შესანახად, ვადა <strong>30 დღე</strong>. სხვა tracking cookie არ გამოიყენება.</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-3">
      <div class="premium-glass p-5 h-100 card-accent-orange interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-orange me-3"><i class="fas fa-user-check"></i></div>
          4. მომხმარებლის უფლებები
        </h3>
        <p class="text-white-80 mb-4">თქვენ გაქვთ სრული კონტროლი თქვენს მონაცემებზე. ნებისმიერ დროს შეგიძლიათ:</p>
        <div class="right-item">
          <div class="right-icon" style="background:rgba(220,53,69,0.12);color:#dc3545;">
            <i class="fas fa-location-arrow"></i>
          </div>
          <div>
            <h6 class="text-white mb-1">ლოკაციის გათიშვა</h6>
            <p class="small text-white-50 mb-0">ბრაუზერის პარამეტრებში გეოლოკაციის უფლება გააუქმეთ ნებისმიერ დროს.</p>
          </div>
        </div>
        <div class="right-item mt-2">
          <div class="right-icon" style="background:rgba(255,193,7,0.12);color:#ffc107;">
            <i class="fas fa-bell-slash"></i>
          </div>
          <div>
            <h6 class="text-white mb-1">ნოტიფიკაციების გაუქმება</h6>
            <p class="small text-white-50 mb-0">Android-ის პარამეტრებში შეტყობინებები გამორთეთ ნებისმიერ დროს.</p>
          </div>
        </div>
        <div class="right-item mt-2">
          <div class="right-icon" style="background:rgba(13,202,240,0.12);color:#0dcaf0;">
            <i class="fas fa-trash-alt"></i>
          </div>
          <div>
            <h6 class="text-white mb-1">Cookie-ების წაშლა</h6>
            <p class="small text-white-50 mb-0">ბრაუზერის პარამეტრებში site data-ს გასუფთავებით ლოკაციის მონაცემები სრულად წაიშლება.</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<style>
.data-item{display:flex;align-items:flex-start;padding:14px;background:rgba(0,0,0,0.2);border-radius:14px;border:1px solid transparent;transition:0.3s;}
.data-item:hover{border-color:rgba(255,255,255,0.1);}
.data-item i{font-size:1.3rem;margin-right:14px;margin-top:2px;flex-shrink:0;}
.service-box{background:rgba(0,0,0,0.22);border-radius:14px;}
.border-cyan-soft{border-left:3px solid rgba(13,202,240,0.5);}
.border-purple-soft{border-left:3px solid rgba(138,43,226,0.5);}
.border-amber-soft{border-left:3px solid rgba(245,183,39,0.5);}
.border-emerald-soft{border-left:3px solid rgba(32,201,151,0.5);}
.premium-list{list-style:none;padding:0;margin:0;}
.premium-list li{display:flex;align-items:flex-start;padding:11px 0;border-bottom:1px solid rgba(255,255,255,0.05);color:rgba(255,255,255,0.75);}
.premium-list li i{font-size:0.9rem;margin-right:12px;margin-top:3px;flex-shrink:0;}
.premium-list li:last-child{border-bottom:none;}
.right-item{display:flex;align-items:flex-start;gap:14px;padding:14px;background:rgba(0,0,0,0.2);border-radius:14px;border:1px solid transparent;transition:0.3s;}
.right-item:hover{border-color:rgba(255,255,255,0.1);}
.right-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>