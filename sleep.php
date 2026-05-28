<?php
require_once 'functions.php';

$pageTitle   = __('sleep_title');
$pageDesc    = __('sleep_desc');
$pageOgTitle = __('sleep_og_title');
$pageTwTitle = __('sleep_tw_title');
$pageTwDesc  = __('sleep_tw_desc');
include 'header.php';
?>

<div class="sleep-bg">
    <div class="sleep-bg-objects" id="sleepBgObjects"></div>
</div>

<div class="sleep-hero mb-4">
    <div class="sleep-hero-bg"></div>
    <div class="sleep-hero-content">
        <div class="sleep-hero-icon">
            <i class="fa-solid fa-moon"></i>
        </div>
        <h1 class="sleep-hero-title" style="font-family: '<?php echo __('font_family'); ?>', sans-serif;">
            <?php echo __('sleep_title'); ?>
        </h1>
        <p class="sleep-hero-desc"><?php echo __('sleep_desc'); ?></p>
    </div>
    <div class="sleep-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
            <path d="M0,30 C360,60 720,0 1440,30 L1440,60 L0,60 Z" fill="#121216"></path>
        </svg>
    </div>
</div>

<div class="container mb-5">
    <div class="sleep-cards-grid">
        <?php
        $tracks = [
            [
                'id'         => 'deep-sleep-1',
                'title_ka'   => 'მედიტაციური მუსიკა',
                'title_en'   => 'Meditative Music',
                'sub_ka'     => 'ღრმა ძილის მუსიკა',
                'sub_en'     => 'Deep Sleep Music',
                'audio'      => 'audio/deepsleepmusic/1.mp3',
            ],
            [
                'id'         => 'forest-sounds',
                'title_ka'   => 'ტყის ხმები',
                'title_en'   => 'Forest Sounds',
                'sub_ka'     => 'ზაფხულის ტყის ხმები',
                'sub_en'     => 'Forest Nature Sounds',
                'audio'      => 'audio/forest/1.mp3',
            ],
            [
                'id'         => 'ocean-sounds',
                'title_ka'   => 'ოკეანის ხმები',
                'title_en'   => 'Ocean Sounds',
                'sub_ka'     => 'ოკეანის და ზღვის ხმები',
                'sub_en'     => 'Ocean and Sea Sounds',
                'audio'      => 'audio/ocean/1.mp3',
            ],
            [
                'id'         => 'rainy-night',
                'title_ka'   => 'წვიმიანი ღამე',
                'title_en'   => 'Rainy Night',
                'sub_ka'     => 'წვიმიანი ღამის ხმები',
                'sub_en'     => 'Rainy Night Sounds',
                'audio'      => 'audio/rain/1.mp3',
            ],
            [
                'id'         => 'fireplace-sounds',
                'title_ka'   => 'ცეცხლის ხმა',
                'title_en'   => 'Fireplace Sounds',
                'sub_ka'     => 'ცეცხლის ხმები, ბუხარი',
                'sub_en'     => 'Fireplace Sounds',
                'audio'      => 'audio/fireplace/1.mp3',
            ],
            [
                'id'         => 'thunderstorm',
                'title_ka'   => 'ჭექა-ქუხილის ხმა',
                'title_en'   => 'Thunderstorm',
                'sub_ka'     => 'ჭექა-ქუხილის ხმები',
                'sub_en'     => 'Thunderstorm Sounds',
                'audio'      => 'audio/thunderstorm/1.mp3',
            ],
            [
                'id'         => 'whitenoise',
                'title_ka'   => 'თეთრი ხმაური',
                'title_en'   => 'White Noise',
                'sub_ka'     => 'თეთრი ხმაურის ხმები',
                'sub_en'     => 'White Noise Sounds',
                'audio'      => 'audio/whitenoise/1.mp3',
            ],
            [
                'id'         => 'pinknoise',
                'title_ka'   => 'ვარდისფერი ხმაური',
                'title_en'   => 'Pink Noise',
                'sub_ka'     => 'ვარდისფერი ხმაურის ხმები',
                'sub_en'     => 'Pink Noise Sounds',
                'audio'      => 'audio/pinknoise/1.mp3',
            ],
        ];

        $current_lang = get_current_lang();
        
        foreach ($tracks as $index => $track):
            $title      = ($current_lang === 'en') ? $track['title_en'] : $track['title_ka'];
            $subtitle   = ($current_lang === 'en') ? $track['sub_en']   : $track['sub_ka'];
            
            $folder = rtrim(dirname($track['audio']), '/');
            $files = array_merge(
                glob($folder . '/*.mp3') ?: [],
                glob($folder . '/*.m4a') ?: [],
                glob($folder . '/*.aac') ?: []
            );
            
            if (empty($files)) {
                $files = [$track['audio']];
            } else {
                natsort($files);
                $files = array_values($files);
            }
            
            $playlistJson = json_encode($files);
            $firstTrack   = $files[0];
            
            $audioSrc   = htmlspecialchars($firstTrack, ENT_QUOTES, 'UTF-8');
            $playerId   = 'sleep-player-' . $track['id'];
            $playBtnId  = 'sleep-playbtn-' . $track['id'];
            $progressId = 'sleep-progress-' . $track['id'];
        ?>
        <div class="sleep-card" data-track-id="<?php echo htmlspecialchars($track['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="sleep-card-body">
                <div class="sleep-track-meta">
                    <h5 class="sleep-track-title" style="font-family: '<?php echo __('font_family'); ?>';"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h5>
                    <p class="sleep-track-sub"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <div class="sleep-controls">
                    <audio id="<?php echo $playerId; ?>" data-playlist='<?php echo htmlspecialchars($playlistJson, ENT_QUOTES, 'UTF-8'); ?>' preload="none">
                        <source src="<?php echo $audioSrc; ?>" type="audio/mpeg">
                    </audio>
                    <div class="sleep-controls-row">
                        <button class="sleep-btn-play" onclick="togglePlay('<?php echo $playerId; ?>', '<?php echo $playBtnId; ?>')">
                            <i class="fa-solid fa-play" id="<?php echo $playBtnId; ?>"></i>
                        </button>
                        <div class="sleep-progress-wrap">
                            <input type="range" class="sleep-progress" id="<?php echo $progressId; ?>" value="0" min="0" max="100" step="0.1"
                                oninput="seekAudio('<?php echo $playerId; ?>', this.value)"
                                onchange="seekAudio('<?php echo $playerId; ?>', this.value)">
                            <div class="sleep-time-row">
                                <span class="sleep-time" id="sleep-current-<?php echo $track['id']; ?>">0:00</span>
                                <span class="sleep-time" id="sleep-duration-<?php echo $track['id']; ?>">0:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* === ანიმირებული ფონი === */
