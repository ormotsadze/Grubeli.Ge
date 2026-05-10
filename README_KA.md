# 🌤️ Grubeli.ge — ქართული ამინდის პლატფორმა

![Grubeli.ge](images/og-preview.png)

> **რეალური დროის ამინდი, ჰაერის ხარისხი, მიწისძვრების გაფრთხილება, ხანძრების მონიტორინგი, AI ასისტენტი და 80-წლიანი კლიმატის ისტორია — ყველაფერი საქართველოსთვის.**

---

## 🚀 ფუნქციები

| ფუნქცია | აღწერა |
|---------|---------|
| **📍 მიმდინარე ამინდი** | რეალური დროის ტემპერატურა, ტენიანობა, ქარი, UV ინდექსი, მზის ამოსვლა/ჩასვლა |
| **📊 10-დღიანი პროგნოზი** | ყოველდღიური მაქს/მინ ტემპერატურები ამინდის ხატულებით |
| **⏰ საათობრივი პროგნოზი** | 12-საათიანი დეტალები ნალექის ალბათობით |
| **💨 ჰაერის ხარისხი (AQI)** | PM2.5, PM10 და US AQI ინდექსი |
| **📜 80-წლიანი კლიმატის ისტორია** | ისტორიული ამინდის მონაცემები 1940-დან დღემდე |
| **🌍 97+ ქალაქი** | ძებნა და ავტო-აღმოჩენა ორენოვანი მხარდაჭერით (ქარ/ინგ) |
| **🌋 მიწისძვრების გაფრთხილება** | რეალური დროის M4.0+ მიწისძვრების შეტყობინებები |
| **🔥 ხანძრების მონიტორინგი** | NASA FIRMS თანამგზავრული ხანძრების გაფრთხილებები |
| **🤖 AI ამინდის ასისტენტი** | Groq-ზე მომუშავე (Llama 3.3) საუბრის რეჟიმის ამინდის Q&A |
| **🌙 მთვარის ფაზები** | მთვარის ამოსვლა, ჩასვლა და ფაზების თვალყურის დევნება |
| **📱 მრავალპლატფორმული** | ვებ + Android (Kotlin WebView) აპლიკაცია |
| **🌐 ორენოვანი** | სრული ქართული ინტერფეისი + ინგლისური API პასუხები |

---

## 🧰 ტექნოლოგიები

### ფრონტენდი
- **HTML5 / CSS3** — Bootstrap 5, მორგებული responsive CSS
- **JavaScript** — ნეიტივ JS (jQuery-ს გარეშე), Geolocation API
- **SVG ხატულები** — 100+ მორგებული ამინდის ხატულა
- **ფონტები** — BPG Arial, BPG NinoMtavruli (ქართული), Poppins

### ბექენდი
- **PHP 8.x** — ძირითადი ბექენდი პარალელური `curl_multi` HTTP-ით
- **JSON ფაილური ქეში** — 600წმ-86400წმ TTL მონაცემთა ტიპის მიხედვით
- **სესიაზე დაფუძნებული throttle** — AI ენდფოინტის დაცვა

