<?php
require_once 'functions.php';
$ai_lang = get_current_lang();
$pageTitle   = __('onthisday_title');
$pageDesc    = __('onthisday_desc');
$pageOgTitle = __('onthisday_og_title');
$pageTwTitle = __('onthisday_tw_title');
$pageTwDesc  = __('onthisday_tw_desc');
include 'header.php';


$today_utc = new DateTime('now', new DateTimeZone('UTC'));
$m = $today_utc->format('m');
$d = $today_utc->format('d');

// ქართული თვეების მასივი ვიზუალური სათაურისთვის
$geo_months = ['იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი'];
$date_title = ($ai_lang === 'en') 
    ? $today_utc->format('F d') 
    : intval($d) . ' ' . $geo_months[intval($m) - 1];

// ვიკიპედიის On This Day API
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
            
            // ვიღებთ ძირითადი სტატიის ბმულს
            $wiki_link = '#';
            if (!empty($item['pages'][0]['content_urls']['desktop']['page'])) {
                $wiki_link = $item['pages'][0]['content_urls']['desktop']['page'];
            }
            
            // ვიღებთ სურათს, თუ ის ხელმისაწვდომია
            $thumbnail = null;
            if (!empty($item['pages'][0]['thumbnail']['source'])) {
                $thumbnail = $item['pages'][0]['thumbnail']['source'];
            }

            $result[] = [
                'year' => $year,
                'text' => $text,
                'link' => $wiki_link,
                'image' => $thumbnail
            ];
        }
        return $result;
    }


    $events = process_wiki_data_with_media($res_data['events'] ?? [], 15, true);
    $births = process_wiki_data_with_media($res_data['births'] ?? [], 8, false);
    $deaths = process_wiki_data_with_media($res_data['deaths'] ?? [], 8, false);
}
?>

<style>
.premium-glass {
    background: rgba(20, 25, 35, 0.55);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
}
.nav-pills .nav-link {
    color: rgba(255, 255, 255, 0.6);
    border-radius: 10px;
    font-size: 0.85rem;
    padding: 6px 10px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}
.nav-pills .nav-link.active, .nav-pills .show>.nav-link {
    color: #fff;
    background: #ff4757;
    border-color: #ff4757;
    box-shadow: 0 4px 12px rgba(255, 71, 87, 0.2);
}
.wiki-link {
    font-size: 0.75rem;
    color: #0dcaf0;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 5px;
    transition: color 0.2s ease;
}
.wiki-link:hover {
    color: #00a8cc;
    text-decoration: underline;
}
.timeline-item {
    border-left: 2px solid rgba(13, 202, 240, 0.25);
    position: relative;
}
.timeline-marker {
    position: absolute;
    left: -7px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #0dcaf0;
    border: 2px solid #141923;
    box-shadow: 0 0 8px rgba(13, 202, 240, 0.6);
}
.event-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.2);
}
</style>

