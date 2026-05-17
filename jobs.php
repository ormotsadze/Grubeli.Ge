<?php
require_once 'functions.php';

// ვიღებთ თარგმანებს 'index_' პრეფიქსით
$pageTitle   = __('jobs_title');
$pageDesc    = __('jobs_desc');
$pageOgTitle = __('jobs_og_title');
$pageTwTitle = __('jobs_tw_title');
$pageTwDesc  = __('jobs_tw_desc');
include 'header.php';
?>


<main class="container-xl py-4 flex-grow-1">

  <div class="premium-glass p-5 shadow-lg mb-4 mt-4 position-relative overflow-hidden reveal-up hero-accent">
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>
    <div class="row position-relative z-index-1">
      <div class="col-lg-8">
        <div class="mb-4">
          <i class="fas fa-briefcase me-3 text-info float-icon" style="font-size:clamp(2rem,5vw,3rem);"></i>
          <h1 class="display-5 fw-bolder text-gradient-premium m-0">ვაკანსია</h1>
        </div>
        <p class="lead text-white-80 fw-light">
          <strong class="text-white">Grubeli.ge</strong> არის მცირე, მაგრამ ამბიციური გუნდი. ჩვენ ვქმნით საქართველოს ყველაზე ჭკვიან ამინდის სერვისს — და ყოველთვის მოხარული ვიქნებით ნიჭიერი ადამიანების მიღებით.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
        <div class="status-pill d-inline-flex align-items-center">
          <i class="fas fa-circle-dot me-2" style="color:#6c757d;"></i>
          <span class="fw-bold" style="color:rgba(255,255,255,0.6);">ვაკანსია: 0</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ვაკანსია არ არის -->
  <div class="premium-glass p-5 text-center reveal-up delay-1 mb-4">
    <div class="empty-icon mx-auto mb-4">
      <i class="fas fa-mug-hot"></i>
    </div>
    <h3 class="text-white mb-3">ამჟამად ღია ვაკანსია არ არის</h3>
    <p class="text-white-60 mb-0" style="max-width:480px;margin:0 auto;">
      ჩვენ ვმუშაობთ პლატფორმის განვითარებაზე. როდესაც გუნდი გაიზრდება, პირველ რიგში აქ განვათავსებთ ვაკანსიებს. გამოგვიწერეთ სოციალურ ქსელებში, რომ გამოტოვება არ მოხდეს.
    </p>
  </div>

  <!-- გუნდის ღირებულებები -->
  <div class="row g-4 reveal-up delay-2">

    <div class="col-md-4">
      <div class="premium-glass p-4 h-100 card-accent-cyan interactive-card text-center">
        <div class="icon-hexagon hex-cyan mx-auto mb-3"><i class="fas fa-rocket"></i></div>
        <h5 class="text-white mb-2">სწრაფი განვითარება</h5>
        <p class="small text-white-50">პატარა გუნდში ყოველი ადამიანი მნიშვნელოვანია — შენი გადაწყვეტილებები პირდაპირ პროდუქტში ჩანს.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="premium-glass p-4 h-100 card-accent-purple interactive-card text-center">
        <div class="icon-hexagon hex-purple mx-auto mb-3"><i class="fas fa-globe"></i></div>
        <h5 class="text-white mb-2">რეალური პროდუქტი</h5>
        <p class="small text-white-50">Grubeli.ge რეალური მომხმარებლებისთვის მუშაობს — შენი სამუშაო ყოველდღე ათასობით ადამიანს ემსახურება.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="premium-glass p-4 h-100 card-accent-emerald interactive-card text-center">
        <div class="icon-hexagon hex-emerald mx-auto mb-3"><i class="fas fa-handshake"></i></div>
        <h5 class="text-white mb-2">მეგობრული გარემო</h5>
        <p class="small text-white-50">ჩვენთვის მნიშვნელოვანია კომფორტული სამუშაო პირობები, პატივისცემა და ერთმანეთის მხარდაჭერა.</p>
      </div>
    </div>

  </div>

  <!-- სპონტანური განაცხადი -->
  <div class="premium-glass p-4 p-md-5 mt-4 card-accent-orange reveal-up delay-3 position-relative overflow-hidden">
    <div class="ambient-glow glow-2" style="opacity:0.07;"></div>
    <div class="row align-items-center position-relative z-index-1">
      <div class="col-lg-8 mb-3 mb-lg-0">
        <h3 class="text-white mb-2">გამოგვიგზავნე CV</h3>
        <p class="text-white-60 mb-0">თუ გგონია, რომ გამოადგები Grubeli.ge-ს, ნუ დაელოდები ვაკანსიას — გამოგვიგზავნე CV და მოკლე წერილი იმის შესახებ, რა გინდა გააკეთო.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="mailto:info@grubeli.ge?subject=სპონტანური განაცხადი" class="btn-apply d-inline-flex align-items-center gap-2">
          <i class="fas fa-paper-plane"></i>
          <span>CV-ს გამოგზავნა</span>
        </a>
      </div>
    </div>
  </div>

</main>

<style>
.empty-icon{width:80px;height:80px;background:rgba(255,255,255,0.05);border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:2rem;color:rgba(255,255,255,0.25);}
.btn-apply{background:rgba(253,126,20,0.15);border:1px solid rgba(253,126,20,0.35);border-radius:14px;padding:13px 22px;color:#fff;text-decoration:none;font-weight:500;transition:0.3s;}
.btn-apply:hover{background:rgba(253,126,20,0.28);color:#fff;transform:translateY(-2px);}
.btn-apply i{color:#fd7e14;}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>