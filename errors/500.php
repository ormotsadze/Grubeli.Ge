<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - სერვერის შეცდომა | Grubeli.ge</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/app.css?ver=1.1.0" rel="stylesheet">
    <link href="../icons/fontawesome/css/all.min.css?v-1.0.0" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@800;900&display=swap" rel="stylesheet">
    <style>
        .error-404-page {
            min-height: 100vh;
            background: #18181d;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .bg-clouds {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .bg-cloud {
            position: absolute;
            font-size: 6rem;
            opacity: 0.03;
            animation: driftCloud 20s linear infinite;
        }

        .bg-cloud:nth-child(1) { top: 5%; left: -10%; animation-duration: 25s; font-size: 8rem; }
        .bg-cloud:nth-child(2) { top: 30%; left: -20%; animation-duration: 30s; animation-delay: -5s; font-size: 5rem; }
        .bg-cloud:nth-child(3) { top: 60%; left: -15%; animation-duration: 22s; animation-delay: -10s; font-size: 7rem; }
        .bg-cloud:nth-child(4) { top: 80%; left: -25%; animation-duration: 35s; animation-delay: -15s; font-size: 4rem; }

        @keyframes driftCloud {
            0% { transform: translateX(-20vw) rotate(0deg); opacity: 0.04; }
            50% { opacity: 0.08; }
            100% { transform: translateX(110vw) rotate(5deg); opacity: 0.02; }
        }

        .error-card-404 {
            max-width: 560px;
            width: 100%;
            background: rgba(27, 27, 27, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 40px;
            padding: 50px 40px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .error-icon-main {
            font-size: 5rem;
            line-height: 1;
            margin-bottom: 10px;
            animation: wobble 3s ease-in-out infinite;
        }

        @keyframes wobble {
            0%, 100% { transform: rotate(0deg) scale(1); }
            25% { transform: rotate(-8deg) scale(1.1); }
            75% { transform: rotate(8deg) scale(1.05); }
        }

        .error-code {
            font-family: 'Poppins', sans-serif;
            font-weight: 900;
            font-size: 8rem;
            line-height: 1;
            background: linear-gradient(135deg, #ff6b35 0%, #e74c3c 50%, #9b51e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            margin: 5px 0;
            letter-spacing: -4px;
        }

        .error-title {
            font-family: 'BPG NinoMtavruli', serif;
            font-size: 1.5rem;
            color: #c6d1e7;
            margin: 10px 0 15px;
        }

        .error-message {
            color: #9ba8c5;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 10px;
            padding: 0 10px;
        }

    

        .btn-404-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            padding: 14px 32px;
            border-radius: 50px;
            background: linear-gradient(135deg, #ff6b35, #e74c3c) !important;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(231, 76, 60, 0.3);
        }

        .btn-404-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(231, 76, 60, 0.5);
            color: #fff;
        }

        .btn-404-home i {
            font-size: 0.9rem;
        }

        .raindrops {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .raindrop {
            position: absolute;
            color: rgba(255, 107, 53, 0.06);
            font-size: 1.2rem;
            animation: drop linear infinite;
        }

        @keyframes drop {
            0% { transform: translateY(-20px) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(100vh) rotate(10deg); opacity: 0; }
        }

        @media (max-width: 576px) {
            .error-card-404 {
                padding: 35px 25px;
                border-radius: 28px;
            }
            .error-code {
                font-size: 5.5rem;
            }
            .error-title {
                font-size: 1.2rem;
            }
            .error-message {
                font-size: 0.9rem;
                padding: 0;
            }
            .error-icon-main {
                font-size: 3.5rem;
            }
        }
    </style>
</head>
<body>

<div class="error-404-page">

    <div class="bg-clouds">
        <div class="bg-cloud">☁️</div>
        <div class="bg-cloud">🌧️</div>
        <div class="bg-cloud">⛅</div>
        <div class="bg-cloud">🌪️</div>
    </div>

    <div class="raindrops" id="raindrops"></div>

    <div class="error-card-404">

        <div class="error-icon-main">⚡️</div>

        <div class="error-code">500</div>

        <div class="error-title">სერვერის პრობლემა</div>

        <div class="error-message">
         ჩვენ უკვე ვმუშაობთ პრობლემის მოგვარებაზე! <br>
        </div>

        <a href="../index.php" class="btn-404-home">
            <i class="fas fa-home"></i> მთავარზე დაბრუნება
        </a>

    </div>
</div>

<script>
    (function createRaindrops() {
        const container = document.getElementById('raindrops');
        const symbols = ['💥', '🔥', '⚡', '🌩️'];
        for (let i = 0; i < 20; i++) {
            const drop = document.createElement('span');
            drop.className = 'raindrop';
            drop.textContent = symbols[Math.floor(Math.random() * symbols.length)];
            drop.style.left = Math.random() * 100 + '%';
            drop.style.fontSize = (0.7 + Math.random() * 1) + 'rem';
            drop.style.animationDuration = (4 + Math.random() * 6) + 's';
            drop.style.animationDelay = (Math.random() * 8) + 's';
            container.appendChild(drop);
        }
    })();
</script>

</body>
</html>