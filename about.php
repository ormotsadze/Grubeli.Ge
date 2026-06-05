<?php
require_once 'functions.php';

// ვიღებთ თარგმანებს 'about_' პრეფიქსით
$pageTitle   = __('about_title');
$pageDesc    = __('about_desc');
$pageOgTitle = __('about_og_title');
$pageTwTitle = __('about_tw_title');
$pageTwDesc  = __('about_tw_desc');
include 'header.php';
?>


<main class="container-xl py-4 flex-grow-1">

  <div class="premium-glass p-5 shadow-lg mb-4 mt-4 position-relative overflow-hidden reveal-up hero-accent">
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    <div class="row position-relative z-index-1">
      <div class="col-lg-8">
        <div class="mb-4">
          <i class="fas fa-cloud-moon me-3 text-info float-icon" style="font-size:clamp(2rem,5vw,3rem);"></i>
          <h1 class="display-5 fw-bolder text-gradient-premium m-0">ჩვენ შესახებ</h1>
        </div>
        <p class="lead text-white-80 fw-light">
          <strong class="text-white">Grubeli.ge</strong> არის საქართველოზე ორიენტირებული ამინდის 
          პლატფორმა, რომელიც აერთიანებს რეალურ მეტეოროლოგიურ მონაცემებს და <strong class="text-white">ხელოვნური ინტელექტის</strong> ანალიზს.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
        <div class="status-pill d-inline-flex align-items-center">
          <i class="fas fa-info-circle me-2 text-primary"></i>
          <span class="fw-bold">სტატუსი: BETA</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">

    <div class="col-lg-6 reveal-up delay-1">
      <div class="premium-glass p-5 h-100 card-accent-cyan interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-cyan me-3"><i class="fas fa-bullseye"></i></div>
          1. ჩვენი მისია
        </h3>
        <p class="text-white-60 mb-4">ჩვენი მიზანია, მომხმარებელს მივაწოდოთ არა მხოლოდ ციფრები, არამედ <strong class="text-white">
		პრაქტიკული პასუხები</strong> სიმშვიდის და უსაფრთხოების განცდა ყოველდღიურობაში.
		ჩვენ გვჯერა, რომ ინფორმაცია მაშინ არის ძვირფასი, როდესაც ის დროული და გასაგებია. სწორედ ამიტომ, ჩვენი მისიაა 
		შევქმნათ საიმედო ციფრული სივრცე, რომელიც რეალურ დროში დაამუშავებს გლობალურ მონაცემებს და აქცევს მათ მარტივ,
		პრაქტიკულ გაფრთხილებებად. ჩვენ არ ვპროგნოზირებთ უბრალოდ ამინდს ან სტიქიას, ჩვენ გეხმარებით იყოთ ერთი ნაბიჯით წინ და 
		 დაგეგმოთ თქვენი დღე სიურპრიზების გარეშე.
		</p>
        <div class="data-item">
          <i class="fas fa-location-dot text-info"></i>
          <div>
            <h6 class="text-white mb-1">მხოლოდ საქართველო</h6>
            <p class="small text-white-50 mb-0">სპეციალურად შექმნილია საქართველოსთვის — ლოკაციის ვალიდაცია, ქართული ენა, ადგილობრივი კონტექსტი.</p>
          </div>
        </div>
		 <div class="data-item mt-2">
		 <i class="fa-brands fa-adversal text-info"></i>
        
          <div>
            <h6 class="text-white mb-1">არავითარი რეკლამა !</h6>
            <p class="small text-white-50 mb-0">
			ჩვენს სისტემაში (ვებსაიტზე, აპში, სოც.ქსელების გვერდებზე) არ არის და არასოდეს იქნება (!) რეკლამა. გვჯერა, რომ მოწოდებული ინფორმაცია უნდა იყოს
			სუფთა, გასაგები და ლამაზი.
			</p>
          </div>
        </div>
        <div class="data-item mt-2">
          <i class="fas fa-brain text-info"></i>
          <div>
            <h6 class="text-white mb-1">AI-powered ანალიზი</h6>
            <p class="small text-white-50 mb-0">სისტემა მეტეოროლოგიურ მონაცემებს გარდაქმნის მარტივ, ადამიანურ ენაზე პასუხებად GPT 5 / GPT‑4o-ის დახმარებით.</p>
          </div>
        </div>
		
		 <div class="data-item mt-2">
         
		  <i class="fa-solid fa-bell text-info"></i>
          <div>
            <h6 class="text-white mb-1">სიზუსტე და სისწრაფე</h6>
            <p class="small text-white-50 mb-0">მიიღოთ შეტყობინება მაშინვე, როცა ეს ყველაზე მეტად გჭირდებათ.Android აპში ჩვენ შეგატყობინებთ არა მხოლოდ საფრთხის, არამედ სასიამოვნო დღეებისა და უქმეების შესახებაც, რათა თქვენი ცხოვრება უფრო კომფორტული გახდეს.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 reveal-up delay-2">
      <div class="premium-glass p-5 h-100 card-accent-purple interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-purple me-3"><i class="fas fa-microchip"></i></div>
          2. ტექნოლოგიური ბაზა
        </h3>
	 <p class="text-white-60 mb-4">ჩვენი სერვისის WEB და მობილური ვერსია არის უფასო და რეკლამის გარეშე <strong class="text-white">
		და ყოველთვის ასე იქნება ! </strong> თქვენ შეგიძლიათ მათი გამოყენება რამდენი ხანის გსურთ. ჩვენი Android აპლიკაცია კი ფასიანია და ღირს სიმბოლური ფასი. 
		ეს აუცილებელია იმისათვის , რომ უბრალოდ ვიარსებოთ და ისევ მოგაწოდოთ თქვენ და სხვებს სერვისი, მათ შორის უფასოდაც. Android აპლიკაცია ხელმისაწვდომია ოფიციალურად Google Play-ზე 
		(<a href="getapp.php" class="text-info">იხილეთ აპის გვერდი</a>) დიდი მადლობა გაგებისთვის.
		</p>
	
        <div class="service-box mb-3 p-3 border-cyan-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-info m-0"><i class="fas fa-cloud-sun me-2"></i>Open-Meteo</h6>
            <span class="badge bg-info bg-opacity-25 text-info">ამინდი + ჰაერი</span>
          </div>
          <p class="small text-white-50 m-0">საათობრივი და 10-დღიანი პროგნოზი, UV, ჰაერის ხარისხი, ისტორიული მონაცემები 80 წლამდე.</p>
        </div>
        <div class="service-box mb-3 p-3 border-purple-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="color-purple m-0"><i class="fas fa-robot me-2"></i>GPT 5 / GPT 4-O</h6>
            <span class="badge color-purple" style="background:rgba(138,43,226,0.2);">AI ასისტენტი</span>
          </div>
          <p class="small text-white-50 m-0">ამინდის ჭკვიანი ინტერპრეტაცია და პრაქტიკული რეკომენდაციები ქართულ და ინგლისურ ენებზე.</p>
        </div>
        <div class="service-box mb-3 p-3 border-amber-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-warning m-0"><i class="fas fa-house-chimney-crack me-2"></i>USGS API</h6>
            <span class="badge bg-warning bg-opacity-25 text-warning">მიწისძვრა</span>
          </div>
          <p class="small text-white-50 m-0">M4.0+ მიწისძვრების გაფრთხილება საქართველოს რეგიონში, განახლება 5 წუთში ერთხელ.</p>
        </div>
   <div class="service-box p-3 border-emerald-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="color-emerald m-0"><i class="fas fa-map-pin me-2"></i>Nager.at</h6>
            <span class="badge color-emerald" style="background:rgba(32,201,151,0.2);">გეო + უქმეები</span>
          </div>
          <p class="small text-white-50 m-0">ერთი დღით ადრე, თქვენ მიიღებთ შეტყობინებას სახელმწიფო დღესასწაულების შესახებ.</p>
        </div>
        <div class="service-box mt-3 p-3 border-danger-soft">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h6 class="text-danger m-0"><i class="fas fa-fire-flame-curved me-2"></i>NASA FIRMS</h6>
            <span class="badge bg-danger bg-opacity-25 text-danger">ხანძარი</span>
          </div>
          <p class="small text-white-50 m-0">VIIRS SNPP NRT — რეალურ დროში ხანძრების მონიტორინგი საქართველოს ტერიტორიაზე.</p>
        </div>
      </div>
    </div>

    <div class="col-lg-12 reveal-up delay-3">
      <div class="premium-glass p-5 h-100 card-accent-emerald interactive-card">
        <h3 class="mb-4 text-white d-flex align-items-center">
          <div class="icon-hexagon hex-emerald me-3"><i class="fas fa-star"></i></div>
          3. მთავარი ფუნქციები
        </h3>
        <ul class="premium-list">
          <li><i class="fas fa-thermometer-half color-emerald"></i><span><strong>რეალური ამინდი</strong> — ტემპ., შეგრძნება, ქარი, ტენიანობა ქართულ აღწერით.</span></li>
          <li><i class="fas fa-clock color-emerald"></i><span><strong>12-საათიანი პროგნოზი</strong> — სქროლირებადი საათობრივი პანელი ხატულებით.</span></li>
          <li><i class="fas fa-calendar-days color-emerald"></i><span><strong>10-დღიანი პროგნოზი</strong> — მაქს/მინ ტემპ. ყოველი დღისთვის.</span></li>
          <li><i class="fas fa-wind color-emerald"></i><span><strong>UV და ჰაერის ხარისხი</strong> — US AQI ინდექსი, UV დონე განმარტებით.</span></li>
          <li><i class="fas fa-sun color-emerald"></i><span><strong>მზის ამოსვლა/ჩასვლა</strong> — ზუსტი დრო და დღის ხანგძლივობა.</span></li>
          <li><i class="fas fa-robot color-emerald"></i><span><strong>AI ასისტენტი</strong> — ქოლგა, ჩაცმა, სეირნობა, ველო, სირბილი, სარეცხი.</span></li>
          <li><i class="fas fa-clock-rotate-left color-emerald"></i><span><strong>ამინდის არქივი</strong> — 80 წლამდე ისტორია ხაზოვანი დიაგრამით.</span></li>
          <li><i class="fas fa-house-chimney-crack color-emerald"></i><span><strong>მიწისძვრის გაფრთხილება</strong> — M4.0+ ბიძგები ქვეყნის რეგიონში.</span></li>
          <li><i class="fas fa-calendar-check color-emerald"></i><span><strong>უქმე დღეები</strong> — დღევანდელი სახელმწიფო დღესასწაული.</span></li>
           <li><i class="fa-regular fa-clock"></i><span><strong>მსოფლიო დრო</strong> — ზუსტი დრო მსოფლიოში ყველაზე პოპულარულ ქალაქში.</span></li>
           <li><i class="fa-solid fa-fire-flame-curved color-emerald"></i><span><strong>ხანძრის გაფრთხილება</strong> — NASA-ს თანამგზავრიდან ღია ხანძრების რეალურ დროში მონიტორინგი.</span></li>
           <li><i class="fa-solid fa-quote-right color-emerald"></i><span><strong>AI ციტატები</strong> — შემთხვევითი, ამინდის თემატიკის საინტერესო ციტატები ყოველ ჩატვირთვაზე.</span></li>
           <li><i class="fa-solid fa-location-crosshairs color-emerald"></i><span><strong>GPS ლოკაცია</strong> — ავტომატური გეოლოკაცია + ქალაქის ძებნა ქართული/ლათინური სიმბოლოებით.</span></li>
           <li><i class="fa-solid fa-bell color-emerald"></i><span><strong>Push შეტყობინებები</strong> — Android-ზე FCM-ით: მიწისძვრა, ხანძარი, შტორმი, ყოველდღიური ამინდი.</span></li>
           <li><i class="fa-regular fa-floppy-disk color-emerald"></i><span><strong>ლოკაციის შენახვა</strong> — ბრაუზერში არჩეული ქალაქის შენახვა შემდეგი ვიზიტისთვის.</span></li>
           <li><i class="fa-regular fa-envelope color-emerald"></i><span><strong>კონტაქტი / წესები / ვაკანსია</strong> — გამოხმაურების, კონფიდენციალურობის და დასაქმების გვერდები.</span></li>
          <li><i class="fas fa-mobile-screen color-emerald"></i><span><strong>Android აპლიკაცია</strong> — WebView-ზე დაფუძნებული, push notifications-ით მიწისძვრის, ხანძრების, შტორმის შესახებ,  და სხვა პრემიუმ ფუნქციებით.</span></li>
        </ul>
      </div>
    </div>

   
  </div>