.sleep-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
    background: #121216;
}

.sleep-bg-objects {
    position: absolute;
    inset: 0;
}

.sleep-bg-item {
    position: absolute;
    animation: sleepBgDrift linear infinite;
    opacity: 0.03;
    line-height: 1;
}

@keyframes sleepBgDrift {
    0% {
        transform: translateX(-15vw) translateY(0) rotate(0deg);
        opacity: 0.02;
    }
    10% { opacity: 0.05; }
    90% { opacity: 0.05; }
    100% {
        transform: translateX(115vw) translateY(-40px) rotate(10deg);
        opacity: 0.01;
    }
}

/* === HERO HEADER (შერწყმული თხევადი მინის ეფექტთან) === */
.sleep-hero {
    position: relative;
    padding: 4rem 1rem 3.5rem;
    text-align: center;
    overflow: hidden;
    /* ფონი გადაყვანილია rgba გრადიენტზე, რომ გატარდეს უკანა ფონის ანიმაციები */
    background: linear-gradient(135deg, rgba(7, 9, 14, 0.7) 0%, rgba(17, 17, 34, 0.4) 60%, rgba(18, 18, 22, 0) 100%);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.sleep-hero-bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(13, 202, 240, 0.06) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(155, 81, 224, 0.05) 0%, transparent 50%);
}

