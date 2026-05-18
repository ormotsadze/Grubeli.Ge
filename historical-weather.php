<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
require_once __DIR__ . '/functions.php';

// განსაზღვრავს მიმდინარე ენას სესიიდან ან ქუქიდან
$ai_lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'ka';

// Determine lat/lon using unified helper
[$lat, $lon] = resolve_coordinates($_GET['lat'] ?? null, $_GET['lon'] ?? null);

// Date range handling with 80-year limit + single-date mode
$today_utc = new DateTime('now', new DateTimeZone('UTC'));
$max_end = (clone $today_utc)->modify('-1 day');
$min_start = (clone $today_utc)->modify('-80 years');

$single_date = isset($_GET['date']) ? $_GET['date'] : null;
$requested_start = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$requested_end = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// If a single date is provided, use it as both start and end
if ($single_date && !$requested_start && !$requested_end) {
    $requested_start = $single_date;
    $requested_end = $single_date;
    $single_day_mode = true;
} else {
    $single_day_mode = false;
}

// defaults: last 30 days (or just the single day)
if (!$requested_end) $requested_end = $max_end->format('Y-m-d');
if (!$requested_start) $requested_start = (clone $max_end)->modify('-29 days')->format('Y-m-d');

// sanitize and clamp
try {
  $rs = new DateTime($requested_start, new DateTimeZone('UTC'));
  $re = new DateTime($requested_end, new DateTimeZone('UTC'));
} catch (Exception $ex) {
  $rs = (clone $max_end)->modify('-29 days');
  $re = clone $max_end;
  $single_day_mode = false;
}

if ($rs < $min_start) $rs = clone $min_start;
if ($re > $max_end) $re = clone $max_end;
if ($rs > $re) $rs = (clone $re)->modify('-29 days');

$start_date = $rs->format('Y-m-d');
$end_date = $re->format('Y-m-d');

$data = fetch_historical($lat, $lon, $start_date, $end_date);