</main>

<style>
.data-item{display:flex;align-items:flex-start;padding:14px;background:rgba(0,0,0,0.2);border-radius:14px;border:1px solid transparent;transition:0.3s;}
.data-item:hover{border-color:rgba(255,255,255,0.1);}
.data-item+.data-item{margin-top:10px;}
.data-item i{font-size:1.3rem;margin-right:14px;margin-top:2px;flex-shrink:0;}
.service-box{background:rgba(0,0,0,0.22);border-radius:14px;}
.border-cyan-soft{border-left:3px solid rgba(13,202,240,0.5);}
.border-purple-soft{border-left:3px solid rgba(138,43,226,0.5);}
.border-amber-soft{border-left:3px solid rgba(245,183,39,0.5);}
.border-emerald-soft{border-left:3px solid rgba(32,201,151,0.5);}
.border-danger-soft{border-left:3px solid rgba(255,68,68,0.5);}
.premium-list{list-style:none;padding:0;margin:0;}
.premium-list li{display:flex;align-items:flex-start;padding:9px 0;border-bottom:1px solid rgba(255,255,255,0.05);color:rgba(255,255,255,0.75);}
.premium-list li i{font-size:0.9rem;margin-right:11px;margin-top:3px;flex-shrink:0;}
.premium-list li:last-child{border-bottom:none;}
.use-pill{padding:14px 10px;background:rgba(255,255,255,0.05);border-radius:14px;border:1px solid rgba(255,255,255,0.08);transition:0.25s;}
.use-pill:hover{background:rgba(255,255,255,0.09);transform:translateY(-2px);}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>