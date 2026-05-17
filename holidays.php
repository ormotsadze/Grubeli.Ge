<?php
require_once 'functions.php';

// ვიღებთ თარგმანებს 'index_' პრეფიქსით
$pageTitle   = __('holidays_title');
$pageDesc    = __('holidays_desc');
$pageOgTitle = __('holidays_og_title');
$pageTwTitle = __('holidays_tw_title');
$pageTwDesc  = __('holidays_tw_desc');
include 'header.php';

// დღესასწაულების მასივი (2026 წლის მონაცემებით)
$holidays = [
    ['date' => '01-01', 'name' => 'ახალი წელი', 'en' => 'New Year\'s Day', 'type' => 'public'],
    ['date' => '01-02', 'name' => 'ბედობა', 'en' => 'Bedoba (Day of Luck)', 'type' => 'public'],
    ['date' => '01-07', 'name' => 'ქრისტეს შობა', 'en' => 'Orthodox Christmas', 'type' => 'religious'],
    ['date' => '01-19', 'name' => 'ნათლისღება', 'en' => 'Epiphany', 'type' => 'religious'],
    ['date' => '03-03', 'name' => 'დედის დღე', 'en' => 'Mother\'s Day', 'type' => 'public'],
    ['date' => '03-08', 'name' => 'ქალთა საერთაშორისო დღე', 'en' => 'International Women\'s Day', 'type' => 'public'],
    ['date' => '04-09', 'name' => 'ეროვნული ერთიანობის დღე', 'en' => 'National Unity Day', 'type' => 'public'],
    ['date' => '04-10', 'name' => 'წითელი პარასკევი (2026)', 'en' => 'Good Friday', 'type' => 'religious'],
    ['date' => '04-11', 'name' => 'დიდი შაბათი', 'en' => 'Great Saturday', 'type' => 'religious'],
    ['date' => '04-12', 'name' => 'აღდგომა (2026)', 'en' => 'Easter Sunday', 'type' => 'religious'],
    ['date' => '04-13', 'name' => 'მიცვალებულთა მოხსენიების დღე', 'en' => 'Easter Monday', 'type' => 'religious'],
    ['date' => '05-09', 'name' => 'ფაშიზმზე გამარჯვების დღე', 'en' => 'Victory Day', 'type' => 'public'],
    ['date' => '05-12', 'name' => 'წმინდა ანდრია პირველწოდებულის დღე', 'en' => 'St. Andrew\'s Day', 'type' => 'religious'],
    ['date' => '05-26', 'name' => 'დამოუკიდებლობის დღე', 'en' => 'Independence Day', 'type' => 'public'],
    ['date' => '08-28', 'name' => 'მარიამობა', 'en' => 'Assumption of Mary', 'type' => 'religious'],
    ['date' => '10-14', 'name' => 'სვეტიცხოვლობა', 'en' => 'Svetitskhovloba', 'type' => 'religious'],
    ['date' => '11-23', 'name' => 'გიორგობა', 'en' => 'St. George\'s Day', 'type' => 'religious']
];

$currentDate = date('m-d');
?>

<style>
    .holiday-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.05);
        background: rgba(255, 255, 255, 0.02);
    }
    .holiday-card.today {
        border-color: #0dcaf0;
        background: rgba(13, 202, 240, 0.1);
        box-shadow: 0 0 20px rgba(13, 202, 240, 0.2);
    }
    .holiday-card.past {
        opacity: 0.5;
        filter: grayscale(0.5);
    }
    .date-badge {
        width: 60px;
        height: 60px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
        font-weight: bold;
    }
    .type-pill {
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 2px 8px;
        border-radius: 20px;
        background: rgba(255,255,255,0.1);
    }
</style>



<main class="container-xl py-5 flex-grow-1">
    
    <div class="text-center mb-5 reveal-up">
        <h1 class="display-5 fw-bolder text-gradient-premium mb-2">დღესასწაულები 2026</h1>
        <p class="text-light opacity-50">საქართველოს ოფიციალური უქმე დღეების კალენდარი</p>
    </div>

    <?php
    // Separate future (including today) and past holidays
    $future_holidays = [];
    $past_holidays = [];
    foreach ($holidays as $h) {
        if ($h['date'] >= $currentDate) {
            $future_holidays[] = $h;
        } else {
            $past_holidays[] = $h;
        }
    }
    ?>

    <!-- მომავალი დღესასწაულები -->
    <div class="row g-3">
        <?php foreach ($future_holidays as $index => $h): 
            $isToday = ($h['date'] == $currentDate);
            $dateParts = explode('-', $h['date']);
            $day = $dateParts[1];
            $month = $dateParts[0];
            
            $statusClass = $isToday ? 'today' : '';
        ?>
            <div class="col-12 col-md-6 col-lg-4 reveal-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                <div class="premium-glass holiday-card p-3 h-100 d-flex align-items-center <?php echo $statusClass; ?>">
                    
                    <div class="date-badge me-3">
                        <span style="font-size: 1.2rem;"><?php echo $day; ?></span>
                        <span style="font-size: 0.7rem; opacity: 0.8;"><?php echo $month; ?></span>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="text-light mb-0 fw-bold"><?php echo $h['name']; ?></h6>
                            <?php if ($isToday): ?>
                                <span class="badge bg-info text-dark pulse-animation" style="font-size: 0.6rem;">დღეს</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-info small opacity-75 mb-1"><?php echo $h['en']; ?></div>
                        <span class="type-pill text-light opacity-50">
                            <?php echo $h['type'] == 'religious' ? 'რელიგიური' : 'სახელმწიფო'; ?>
                        </span>
                    </div>
                    
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($past_holidays)): ?>
    <div class="mt-4">
        <h5 class="text-white-50 mb-3" style="opacity: 0.5; font-weight: 300;">
            <i class="fa-regular fa-calendar-check me-2"></i> გასული დღესასწაულები
        </h5>
        <div class="row g-3">
            <?php foreach ($past_holidays as $index => $h): 
                $dateParts = explode('-', $h['date']);
                $day = $dateParts[1];
                $month = $dateParts[0];
            ?>
                <div class="col-12 col-md-6 col-lg-4 reveal-up" style="animation-delay: <?php echo $index * 0.03; ?>s;">
                    <div class="premium-glass holiday-card p-3 h-100 d-flex align-items-center past">
                        
                        <div class="date-badge me-3">
                            <span style="font-size: 1.2rem;"><?php echo $day; ?></span>
                            <span style="font-size: 0.7rem; opacity: 0.8;"><?php echo $month; ?></span>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="text-light mb-0 fw-bold"><?php echo $h['name']; ?></h6>
                            </div>
                            <div class="text-info small opacity-75 mb-1"><?php echo $h['en']; ?></div>
                            <span class="type-pill text-light opacity-50">
                                <?php echo $h['type'] == 'religious' ? 'რელიგიური' : 'სახელმწიფო'; ?>
                            </span>
                        </div>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="mt-5 p-4 premium-glass border-cyan-soft reveal-up">
        <p class="text-light opacity-75 mb-0 small">
            <i class="fas fa-info-circle me-2 text-info"></i>
            შენიშვნა: "მოძრავი" დღესასწაულების (აღდგომა და მასთან დაკავშირებული დღეები) თარიღები მითითებულია სპეციალურად <strong>2026</strong> წლისთვის. უქმე დღეებში სახელმწიფო დაწესებულებები და ბანკების უმეტესობა არ მუშაობს.
        </p>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>