.sleep-hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
}

.sleep-hero-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.2rem;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(8px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: #0dcaf0;
    box-shadow: 0 0 30px rgba(13, 202, 240, 0.1);
    animation: moonGlow 4s ease-in-out infinite alternate;
}

@keyframes moonGlow {
    0% { box-shadow: 0 0 30px rgba(13, 202, 240, 0.1); }
    100% { box-shadow: 0 0 50px rgba(13, 202, 240, 0.25), 0 0 70px rgba(155, 81, 224, 0.1); }
}

.sleep-hero-title {
    font-size: 2.4rem;
    color: #fff;
    margin: 0 0 0.7rem;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.sleep-hero-desc {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.6);
    margin: 0;
    line-height: 1.6;
}

.sleep-hero-wave {
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    width: 100%;
    height: 60px;
    z-index: 3;
}

.sleep-hero-wave svg {
    display: block;
    width: 100%;
    height: 100%;
}

/* === LIQUID GLASS CARDS GRID === */
.sleep-cards-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 1rem;
    margin-top: -1.5rem;
    position: relative;
    z-index: 5;
    padding: 0 0.5rem;
}

@media (min-width: 576px) {
    .sleep-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 992px) {
    .sleep-cards-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }
}

@media (min-width: 1200px) {
    .sleep-cards-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* === LIQUID GLASS CARD DESIGN === */
.sleep-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(255, 255, 255, 0.01) 100%);
    backdrop-filter: blur(20px) saturate(130%);
    -webkit-backdrop-filter: blur(20px) saturate(130%);
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.07);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}

.sleep-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 40%;
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0) 100%);
    pointer-events: none;
}

.sleep-card:hover {
    transform: translateY(-5px);
    border-color: rgba(13, 202, 240, 0.25);
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.01) 100%);
    box-shadow: 0 12px 40px rgba(13, 202, 240, 0.08), 0 4px 12px rgba(0, 0, 0, 0.3);
}

.sleep-card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 155px;
}

.sleep-track-meta {
    margin-bottom: 1rem;
}

.sleep-track-title {
    font-size: 1.1rem;
    color: #e2e8f0;
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    letter-spacing: 0.2px;
}

.sleep-track-sub {
    font-size: 0.82rem;
    color: #94a3b8;
    margin: 0;
}

/* === GLASS CONTROLS === */
.sleep-controls {
    width: 100%;
}

.sleep-controls-row {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.sleep-btn-play {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.15), rgba(155, 81, 224, 0.15)) !important;
    backdrop-filter: blur(4px);
    color: #0dcaf0;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    padding: 0;
}

.sleep-btn-play:hover {
    transform: scale(1.08);
    color: #fff;
    border-color: rgba(13, 202, 240, 0.4);
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.3), rgba(155, 81, 224, 0.3)) !important;
    box-shadow: 0 0 20px rgba(13, 202, 240, 0.3);
}

.sleep-btn-play:active {
    transform: scale(0.95);
}

.sleep-progress-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.sleep-progress {
    width: 100%;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 4px;
    outline: none;
    cursor: pointer;
}

.sleep-progress::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #0dcaf0 !important;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 0 10px rgba(13, 202, 240, 0.6);
    transition: transform 0.2s ease;
}

.sleep-progress::-webkit-slider-thumb:hover {
    transform: scale(1.3);
}

.sleep-progress::-moz-range-thumb {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #0dcaf0 !important;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 0 10px rgba(13, 202, 240, 0.6);
}

.sleep-progress::-moz-range-track {
    height: 4px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 4px;
}

.sleep-time-row {
    display: flex;
    justify-content: space-between;
}

.sleep-time {
    font-size: 0.72rem;
    color: #64748b;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.3px;
}
</style>

