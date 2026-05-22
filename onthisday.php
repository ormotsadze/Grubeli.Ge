<?php
require_once 'functions.php';
$ai_lang = get_current_lang();
$pageTitle   = __('onthisday_title');
$pageDesc    = __('onthisday_desc');
$pageOgTitle = __('onthisday_og_title');
$pageTwTitle = __('onthisday_tw_title');
$pageTwDesc  = __('onthisday_tw_desc');
include 'header.php';

// === DATE HANDLING ===
$req_m = isset($_GET['m']) ? intval($_GET['m']) : 0;
$req_d = isset($_GET['d']) ? intval($_GET['d']) : 0;

$today_utc = new DateTime('now', new DateTimeZone('UTC'));
$m = ($req_m >= 1 && $req_m <= 12) ? sprintf('%02d', $req_m) : $today_utc->format('m');
$d = ($req_d >= 1 && $req_d <= 31) ? sprintf('%02d', $req_d) : $today_utc->format('d');

$view_date = new DateTime("2000-{$m}-{$d} 12:00:00", new DateTimeZone('UTC'));
$is_today = ($m === $today_utc->format('m') && $d === $today_utc->format('d'));

// Georgian months
$geo_months = ['იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი'];
$geo_weekdays = ['კვირა','ორშაბათი','სამშაბათი','ოთხშაბათი','ხუთშაბათი','პარასკევი','შაბათი'];

$date_formatted = ($ai_lang === 'en')
    ? $view_date->format('F j')
    : intval($d) . ' ' . $geo_months[intval($m) - 1];
$weekday = ($ai_lang === 'en')
    ? $view_date->format('l')
    : $geo_weekdays[intval($view_date->format('w'))];

// === WIKIPEDIA API ===
$wiki_url = "https://en.wikipedia.org/api/rest_v1/feed/onthisday/all/{$m}/{$d}";
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: GrubeliWeatherApp/1.0 (contact@grubeli.ge)\r\n"
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($wiki_url, false, $context);

$events = [];
$births = [];
$deaths = [];

if ($response) {
    $res_data = json_decode($response, true);

    function process_wiki_data_with_media($raw_items, $limit = 10, $reverse_chronological = false) {
        if (empty($raw_items)) return [];
        if ($reverse_chronological) {
            $raw_items = array_reverse($raw_items);
        }
        $raw_items = array_slice($raw_items, 0, $limit);
        $result = [];
        foreach ($raw_items as $item) {
            $year = $item['year'] ?? '';
            $text = $item['text'] ?? '';
            $wiki_link = '#';
            if (!empty($item['pages'][0]['content_urls']['desktop']['page'])) {
                $wiki_link = $item['pages'][0]['content_urls']['desktop']['page'];
            }
            $thumbnail = null;
            if (!empty($item['pages'][0]['thumbnail']['source'])) {
                $thumbnail = $item['pages'][0]['thumbnail']['source'];
            }
            // Split text into name (first part) and description (rest)
            $name = $text;
            $desc = '';
            $comma_pos = mb_strpos($text, ',');
            if ($comma_pos !== false) {
                $name = mb_substr($text, 0, $comma_pos);
                $desc = trim(mb_substr($text, $comma_pos + 1));
            }
            $result[] = [
                'year' => $year,
                'text' => $text,
                'name' => $name,
                'desc' => $desc,
                'link' => $wiki_link,
                'image' => $thumbnail
            ];
        }
        return $result;
    }

    // Load ALL births and deaths (up to 30), no events panel
    $births = process_wiki_data_with_media($res_data['births'] ?? [], 10, false);
    $deaths = process_wiki_data_with_media($res_data['deaths'] ?? [], 10, false);
}

// === STATISTICS ===
$all_years = [];
foreach (array_merge($births, $deaths) as $item) {
    if (!empty($item['year']) && is_numeric($item['year'])) {
        $all_years[] = intval($item['year']);
    }
}


// === YEAR SUFFIX LABEL ===
$yr_suffix = $ai_lang === 'en' ? 'yr' : 'წ';
?>
<style>
/* ========== ON THIS DAY – PREMIUM STYLES ========== */

