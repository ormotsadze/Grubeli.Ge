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
      <div class="col-lg-12">
        <div class="mb-4">
          <i class="fa-brands fa-android me-3 text-success float-icon" style="font-size:clamp(2rem,5vw,3rem);"></i>
          <h1 class="display-5 fw-bolder text-gradient-premium m-0">Grubeli.Ge Pro </h1>
        </div>
        <p class="lead text-white-80 fw-light">
          <strong class="text-white">Android აპლიკაცია</strong> – ყველა ფუნქცია, რაც გჭირდებათ ამინდის შესახებ ინფორმირებული რომ იყოთ, პირდაპირ თქვენს ტელეფონზე. <strong class="text-white">Push შეტყობინებები</strong>, <strong class="text-white">რეალურ დროში გაფრთხილებები</strong> და <strong class="text-white">AI-ზე მომუშავე ჭკვიანი ასისტენტი</strong> — ყველაფერი ქართულ ენაზე.
        </p>
      </div>
     
    </div>
  </div>

  <!-- Download Buttons -->
  <div class="row g-4 mb-4 reveal-up delay-1">
    <div class="col-md-12">
      <a href="https://play.google.com/store/apps/details?id=ge.grubeli.weatherapp" target="_blank" rel="noopener" class="premium-glass p-4 h-100 d-block text-center text-decoration-none download-card card-accent-emerald interactive-card">
        <div class="icon-hexagon hex-emerald mx-auto mb-3" style="width:70px;height:70px;font-size:2rem;">
          <i class="fa-brands fa-google-play"></i>
        </div>
        <h4 class="text-white fw-bold mb-2">Google Play Store</h4>
        <p class="text-white-60 small mb-3">ოფიციალური ვერსია Google Play-ზე</p>
        <span class="badge px-3 py-2 rounded-pill fs-6 fw-normal" style="background:rgba(32,201,151,0.25);color:#20c997;">
          <i class="fa-brands fa-google-play me-1"></i> Google Play Store
        </span>
      
      </a>
    </div>
  </div>

  <!-- Features -->
  <div class="row g-4">

    <div class="col-lg-12 reveal-up delay-1">
      <div class="premium-glass p-5 h-100 card-accent-cyan interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-cyan me-3"><i class="fa-solid fa-mobile-screen"></i></div>
          რატომ Android აპლიკაცია?
        </h3>
        <p class="text-white-60 mb-4">ვებ-ვერსიაც კომფორტულია, მაგრამ <strong class="text-white">Grubeli.Ge</strong> <span class="version-badge">PRO</span> მოგცემთ დამატებით პრემიუმ ფუნქციებს, რაც ბრაუზერში არ არის:</p>

        <div class="row g-3 features-grid">
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon-wrap"><span class="feature-icon">✨</span></div>
              <h6 class="text-white mb-1">AI რჩევები</h6>
              <p class="small text-white-50 mb-0">ღილაკზე ერთი თითის დაჭერით, მიიღეთ ინდივიდუალური რჩევები თქვენი ამინდის პირობების მიხედვით - დამჭირდება სათვალე? რა ჩავიცვა? დღეს გავრეცხო მანქანა? და ასე შემდეგ.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon-wrap"><i class="fa-solid fa-bell text-info"></i></div>
              <h6 class="text-white mb-1">Push შეტყობინებები</h6>
              <p class="small text-white-50 mb-0">მიიღეთ რეალურ დროში გაფრთხილებები მიწისძვრის, ხანძრის, შტორმის ან ყოველდღიური ამინდის შესახებ — მაშინაც კი, როცა აპი დახურულია.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon-wrap"><i class="fa-solid fa-house-chimney-crack text-danger"></i></div>
              <h6 class="text-white mb-1">მიწისძვრის გაფრთხილება</h6>
              <p class="small text-white-50 mb-0">M4.0+ ბიძგების შესახებ მყისიერი შეტყობინება, სანამ ახალ ამბებს ნახავთ.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon-wrap"><i class="fa-solid fa-fire-flame-curved text-warning"></i></div>
              <h6 class="text-white mb-1">ხანძრის მონიტორინგი</h6>
              <p class="small text-white-50 mb-0">NASA FIRMS-ის რეალურ დროში მონაცემები — ღია ხანძრების გაფრთხილება თქვენს რეგიონში.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon-wrap"><i class="fa-solid fa-wind text-info"></i></div>
              <h6 class="text-white mb-1">შტორმის გაფრთხილება</h6>
              <p class="small text-white-50 mb-0">საშიში ამინდის პირობების (ძლიერი ქარი, ქარიშხალი, სეტყვა) დროული შეტყობინება.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon-wrap"><i class="fa-solid fa-grip"></i></div>
              <h6 class="text-white mb-1">ეკრანის პრემიუმ ვიჯეტი</h6>
              <p class="small text-white-50 mb-0">ვიჯეთი მთავარ ეკრანზე — ყველა საჭირო ინფორმაცია, აპის გახსნის გარეშე.</p>
            </div>
          </div>
        
        </div>
