<?php
require_once 'functions.php';

// ვიღებთ თარგმანებს 'index_' პრეფიქსით
$pageTitle   = __('getapp_title');
$pageDesc    = __('getapp_desc');
$pageOgTitle = __('getapp_og_title');
$pageTwTitle = __('getapp_tw_title');
$pageTwDesc  = __('getapp_tw_desc');
include 'header.php';
?>

<main class="container-xl py-4 flex-grow-1">

  <!-- HERO -->
  <div class="premium-glass p-5 shadow-lg mb-4 mt-4 position-relative overflow-hidden reveal-up hero-accent">
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    <div class="row position-relative z-index-1 align-items-center">
      <div class="col-lg-8">
        <div class="mb-4">
          <i class="fa-brands fa-android me-3 text-success float-icon" style="font-size:clamp(2rem,5vw,3rem);"></i>
          <h1 class="display-5 fw-bolder text-gradient-premium m-0">Grubeli.Ge Pro </h1>
        </div>
        <p class="lead text-white-80 fw-light">
          <strong class="text-white">Android აპლიკაცია</strong> – ყველა ფუნქცია, რაც გჭირდებათ ამინდის შესახებ ინფორმირებული რომ იყოთ, პირდაპირ თქვენს ტელეფონზე. <strong class="text-white">Push შეტყობინებები</strong>, <strong class="text-white">რეალურ დროში გაფრთხილებები</strong> და <strong class="text-white">AI-ზე მომუშავე ჭკვიანი ასისტენტი</strong> — ყველაფერი ქართულ ენაზე.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
        <div class="d-inline-flex flex-column align-items-lg-end gap-3 w-100">
          <div class="status-pill d-inline-flex align-items-center">
           
            <span class="fw-bold">Grubeli.Ge   <span class="version-badge">PRO</span></span>
          </div>
          <div class="status-pill d-inline-flex align-items-center">
            <i class="fa-solid fa-mobile-screen-button me-2 text-info"></i>
            <span class="fw-bold">დააინსტალირე აპი</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Download Buttons -->
  <div class="row g-4 mb-4 reveal-up delay-1">
    <div class="col-md-6">
      <a href="app-release.apk" class="premium-glass p-4 h-100 d-block text-center text-decoration-none download-card card-accent-cyan interactive-card" download>
        <div class="icon-hexagon hex-cyan mx-auto mb-3" style="width:70px;height:70px;font-size:2rem;">
          <i class="fa-solid fa-download"></i>
        </div>
        <h4 class="text-white fw-bold mb-2">პირდაპირი APK</h4>
        <p class="text-white-60 small mb-3">ჩამოტვირთე აპკ ფაილი პირდაპირ საიტიდან</p>
        <span class="badge bg-info bg-opacity-25 text-info px-3 py-2 rounded-pill fs-6 fw-normal">
          <i class="fa-solid fa-circle-down me-1"></i> ჩამოტვირთვა (4.5 MB)
        </span>
        <div class="mt-3 text-white-50 small">
          <i class="fa-solid fa-shield-halved me-1 text-success"></i> უსაფრთხო · სწრაფი · პირდაპირი ბმული
        </div>
      </a>
    </div>
    <div class="col-md-6">
      <a href="https://play.google.com/store/apps/details?id=ge.grubeli.weatherapp" target="_blank" rel="noopener" class="premium-glass p-4 h-100 d-block text-center text-decoration-none download-card card-accent-emerald interactive-card">
        <div class="icon-hexagon hex-emerald mx-auto mb-3" style="width:70px;height:70px;font-size:2rem;">
          <i class="fa-brands fa-google-play"></i>
        </div>
        <h4 class="text-white fw-bold mb-2">Google Play Store</h4>
        <p class="text-white-60 small mb-3">მიიღე ოფიციალური ვერსია Google Play-დან</p>
        <span class="badge px-3 py-2 rounded-pill fs-6 fw-normal" style="background:rgba(32,201,151,0.25);color:#20c997;">
          <i class="fa-brands fa-google-play me-1"></i> Google Play Store
        </span>
        <div class="mt-3 text-white-50 small">
          <i class="fa-solid fa-star me-1 text-warning"></i> ავტომატური განახლებები · ოფიციალური წყარო
        </div>
      </a>
    </div>
  </div>

  <!-- Features -->
  <div class="row g-4">

    <div class="col-lg-6 reveal-up delay-1">
      <div class="premium-glass p-5 h-100 card-accent-cyan interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-cyan me-3"><i class="fa-solid fa-mobile-screen"></i></div>
          რატომ Android აპლიკაცია?
        </h3>
        <p class="text-white-60 mb-4">ვებ-ვერსიაც კომფორტულია, მაგრამ <strong class="text-white">Grubeli.Ge Pro</strong> მოგცემთ იმ ფუნქციებს, რაც ბრაუზერში შეუძლებელია:</p>

        <div class="data-item">
          <i class="fa-solid fa-bell text-info"></i>
          <div>
            <h6 class="text-white mb-1">Push შეტყობინებები</h6>
            <p class="small text-white-50 mb-0">მიიღეთ რეალურ დროში გაფრთხილებები მიწისძვრის, ხანძრის, შტორმის ან ყოველდღიური ამინდის შესახებ — მაშინაც კი, როცა აპი დახურულია.</p>
          </div>
        </div>

        <div class="data-item mt-2">
          <i class="fa-solid fa-house-chimney-crack text-danger"></i>
          <div>
            <h6 class="text-white mb-1">მიწისძვრის გაფრთხილება</h6>
            <p class="small text-white-50 mb-0">M4.0+ ბიძგების შესახებ მყისიერი შეტყობინება, სანამ ახალ ამბებს ნახავთ.</p>
          </div>
        </div>

        <div class="data-item mt-2">
          <i class="fa-solid fa-fire-flame-curved text-warning"></i>
          <div>
            <h6 class="text-white mb-1">ხანძრის მონიტორინგი</h6>
            <p class="small text-white-50 mb-0">NASA FIRMS-ის რეალურ დროში მონაცემები — ღია ხანძრების გაფრთხილება თქვენს რეგიონში.</p>
          </div>
        </div>

        <div class="data-item mt-2">
          <i class="fa-solid fa-wind text-info"></i>
          <div>
            <h6 class="text-white mb-1">შტორმის გაფრთხილება</h6>
            <p class="small text-white-50 mb-0">საშიში ამინდის პირობების (ძლიერი ქარი, ქარიშხალი, სეტყვა) დროული შეტყობინება.</p>
          </div>
        </div>

        <div class="data-item mt-2">
          <i class="fa-solid fa-widget text-success"></i>
          <div>
            <h6 class="text-white mb-1">ეკრანის ვიჯეტი</h6>
            <p class="small text-white-50 mb-0">მთავარ ეკრანზე ვიჯეტი — ამინდი ერთი შეხედვით, აპის გახსნის გარეშე.</p>
          </div>
        </div>

      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-2">
      <div class="premium-glass p-5 h-100 card-accent-emerald interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-emerald me-3"><i class="fa-solid fa-star"></i></div>
          პრემიუმ ფუნქციები
        </h3>

        <ul class="premium-list">
          <li><i class="fa-regular fa-bell color-emerald"></i> <span><strong>FCM Push შეტყობინებები</strong> — მიწისძვრა, ხანძარი, ქარიშხალი, ყოველდღიური ამინდი, Solar Flare.</span></li>
          <li><i class="fa-solid fa-grip"></i> <span><strong>ვიჯეტი მთავარ ეკრანზე</strong> — Android-ის ვიჯეტი, განახლდება ავტომატურად.</span></li>
          <li><i class="fa-solid fa-truck-fast"></i> <span><strong>ჩქარი ჩატვირთვა</strong> — WebView-ის ოპტიმიზებული ქეში, მინიმალური ტრაფიკი.</span></li>
          <li><i class="fa-solid fa-street-view"></i> <span><strong>ავტომატური ლოკაცია</strong> — Android-ის GPS-ის ზუსტი განსაზღვრა, Background Location.</span></li>
          <li><i class="fa-solid fa-plane-slash"></i> <span><strong>Offline ქეში</strong> — ბოლო მონაცემები ხელმისაწვდომია ინტერნეტის გარეშეც.</span></li>
          <li><i class="fa-solid fa-code-commit"></i> <span><strong>Native ბრიჯი</strong> — Android ↔ JavaScript ბრძანებები, FCM-ის რეგისტრაცია.</span></li>
          <li><i class="fa-regular fa-bell-slash"></i> <span><strong>შეტყობინებების მართვა</strong> — ჩართე/გამორთე კატეგორიები: Earthquake, Fire, Storm, Daily, Solar.</span></li>
          <li><i class="fa-solid fa-wand-sparkles"></i> <span><strong>10-წუთიანი ინტერვალი</strong> — ფონური განახლება, გაფრთხილებების მყისიერი მიღება.</span></li>
          <li><i class="fa-regular fa-clone"></i> <span><strong>Manifest V3</strong> — თანამედროვე WebView, Material You დიზაინი.</span></li>
          <li><i class="fa-solid fa-shield-halved"></i> <span><strong>უსაფრთხო</strong> — მინიმალური ნებართვები, გამჭვირვალე წყაროს კოდი.</span></li>
        </ul>
      </div>
    </div>

    <div class="col-lg-12 reveal-up delay-3">
      <div class="premium-glass p-5 card-accent-orange interactive-card text-center">
        <h3 class="mb-4 text-white d-flex align-items-center justify-content-center">
          <div class="icon-hexagon hex-orange me-3"><i class="fa-solid fa-circle-question"></i></div>
          ხშირად დასმული კითხვები
        </h3>

        <div class="row g-4 text-start">
          <div class="col-md-6">
            <div class="faq-item p-3 h-100">
              <h6 class="text-white fw-bold mb-2"><i class="fa-solid fa-angle-right text-info me-2"></i>რა ღირს Grubeli.Ge Pro?</h6>
              <p class="small text-white-50 mb-0"><strong class="text-white">Grubeli.Ge Pro</strong> არის სიმბოლური ფასის მქონე აპლიკაცია. ეს თანხა პირდაპირ მიდის სერვერების, API-ების და პლატფორმის განვითარების უზრუნველსაყოფად. რეკლამისგან თავისუფალი გარემოს შენარჩუნება, რეალურ დროში გაფრთხილებები და AI-ზე დაფუძნებული ჭკვიანი ასისტენტი რესურსებს მოითხოვს — თქვენი მხარდაჭერა საშუალებას გვაძლევს, გავაგრძელოთ ხარისხიანი სერვისის შეთავაზება. ამ მხარდაჭერის გარეშე პლატფორმის არსებობა შეუძლებელი იქნებოდა.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="faq-item p-3 h-100">
              <h6 class="text-white fw-bold mb-2"><i class="fa-solid fa-angle-right text-info me-2"></i>რა განსხვავებაა ვებ-ვერსიასა და აპს შორის?</h6>
              <p class="small text-white-50 mb-0">აპი გთავაზობთ <strong class="text-white">Push შეტყობინებებს</strong> (მიწისძვრა, ხანძარი, შტორმი), <strong class="text-white">ეკრანის ვიჯეტს</strong>, <strong class="text-white">Background Location</strong>-ს და <strong class="text-white">Offline ქეშს</strong>. ვებ-ვერსიაც ძლიერია, მაგრამ აპი — უფრო მეტია.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="faq-item p-3 h-100">
              <h6 class="text-white fw-bold mb-2"><i class="fa-solid fa-angle-right text-info me-2"></i>არის აპი iOS-ისთვის?</h6>
              <p class="small text-white-50 mb-0">ამ ეტაპზე მხოლოდ <strong class="text-white">Android</strong>. iOS ვერსია განვითარების პროცესშია — მალე App Store-ზეც.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="faq-item p-3 h-100">
              <h6 class="text-white fw-bold mb-2"><i class="fa-solid fa-angle-right text-info me-2"></i>რომელი Android ვერსიებია მხარდაჭერილი?</h6>
              <p class="small text-white-50 mb-0"><strong class="text-white">Android 8.0 (Oreo)</strong> და უფრო ახალი. ოპტიმიზებულია Material You-სთვის Android 12+-ზე.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- How to Install -->
    <div class="col-lg-12 reveal-up delay-3">
      <div class="premium-glass p-5 card-accent-purple interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-purple me-3"><i class="fa-solid fa-circle-info"></i></div>
          როგორ დავაყენოთ?
        </h3>
        <div class="row g-4">
          <div class="col-md-6">
            <div class="install-step p-3">
              <div class="step-number">1</div>
              <h6 class="text-white mb-1">ჩამოტვირთეთ APK</h6>
              <p class="small text-white-50 mb-0">დააჭირეთ ღილაკს „პირდაპირი APK“ ან გადადით Google Play Store-ზე.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="install-step p-3">
              <div class="step-number">2</div>
              <h6 class="text-white mb-1">დაუშვით უცნობი წყაროები</h6>
              <p class="small text-white-50 mb-0">თუ APK-დაა აყენებთ, ჩართეთ <strong class="text-white">„Install from unknown sources“</strong> პარამეტრი.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="install-step p-3">
              <div class="step-number">3</div>
              <h6 class="text-white mb-1">დააყენეთ აპი</h6>
              <p class="small text-white-50 mb-0">გახსენით ჩამოტვირთული ფაილი და მიყევით ინსტრუქციას. ინსტალაცია გრძელდება რამდენიმე წამს.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="install-step p-3">
              <div class="step-number">4</div>
              <h6 class="text-white mb-1">დაიწყეთ გამოყენება</h6>
              <p class="small text-white-50 mb-0">გახსენით აპი, მიანიჭეთ ლოკაციის ნებართვა და ისიამოვნეთ ყველა ფუნქციით!</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<style>
.download-card {
  border-radius: 24px;
  transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.download-card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}
.faq-item {
  background: rgba(0,0,0,0.2);
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.05);
  transition: 0.25s;
}
.faq-item:hover {
  background: rgba(0,0,0,0.3);
  border-color: rgba(255,255,255,0.1);
}
.install-step {
  background: rgba(0,0,0,0.2);
  border-radius: 16px;
  border-left: 3px solid rgba(138,43,226,0.5);
  position: relative;
  padding-left: 50px !important;
}
.step-number {
  position: absolute;
  left: 12px;
  top: 12px;
  width: 28px;
  height: 28px;
  background: rgba(138,43,226,0.3);
  color: #a78bfa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>