/* --- Cosmic Hero Sphere --- */
.otd-hero {
    position: relative;
    text-align: center;
    padding: 2.5rem 1rem 2rem;
    overflow: hidden;
    border-radius: 28px;
    background: radial-gradient(ellipse at 30% 20%, rgba(13, 202, 240, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse at 70% 80%, rgba(255, 71, 87, 0.06) 0%, transparent 60%),
                rgba(20, 25, 35, 0.5);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    margin-bottom: 1.5rem;
}
.otd-hero::before {
    content: '';
    position: absolute;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(13, 202, 240, 0.08) 0%, transparent 70%);
    top: -80px;
    right: -60px;
    pointer-events: none;
    animation: otd-pulse 6s ease-in-out infinite;
}
.otd-hero::after {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 71, 87, 0.06) 0%, transparent 70%);
    bottom: -60px;
    left: -40px;
    pointer-events: none;
    animation: otd-pulse 8s ease-in-out infinite reverse;
}
@keyframes otd-pulse {
    0%, 100% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.2); opacity: 1; }
}
#otd-month, #otd-day {
    /* მომრგვალებული კუთხეები */
    border-radius: 15px;
    
    /* Liquid Glass ეფექტი */
    background: rgba(255, 255, 255, 0.05); /* ნახევრად გამჭვირვალე თეთრი ფონი */
    backdrop-filter: blur(10px);          /* მინის ეფექტის ბუნდოვნება */
    -webkit-backdrop-filter: blur(10px);  /* Safari-სთვის */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    /* საზღვრები და დაშორებები */
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 8px 15px;
    color: #fff;                          /* ტექსტის ფერი */
    outline: none;
    cursor: pointer;
    transition: all 0.3s ease;
}
/* ეს შეცვლის ჩამოსაშლელი სიის ფონს */
select option {
    background-color: #1a1a2e; /* მუქი ლურჯი/შავი */
    color: #ffffff;
    padding: 10px;
}
/* ჰოვერ ეფექტი (როცა მაუსს მიიტან) */
#otd-month:hover, #otd-day:hover {
    background: rgba(255, 255, 255, 0.10);
}
/* --- Date Orb --- */
.otd-date-orb {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.15), rgba(255, 71, 87, 0.1));
    border: 2px solid rgba(13, 202, 240, 0.2);
    box-shadow: 0 0 40px rgba(13, 202, 240, 0.1), inset 0 0 40px rgba(13, 202, 240, 0.05);
    margin: 0 auto 0.75rem;
    position: relative;
    transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.otd-date-orb:hover {
    transform: scale(1.08) rotate(4deg);
    border-color: rgba(13, 202, 240, 0.4);
}
.otd-date-orb .day-num {
    font-size: 2.2rem;
    font-weight: 900;
    color: #fff;
    line-height: 1;
    font-family: 'Poppins', sans-serif;
    text-shadow: 0 2px 20px rgba(13, 202, 240, 0.3);
}
.otd-date-orb .month-name {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.7);
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-top: 2px;
}
.otd-date-orb .orb-ring {
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    border: 1px solid rgba(13, 202, 240, 0.1);
    animation: otd-orb-spin 12s linear infinite;
    pointer-events: none;
}
.otd-date-orb .orb-ring-2 {
    position: absolute;
    inset: -12px;
    border-radius: 50%;
    border: 1px dashed rgba(255, 71, 87, 0.08);
    animation: otd-orb-spin 20s linear infinite reverse;
    pointer-events: none;
}
@keyframes otd-orb-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* --- Hero metadata --- */
.otd-hero-title {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.5);
    letter-spacing: 3px;
    text-transform: uppercase;
    font-weight: 500;
    margin: 0;
}
.otd-hero-weekday {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.35);
    margin-top: 4px;
}


/* --- Nav tabs premium (only 2 tabs: births, deaths) --- */
.otd-tabs {
    display: flex;
    gap: 6px;
    padding: 4px;
    background: rgba(255,255,255,0.02);
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.04);
    margin-bottom: 1.2rem;
    flex-wrap: wrap;
}
.otd-tab-btn {
    flex: 1;
    min-width: 100px;
    padding: 8px 12px;
    border: none;
    border-radius: 12px;
    background: transparent;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    position: relative;
}
/* Births tab – always green tint, active = vibrant */
.otd-tab-btn[data-tab="births"] {
    color: rgba(46, 204, 113, 0.45);
}
.otd-tab-btn[data-tab="births"]:hover {
    color: #2ecc71;
    background: rgba(46, 204, 113, 0.06);
}
.otd-tab-btn[data-tab="births"].active {
    color: #2ecc71;
    background: rgba(46, 204, 113, 0.15);
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.12);
}
/* Deaths tab – always purple tint, active = vibrant */
.otd-tab-btn[data-tab="deaths"] {
    color: rgba(187, 143, 206, 0.45);
}
.otd-tab-btn[data-tab="deaths"]:hover {
    color: #bb8fce;
    background: rgba(155, 89, 182, 0.06);
}
.otd-tab-btn[data-tab="deaths"].active {
    color: #bb8fce;
    background: rgba(155, 89, 182, 0.15);
    box-shadow: 0 4px 15px rgba(155, 89, 182, 0.12);
}
.otd-tab-btn i { font-size: 0.9rem; }
.otd-tab-count {
    font-size: 0.6rem;
    background: rgba(255,255,255,0.06);
    padding: 1px 7px;
    border-radius: 10px;
    margin-left: 2px;
}

