<?php
require_once 'functions.php';

$pageTitle   = __('sleep_title');
$pageDesc    = __('sleep_desc');
$pageOgTitle = __('sleep_og_title');
$pageTwTitle = __('sleep_tw_title');
$pageTwDesc  = __('sleep_tw_desc');
include 'header.php';
?>

<!-- ანიმირებული ფონი - მთვარე და ვარსკვლავები -->
<div class="sleep-bg">
    <div class="sleep-bg-objects" id="sleepBgObjects"></div>
</div>

<!-- Modern Hero Header -->
<div class="sleep-hero">
    <div class="sleep-hero-bg"></div>
    <div class="sleep-hero-content">
        <div class="sleep-hero-icon">
            <i class="fa-solid fa-moon"></i>
        </div>
        <h1 class="sleep-hero-title"><?php echo __('sleep_title'); ?></h1>
        <p class="sleep-hero-desc"><?php echo __('sleep_desc'); ?></p>
    </div>
    <div class="sleep-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
            <path d="M0,30 C360,60 720,0 1440,30 L1440,60 L0,60 Z" fill="#18181d"></path>
        </svg>
    </div>
</div>

<div class="container mb-5">
    <div class="sleep-cards-grid">
        <?php

        $tracks = [
            [
                'id'         => 'deep-sleep-1',
                'title_ka'   => 'ღრმა ძილი 1',
                'title_en'   => 'Deep Sleep 1',
                'sub_ka'     => 'ღრმა ძილის მუსიკა',
                'sub_en'     => 'Deep Sleep Music',
                'audio'      => 'audio/deepsleepmusic/1.mp3',
                'cover'      => 'audio/deepsleepmusic/cover.jpg',
            ],
            [
                'id'         => 'deep-sleep-2',
                'title_ka'   => 'ღრმა ძილი 2',
                'title_en'   => 'Deep Sleep 2',
                'sub_ka'     => 'ღრმა ძილის მუსიკა',
                'sub_en'     => 'Deep Sleep Music',
                'audio'      => 'audio/forest/1.mp3',
                'cover'      => 'audio/forest/cover.jpg',
            ],
            [
                'id'         => 'moonlight',
                'title_ka'   => 'მთვარის შუქი',
                'title_en'   => 'Moonlight',
                'sub_ka'     => 'მთვარის შუქზე დასვენება',
                'sub_en'     => 'Relax by Moonlight',
                'audio'      => 'audio/ocean/1.mp3',
                'cover'      => 'audio/ocean/cover.jpg',
            ],
            [
                'id'         => 'starry-night',
                'title_ka'   => 'ვარსკვლავები',
                'title_en'   => 'Starry Night',
                'sub_ka'     => 'ვარსკვლავური ცის ხმები',
                'sub_en'     => 'Starry Sky Sounds',
                'audio'      => 'audio/rain/1.mp3',
                'cover'      => 'audio/rain/cover.jpg',
            ],
            [
                'id'         => 'night-calm',
                'title_ka'   => 'ღამის სიმშვიდე',
                'title_en'   => 'Night Calm',
                'sub_ka'     => 'ღამის სიმშვიდის ხმები',
                'sub_en'     => 'Night Calm Sounds',
                'audio'      => 'audio/deepsleepmusic/1.mp3',
                'cover'      => 'audio/deepsleepmusic/cover.jpg',
            ],
            [
                'id'         => 'ocean-sound',
                'title_ka'   => 'ოკეანის ხმა',
                'title_en'   => 'Ocean Sound',
                'sub_ka'     => 'ოკეანის ტალღების ხმა',
                'sub_en'     => 'Ocean Wave Sounds',
                'audio'      => 'audio/deepsleepmusic/1.mp3',
                'cover'      => 'audio/deepsleepmusic/cover.jpg',
            ],
            [
                'id'         => 'forest-whisper',
                'title_ka'   => 'ტყის ჩურჩული',
                'title_en'   => 'Forest Whisper',
                'sub_ka'     => 'ტყის ბუნებრივი ხმები',
                'sub_en'     => 'Forest Nature Sounds',
                'audio'      => 'audio/deepsleepmusic/1.mp3',
                'cover'      => 'audio/deepsleepmusic/cover.jpg',
            ],
            [
                'id'         => 'raindrops',
                'title_ka'   => 'წვიმის წვეთები',
                'title_en'   => 'Raindrops',
                'sub_ka'     => 'წვიმის დამამშვიდებელი ხმა',
                'sub_en'     => 'Soothing Rain Sounds',
                'audio'      => 'audio/deepsleepmusic/1.mp3',
                'cover'      => 'audio/deepsleepmusic/cover.jpg',
            ],
        ];

        $current_lang = get_current_lang();
        foreach ($tracks as $index => $track):
            $title      = ($current_lang === 'en') ? $track['title_en'] : $track['title_ka'];
            $subtitle   = ($current_lang === 'en') ? $track['sub_en']   : $track['sub_ka'];
            $audioSrc   = htmlspecialchars($track['audio'], ENT_QUOTES, 'UTF-8');
            $coverSrc   = htmlspecialchars($track['cover'], ENT_QUOTES, 'UTF-8');
            $playerId   = 'sleep-player-' . $track['id'];
            $playBtnId  = 'sleep-playbtn-' . $track['id'];
            $progressId = 'sleep-progress-' . $track['id'];
        ?>
        <div class="sleep-card mt-4" data-track-id="<?php echo htmlspecialchars($track['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="sleep-card-inner">
                <div class="sleep-cover-wrapper">
                    <img src="<?php echo $coverSrc; ?>" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" class="sleep-cover" loading="lazy">
                    <div class="sleep-cover-overlay">
                        <button class="sleep-cover-play-btn" onclick="togglePlay('<?php echo $playerId; ?>', '<?php echo $playBtnId; ?>')">
                            <i class="fa-solid fa-play" id="<?php echo $playBtnId; ?>"></i>
                        </button>
                    </div>
                </div>
                <div class="sleep-card-body">
                    <h5 class="sleep-track-title"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h5>
                    <p class="sleep-track-sub"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>

                    <!-- Custom Audio Controls -->
                    <div class="sleep-controls">
                        <audio id="<?php echo $playerId; ?>" preload="none">
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
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* === ანიმირებული ფონი – მთვარე და ვარსკვლავები === */
.sleep-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
    background: #18181d;
}