// Reverse-geocode (cached) და გადათარგმნა, თუ მომხმარებელი ინგლისურ ვერსიაზეა
$placeName = get_location_name($lat, $lon);
if ($ai_lang === 'en') {
    $placeName = translate_place_name($placeName);
}
?>
<style>
.premium-glass {
  background: rgba(20, 25, 35, 0.6);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 24px;
}
.hero-accent { border-bottom: 2px solid rgba(255,255,255,0.07); }
.text-gradient-premium {
  background: linear-gradient(135deg, #ffffff 20%, #8ca4ff 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.ambient-glow {
  position: absolute;
  width: 250px; height: 250px;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.15;
  pointer-events: none;
}
.glow-1 { top: -60px; left: -60px; background: #0dcaf0; }
.glow-2 { bottom: -60px; right: -60px; background: #8a2be2; opacity: 0.12; }
.float-icon {
  display: inline-block;
  animation: floatAnim 3s ease-in-out infinite;
  position: relative;
  z-index: 2;
}
@keyframes floatAnim {
  0%,100% { transform: translateY(0); }
  50%      { transform: translateY(-4px); }
}
.reveal-up {
  animation: revealUp 0.7s cubic-bezier(0.2,0.8,0.2,1) forwards;
  opacity: 0;
  transform: translateY(20px);
}
@keyframes revealUp { to { opacity:1; transform:translateY(0); } }

.history-hero {
  padding: 1rem;
  border-radius: 16px !important;
}
.history-hero .date-input {
  background: rgba(255,255,255,0.06) !important;
  border: 1px solid rgba(255,255,255,0.1) !important;
  border-radius: 8px !important;
  color: #fff !important;
  padding: 6px 8px !important;
  font-size: 0.8rem !important;
  width: 100%;
  min-width: 0;
}
.history-hero .date-input:focus {
  border-color: #0dcaf0 !important;
  outline: none !important;
  box-shadow: 0 0 0 2px rgba(13,202,240,0.15) !important;
}
.history-hero .compact-form {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 12px;
  padding: 10px 12px;
}
.history-hero .btn-sm {
  font-size: 0.7rem !important;
  line-height: 1.2 !important;
  border-radius: 6px !important;
  min-height: 32px !important;
}
@media (max-width: 575.98px) {
  .history-hero {
    padding: 0.75rem !important;
    border-radius: 14px !important;
  }
  .history-hero .compact-form {
    padding: 8px !important;
    border-radius: 10px !important;
  }
  .history-hero .date-input {
    font-size: 0.7rem !important;
    padding: 4px 6px !important;
  }
  .history-hero .btn-sm {
    font-size: 0.65rem !important;
    min-height: 28px !important;
    padding: 2px 6px !important;
  }
}
</style>

<?php
$pageTitle   = __('historical_title');
$pageDesc    = __('historical_desc');
$pageOgTitle = __('historical_og_title');
$pageTwTitle = __('historical_tw_title');
$pageTwDesc  = __('historical_tw_desc');
include 'header.php';
?>

    <div class="container mt-3">

      <div class="premium-glass history-hero shadow-lg mb-3 mt-2 position-relative overflow-hidden reveal-up">
        <p class="mb-2">
          <strong class="text-white"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($placeName, ENT_QUOTES, 'UTF-8'); ?></strong>
          <span class="text-white-50 ms-1" style="font-size:0.75rem;">
            <i class="fa-regular fa-calendar me-1"></i>
            <span class="history-subtitle"><?php echo htmlspecialchars($start_date . ' — ' . $end_date, ENT_QUOTES, 'UTF-8'); ?></span>
          </span>
        </p>

        <div class="compact-form">
          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <label style="font-size:13px;color:rgba(255,255,255,0.5);">
              <i class="fa-regular fa-keyboard me-1"></i> 
              <?php echo ($ai_lang === 'en' ? 'Select Date (80 yrs max)' : 'აირჩიეთ თარიღი (80 წ)'); ?>
            </label>
          </div>
          <form method="get" class="history-form d-flex align-items-center gap-1" role="search" aria-label="<?php echo ($ai_lang === 'en' ? 'Historical Filter' : 'ისტორიული ფილტრი'); ?>">
            <input type="hidden" name="lat" value="<?php echo htmlspecialchars($lat, ENT_QUOTES, 'UTF-8'); ?>" />
            <input type="hidden" name="lon" value="<?php echo htmlspecialchars($lon, ENT_QUOTES, 'UTF-8'); ?>" />
            <input type="hidden" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?>" />
            <input
              id="start_date"
              class="date-input"
              type="date"
              name="start_date"
              value="<?php echo htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8'); ?>"
              max="<?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?>"
              min="<?php echo htmlspecialchars($min_start->format('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
              title="<?php echo ($ai_lang === 'en' ? 'Enter date' : 'შეიყვანეთ თარიღი'); ?>"
            />
            <button type="submit" class="btn btn-danger btn-sm flex-shrink-0 px-2">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <button type="button" id="clearFilter" class="btn btn-dark btn-sm flex-shrink-0 px-2">
              <i class="fa-solid fa-broom"></i>
            </button>
          </form>
        </div>
      </div>

      <?php 
      // ─── Extract daily data early (needed both for card and main display) ───
      $times = $data['daily']['time'] ?? [];
      $temps_max = $data['daily']['temperature_2m_max'] ?? [];
      $temps_min = $data['daily']['temperature_2m_min'] ?? [];
      $icons = $data['daily']['icon'] ?? [];
      $descs = $data['daily']['description_geo'] ?? [];
      $tz = $data['timezone'] ?? 'UTC';
      ?>

      <?php 
      // ─── SINGLE-DAY BEAUTIFUL CARD ───
      if ($single_day_mode && $data && isset($times[0])): 
          $sd_dt = new DateTime($times[0], new DateTimeZone($tz));
          $sd_icon = isset($icons[0]) ? icon_url($icons[0], true) : 'icons/sun.svg';
          $sd_max = isset($temps_max[0]) ? round($temps_max[0]) : '--';
          $sd_min = isset($temps_min[0]) ? round($temps_min[0]) : '--';
          
          // ამინდის დინამიური აღწერის თარგმანი ფუნქციით
          $sd_desc = isset($descs[0]) ? get_weather_description_by_text($descs[0]) : '';

          // კვირისა და თვეების სახელები ენის მიხედვით
          if ($ai_lang === 'en') {
              $geo_days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
              $geo_months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
              $sd_date_geo = $geo_months[intval($sd_dt->format('n')) - 1] . ' ' . intval($sd_dt->format('j')) . ', ' . $sd_dt->format('Y');
          } else {
              $geo_days = ['კვირა','ორშაბათი','სამშაბათი','ოთხშაბათი','ხუთშაბათი','პარასკევი','შაბათი'];
              $geo_months = ['იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი'];
              $sd_date_geo = intval($sd_dt->format('j')) . ' ' . $geo_months[intval($sd_dt->format('n')) - 1] . ' ' . $sd_dt->format('Y');
          }
          
          $sd_day_geo = $geo_days[intval($sd_dt->format('w'))];
      ?>
      <div class="premium-glass p-4 p-md-5 mb-4 position-relative overflow-hidden reveal-up text-center" style="background: linear-gradient(145deg, rgba(20,25,35,0.7) 0%, rgba(30,40,60,0.5) 100%); border: 1px solid rgba(255,255,255,0.06); border-radius: 28px;">
          <div class="ambient-glow" style="position:absolute; width:300px; height:300px; top:-80px; right:-80px; background:rgba(13,202,240,0.12); filter:blur(100px); pointer-events:none;"></div>
          <div class="ambient-glow" style="position:absolute; width:200px; height:200px; bottom:-60px; left:-60px; background:rgba(138,43,226,0.08); filter:blur(80px); pointer-events:none;"></div>
          
          <div class="position-relative" style="z-index:1;">
              <div class="d-inline-block px-3 py-1 mb-3 rounded-pill" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); font-size:0.75rem; color:rgba(255,255,255,0.6); letter-spacing:0.5px;">
                  <i class="fa-regular fa-clock me-1"></i> 
                  <?php echo ($ai_lang === 'en' ? 'On this day in history' : 'შარშან ამ დღეს'); ?>
              </div>
              
              <h2 class="fw-bold text-white mb-0" style="font-family:'BPG NinoMtavruli', sans-serif; font-size:1.6rem; text-shadow:0 2px 8px rgba(0,0,0,0.3);">
                  <?php echo htmlspecialchars($sd_date_geo, ENT_QUOTES, 'UTF-8'); ?>
              </h2>
              <p class="text-white-50 mb-4" style="font-size:0.85rem;">
                  <i class="fa-regular fa-calendar-days me-1"></i> <?php echo htmlspecialchars($sd_day_geo, ENT_QUOTES, 'UTF-8'); ?>
              </p>
              
              <div class="d-flex flex-column align-items-center justify-content-center gap-3 mb-3">
                  <img src="<?php echo htmlspecialchars($sd_icon, ENT_QUOTES, 'UTF-8'); ?>" 
                       alt="<?php echo ($ai_lang === 'en' ? 'Weather' : 'ამინდი'); ?>" 
                       style="width:90px; height:90px; object-fit:contain; filter:drop-shadow(0 8px 20px rgba(0,0,0,0.25));" 
                       class="float-icon" />
                  <div class="text-center">
                      <div class="d-flex align-items-baseline justify-content-center gap-4">
                          <div>
                              <small class="text-white-50 d-block mb-1" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px;"><?php echo ($ai_lang === 'en' ? 'Max' : 'მაქს'); ?></small>
                              <span class="fw-bold text-white" style="font-size:2.4rem; line-height:1; text-shadow:0 3px 10px rgba(0,0,0,0.2);">
                                  <?php echo htmlspecialchars($sd_max, ENT_QUOTES, 'UTF-8'); ?>
                              </span>
                              <span class="text-white-50" style="font-size:1.2rem;">°C</span>
                          </div>
                          <div class="px-3" style="border-left:1px solid rgba(255,255,255,0.12);">
                              <small class="text-white-50 d-block mb-1" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px;"><?php echo ($ai_lang === 'en' ? 'Min' : 'მინ'); ?></small>
                              <span class="fw-bold text-white" style="font-size:2.4rem; line-height:1; text-shadow:0 3px 10px rgba(0,0,0,0.2);">
                                  <?php echo htmlspecialchars($sd_min, ENT_QUOTES, 'UTF-8'); ?>
                              </span>
                              <span class="text-white-50" style="font-size:1.2rem;">°C</span>
                          </div>
                      </div>
                  </div>
              </div>
              
              <div class="d-inline-block px-4 py-2 rounded-pill" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.06); font-family:'BPG NinoMtavruli', sans-serif;">
                  <i class="fa-regular fa-cloud me-1 text-info"></i>
                  <?php echo htmlspecialchars($sd_desc, ENT_QUOTES, 'UTF-8'); ?>
              </div>
          </div>
      </div>
      <?php endif; ?>

      <?php if (!$data): ?>
        <div class="alert alert-warning"><?php echo ($ai_lang === 'en' ? 'Failed to fetch historical data.' : 'ისტორიული მონაცემების მიღება ვერ მოხერხდა.'); ?></div>
      <?php elseif (!$single_day_mode): ?>
        <?php
        $labels = [];
        $maxs = [];
        $mins = [];
        for ($i = 0; $i < count($times); $i++) {
          $labels[] = (new DateTime($times[$i], new DateTimeZone($tz)))->format('Y-m-d');
          $maxs[] = isset($temps_max[$i]) ? round($temps_max[$i], 1) : null;
          $mins[] = isset($temps_min[$i]) ? round($temps_min[$i], 1) : null;
        }
        ?>

        <div class="chart-card mb-3">
          <div style="color:#fff;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center;">
            <div style="font-size:14px"><strong><?php echo ($ai_lang === 'en' ? 'Showing' : 'ნაჩვენებია'); ?></strong>: <?php echo htmlspecialchars($start_date . ' - ' . $end_date, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="chart-legend">
                <span style="color:#ff8a65">● <?php echo ($ai_lang === 'en' ? 'Max' : 'მაქს'); ?></span> 
                <span style="margin-left:8px;color:#4fc3f7">● <?php echo ($ai_lang === 'en' ? 'Min' : 'მინ'); ?></span>
            </div>
          </div>
          <canvas id="historyChart" height="120"></canvas>
        </div>

        <div class="row">
          <?php for ($i = 0; $i < count($times); $i++):
            $dt = new DateTime($times[$i], new DateTimeZone($tz));
            $icon = isset($icons[$i]) ? icon_url($icons[$i], true) : 'icons/sun.svg';
            $dmax = isset($temps_max[$i]) ? round($temps_max[$i]) : '--';
            $dmin = isset($temps_min[$i]) ? round($temps_min[$i]) : '--';
            
            // ამინდის აღწერის დინამიური თარგმანი
            $desc = isset($descs[$i]) ? get_weather_description_by_text($descs[$i]) : '';
          ?>
          <div class="col-6 col-sm-4 col-md-3 mb-3">
            <div class="card bg-dark text-white">
              <div class="card-body text-center">
                <div><?php echo $dt->format('d.m.Y'); ?></div>
                <img src="<?php echo htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?>" style="width:56px;height:56px;" alt="Weather icon" />
                <div style="font-size:16px;"><?php echo htmlspecialchars($dmax, ENT_QUOTES, 'UTF-8'); ?>&deg; / <?php echo htmlspecialchars($dmin, ENT_QUOTES, 'UTF-8'); ?>&deg;</div>
                <div style="font-size:12px;color:#ddd;"><?php echo htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'); ?></div>
              </div>
            </div>
          </div>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>

    <script>
    (function(){
      const startEl = document.getElementById('start_date');
      let endEl = document.getElementById('end_date');
      const clearBtn = document.getElementById('clearFilter');
      const subtitleEl = document.querySelector('.history-subtitle');
      if (!startEl) return;

      if (!endEl) {
        endEl = document.createElement('input');
        endEl.type = 'hidden';
        endEl.name = 'end_date';
        endEl.id = 'end_date';
        endEl.value = '<?php echo htmlspecialchars($end_date); ?>';
        const form = document.querySelector('.history-form');
        if (form) form.appendChild(endEl);
      }

      endEl.dataset.manual = endEl.dataset.manual || '0';
      endEl.addEventListener('input', function(){ endEl.dataset.manual = '1'; });
      endEl.addEventListener('change', function(){ endEl.dataset.manual = '1'; });

      function clampToMax(date) {
        const maxD = new Date('<?php echo $max_end->format('Y-m-d'); ?>T00:00:00Z');
        return date > maxD ? maxD : date;
      }

      function updateSubtitle(startVal, endVal) {
        if (!subtitleEl) return;
        subtitleEl.innerHTML = (startVal || '') + (startVal || endVal ? ' — ' : '') + (endVal || '');
      }

      startEl.addEventListener('change', function(){
        if (endEl.dataset.manual === '1') { updateSubtitle(startEl.value, endEl.value); return; }
        const s = new Date(startEl.value + 'T00:00:00Z');
        if (isNaN(s)) { updateSubtitle('', ''); return; }
        const newEnd = new Date(s);
        newEnd.setMonth(newEnd.getMonth() + 1);
        newEnd.setDate(newEnd.getDate() - 1);
        const clamped = clampToMax(newEnd);
        const y = clamped.getUTCFullYear();
        const m = String(clamped.getUTCMonth() + 1).padStart(2,'0');
        const d = String(clamped.getUTCDate()).padStart(2,'0');
        endEl.value = `${y}-${m}-${d}`;
        updateSubtitle(startEl.value, endEl.value);
      });

      if (clearBtn) {
        clearBtn.addEventListener('click', function(){
          startEl.value = '';
          endEl.value = '';
          endEl.dataset.manual = '1';
          updateSubtitle('', '');
          startEl.focus();
        });
      }

      try {
        const params = new URLSearchParams(window.location.search);
        if (!params.has('end_date')) {
          startEl.dispatchEvent(new Event('change'));
        } else {
          updateSubtitle(startEl.value, endEl.value);
        }
      } catch (e) {}
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    const labels = <?php echo json_encode($labels ?? []); ?>;
    const maxs = <?php echo json_encode($maxs ?? []); ?>;
    const mins = <?php echo json_encode($mins ?? []); ?>;

    (function(){
      const ctx = document.getElementById('historyChart');
      if (!ctx) return;
      const gradientMax = ctx.getContext('2d').createLinearGradient(0,0,0,200);
      gradientMax.addColorStop(0, 'rgba(255,138,101,0.28)');
      gradientMax.addColorStop(1, 'rgba(255,138,101,0.03)');

      const gradientMin = ctx.getContext('2d').createLinearGradient(0,0,0,200);
      gradientMin.addColorStop(0, 'rgba(79,195,247,0.22)');
      gradientMin.addColorStop(1, 'rgba(79,195,247,0.03)');

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: '<?php echo ($ai_lang === 'en' ? 'Max' : 'მაქს'); ?>',
              data: maxs,
              backgroundColor: gradientMax,
              borderColor: '#ff8a65',
              pointRadius: 2,
              tension: 0.25,
              fill: true
            },
            {
              label: '<?php echo ($ai_lang === 'en' ? 'Min' : 'მინ'); ?>',
              data: mins,
              backgroundColor: gradientMin,
              borderColor: '#4fc3f7',
              pointRadius: 2,
              tension: 0.25,
              fill: true
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { display: true, grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#ddd' } },
            y: { display: true, grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#ddd' } }
          },
          plugins: {
            legend: { labels: { color: '#ddd' } },
            tooltip: { mode: 'index', intersect: false }
          },
        }
      });
    })();
    </script>
  <?php require_once __DIR__ . '/footer.php'; ?>
  </body>
</html>