/* --- Content panels --- */
.otd-panel {
    display: none;
    animation: otd-fade-up 0.4s ease;
}
.otd-panel.active { display: block; }
@keyframes otd-fade-up {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* --- List item cards --- */
.otd-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 12px;
    margin-bottom: 6px;
    border-radius: 14px;
    background: rgba(255,255,255,0.01);
    border: 1px solid rgba(255,255,255,0.03);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.otd-item:hover {
    background: rgba(255,255,255,0.04);
    border-color: rgba(255,255,255,0.08);
    transform: translateX(4px);
}
.otd-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    border-radius: 0 3px 3px 0;
    opacity: 0;
    transition: opacity 0.3s;
}
.otd-item:hover::before { opacity: 1; }
.otd-item[data-category="births"]::before { background: #2ecc71; }
.otd-item[data-category="deaths"]::before { background: #bb8fce; }

.otd-year-badge {
    flex-shrink: 0;
    min-width: 58px;
    padding: 5px 8px;
    border-radius: 10px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.06);
    text-align: center;
    font-size: 0.8rem;
    font-weight: 700;
    color: rgba(255,255,255,0.8);
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s;
}
.otd-item[data-category="births"]:hover .otd-year-badge {
    background: rgba(46, 204, 113, 0.1);
    border-color: rgba(46, 204, 113, 0.2);
    color: #2ecc71;
}
.otd-item[data-category="deaths"]:hover .otd-year-badge {
    background: rgba(155, 89, 182, 0.1);
    border-color: rgba(155, 89, 182, 0.2);
    color: #bb8fce;
}

/* Year suffix label – inherits font/size from parent badge, just slightly muted */
.otd-year-suffix {
    font-family: inherit;
    font-size: inherit;
    font-weight: inherit;
    color: rgba(218, 213, 213, 0.85);
    margin-left: 2px;
}

.otd-item-img {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid rgba(255,255,255,0.06);
    background: rgba(0,0,0,0.3);
    transition: transform 0.3s;
}
.otd-item:hover .otd-item-img { transform: scale(1.05); }

.otd-item-text {
    flex: 1;
    min-width: 0;
}
.otd-item-name {
    margin: 0;
    font-weight: 700;
    color: #fff;
    font-size: 0.85rem;
    line-height: 1.3;
}
.otd-item-desc {
    margin: 2px 0 0 0;
    color: rgba(255,255,255,0.55);
    font-size: 0.78rem;
    line-height: 1.4;
}
.otd-item-link {
    font-size: 0.7rem;
    color: rgba(13, 202, 240, 0.6);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    transition: color 0.2s;
}
.otd-item-link:hover {
    color: #0dcaf0;
    text-decoration: underline;
}

/* --- Date Picker --- */
.otd-picker-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.04);
    border-radius: 14px;
    padding: 6px 10px 6px 14px;
    margin-bottom: 1.5rem;
}
.otd-picker-wrap i {
    color: rgba(255,255,255,0.3);
    font-size: 0.85rem;
}
.otd-picker-wrap select {
    background: transparent;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
    padding: 4px 2px;
    cursor: pointer;
    -webkit-appearance: none;
    appearance: none;
    flex: 1;
}
.otd-picker-wrap select:focus {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}
.otd-picker-wrap select option {
    background: #1a1d27;
    color: #fff;
}
.otd-picker-go {
    padding: 5px 12px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #4285f4, #9b51e0) !important;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    white-space: nowrap;
    animation: otd-search-pulse 2s ease-in-out infinite;
    display: flex;
    align-items: center;
    gap: 4px;
}
.otd-picker-go:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 20px rgba(66, 133, 244, 0.4);
}
.otd-picker-go:active {
    transform: scale(0.95);
}
@keyframes otd-search-pulse {
    0%, 100% { box-shadow: 0 4px 15px rgba(66, 133, 244, 0.25); }
    50% { box-shadow: 0 4px 25px rgba(66, 133, 244, 0.5); }
}