### APIs
| API | გამოყენება | ქეშის TTL |
|-----|-----------|-----------|
| [Open-Meteo](https://open-meteo.com/) | ამინდის პროგნოზი + ჰაერის ხარისხი | 600წმ |
| [Open-Meteo Archive](https://archive-api.open-meteo.com/) | ისტორიული მონაცემები (80წ) | მუდმივი |
| [Nominatim (OSM)](https://nominatim.openstreetmap.org/) | რევერსული გეოკოდირება | 86400წმ |
| [USGS](https://earthquake.usgs.gov/) | მიწისძვრების გაფრთხილებები | 900წმ |
| [NASA FIRMS](https://firms.modaps.eosdis.nasa.gov/) | ხანძრების გაფრთხილებები | 3600წმ |
| [Nager.at](https://date.nager.at/) | ქართული დღესასწაულები | ~365დღ |
| [Groq](https://groq.com/) | AI ასისტენტი (Llama 3.3) | არა |

### Android
- **Kotlin** — WebView გარსი ნეიტივ splash ეკრანით
- **Gradle** — Version catalog, Google + Jitpack რეპოები

---

## ⚡ შესრულება

- **პარალელური API გამოძახებები** `curl_multi_exec()`-ის საშუალებით — ამცირებს გვერდის ჩატვირთვას ~65%-ით
- **მრავალდონიანი ქეში** — ფაილური JSON პერ-ენდფოინტ TTL-ით
- **Lazy-ჩატვირთული რესურსები** — ფონტები, ხატულები, Chart.js გადადებული
- **რესპონსივ დიზაინი** — მობილურ-პირველი, 44×44px ტაჩ სამიზნეები

---

## 📦 ინსტალაცია

### მოთხოვნები
- PHP 8.0+
- Apache / Nginx `mod_rewrite`-ით
- cURL გაფართოება ჩართული
- OpenSSL გაფართოება ჩართული

### დაყენება

```bash
# 1. კლონირება
git clone https://github.com/ormotsadze/Grubeli.Ge.git
cd Grubeli.Ge

# 2. API გასაღებების კონფიგურაცია (არასავალდებულო)
cp .env.example .env
# გაასწორეთ .env NASA API გასაღებით (ხანძრების მონიტორინგისთვის)

# 3. გაუშვით
php -S localhost:8000
# ან მიუთითეთ Apache/Nginx webroot-ი პროექტის დირექტორიაზე

# 4. გახსენით ბრაუზერში
open http://localhost:8000
```

### Android აპლიკაციის გაშვება

```bash
cd android
./gradlew assembleDebug
# APK გენერირებულია: android/app/build/outputs/apk/debug/
```

---

## 🗺️ პროექტის სტრუქტურა

```
weather/
├── index.php              # მთავარი ამინდის გვერდი
├── functions.php           # ძირითადი: API, ქეში, Helpers (734 სტრიქონი)
├── header.php              # <head> + ნავიგაცია
├── footer.php              # Footer + ქვედა drawer
├── historical-weather.php  # 80-წლიანი კლიმატის არქივი
├── save_location.php       # Cookie-ზე დაფუძნებული ლოკაციის შენახვა
├── about.php               # ჩვენ შესახებ
├── contact.php             # კონტაქტი
├── privacy.php             # კონფიდენციალურობის პოლიტიკა
├── jobs.php                # ვაკანსიები
├── global-time.php         # მსოფლიო საათი
├── holidays.php            # ქართული დღესასწაულების კალენდარი
├── cities.json             # 97 ქართული ქალაქი
│
├── ai/
│   ├── config.php          # Environment parser
│   ├── ai_helper.php       # Groq API wrapper
│   ├── ai-suggest.php      # AI შემოთავაზების ძრავა
│   ├── send_message.php    # AJAX ენდფოინტი
│   └── quotes.php          # შემთხვევითი ციტატები
│
├── css/
│   └── app.css             # მორგებული სტილები (1914 სტრიქონი)
│
├── js/
│   └── geolocation.js      # გეოლოკაცია + Android ხიდი
│
├── icons/                  # 100+ SVG ამინდის ხატულა
├── fonts/                  # BPG ქართული ფონტები
├── images/                 # სტატიკური სურათები
│
├── android/                # Android აპი (Kotlin/Gradle)
│   ├── app/
│   ├── build.gradle.kts
│   └── settings.gradle.kts
│
├── .gitignore
├── .htaccess               # Apache ქეშის ჰედერები
├── README.md               # ინგლისური README
└── README_KA.md            # ქართული README (ეს ფაილი)
```

---

## 🔒 უსაფრთხოების ჩანაწერები

- **SSL ვერიფიკაცია** — ჩართულია ყველა გარე API გამოძახებისთვის (გასწორებულია ანალიზის მიხედვით)
- **ქეში** — შენახულია webroot-ის გარეთ (`../cache/`)
- **სესია** — ძირითადი დაცვა 5წმ AI throttle-ით
- **CORS** — Nominatim User-Agent შეიცავს საკონტაქტო email-ს
- **CSRF** — ძირითადი იმპლემენტაცია save/endpoint-ზე

იხილეთ [SECURITY.md](SECURITY.md) სრული დეტალებისთვის.

---

## 🌐 API ენდფოინტები

| ენდფოინტი | მეთოდი | აღწერა |
|-----------|--------|---------|
| `index.php` | GET | მთავარი ამინდის გვერდი (lat/lon პარამეტრები) |
| `historical-weather.php` | GET | ისტორიული მონაცემების მოთხოვნა |
| `save_location.php` | POST | მომხმარებლის ლოკაციის შენახვა cookie-ში |
| `ai/send_message.php` | POST | AI ასისტენტის ჩატი |
| `ai/ai-suggest.php` | GET | AI ამინდის შემოთავაზებები |
| `global-time.php` | GET | მსოფლიო დროის ზონები |

---

## 🤝 კონტრიბუცია

კონტრიბუცია მისასალმებელია! გთხოვთ, ჯერ წაიკითხეთ [CONTRIBUTING.md](CONTRIBUTING.md).

1. გააკეთეთ Fork რეპოზიტორიის
2. შექმენით თქვენი feature ბრანჩი (`git checkout -b feature/amazing`)
3. დააკომიტეთ ცვლილებები (`git commit -m 'Add amazing feature'`)
4. Push გაუკეთეთ ბრანჩს (`git push origin feature/amazing`)
5. გახსენით Pull Request

---

## 📄 ლიცენზია

ეს პროექტი არის თავისუფალი პროგრამული უზრუნველყოფა: შეგიძლიათ მისი გავრცელება და/ან შეცვლა **GNU General Public License v3.0** (GPLv3) პირობების შესაბამისად. იხილეთ [LICENSE](LICENSE) ფაილი დეტალებისთვის.

---

## 🙏 მადლობა

- [Open-Meteo](https://open-meteo.com/) უფასო ამინდისა და ჰაერის ხარისხის API-სთვის
- [OpenStreetMap](https://www.openstreetmap.org/) / Nominatim გეოკოდირებისთვის
- [USGS](https://earthquake.usgs.gov/) მიწისძვრების მონაცემებისთვის
- [NASA FIRMS](https://firms.modaps.eosdis.nasa.gov/) ხანძრების გაფრთხილებისთვის
- [Groq](https://groq.com/) AI ინფერენციისთვის
- [BPG Fonts](https://fonts.ge/) ქართული შრიფტებისთვის
- [Bootstrap 5](https://getbootstrap.com/) რესპონსივ ფრეიმვორკისთვის

---

<p align="center">შექმნილია სიყვარულით საქართველოში 🇬🇪</p>