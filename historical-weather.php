<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
require_once __DIR__ . '/functions.php';

// Determine lat/lon using unified helper
[$lat, $lon] = resolve_coordinates($_GET['lat'] ?? null, $_GET['lon'] ?? null);

// Date range handling with 80-year limit
$today_utc = new DateTime('now', new DateTimeZone('UTC'));
$max_end = (clone $today_utc)->modify('-1 day');
$min_start = (clone $today_utc)->modify('-80 years');

$requested_start = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$requested_end = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// defaults: last 30 days
if (!$requested_end) $requested_end = $max_end->format('Y-m-d');
if (!$requested_start) $requested_start = (clone $max_end)->modify('-29 days')->format('Y-m-d');

// sanitize and clamp
try {
  $rs = new DateTime($requested_start, new DateTimeZone('UTC'));
  $re = new DateTime($requested_end, new DateTimeZone('UTC'));
} catch (Exception $ex) {
  $rs = (clone $max_end)->modify('-29 days');
  $re = clone $max_end;
}

if ($rs < $min_start) $rs = clone $min_start;
if ($re > $max_end) $re = clone $max_end;
if ($rs > $re) $rs = (clone $re)->modify('-29 days');

$start_date = $rs->format('Y-m-d');
$end_date = $re->format('Y-m-d');

$data = fetch_historical($lat, $lon, $start_date, $end_date);

// Reverse-geocode (cached)
$placeName = get_location_name($lat, $lon);
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
</style>

<?php
$pageTitle = "ისტორიული მონაცემები";
$pageDesc = "იხილეთ ამინდის ისტორიული მონაცემები და სტატისტიკა საქართველოში. Grubeli.ge-ს არქივი საშუალებას გაძლევთ გაიგოთ, როგორი ამინდი იყო წარსულში თქვენს ქალაქში.";
include 'header.php';
?>

    <div class="container mt-3">

      <div class="premium-glass p-5 shadow-lg mb-4 mt-4 position-relative overflow-hidden reveal-up hero-accent">
        <div class="ambient-glow glow-1"></div>
        <div class="ambient-glow glow-2"></div>

       <div class="row position-relative z-index-1">
          <div class="col-lg-6 mb-4 mb-lg-0">
         
<div class="align-items-center justify-content-start mb-4 reveal-up">
    <i class="fa-solid fa-cloud-arrow-down me-3 text-info float-icon" style="font-size: 1rem;"></i>
    
   <span class="fw-bolder text-gradient-premium m-0 p-0" style="font-size:1.75rem;">
      არქივი (80 წ)
    </span>
</div>
            <p class="mb-0" style="color:rgba(255,255,255,0.7);">
              <i class="fa-solid fa-location-dot me-1 text-info"></i>
              <strong class="text-white"><?php echo htmlspecialchars($placeName, ENT_QUOTES, 'UTF-8'); ?></strong>
              &nbsp;·&nbsp;
              <i class="fa-regular fa-calendar me-1"></i>
              <span class="history-subtitle"><?php echo htmlspecialchars($start_date . ' — ' . $end_date, ENT_QUOTES, 'UTF-8'); ?></span>
            </p>
          </div>

          <div class="col-lg-6">
            <form method="get" class="history-form" role="search" aria-label="ისტორიული ფილტრი">
              <input type="hidden" name="lat" value="<?php echo htmlspecialchars($lat, ENT_QUOTES, 'UTF-8'); ?>" />
              <input type="hidden" name="lon" value="<?php echo htmlspecialchars($lon, ENT_QUOTES, 'UTF-8'); ?>" />
              <input type="hidden" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?>" />

              <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px;">
                <label style="display:block;font-size:13px;color:rgba(255,255,255,0.6);margin-bottom:8px;">
                  <i class="fa-regular fa-keyboard me-1"></i> აირჩიეთ თარიღი
                  <span style="color:rgba(255,255,255,0.35);">(მაქს. 80 წ)</span>
                </label>
                <input
                  id="start_date"
                  class="date-input w-100 mb-3"
                  type="date"
                  name="start_date"
                  value="<?php echo htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8'); ?>"
                  max="<?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?>"
                  min="<?php echo htmlspecialchars($min_start->format('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                  title="შეიყვანეთ თარიღი"
                />
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-danger flex-fill">
                    <i class="fa-solid fa-magnifying-glass"></i> ძიება
                  </button>
                  <button type="button" id="clearFilter" class="btn btn-dark">
                    <i class="fa-solid fa-broom"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <?php if (!$data): ?>
        <div class="alert alert-warning">ისტორიული მონაცემების მიღება ვერ მოხერხდა.</div>
      <?php else: ?>
        <?php
        $times = $data['daily']['time'] ?? [];
        $temps_max = $data['daily']['temperature_2m_max'] ?? [];
        $temps_min = $data['daily']['temperature_2m_min'] ?? [];
        $icons = $data['daily']['icon'] ?? [];
        $descs = $data['daily']['description_geo'] ?? [];
        $tz = $data['timezone'] ?? 'UTC';

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
            <div style="font-size:14px"><strong>ნაჩვენებია</strong>: <?php echo htmlspecialchars($start_date . ' - ' . $end_date, ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="chart-legend"><span style="color:#ff8a65">● მაქს</span> <span style="margin-left:8px;color:#4fc3f7">● მინ</span></div>
          </div>
          <canvas id="historyChart" height="120"></canvas>
        </div>

        <div class="row">
          <?php for ($i = 0; $i < count($times); $i++):
            $dt = new DateTime($times[$i], new DateTimeZone($tz));
            $icon = isset($icons[$i]) ? icon_url($icons[$i], true) : 'icons/sun.svg';
            $dmax = isset($temps_max[$i]) ? round($temps_max[$i]) : '--';
            $dmin = isset($temps_min[$i]) ? round($temps_min[$i]) : '--';
            $desc = isset($descs[$i]) ? $descs[$i] : '';
          ?>
          <div class="col-6 col-sm-4 col-md-3 mb-3">
            <div class="card bg-dark text-white">
              <div class="card-body text-center">
                <div><?php echo $dt->format('d.m.Y'); ?></div>
                <img src="<?php echo htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?>" style="width:56px;height:56px;" />
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
              label: 'Max',
              data: maxs,
              backgroundColor: gradientMax,
              borderColor: '#ff8a65',
              pointRadius: 2,
              tension: 0.25,
              fill: true
            },
            {
              label: 'Min',
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