/* --- Empty state --- */
.otd-empty {
    text-align: center;
    padding: 3rem 1rem;
}
.otd-empty-icon {
    font-size: 3rem;
    color: rgba(255,255,255,0.05);
    margin-bottom: 1rem;
}
.otd-empty-text {
    color: rgba(255,255,255,0.3);
    font-size: 0.9rem;
}

/* --- Attribution --- */
.otd-attribution {
    text-align: center;
    padding: 1.5rem 0 0.5rem;
    font-size: 0.7rem;
    color: rgba(255,255,255,0.15);
    letter-spacing: 0.5px;
}
.otd-attribution i { margin-right: 4px; }

/* --- Responsive --- */
@media (max-width: 576px) {
    .otd-date-orb { width: 90px; height: 90px; }
    .otd-date-orb .day-num { font-size: 1.8rem; }
    .otd-date-orb .month-name { font-size: 0.65rem; }
    .otd-hero { padding: 1.8rem 0.8rem 1.5rem; }
    .otd-hero-title { font-size: 0.9rem; letter-spacing: 2px; }
    .otd-stats { flex-wrap: wrap; }
    .otd-stat-item { min-width: 50%; border-bottom: 1px solid rgba(255,255,255,0.03); }
    .otd-stat-item:nth-child(2) { border-right: none; }
    .otd-stat-num { font-size: 1rem; }
    .otd-tab-btn { min-width: 80px; font-size: 0.72rem; padding: 6px 8px; }
    .otd-item { padding: 8px 10px; }
    .otd-year-badge { min-width: 48px; font-size: 0.7rem; padding: 4px 6px; }
    .otd-item-img { width: 40px; height: 40px; }
    .otd-item-name { font-size: 0.8rem; }
    .otd-item-desc { font-size: 0.72rem; }
    .otd-picker-wrap { flex-wrap: wrap; }
}
</style>

<div class="container mt-4 mb-5 px-3">


    <!-- ===== HERO ===== -->
    <div class="otd-hero reveal-up">
        <div class="otd-date-orb">
            <div class="orb-ring"></div>
            <div class="orb-ring-2"></div>
            <span class="day-num"><?php echo intval($d); ?></span>
            <span class="month-name"><?php echo $geo_months[intval($m) - 1]; ?></span>
        </div>
        <p class="otd-hero-title"><?php echo $ai_lang === 'en' ? 'Born & Died On This Day' : 'დღეს დაიბადნენ და გარდაიცვალნენ'; ?></p>
        <p class="otd-hero-weekday">
            <i class="fa-regular fa-calendar me-1"></i><?php echo htmlspecialchars($weekday); ?>
            &nbsp;·&nbsp; <?php echo htmlspecialchars($date_formatted); ?>
        </p>
    </div>

    <?php if (empty($births) && empty($deaths)): ?>
        <div class="otd-empty reveal-up">
            <div class="otd-empty-icon"><i class="fa-regular fa-clock"></i></div>
            <p class="otd-empty-text">
                <?php echo $ai_lang === 'en'
                    ? 'No historical records found for this date.'
                    : 'ისტორიას არაფერი ახსოვს ამ თარიღის შესახებ.'; ?>
            </p>
        </div>
    <?php else: ?>

  
       

        <!-- ===== DATE PICKER ===== -->
        <div class="otd-picker-wrap reveal-up">
            <i class="fa-regular fa-calendar"></i>
            <select id="otd-month">
                <?php foreach ($geo_months as $idx => $mn): $val = sprintf('%02d', $idx + 1); ?>
                    <option value="<?php echo $val; ?>" <?php echo $val === $m ? 'selected' : ''; ?>>
                        <?php echo $ai_lang === 'en' ? date('F', mktime(0,0,0,$idx+1,1)) : $mn; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select id="otd-day">
                <?php for ($i = 1; $i <= 31; $i++): $val = sprintf('%02d', $i); ?>
                    <option value="<?php echo $val; ?>" <?php echo $val === $d ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <button class="otd-picker-go" onclick="goToDate()">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <!-- ===== TABS ===== -->
        <div class="otd-tabs reveal-up">
            <button class="otd-tab-btn active" data-tab="births" onclick="switchOtdTab('births', this)">
                <i class="fa-solid fa-cake-candles"></i>
                <span><?php echo $ai_lang === 'en' ? 'Born' : 'დაიბადნენ'; ?></span>
                <span class="otd-tab-count"><?php echo count($births); ?></span>
            </button>
            <button class="otd-tab-btn" data-tab="deaths" onclick="switchOtdTab('deaths', this)">
                <i class="fa-solid fa-feather-pointed"></i>
                <span><?php echo $ai_lang === 'en' ? 'Passed' : 'გარდაიცვალნენ'; ?></span>
                <span class="otd-tab-count"><?php echo count($deaths); ?></span>
            </button>
        </div>

        <!-- ===== PANEL: BIRTHS ===== -->
        <div class="otd-panel active" id="otd-panel-births">
            <?php if (!empty($births)): ?>
                <?php foreach ($births as $b): ?>
                    <div class="otd-item" data-category="births">
                        <span class="otd-year-badge"><?php echo htmlspecialchars((string)$b['year'], ENT_QUOTES, 'UTF-8'); ?> <span class="otd-year-suffix"><?php echo $yr_suffix; ?></span></span>
                        <?php if (!empty($b['image'])): ?>
                            <img src="<?php echo htmlspecialchars($b['image'], ENT_QUOTES, 'UTF-8'); ?>" class="otd-item-img" alt="" loading="lazy" />
                        <?php endif; ?>
                        <div class="otd-item-text">
                        <p class="otd-item-name">
    <i class="fa-solid fa-star text-success" style="font-size:0.6rem; opacity:0.5; margin-right:4px;"></i>
    <?php echo htmlspecialchars(otd_translate_text($b['name']), ENT_QUOTES, 'UTF-8'); ?>