<span class="text-white-50 small mt-3 d-block"><i class="fa-solid fa-align-left"></i> და კიდევ ბევრი სხვა ფუნქცია, რომელიც მუდმივად ემატება აპლიკაციას.</span>
      </div>
    </div>

   

    <div class="col-lg-12 reveal-up delay-3">
      <div class="premium-glass p-5 card-accent-orange interactive-card text-center">
        <h3 class="mb-4 text-white d-flex align-items-center justify-content-center">
          <div class="icon-hexagon hex-orange me-3"><i class="fa-solid fa-circle-question"></i></div>
          ხშირად დასმული კითხვები
        </h3>

        <div class="accordion accordion-flush" id="faqAccordion">
          <div class="accordion-item bg-transparent">
            <h2 class="accordion-header" id="flush-headingOne">
              <button class="accordion-button collapsed text-white bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                რა ღირს აპლიკაცია?
              </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-white-50 text-start">
                <strong class="text-white">Grubeli.Ge</strong> <span class="version-badge">PRO</span> არის სიმბოლური ფასის მქონე აპლიკაცია. ეს თანხა პირდაპირ მიდის სერვერების, API-ების და პლატფორმის განვითარების უზრუნველსაყოფად. რეკლამისგან თავისუფალი გარემოს შენარჩუნება, რეალურ დროში გაფრთხილებები და AI-ზე დაფუძნებული ჭკვიანი ასისტენტი რესურსებს მოითხოვს — თქვენი მხარდაჭერა საშუalian. ам мхарdаჭeрiс гaрeшe плаtfоrmiс арceбoбa шeуძlеბeли iქнeбoდa.
              </div>
            </div>
          </div>
          <div class="accordion-item bg-transparent">
            <h2 class="accordion-header" id="flush-headingTwo">
              <button class="accordion-button collapsed text-white bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                რა განსხვავებაა ვებ-ვერსიასა და აპს შორის?
              </button>
            </h2>
            <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-white-50 text-start">
                აპი გთავაზობთ <strong class="text-white">Push შეტყობინებებს</strong> (მიწისძვრა, ხანძარი, შტორმი), <strong class="text-white">ეკრანის ვიჯეტს</strong>, <strong class="text-white">Background Location</strong>-ს და <strong class="text-white">Offline ქეშს</strong>. ვებ-ვერსიაც ძლიერია, მაგრამ აპი — უფრო მეტია.
              </div>
            </div>
          </div>
          <div class="accordion-item bg-transparent">
            <h2 class="accordion-header" id="flush-headingThree">
              <button class="accordion-button collapsed text-white bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                არის აპი iOS-ისთვის?
              </button>
            </h2>
            <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-white-50 text-start">
                ამ ეტაპზე მხოლოდ <strong class="text-white">Android</strong>. iOS ვერსია განვითარების პროცესშია — მალე App Store-ზეც.
              </div>
            </div>
          </div>
          <div class="accordion-item bg-transparent">
            <h2 class="accordion-header" id="flush-headingFour">
              <button class="accordion-button collapsed text-white bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                რომელი Android ვერსიებია მხარდაჭერილი?
              </button>
            </h2>
            <div id="flush-collapseFour" class="accordion-collapse collapse" aria-labelledby="flush-headingFour" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-white-50 text-start">
                <strong class="text-white">Android 8.0 (Oreo)</strong> და უფრო ახალი. ოპტიმიზებულია Material You-სთვის Android 12+-ზე.
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</main>

<style>
  .accordion-button {
    background: rgba(255,255,255,0.05) !important;
    color: #fff !important;
    border-radius: 18px !important;
  }
  .accordion-body {
    border-radius: 18px !important;
  }
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
.accordion-item {
    border: none;
}
.accordion-button {
    font-weight: bold;
    font-size: 1.1rem;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    background-color: rgba(0,0,0,0.2) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.25s ease;
}
.accordion-button:not(.collapsed) {
    background-color: rgba(0,0,0,0.3) !important;
    border-color: rgba(255,255,255,0.1);
}
.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(255,255,255,0.2);
}
.accordion-button::after {
    filter: invert(1) brightness(200%);
}
.accordion-body {
    padding: 0.5rem 1.25rem 1.5rem;
    border-radius: 8px;
    background-color: rgba(0,0,0,0.1);
    margin-bottom: 0.5rem;
}

.features-grid {
  margin-top: 1rem;
}

.feature-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 18px;
  padding: 1.5rem 1.25rem;
  height: 100%;
  transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  display: flex;
  flex-direction: column;
}

.feature-card:hover {
  background: rgba(255,255,255,0.06);
  border-color: rgba(255,255,255,0.12);
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}

.feature-icon-wrap {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.05);
  border-radius: 16px;
  margin-bottom: 1rem;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.feature-icon-wrap i {
  font-size: 1.4rem;
}

.feature-card h6 {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.feature-card p {
  font-size: 0.85rem;
  line-height: 1.5;
  flex: 1;
}

@media (max-width: 576px) {
  .feature-card {
    padding: 1.25rem 1rem;
  }
  .feature-icon-wrap {
    width: 42px;
    height: 42px;
    font-size: 1.25rem;
  }
  .feature-icon-wrap i {
    font-size: 1.2rem;
  }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>