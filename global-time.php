<?php
require_once 'functions.php';

// ვიღებთ თარგმანებს 'index_' პრეფიქსით
$pageTitle   = __('global_time_title');
$pageDesc    = __('global_time_desc');
$pageOgTitle = __('global_time_og_title');
$pageTwTitle = __('global_time_tw_title');
$pageTwDesc  = __('global_time_tw_desc');
include 'header.php';

// ქალაქების სია დალაგებული პრიორიტეტით: მეზობლები -> ევროპა -> აზია/ამერიკა
$cities = [
    // მეზობლები
    ['name' => 'თბილისი', 'tz' => 'Asia/Tbilisi', 'country' => 'საქართველო'],
    ['name' => 'ერევანი', 'tz' => 'Asia/Yerevan', 'country' => 'სომხეთი'],
    ['name' => 'ბაქო', 'tz' => 'Asia/Baku', 'country' => 'აზერბაიჯანი'],
    ['name' => 'სტამბოლი', 'tz' => 'Europe/Istanbul', 'country' => 'თურქეთი'],
    ['name' => 'ანკარა', 'tz' => 'Europe/Istanbul', 'country' => 'თურქეთი'],
    ['name' => 'კიევი', 'tz' => 'Europe/Kyiv', 'country' => 'უკრაინა'],
    
    // ახლო რეგიონი და ევროპა
    ['name' => 'დუბაი', 'tz' => 'Asia/Dubai', 'country' => 'საამიროები'],
    ['name' => 'ათენი', 'tz' => 'Europe/Athens', 'country' => 'საბერძნეთი'],
    ['name' => 'რომი', 'tz' => 'Europe/Rome', 'country' => 'იტალია'],
    ['name' => 'ბერლინი', 'tz' => 'Europe/Berlin', 'country' => 'გერმანია'],
    ['name' => 'პარიზი', 'tz' => 'Europe/Paris', 'country' => 'საფრანგეთი'],
    ['name' => 'მადრიდი', 'tz' => 'Europe/Madrid', 'country' => 'ესპანეთი'],
    ['name' => 'ვარშავა', 'tz' => 'Europe/Warsaw', 'country' => 'პოლონეთი'],
    ['name' => 'პრაღა', 'tz' => 'Europe/Prague', 'country' => 'ჩეხეთი'],
    ['name' => 'ლონდონი', 'tz' => 'Europe/London', 'country' => 'დიდი ბრიტანეთი'],
    
    // აზია და ოკეანეთი
    ['name' => 'დოჰა', 'tz' => 'Asia/Qatar', 'country' => 'კატარი'],
    ['name' => 'ტოკიო', 'tz' => 'Asia/Tokyo', 'country' => 'იაპონია'],
    ['name' => 'სეული', 'tz' => 'Asia/Seoul', 'country' => 'კორეა'],
    ['name' => 'პეკინი', 'tz' => 'Asia/Shanghai', 'country' => 'ჩინეთი'],
    ['name' => 'სინგაპური', 'tz' => 'Asia/Singapore', 'country' => 'სინგაპური'],
    ['name' => 'ბანგკოკი', 'tz' => 'Asia/Bangkok', 'country' => 'ტაილანდი'],
    ['name' => 'სიდნეი', 'tz' => 'Australia/Sydney', 'country' => 'ავსტრალია'],
    
    // ამერიკა
    ['name' => 'ნიუ-იორკი', 'tz' => 'America/New_York', 'country' => 'აშშ'],
    ['name' => 'ტორონტო', 'tz' => 'America/Toronto', 'country' => 'კანადა'],
    ['name' => 'ჩიკაგო', 'tz' => 'America/Chicago', 'country' => 'აშშ'],
    ['name' => 'მაიამი', 'tz' => 'America/New_York', 'country' => 'აშშ'],
    ['name' => 'ლოს-ანჯელესი', 'tz' => 'America/Los_Angeles', 'country' => 'აშშ'],
    ['name' => 'სან-ფრანცისკო', 'tz' => 'America/Los_Angeles', 'country' => 'აშშ'],
    ['name' => 'მეხიკო', 'tz' => 'America/Mexico_City', 'country' => 'მექსიკა'],
    ['name' => 'ბუენოს-აირესი', 'tz' => 'America/Argentina/Buenos_Aires', 'country' => 'არგენტინა']
];
?>

<style>
    .time-card {
        transition: transform 0.2s ease, background 0.3s ease;
        border: 1px solid rgba(255,255,255,0.08);
        padding: 1rem 0.4rem !important;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .time-card:hover {
        transform: scale(1.03);
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(13, 202, 240, 0.4);
    }
    .digital-clock {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 1.25rem; /* ზომა ოპტიმიზებულია, რომ არ გადმოვიდეს */
        font-weight: 700;
        color: #0dcaf0;
        margin-top: 4px;
        letter-spacing: 1px;
    }
    .city-name {
        font-size: 0.95rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }
    .country-name {
        font-size: 0.7rem;
        opacity: 0.4;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* მობილური ოპტიმიზაცია */
    @media (max-width: 576px) {
        .digital-clock { font-size: 1.15rem; }
        .city-name { font-size: 0.85rem; }
        .time-card { min-height: 90px; }
    }
</style>



<main class="container-xl py-5 flex-grow-1">
    
    <div class="text-center mb-5 reveal-up">
        <h1 class="display-5 fw-bolder text-gradient-premium mb-2">მსოფლიო დრო</h1>
        <p class="text-light opacity-50 small">ზუსტი დროის ზონები მეზობელი ქვეყნებიდან გლობალურ მეგაპოლისებამდე</p>
    </div>

    <div class="row g-2 g-md-3 justify-content-center">
        <?php foreach ($cities as $index => $city): ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2 reveal-up" style="animation-delay: <?php echo $index * 0.03; ?>s;">
                <div class="premium-glass time-card text-center">
                    <span class="city-name text-light"><?php echo $city['name']; ?></span>
                    <span class="country-name text-light"><?php echo $city['country']; ?></span>
                    
                    <div class="digital-clock" data-timezone="<?php echo $city['tz']; ?>">
                        --:--:--
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<script>
    function updateClocks() {
        const clocks = document.querySelectorAll('.digital-clock');
        const now = new Date();
        
        clocks.forEach(clock => {
            const timezone = clock.getAttribute('data-timezone');
            try {
                clock.innerText = now.toLocaleTimeString('en-GB', {
                    timeZone: timezone,
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            } catch (e) {
                clock.innerText = "Error";
            }
        });
    }
    setInterval(updateClocks, 1000);
    updateClocks();
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>