</p>
<?php if (!empty($b['desc'])): ?>
    <p class="otd-item-desc"><?php echo htmlspecialchars(otd_translate_text($b['desc']), ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
                            <?php if ($b['link'] !== '#'): ?>
                                <a href="<?php echo htmlspecialchars($b['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="otd-item-link">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> <?php echo $ai_lang === 'en' ? 'Biography' : 'ბიოგრაფია'; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="otd-empty"><p class="otd-empty-text"><?php echo $ai_lang === 'en' ? 'No births recorded.' : 'დაბადებული არ არის.'; ?></p></div>
            <?php endif; ?>
        </div>

        <!-- ===== PANEL: DEATHS ===== -->
        <div class="otd-panel" id="otd-panel-deaths">
            <?php if (!empty($deaths)): ?>
                <?php foreach ($deaths as $d_item): ?>
                    <div class="otd-item" data-category="deaths">
                        <span class="otd-year-badge"><?php echo htmlspecialchars((string)$d_item['year'], ENT_QUOTES, 'UTF-8'); ?> <span class="otd-year-suffix"><?php echo $yr_suffix; ?></span></span>
                        <?php if (!empty($d_item['image'])): ?>
                            <img src="<?php echo htmlspecialchars($d_item['image'], ENT_QUOTES, 'UTF-8'); ?>" class="otd-item-img" alt="" loading="lazy" />
                        <?php endif; ?>
                        <div class="otd-item-text">
                           <p class="otd-item-name">
    <i class="fa-solid fa-feather" style="font-size:0.6rem; opacity:0.4; margin-right:4px; color:#bb8fce;"></i>
    <?php echo htmlspecialchars(otd_translate_text($d_item['name']), ENT_QUOTES, 'UTF-8'); ?>
</p>
<?php if (!empty($d_item['desc'])): ?>
    <p class="otd-item-desc"><?php echo htmlspecialchars(otd_translate_text($d_item['desc']), ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
                            <?php if ($d_item['link'] !== '#'): ?>
                                <a href="<?php echo htmlspecialchars($d_item['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="otd-item-link">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> <?php echo $ai_lang === 'en' ? 'Legacy' : 'მემკვიდრეობა'; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="otd-empty"><p class="otd-empty-text"><?php echo $ai_lang === 'en' ? 'No deaths recorded.' : 'გარდაცვლილი არ არის.'; ?></p></div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

    <!-- ===== ATTRIBUTION ===== -->
    <div class="otd-attribution">
        <i class="fa-brands fa-wikipedia-w"></i>
        <?php echo $ai_lang === 'en'
            ? 'Data sourced from Wikipedia under CC BY-SA license.'
            : 'მონაცემები აღებულია ვიკიპედიიდან CC BY-SA ლიცენზიით.'; ?>
    </div>
</div>

<script>
// === Tabs ===
function switchOtdTab(tab, btn) {
    document.querySelectorAll('.otd-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.otd-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('otd-panel-' + tab).classList.add('active');
    btn.classList.add('active');
}

// === Date Picker ===
function goToDate() {
    const m = document.getElementById('otd-month').value;
    const d = document.getElementById('otd-day').value;
    window.location.href = 'onthisday.php?m=' + m + '&d=' + d;
}

// === Reveal-up observer ===
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal-up').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>