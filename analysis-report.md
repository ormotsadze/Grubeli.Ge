# Grubeli.ge — სრული კოდის ანალიზი

## 📋 სარჩევი
1. [არქიტექტურა](#1-არქიტექტურა)
2. [ფუნქციები და მონაცემთა ნაკადი](#2-ფუნქციები-და-მონაცემთა-ნაკადი)
3. [უსაფრთხოება](#3-უსაფრთხოება)
4. [ქეშირება](#4-ქეშირება)
5. [პერფორმანსი](#5-პერფორმანსი)
6. [დუბლირებადი კოდი / სტილები](#6-დუბლირებადი-კოდი--სტილები)
7. [SEO ოპტიმიზაცია](#7-seo-ოპტიმიზაცია)
8. [Accessibility (A11y)](#8-accessibility-a11y)
9. [PWA / Offline](#9-pwa--offline)
10. [რეკომენდაციები (Priority Ordered)](#10-რეკომენდაციები)

---

## 1. არქიტექტურა

### Project Tree (ძირითადი)
```
weather/
├── index.php          → მთავარი გვერდი (1010 line)
├── functions.php      → Core functions (API, Cache, Helpers)
├── header.php         → <head> + navbar
├── footer.php         → Bottom nav + mobile drawer
├── historical-weather.php → 80-წლიანი არქივი
├── save_location.php  → AJAX endpoint
├── about.php, contact.php, privacy.php, jobs.php, global-time.php, holidays.php
├── ai/
│   ├── config.php     → .env parser (GROQ + NASA keys)
│   ├── ai_helper.php  → Groq API wrapper
│   ├── ai-suggest.php → Suggestion engine
│   ├── send_message.php → AJAX AI endpoint
│   └── quotes.php     → Random quote loader
├── css/
│   ├── app.css        → 1914 line custom CSS
│   └── bootstrap*.css → Bootstrap 5 (multiple variants)
├── js/
│   └── geolocation.js → Geo + Android bridge
├── icons/             → 100+ SVG weather icons
├── fonts/             → BPG Georgian fonts
├── cities.json        → 97 Georgian cities
└── android/           → Android app (Kotlin/Gradle)
```

### Data Flow per Page Load
```
User Request → index.php?lat=X&lon=Y
  ├── session_start()
  ├── require functions.php
  ├── GET > Cookie > Default coord select
  ├── is_in_georgia() validation
  ├── fetch_weather()  → Open-Meteo Forecast API   [10s timeout]
  ├── fetch_air_quality() → Open-Meteo Air API     [10s timeout]
  ├── Nominatim reverse geocode                     [5s timeout]
  ├── checkEarthquakeRisk() → USGS API             [10s timeout]
  ├── checkFireRisk() → NASA FIRMS API             [15s timeout]
  ├── getGeorgianHolidays() → Nager.at API         [10s timeout]
  ├── require ai_helper.php + ai-suggest.php + quotes.php
  ├── include header.php + footer.php
  └── HTML rendered
```

**Total worst-case wait: ~60 seconds of sequential HTTP calls!**

---

## 2. ფუნქციები და მონაცემთა ნაკადი

### Core Features
| ფუნქცია | წყარო | ქეში TTL | შენიშვნა |
|---------|-------|----------|----------|
| Current Weather | Open-Meteo Forecast | 600s | hourly + daily + current |
| Air Quality | Open-Meteo Air | 600s | PM2.5, PM10, US AQI |
| UV Index | Open-Meteo (hourly) | 600s | Shared weather cache |
| 10-day Forecast | Open-Meteo | 600s | Max/Min/Code per day |
| Hourly Forecast | Open-Meteo | 600s | 12 hours ahead |
| Sun Rise/Set | Open-Meteo daily | 600s | + day length |
| Historical (80yr) | Open-Meteo Archive | Permanent | Per-date-range cache |
| Reverse Geocode | Nominatim (OSM) | **NO CACHE** | Every page load! |
| Earthquakes M4.0+ | USGS | 300s | Only Georgia bounding box |
| Fire Alerts | NASA FIRMS | 3600s | High confidence only |
| Holidays | Nager.at / GE | per year | ~1 req/year |
| AI Assistant | Groq / Llama 3.3 | NO | 5s flood protection |
| Location Save | Cookie + localStorage | 1 year | SameSite=None |

---

## 3. უსაფრთხოება

### 🔴 Critical Issues

#### 1. SSL Verification Disabled (NASA API)
**File:** `functions.php:542`
```php
CURLOPT_SSL_VERIFYPEER => false
```
Man-in-the-middle attack vector on NASA FIRMS API calls.

#### 2. Error Log Exposes Full API Response
**File:** `save_location.php:77`
```php
error_log("WEATHER_DEBUG_DATA (coords: $lat, $lon): " . json_encode($weather));
```
Logs entire weather data including potentially sensitive location/temperature patterns. Should be removed or limited in production.

#### 3. No CSRF Protection
- `save_location.php` — no CSRF token, accepts arbitrary POST
- `ai/send_message.php` — only session-based 5s throttle, no CSRF

#### 4. Unvalidated Debug Parameter
**File:** `index.php:290-294`
```php
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    echo "DEBUG: lat=" . htmlspecialchars($lat) . " lon=" . htmlspecialchars($lon);
}
```
OK in isolation, but `debug` parameter not validated beyond `== '1'`.

### 🟡 Moderate Issues

#### 5. Nominatim User-Agent Violation
**File:** `index.php:37`, `historical-weather.php:49`, `save_location.php:55`
```
User-Agent: GrubeliApp/1.0
```
Nominatim ToS requires contact info (email) in User-Agent. Should be:
```
User-Agent: GrubeliApp/1.0 (contact@grubeli.ge)
```

#### 6. Cache Directory Permissions
The `cache_dir()` function creates directories with `0755`. If cache is outside webroot, this is fine, but `checkFireRisk()` and `getGeorgianHolidays()` use `__DIR__ . '/../cache'` which may be inside webroot if project layout changes.

#### 7. No Rate Limiting
`save_location.php` can be spammed → creates cookies on every request. No IP-based or token-based rate limiting.

#### 8. Session Security
- `session_start()` without `session_regenerate_id()` — potential session fixation
- No session timeout configuration
- Session data stores `weather_cache` (can grow large)

### 🟢 Low Issues

#### 9. `file_get_contents` suppression with `@`
Hides legitimate errors, makes debugging impossible in production.

#### 10. SameSite=None on HTTP
```php
'samesite' => 'None'  // save_location.php:43
```
Chrome ignores SameSite=None without Secure flag. Degrades to Lax on HTTP, but this should be documented.

---

## 4. ქეშირება

### Current Cache Architecture

```
Weather:      cache_dir()/weather_{lat}_{lon}.json       TTL: 600s
Air Quality:  cache_dir()/air_{lat}_{lon}.json            TTL: 600s
Historical:   cache_dir()/historical_{...}.json           TTL: ∞ (permanent)
Earthquakes:  cache_dir()/earthquake_alert.json            TTL: 300s
Fire Alerts:  __DIR__/../cache/fire_alerts.json           TTL: 3600s
Holidays:     __DIR__/../cache/holidays_{year}.json       TTL: ~365d
```

### Problems

#### 1. Inconsistent Cache Directory Paths
- `cache_dir()` returns `dirname(__DIR__) . '/cache'` (outside webroot)
- `checkFireRisk()` uses `__DIR__ . '/../cache/'` (also outside, but different resolution)
- Should be unified to one function.

#### 2. No Cache Busting for Stale Data
When TTL expires, the next request waits for the full API call. No `stale-while-revalidate` pattern.

#### 3. Nominatim is NEVER CACHED
```php
// index.php:36-46 — runs on every single page load
$gurl = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat={$lat}&lon={$lon}&accept-language=ka";
$g = @file_get_contents($gurl, false, stream_context_create($opts));
```
Nominatim has rate limits (1 req/sec). This WILL get blocked under load.

#### 4. Cache Stampede Risk
All caches expire independently. If several expire simultaneously, the server makes 5+ concurrent synchronous API calls.

#### 5. Cache Key Rounding
```php
function get_cache_key($lat, $lon) {
    return 'weather_' . intval($lat * 1000) . '_' . intval($lon * 1000) . '.json';
}
```
Rounds to 3 decimal places (~111m precision). Good for sharing cache between nearby locations, but could theoretically serve slightly wrong data for border cases.

#### 6. The `.htaccess` Cache Headers
```apache
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
```
Good, but JS files should use versioned URLs for proper cache invalidation. Currently no versioning.

---

## 5. პერფორმანსი

### 🔴 Critical Issues

#### 1. Synchronous API Cascade (~60s worst case)
Every page load makes 6 sequential HTTP requests. PHP waits for each to finish before starting the next. This means:
- If Open-Meteo takes 3s → Nominatim 2s → USGS 5s → NASA 8s → Nager 2s = **20s page load**
- If any API is down (timeout): **potentially 50+ seconds**

**Solution:** Use `curl_multi_exec()` or queued async pattern. At minimum, fire weather + air calls in parallel with curl_multi.

#### 2. Large CSS Bundle (1914 lines)
`css/app.css` contains ALL styles (main, header, footer, drawer, historical, about, contact, animations, popups, search, etc.). This should be split:
- `critical.css` (above-fold styles) → inline in `<head>`
- `app.css` → async load
- Page-specific styles → lazy load

#### 3. No Image Optimization
- `main-icon-weather` is 110×110 PNG/JPG but served as SVG → OK
- Holiday card icon same size as main icon
- No responsive images for background (`widget_bg_day_image_v2.png`)

#### 4. Font Loading
```html
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@800;900&display=swap" 
      rel="stylesheet" media="print" onload="this.media='all'">
```
Google Fonts adds ~200ms+ to page load. Local fonts (BPG Arial, BPG NinoMtavruli) are loaded with `font-display: swap` which is good.

### 🟡 Moderate Issues

#### 5. jQuery-style DOM Operations
The app doesn't use jQuery (good), but has multiple `document.addEventListener('DOMContentLoaded')` handlers and inline `<script>` blocks scattered across footer + index.

#### 6. Chart.js Loaded from CDN
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
```
Adds ~250KB to historical page. Should be lazy-loaded or deferred.

#### 7. No Critical CSS Inlining
The hero card, main weather section, and details grid all use CSS from `app.css` which is loaded after `<head>`. First paint is delayed.

### 🟢 Minor Improvements

#### 8. Font Awesome Rendering
```html
<link href="icons/fontawesome/css/all.min.css" rel="stylesheet" media="print" onload="this.media='all'">
```
Good — uses `media="print"` + `onload` pattern. But this still blocks rendering briefly.

#### 9. Multiple Reflows
Search box city filtering creates new DOM elements (`.list-group-item`) on every keystroke. Consider debouncing (currently fires on every keydown).

#### 10. No IntersectionObserver
`reveal-up` animation (scroll-triggered) relies on CSS animation firing on load. Should use IntersectionObserver for true scroll-based reveal.

---

## 6. დუბლირებადი კოდი / სტილები

### Code Duplication

#### 1. Reverse Geocode (3 copies)
| Location | Lines |
|----------|-------|
| `index.php` | 36-46 |
| `historical-weather.php` | 45-61 |
| `save_location.php` | 51-71 |

**All identical logic.** Should be extracted to `functions.php`:
```php
function reverse_geocode($lat, $lon) { ... return $placeName; }
```

#### 2. Coordinate Resolution (3 copies)
| Location | Lines |
|----------|-------|
| `index.php` | 13-22 |
| `footer.php` | 2-6 |
| `historical-weather.php` | 8-11 |

**Same GET > Cookie > Default logic repeated.** Should be a helper.

#### 3. Cookie get/set helpers (2 copies)
- `index.php` lines 959-964 (`getCookie()`)
- `js/geolocation.js` lines 1-6 (`getCookie()`)

**Same function defined twice.**

#### 4. Loading Animation Classes (multiple files)
- `pulse-animation` in `index.php` lines 226-234 + 365-373
- `pulse` in `contact.php` line 143
- `ai-badge-pulse` in `app.css`

**3 separate pulse animations doing the same thing.**

#### 5. Reveal-up Animation (3+ places)
- `index.php` line 205, 337
- `historical-weather.php` line 75, 221-226
- `about.php` line 10

**Duplicated in both CSS and inline styles.**

#### 6. Gradient-move Animation (3 places in app.css)
- Line 517-521 (`@keyframes gradient-move`)
- Line 560-564 (`@keyframes ai-gradient-flow`)
- Lines 948-952 (duplicate `gradient-move` definition)

**Same animation, different names.**

#### 7. Weather Icon Fallback Logic
- `weather_code_to_icon()` — maps codes → files
- `icon_url()` — validates path + fallbacks
Both have overlapping fallback chains.

### CSS Duplication

| Selector / Block | Defined At | Also At |
|-----------------|------------|---------|
| `.premium-glass` | `app.css:1617` | `historical-weather.php:187-193` |
| `.float-icon` + `@keyframes` | `app.css:1659` | `historical-weather.php:211-220` |
| `.ambient-glow` | `app.css:1649` | `historical-weather.php:201-209` |
| `.reveal-up` + animation | `app.css:1670` | `historical-weather.php:221-226` |
| `.text-gradient-premium` | `app.css:1641` | `historical-weather.php:196-199` |

---

## 7. SEO ოპტიმიზაცია

### 🔴 Missing Critical SEO Elements

#### 1. No Canonical URLs
Every `index.php?lat=X&lon=Y` is a different URL but same page. Without `<link rel="canonical">`, search engines may see duplicate content.

#### 2. No JSON-LD Structured Data
No Schema.org markup for:
- `Weather` (current conditions)
- `City` (GeoCoordinates)
- `WebSite` / `WebPage`
- `Organization` (for Grubeli.ge brand)

#### 3. No Sitemap.xml
Search engines have no guide to all pages. Dynamic pages (different cities) are not discoverable.

#### 4. No Robots.txt
No instructions for crawlers.

#### 5. Same OG Image for All Pages
```html
<meta property="og:image" content="images/og-preview.png">
```
Static image regardless of city. Should generate dynamic OG images.

### 🟡 Moderate SEO Issues

#### 6. Title Structure
```html
<title>ამინდი {city} - Grubeli.ge</title>
```
Good base, but missing:
- No hreflang tags for Georgian content
- Could include temperature in title for rich snippets

#### 7. No BreadcrumbList Structured Data
Would help search engines understand site hierarchy.

#### 8. Dynamic Content Crawling
City pages use `?lat=X&lon=Y` parameters. These are not SEO-friendly URLs. Example:
- Current: `index.php?lat=41.7151&lon=44.8271`
- Better: `/tbilisi/` or `/weather/tbilisi`

#### 9. No `meta keywords` (minor — Google ignores)
Not critical, but no other meta tags beyond description.

### 🟢 Good SEO Practices Already Present

- ✅ Unique `<title>` per page (city name in title)
- ✅ Unique `<meta name="description">` per page
- ✅ Open Graph tags (`og:title`, `og:description`, `og:image`, `og:url`)
- ✅ SEO-friendly defaults in `header.php`
- ✅ `alt` attributes on weather icons
- ✅ Semantic HTML structure (header, main, footer)

---

## 8. Accessibility (A11y)

### Issues Found

#### 1. ARIA Attributes Missing
- Search input has `role="combobox"` and `aria-expanded` — good
- But AI response area has no `aria-live="polite"` (dynamic content updates)
- Modal triggers could use `aria-haspopup="dialog"`

#### 2. Color Contrast
- Light text (`#c6d1e7`) on light backgrounds may fail WCAG AA
- UV text in modal uses color-only indicators (no icons/symbols)
- AI response text has poor contrast in some states

#### 3. Focus Indicators
```css
* { outline: none !important; }  /* app.css:25 */
```
**Completely removes focus outlines!** This breaks keyboard navigation. Should use:
```css
*:focus-visible { outline: 2px solid #0dcaf0; outline-offset: 2px; }
```

#### 4. Touch Targets
- Hourly forecast buttons are small (42×42px icons)
- Scroll arrows (38×38px) are below recommended 44×44px

#### 5. Screen Reader Issues
- Footer navigation icons have no `aria-label`
- "Close" button in drawer has `aria-label` — good
- Holiday icon has no `alt` or `aria-hidden="true"`

#### 6. No Skip Links
No "skip to content" link for keyboard users.

---

## 9. PWA / Offline

### Completely Missing
- ❌ **No Service Worker** — no offline capability at all
- ❌ **No Web App Manifest** — can't install as PWA
- ❌ **No `manifest.json`**
- ❌ **No `service-worker.js`**

### Why It Matters
The app has an Android WebView wrapper, but no PWA support. Users on mobile Chrome cannot:
- Install the app to home screen
- View any cached data offline
- Get push notifications (requires service worker)

---

## 10. რეკომენდაციები

### Priority: **CRITICAL** (Fix Immediately)

| # | Issue | File | Fix |
|---|-------|------|-----|
| 1 | SSL disabled for NASA | `functions.php:542` | Change to `CURLOPT_SSL_VERIFYPEER => true` |
| 2 | Nominatim not cached | `index.php:36-46` | Cache reverse geocode for 24h (city rarely changes) |
| 3 | Remove production `error_log` | `save_location.php:77` | Remove or guard with `if (defined('DEV_MODE'))` |
| 4 | Focus outline removed globally | `app.css:25` | Replace with `:focus-visible` pattern |
| 5 | User-Agent missing contact | Multiple files | Add `(contact@grubeli.ge)` to all User-Agent strings |

### Priority: **HIGH** (This Sprint)

| # | Issue | Fix |
|---|-------|-----|
| 6 | Parallelize API calls | Use `curl_multi_exec()` for weather + air quality + Nominatim |
| 7 | Reverse geocode helper | Extract to `functions.php`, reuse in 3 files |
| 8 | Coordinate resolution helper | Extract to `functions.php`, reuse in 3 files |
| 9 | Cache Nominatim results | Add to file cache with 24h TTL |
| 10 | Remove duplicate `pulse-animation` | Consolidate into single CSS class in `app.css` |
| 11 | Remove duplicate `reveal-up` | Keep only in `app.css`, remove from page-specific styles |
| 12 | Remove duplicate `gradient-move` | Use single keyframes name everywhere |
| 13 | Increase earthquake cache TTL | 300s → 900s (15 min — earthquake data doesn't change minutely) |
| 14 | Fix SameSite=None on HTTP | Add `if ($secure)` check before setting SameSite=None |

### Priority: **MEDIUM** (Next Sprint)

| # | Issue | Fix |
|---|-------|-----|
| 15 | Split large CSS | `critical.css` (inline) + `app.css` (async) |
| 16 | Add `<link rel="canonical">` | All pages |
| 17 | Add JSON-LD structured data | Weather, City, Website schemas |
| 18 | Dynamic OG images | Generate per-city OG images or use text overlay |
| 19 | Consolidate cache functions | Unify `cache_dir()` usage across all files |
| 20 | Debounce search input | Add 200ms debounce to city search |
| 21 | Lazy-load Chart.js | Only load on historical page, use IntersectionObserver |
| 22 | Add `aria-live="polite"` | AI response container |
| 23 | 44×44px touch targets | Increase hourly scroll arrows and suggestion buttons |

### Priority: **LOW** (Future)

| # | Issue | Fix |
|---|-------|-----|
| 24 | Service Worker + PWA manifest | Enable offline viewing + install prompt |
| 25 | SEO-friendly URLs | `example.com/tbilisi/` instead of `?lat=X&lon=Y` |
| 26 | Sitemap.xml | Dynamic sitemap for all 97 cities |
| 27 | Stale-while-revalidate cache | Serve stale cache + refresh in background |
| 28 | IntersectionObserver for reveal | Replace CSS-only reveal-up with JS observer |
| 29 | Session size optimization | Don't store `weather_cache` in session |
| 30 | CSRF tokens | Add to save_location.php and send_message.php |

### Quick Wins (30 min each)

| # | Task | Impact |
|---|------|--------|
| A | Cache Nominatim for 24h | Major speedup, avoids rate limits |
| B | Extract 3 duplicated functions | Cleaner code, easier maintenance |
| C | Remove duplicate CSS animations | -2KB CSS, cleaner code |
| D | Fix SSL verify on NASA | Security fix |
| E | Increase earthquake cache to 15min | 3x fewer API calls |

---

## Summary

**Overall Assessment:** The project is well-structured for a Georgian weather app with strong feature set (AI, earthquakes, fire, holidays, historical data, Android app). However, it has **critical performance bottlenecks** (sequential synchronous API calls) and **important security issues** (disabled SSL, unauthenticated endpoints).

**Top 3 Actions:**
1. 🚨 Fix SSL verification for NASA API
2. 🚀 Parallelize API calls (cut page load time by 60-80%)
3. 🗑️ Deduplicate 5+ repeated code blocks

The caching strategy is functional but unoptimized — 80% improvement is achievable by caching Nominatim results and increasing TTLs. The PWA gap is the biggest missed opportunity for mobile users.