<div class="container mt-4 mb-5 px-3">
    
    <div class="premium-glass p-3 mb-4 text-center reveal-up">
        <h4 class="m-0 text-white fw-bold" style="font-family: '<?php echo __('font_family'); ?>'">
            <i class="fa-solid fa-calendar-day text-danger me-2"></i>
            <?php echo htmlspecialchars($date_title, ENT_QUOTES, 'UTF-8'); ?> - <?php echo ($ai_lang === 'en' ? 'In History' : 'ისტორიაში'); ?>
        </h4>
    </div>

    <?php if (empty($events) && empty($births)): ?>
        <div class="alert alert-warning text-center premium-glass border-0 text-white">
            <?php echo ($ai_lang === 'en' ? 'No historical data found for today.' : 'დღევანდელი დღის ისტორიული მონაცემები ვერ მოიძებნა.'); ?>
        </div>
    <?php else: ?>
        
        <div class="premium-glass p-3 mb-4 reveal-up">
            <ul class="nav nav-pills justify-content-center gap-2 mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-births-tab" data-bs-toggle="pill" data-bs-target="#pills-births" type="button" role="tab" aria-controls="pills-births" aria-selected="true">
                        <i class="fa-solid fa-cake-candles me-1"></i> <?php echo ($ai_lang === 'en' ? 'Born Today' : 'დაიბადნენ'); ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-deaths-tab" data-bs-toggle="pill" data-bs-target="#pills-deaths" type="button" role="tab" aria-controls="pills-deaths" aria-selected="false">
                        <i class="fa-solid fa-monument me-1"></i> <?php echo ($ai_lang === 'en' ? 'Died Today' : 'გარდაიცვალნენ'); ?>
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-births" role="tabpanel" aria-labelledby="pills-births-tab">
                    <?php if (!empty($births)): foreach ($births as $b): ?>
                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-light-subtle" style="border-color: rgba(255,255,255,0.05) !important;">
                            <span class="badge bg-dark text-white me-3 py-1 px-2 flex-shrink-0" style="font-size:0.75rem; min-width:55px; text-align:center;">
                                <?php echo htmlspecialchars((string)$b['year'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <?php if (!empty($b['image'])): ?>
                                <img src="<?php echo htmlspecialchars($b['image'], ENT_QUOTES, 'UTF-8'); ?>" class="event-thumb me-3" alt="Thumbnail" loading="lazy" />
                            <?php endif; ?>
                            <div class="w-100">
                                <p class="m-0 text-white small" style="line-height:1.4;">
                                    <?php echo htmlspecialchars($b['text'], ENT_QUOTES, 'UTF-8'); ?><br>
                                    <?php if ($b['link'] !== '#'): ?>
                                       <a href="<?php echo htmlspecialchars($b['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="wiki-link">
            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:10px;"></i> 
            <?php echo ($ai_lang === 'en' ? 'Read Article' : 'სტატია სრულად'); ?>
        </a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="text-white-50 text-center small my-2"><?php echo ($ai_lang === 'en' ? 'No data' : 'მონაცემები არ არის'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="pills-deaths" role="tabpanel" aria-labelledby="pills-deaths-tab">
                    <?php if (!empty($deaths)): foreach ($deaths as $d): ?>
                        <div class="d-flex align-items-center mb-2 pb-2 border-bottom border-light-subtle" style="border-color: rgba(255,255,255,0.05) !important;">
                            <span class="badge bg-dark text-white me-3 py-1 px-2 flex-shrink-0" style="font-size:0.75rem; min-width:55px; text-align:center; border: 1px solid rgba(255,255,255,0.1); ">
                                <?php echo htmlspecialchars((string)$d['year'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <?php if (!empty($d['image'])): ?>
                                <img src="<?php echo htmlspecialchars($d['image'], ENT_QUOTES, 'UTF-8'); ?>" class="event-thumb me-3" alt="Thumbnail" loading="lazy" />
                            <?php endif; ?>
                            <div class="w-100">
                                <p class="m-0 text-white small" style="line-height:1.4;">
                                    <?php echo htmlspecialchars($d['text'], ENT_QUOTES, 'UTF-8'); ?><br>
                                    <?php if ($d['link'] !== '#'): ?>
                                        <a href="<?php echo htmlspecialchars($d['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="wiki-link"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size:10px;"></i> Read Article</a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="text-white-50 text-center small my-2"><?php echo ($ai_lang === 'en' ? 'No data' : 'მონაცემები არ არის'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="premium-glass p-3 reveal-up">
            <h5 class="text-white fw-bold mb-3" style="font-family: 'BPG NinoMtavruli', sans-serif; font-size: 1rem;">
                <i class="fa-solid fa-timeline text-info me-2"></i>
                <?php echo ($ai_lang === 'en' ? 'Historical Events' : 'მნიშვნელოვანი მოვლენები'); ?>
            </h5>
            
            <div class="ps-2">
                <?php foreach ($events as $ev): ?>
                    <div class="timeline-item ps-4 pb-3">
                        <div class="timeline-marker"></div>
                        <div class="d-flex align-items-start gap-3">
                            <?php if (!empty($ev['image'])): ?>
                                <img src="<?php echo htmlspecialchars($ev['image'], ENT_QUOTES, 'UTF-8'); ?>" class="event-thumb mt-1" alt="Event Image" loading="lazy" />
                            <?php endif; ?>
                            <div>
                                <h6 class="text-info fw-bold m-0 small"><?php echo htmlspecialchars((string)$ev['year'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                <p class="m-0 text-white small mt-1" style="line-height:1.4;">
                                    <?php echo htmlspecialchars($ev['text'], ENT_QUOTES, 'UTF-8'); ?><br>
                                    <?php if ($ev['link'] !== '#'): ?>
                                   <a href="<?php echo htmlspecialchars($ev['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="wiki-link">
            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:10px;"></i> 
            <?php echo ($ai_lang === 'en' ? 'Read Full Article' : 'სტატია სრულად'); ?>
        </a>
                                        <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php endif; ?>
    <div class="text-center mt-4 " style="opacity: 0.5; font-size: 0.75rem; color: #fff;">
    <i class="fa-brands fa-wikipedia-w me-1"></i>
    <?php echo ($ai_lang === 'en' 
        ? 'Data sourced from Wikipedia under CC BY-SA license.' 
        : 'მონაცემები აღებულია ვიკიპედიიდან CC BY-SA ლიცენზიით.'); ?>
</div>
</div>


<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>