.sleep-bg-objects {
    position: absolute;
    inset: 0;
}

/* ცალკეული ფონური ობიექტები */
.sleep-bg-item {
    position: absolute;
    animation: sleepBgDrift linear infinite;
    opacity: 0.04;
    line-height: 1;
}

@keyframes sleepBgDrift {
    0% {
        transform: translateX(-15vw) translateY(0) rotate(0deg);
        opacity: 0.03;
    }
    10% { opacity: 0.07; }
    90% { opacity: 0.07; }
    100% {
        transform: translateX(115vw) translateY(-30px) rotate(10deg);
        opacity: 0.02;
    }
}

/* === HERO HEADER === */
.sleep-hero {
    position: relative;
    padding: 3rem 1rem 2.5rem;
    text-align: center;
    overflow: hidden;
    background: linear-gradient(135deg, #0a0d11 0%, #1a1a2e 30%, #16213e 60%, #0f3460 100%);
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.sleep-hero-bg {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(13, 202, 240, 0.08) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(155, 81, 224, 0.06) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 80%, rgba(255, 193, 7, 0.04) 0%, transparent 40%);
}

.sleep-hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
}

.sleep-hero-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, rgba(13, 202, 240, 0.2), rgba(155, 81, 224, 0.2));
    border: 2px solid rgba(13, 202, 240, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: #0dcaf0;
    box-shadow: 0 0 40px rgba(13, 202, 240, 0.15);
    animation: moonGlow 4s ease-in-out infinite alternate;
}

@keyframes moonGlow {
    0% { box-shadow: 0 0 40px rgba(13, 202, 240, 0.15); }
    100% { box-shadow: 0 0 60px rgba(13, 202, 240, 0.3), 0 0 80px rgba(155, 81, 224, 0.1); }
}

.sleep-hero-title {
    font-family: 'BPG NinoMtavruli', sans-serif;
    font-size: 2.2rem;
    color: #fff;
    margin: 0 0 0.6rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.sleep-hero-desc {
    font-size: 0.95rem;
    color: rgba(255, 255, 255, 0.7);
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

/* === CARDS GRID === */
.sleep-cards-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    margin-top: -1.5rem;
    position: relative;
    z-index: 5;
}

@media (min-width: 768px) {
    .sleep-cards-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
}

@media (min-width: 1200px) {
    .sleep-cards-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* === CARD === */
.sleep-card {
    background: #1b1b1b;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.06);
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.sleep-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5);
    border-color: rgba(13, 202, 240, 0.15);
}

.sleep-card-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* === COVER === */
.sleep-cover-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #0a0d11;
}

.sleep-cover {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease;
}

.sleep-card:hover .sleep-cover {
    transform: scale(1.08);
}

.sleep-cover-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.35);
    opacity: 0;
    transition: opacity 0.35s ease;
}

