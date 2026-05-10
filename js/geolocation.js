function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-location');

    function setBtnLoading() {
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.style.pointerEvents = 'none';
        }
    }

    function setBtnReady() {
        if (btn) {
            btn.style.pointerEvents = 'auto';
            btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i>';
        }
    }

 function sendLocation(lat, lon) {
    // 1. პირველ რიგში, განაახლე localStorage, რომ header.php-მ სწორი მონაცემი დაინახოს
    localStorage.setItem("lat", lat);
    localStorage.setItem("lon", lon);
    
    // 2. ასევე განაახლე ქუქი, რადგან index.php ქუქიებსაც ამოწმებს
    document.cookie = `user_lat=${lat}; path=/; max-age=86400; SameSite=Lax`;
    document.cookie = `user_lon=${lon}; path=/; max-age=86400; SameSite=Lax`;

    fetch('save_location.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({lat: lat, lon: lon})
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (window.AndroidBridge && data.ok) {
            window.AndroidBridge.saveWeatherData(JSON.stringify(data));
        }
        
        // 3. გადატვირთე გვერდი ახალი პარამეტრებით
        window.location.href = 'index.php?lat=' + lat + '&lon=' + lon;
    })
    .catch(function(err) {
        console.error('save_location error', err);
        setBtnReady();
    });
}
    if (btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            // სპინერი დაუყოვნებლივ 
            setBtnLoading();

            const header = document.getElementById('site-header');
            const headerOffset = header ? header.offsetHeight - 90 : 0;
            window.scrollTo({ top: headerOffset, behavior: 'smooth' });

            if (!navigator.geolocation) {
                alert('ბრაუზერმა არ გაამხილა გეოლოკაცია');
                setBtnReady();
                return;
            }

            // ✅ permissions.query() ამოღებულია — getCurrentPosition() პირდაპირ
            // ბრაუზერი/WebView თავად აჩვენებს ნებართვის დიალოგს
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    sendLocation(pos.coords.latitude, pos.coords.longitude);
                },
                function(err) {
                    if (err.code === err.PERMISSION_DENIED) {
                        alert('ლოკაციაზე წვდომა უარყოფილია. გთხოვთ, პარამეტრებში ჩართოთ და სცადოთ თავიდან.');
                    } else if (err.code === err.TIMEOUT) {
                        navigator.geolocation.getCurrentPosition(
                            function(pos) {
                                sendLocation(pos.coords.latitude, pos.coords.longitude);
                            },
                            function() {
                                alert('გეოლოკაციის მიღება შეუძლებელია');
                                setBtnReady();
                            },
                            { enableHighAccuracy: true, timeout: 20000 }
                        );
                        return;
                    } else {
                        alert('გეოლოკაციის მიღება შეუძლებელია');
                    }
                    setBtnReady();
                },
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 }
            );
        });
    }

if (typeof window.GRUBELI_AUTO_PROMPT !== 'undefined' && window.GRUBELI_AUTO_PROMPT === true) {
    // 👇 არ გავუშვათ, თუ უკვე გვაქვს cookie/manual selection
    if (!getCookie('user_lat')) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(pos) { sendLocation(pos.coords.latitude, pos.coords.longitude); },
                function(err) { console.warn('Auto geolocation failed', err); },
                { timeout: 10000 }
            );
        }
    }
}

    function setVh() {
        document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01) + 'px');
    }
    setVh();
    window.addEventListener('resize', setVh);
});

window.setInitialLocation = function(lat, lon) {
    const oldLat = localStorage.getItem("lat");
    const oldLon = localStorage.getItem("lon");

    if (oldLat !== lat.toString() || oldLon !== lon.toString()) {
        localStorage.setItem("lat", lat);
        localStorage.setItem("lon", lon);
        
        document.cookie = `user_lat=${lat}; path=/; max-age=31536000; SameSite=None; Secure`;
        document.cookie = `user_lon=${lon}; path=/; max-age=31536000; SameSite=None; Secure`;
        
        window.location.href = `index.php?lat=${lat}&lon=${lon}`; 
    }
};