<script>
// ═══════════════════════════════════════════════════════════════
//  ანიმირებული ფონი
// ═══════════════════════════════════════════════════════════════
(function createSleepBg() {
    var container = document.getElementById('sleepBgObjects');
    if (!container) return;

    var symbols = ['🌙', '⭐', '✨', '☁️', '🌠', '💫'];
    var count = 20;

    for (var i = 0; i < count; i++) {
        var el = document.createElement('span');
        el.className = 'sleep-bg-item';
        el.textContent = symbols[Math.floor(Math.random() * symbols.length)];
        el.style.top = (Math.random() * 100) + '%';
        el.style.left = (Math.random() * 10) + '%';
        el.style.fontSize = (Math.random() * 2 + 1.5) + 'rem';
        el.style.animationDuration = (Math.random() * 25 + 20) + 's';
        el.style.animationDelay = (Math.random() * -30) + 's';
        container.appendChild(el);
    }
})();

// ═══════════════════════════════════════════════════════════════
//  აუდიო ფლეერის მართვა
// ═══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
    var sleepPlayers = document.querySelectorAll('.sleep-controls audio');

    sleepPlayers.forEach(function(player) {
        var playlistRaw = player.getAttribute('data-playlist');
        if (playlistRaw) {
            var playlist = JSON.parse(playlistRaw);
            if (playlist.length === 1) {
                player.loop = true;
            }
            player.dataset.currentIndex = 0;
        }

        player.addEventListener('timeupdate', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            var progress = card.querySelector('.sleep-progress');
            var currentTimeEl = card.querySelector('.sleep-time:first-of-type');
            if (player.duration && progress) {
                progress.value = (player.currentTime / player.duration) * 100;
            }
            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(player.currentTime);
            }
        });

        player.addEventListener('loadedmetadata', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            var durationEl = card.querySelector('.sleep-time:last-of-type');
            if (durationEl) {
                durationEl.textContent = formatTime(player.duration);
            }
        });

        player.addEventListener('ended', function() {
            var playlistRaw = player.getAttribute('data-playlist');
            var playlist = playlistRaw ? JSON.parse(playlistRaw) : [];

            if (playlist.length > 1) {
                var currentIndex = parseInt(player.dataset.currentIndex || 0);
                currentIndex++;
                
                if (currentIndex >= playlist.length) {
                    currentIndex = 0;
                }
                
                player.dataset.currentIndex = currentIndex;
                player.src = playlist[currentIndex];
                player.play();
            } else {
                var card = player.closest('.sleep-card');
                if (!card) return;
                var icon = card.querySelector('.sleep-btn-play i');
                var progress = card.querySelector('.sleep-progress');
                if (icon) icon.className = 'fa-solid fa-play';
                if (progress) progress.value = 0;
                var timeEl = card.querySelector('.sleep-time:first-of-type');
                if (timeEl) timeEl.textContent = '0:00';
            }
        });

        player.addEventListener('play', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            var icon = card.querySelector('.sleep-btn-play i');
            if (icon) icon.className = 'fa-solid fa-pause';

            document.querySelectorAll('.sleep-controls audio').forEach(function(other) {
                if (other !== player && !other.paused) {
                    other.pause();
                    var otherCard = other.closest('.sleep-card');
                    if (otherCard) {
                        var oi = otherCard.querySelector('.sleep-btn-play i');
                        if (oi) oi.className = 'fa-solid fa-play';
                    }
                }
            });
        });

        player.addEventListener('pause', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            var icon = card.querySelector('.sleep-btn-play i');
            if (icon) icon.className = 'fa-solid fa-play';
        });
    });
});

function togglePlay(playerId, btnId) {
    var player = document.getElementById(playerId);
    if (!player) return;
    if (player.paused) { player.play(); }
    else { player.pause(); }
}

function seekAudio(playerId, value) {
    var player = document.getElementById(playerId);
    if (player && player.duration) {
        player.currentTime = (value / 100) * player.duration;
    }
}

function formatTime(seconds) {
    if (isNaN(seconds) || !isFinite(seconds)) return '0:00';
    var m = Math.floor(seconds / 60);
    var s = Math.floor(seconds % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
</body>
</html>