.sleep-cover-wrapper:hover .sleep-cover-overlay {
    opacity: 1;
}

.sleep-cover-play-btn {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.8);
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(12px);
    color: #fff;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}

.sleep-cover-play-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.08);
    box-shadow: 0 0 30px rgba(13, 202, 240, 0.3);
}

/* === CARD BODY === */
.sleep-card-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    flex: 1;
}

.sleep-track-title {
    font-family: 'BPG NinoMtavruli', sans-serif;
    font-size: 1rem;
    color: #c6d1e7;
    margin: 0;
    font-weight: 600;
}

.sleep-track-sub {
    font-size: 0.78rem;
    color: #9ba8c5;
    margin: 0 0 0.3rem;
}

/* === CUSTOM AUDIO CONTROLS === */
.sleep-controls {
    margin-top: auto;
    padding-top: 0.5rem;
}

.sleep-controls-row {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.sleep-btn-play {
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #0dcaf0, #0d6efd);
    color: #fff;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(13, 202, 240, 0.25);
    padding: 0;
}

.sleep-btn-play:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(13, 202, 240, 0.35);
}

.sleep-btn-play:active {
    transform: scale(0.95);
}

.sleep-progress-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.sleep-progress {
    width: 100%;
    height: 4px;
    -webkit-appearance: none;
    appearance: none;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    outline: none;
    cursor: pointer;
    transition: height 0.2s ease;
}

.sleep-progress::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #0dcaf0;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 2px 8px rgba(13, 202, 240, 0.4);
    transition: transform 0.2s ease;
}

.sleep-progress::-webkit-slider-thumb:hover {
    transform: scale(1.2);
}

.sleep-progress::-moz-range-thumb {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #0dcaf0;
    cursor: pointer;
    border: 2px solid #fff;
}

.sleep-progress::-moz-range-track {
    height: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

.sleep-time-row {
    display: flex;
    justify-content: space-between;
}

.sleep-time {
    font-size: 0.7rem;
    color: #9ba8c5;
    font-family: 'Poppins', sans-serif;
    letter-spacing: 0.3px;
}
</style>

<script>
// ═══════════════════════════════════════════════════════════════
//  ანიმირებული ფონი – მთვარე, ვარსკვლავები, ღრუბლები
// ═══════════════════════════════════════════════════════════════
(function createSleepBg() {
    var container = document.getElementById('sleepBgObjects');
    if (!container) return;

    var symbols = ['🌙', '⭐', '✨', '☁️', '🌠', '💫'];
    var count = 24;

    for (var i = 0; i < count; i++) {
        var el = document.createElement('span');
        el.className = 'sleep-bg-item';
        el.textContent = symbols[Math.floor(Math.random() * symbols.length)];
        el.style.top = (Math.random() * 100) + '%';
        el.style.left = (Math.random() * 10) + '%';
        el.style.fontSize = (Math.random() * 3 + 2) + 'rem';
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
        // Update progress as audio plays
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

        // Set duration when metadata loaded
        player.addEventListener('loadedmetadata', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            var durationEl = card.querySelector('.sleep-time:last-of-type');
            if (durationEl) {
                durationEl.textContent = formatTime(player.duration);
            }
        });

        // Reset when ended
        player.addEventListener('ended', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            var icons = card.querySelectorAll('.sleep-btn-play i, .sleep-cover-play-btn i');
            var progress = card.querySelector('.sleep-progress');
            icons.forEach(function(icon) { icon.className = 'fa-solid fa-play'; });
            if (progress) progress.value = 0;
            var timeEl = card.querySelector('.sleep-time:first-of-type');
            if (timeEl) timeEl.textContent = '0:00';
        });

        // Handle play/pause icon updates
        player.addEventListener('play', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            var icons = card.querySelectorAll('.sleep-btn-play i, .sleep-cover-play-btn i');
            icons.forEach(function(icon) { icon.className = 'fa-solid fa-pause'; });

            // Pause all other players
            document.querySelectorAll('.sleep-controls audio').forEach(function(other) {
                if (other !== player && !other.paused) {
                    other.pause();
                    var otherCard = other.closest('.sleep-card');
                    if (otherCard) {
                        otherCard.querySelectorAll('.sleep-btn-play i, .sleep-cover-play-btn i')
                            .forEach(function(oi) { oi.className = 'fa-solid fa-play'; });
                    }
                }
            });
        });

        player.addEventListener('pause', function() {
            var card = player.closest('.sleep-card');
            if (!card) return;
            card.querySelectorAll('.sleep-btn-play i, .sleep-cover-play-btn i')
                .forEach(function(icon) { icon.className = 'fa-solid fa-play'; });
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