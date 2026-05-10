# 🌤️ Grubeli.ge — Georgian Weather Platform

![Grubeli.ge](images/og-preview.png)

> **Real-time weather, air quality, earthquake alerts, fire monitoring, AI assistant, and 80-year historical climate data — all for Georgia.**

---

## 🚀 Features

| Feature | Description |
|---------|-------------|
| **📍 Current Weather** | Real-time temperature, humidity, wind, UV index, sunrise/sunset |
| **📊 10-Day Forecast** | Daily max/min temperatures with weather icons |
| **⏰ Hourly Forecast** | 12-hour breakdown with precipitation probability |
| **💨 Air Quality (AQI)** | PM2.5, PM10, and US AQI index |
| **📜 80-Year Climate History** | Historical weather data from 1940–present |
| **🌍 97+ Georgian Cities** | Search & auto-detect with bilingual (KA/EN) support |
| **🌋 Earthquake Alerts** | Real-time M4.0+ earthquake notifications for Georgia |
| **🔥 Fire Monitoring** | NASA FIRMS satellite fire alerts |
| **🤖 AI Weather Assistant** | Groq-powered (Llama 3.3) conversational weather Q&A |
| **🌙 Moon Phases** | Moonrise, moonset, and lunar phase tracking |
| **📱 Cross-Platform** | Web + Android (Kotlin WebView) app |
| **🌐 Bilingual** | Full Georgian UI + English API responses |

---

## 🧰 Tech Stack

### Frontend
- **HTML5 / CSS3** — Bootstrap 5, custom responsive CSS
- **JavaScript** — Native JS (no jQuery), Geolocation API
- **SVG Icons** — 100+ custom weather icons
- **Fonts** — BPG Arial, BPG NinoMtavruli (Georgian), Poppins

### Backend
- **PHP 8.x** — Core backend with parallel `curl_multi` HTTP
- **JSON File Cache** — 600s-86400s TTL per data type
- **Session-based throttle** — AI endpoint protection

### APIs
| API | Usage | Cache TTL |
|-----|-------|-----------|
| [Open-Meteo](https://open-meteo.com/) | Weather forecast + air quality | 600s |
| [Open-Meteo Archive](https://archive-api.open-meteo.com/) | Historical data (80yr) | Permanent |
| [Nominatim (OSM)](https://nominatim.openstreetmap.org/) | Reverse geocode | 86400s |
| [USGS](https://earthquake.usgs.gov/) | Earthquake alerts | 900s |
| [NASA FIRMS](https://firms.modaps.eosdis.nasa.gov/) | Fire alerts | 3600s |
| [Nager.at](https://date.nager.at/) | Georgian holidays | ~365d |
| [Groq](https://groq.com/) | AI assistant (Llama 3.3) | None |

### Android
- **Kotlin** — WebView wrapper with native splash screen
- **Gradle** — Version catalog, Google + Jitpack repos

---

## ⚡ Performance

- **Parallel API calls** via `curl_multi_exec()` — cuts page load by ~65%
- **Multi-tier cache** — File-based JSON with per-endpoint TTL
- **Lazy-loaded assets** — Fonts, icons, Chart.js deferred
- **Responsive design** — Mobile-first, 44×44px touch targets

---

## 📦 Installation

### Requirements
- PHP 8.0+
- Apache / Nginx with `mod_rewrite`
- cURL extension enabled
- OpenSSL extension enabled

### Setup

```bash
# 1. Clone the repository
git clone https://github.com/ormotsadze/Grubeli.Ge.git
cd Grubeli.Ge

# 2. Set up API keys (optional)
cp .env.example .env
# Edit .env with your NASA API key (for fire monitoring)

# 3. Serve the app
php -S localhost:8000
# Or point your Apache/Nginx webroot to the project directory

# 4. Open in browser
open http://localhost:8000
```

### Running the Android App

```bash
cd android
./gradlew assembleDebug
# APK generated at: android/app/build/outputs/apk/debug/
```

---

## 🗺️ Project Structure

```
weather/
├── index.php              # Main weather page
├── functions.php           # Core: API, Cache, Helpers (734 lines)
├── header.php              # <head> + navigation
├── footer.php              # Footer + bottom drawer
├── historical-weather.php  # 80-year climate archive
├── save_location.php       # Cookie-based location save
├── about.php               # About page
├── contact.php             # Contact page
├── privacy.php             # Privacy policy
├── jobs.php                # Job listings
├── global-time.php         # World clock
├── holidays.php            # Georgian holidays calendar
├── cities.json             # 97 Georgian cities
│
├── ai/
│   ├── config.php          # Environment parser
│   ├── ai_helper.php       # Groq API wrapper
│   ├── ai-suggest.php      # AI suggestion engine
│   ├── send_message.php    # AJAX endpoint
│   └── quotes.php          # Random quotes
│
├── css/
│   └── app.css             # Custom styles (1914 lines)
│
├── js/
│   └── geolocation.js      # Geo + Android bridge
│
├── icons/                  # 100+ SVG weather icons
├── fonts/                  # BPG Georgian fonts
├── images/                 # Static images
│
├── android/                # Android app (Kotlin/Gradle)
│   ├── app/
│   ├── build.gradle.kts
│   └── settings.gradle.kts
│
├── .gitignore
├── .htaccess               # Apache cache headers
└── README.md               # This file
```

---

## 🔒 Security Notes

- **SSL Verification** — Enabled for all external API calls (fixed per analysis)
- **Cache** — Stored outside webroot (`../cache/`)
- **Session** — Basic protection with 5s AI throttle
- **CORS** — Nominatim User-Agent includes contact email
- **CSRF** — Basic implementation on save/endpoint

See [SECURITY.md](SECURITY.md) for full details.

---

## 🌐 API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `index.php` | GET | Main weather page (lat/lon params) |
| `historical-weather.php` | GET | Historical data query |
| `save_location.php` | POST | Save user location to cookie |
| `ai/send_message.php` | POST | AI assistant chat |
| `ai/ai-suggest.php` | GET | AI weather suggestions |
| `global-time.php` | GET | World time zones |

---

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) first.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing`)
5. Open a Pull Request

---

## 📄 License

This project is free software: you can redistribute it and/or modify it under the terms of the **GNU General Public License v3.0** (GPLv3). See the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- [Open-Meteo](https://open-meteo.com/) for free weather & air quality API
- [OpenStreetMap](https://www.openstreetmap.org/) / Nominatim for geocoding
- [USGS](https://earthquake.usgs.gov/) for earthquake data
- [NASA FIRMS](https://firms.modaps.eosdis.nasa.gov/) for fire alerts
- [Groq](https://groq.com/) for AI inference
- [BPG Fonts](https://fonts.ge/) for Georgian typefaces
- [Bootstrap 5](https://getbootstrap.com/) for responsive framework

---

<p align="center">Made with ❤️ in Georgia 🇬🇪</p>