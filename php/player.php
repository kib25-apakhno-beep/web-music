<audio id="main-audio" src=""></audio>

<div class="fixed-bottom mb-4 d-flex justify-content-center" style="z-index: 1050; pointer-events: none;">
    <div class="d-flex align-items-center justify-content-between px-4 py-2" 
         style="background: rgba(6, 4, 16, 0.6); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); 
                border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 50px; 
                width: 95%; max-width: 900px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); pointer-events: auto;">
        
        <div class="d-flex align-items-center" style="width: 30%;">
            <div id="record-disk" class="rounded-circle d-flex justify-content-center align-items-center me-3" 
                 style="width: 55px; height: 55px; background: linear-gradient(135deg, #1a1a2e, #16213e); border: 2px solid rgba(255, 107, 193, 0.5);">
                <div class="rounded-circle" style="width: 12px; height: 12px; background: #060410; border: 1px solid #ff6bc1;"></div>
            </div>
            
            <div class="d-flex flex-column justify-content-center">
                <div class="d-flex align-items-end mb-1">
                    <h6 id="player-title" class="text-white mb-0 me-2 text-truncate" style="font-size: 1rem; font-weight: 700; max-width: 150px;">Оберіть трек</h6>
                    <div id="eq-bars" class="d-flex align-items-end d-none" style="height: 16px;">
                        <div class="eq-bar"></div><div class="eq-bar"></div><div class="eq-bar"></div>
                    </div>
                </div>
                <p id="player-artist" class="text-white-50 mb-0 text-truncate" style="font-size: 0.8rem; max-width: 150px;">Vestra</p>
            </div>
        </div>

        <div class="d-flex flex-column align-items-center" style="width: 40%;">
            <div class="d-flex align-items-center gap-4 mb-1 mt-1">
                <i class="bi bi-shuffle text-white-50 fs-6 player-icon d-none d-md-block" onclick="toggleActive(this)"></i>
                
                <i id="prev-btn" class="bi bi-skip-start-fill text-white fs-4 player-icon"></i>
                
                <div id="play-pause-btn" class="play-pause-btn rounded-circle d-flex justify-content-center align-items-center" 
                     style="width: 48px; height: 48px; background: linear-gradient(135deg, #d1228f, #8a43f2); cursor: pointer;">
                    <i id="play-icon" class="bi bi-play-fill text-white fs-2 ms-1"></i>
                </div>
                
                <i id="next-btn" class="bi bi-skip-end-fill text-white fs-4 player-icon"></i>
                
                <i class="bi bi-repeat text-white-50 fs-6 player-icon d-none d-md-block" onclick="toggleActive(this)"></i>
            </div>
            
            <div class="d-flex align-items-center w-100">
                <span id="current-time" class="text-white-50 me-2" style="font-size: 0.7rem;">0:00</span>
                <input type="range" id="progress-bar" class="custom-range flex-grow-1" min="0" max="100" value="0">
                <span id="total-time" class="text-white-50 ms-2" style="font-size: 0.7rem;">0:00</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end" style="width: 30%;">
            <i id="player-heart" class="bi bi-heart text-white-50 fs-5 player-icon me-4 d-none d-lg-block" onclick="toggleFavoritePlayer()"></i>
            <i class="bi bi-volume-up text-white-50 fs-5 player-icon me-2"></i>
            <div style="width: 80px;">
                <input type="range" id="volume-bar" class="custom-range" min="0" max="100" value="70">
            </div>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // ЧАСТИНА 1: ЛОГІКА МУЗИЧНОГО ПЛЕЄРА
    // ==========================================
    const audio = document.getElementById('main-audio');
    const playPauseBtn = document.getElementById('play-pause-btn');
    const playIcon = document.getElementById('play-icon');
    const progressBar = document.getElementById('progress-bar');
    const volumeBar = document.getElementById('volume-bar');
    const currentTimeEl = document.getElementById('current-time');
    const totalTimeEl = document.getElementById('total-time');
    const recordDisk = document.getElementById('record-disk');
    const eqBars = document.getElementById('eq-bars');
    const playerTitle = document.getElementById('player-title');
    const playerArtist = document.getElementById('player-artist');
    const playerHeart = document.getElementById('player-heart');
    
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    let isPlaying = false;
    let currentPlaylist = []; 
    let currentTrackIndex = 0; 

    audio.volume = volumeBar.value / 100;
    updateSliderBackground(volumeBar, volumeBar.value);

    function loadAndPlay(playlist, startIndex) {
        currentPlaylist = playlist;
        currentTrackIndex = startIndex;
        playCurrentTrack();
    }

    function playCurrentTrack() {
        if (currentPlaylist.length === 0) return;
        const track = currentPlaylist[currentTrackIndex];
        
        audio.src = track.url;
        playerTitle.innerText = track.title;
        playerArtist.innerText = track.artist;
        
        checkFavoriteStatus(track.filename);

        audio.play();
        isPlaying = true;
        updatePlayerUI();
    }

    nextBtn.addEventListener('click', () => {
        if (currentPlaylist.length > 0) {
            currentTrackIndex = (currentTrackIndex + 1) % currentPlaylist.length;
            playCurrentTrack();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentPlaylist.length > 0) {
            currentTrackIndex = (currentTrackIndex - 1 + currentPlaylist.length) % currentPlaylist.length;
            playCurrentTrack();
        }
    });

    audio.addEventListener('ended', () => {
        nextBtn.click();
    });

    playPauseBtn.addEventListener('click', () => {
        if (!audio.src || audio.src.endsWith(window.location.pathname)) return;
        if (isPlaying) { audio.pause(); } else { audio.play(); }
        isPlaying = !isPlaying;
        updatePlayerUI();
    });

    function updatePlayerUI() {
        if (isPlaying) {
            playIcon.classList.replace('bi-play-fill', 'bi-pause-fill');
            playIcon.classList.remove('ms-1');
            recordDisk.classList.add('spinning-record');
            eqBars.classList.remove('d-none');
        } else {
            playIcon.classList.replace('bi-pause-fill', 'bi-play-fill');
            playIcon.classList.add('ms-1');
            recordDisk.classList.remove('spinning-record');
            eqBars.classList.add('d-none');
        }
    }

    audio.addEventListener('timeupdate', () => {
        if (audio.duration) {
            const progressPercent = (audio.currentTime / audio.duration) * 100;
            progressBar.value = progressPercent;
            updateSliderBackground(progressBar, progressPercent);
            currentTimeEl.innerText = formatTime(audio.currentTime);
            totalTimeEl.innerText = formatTime(audio.duration);
        }
    });

    progressBar.addEventListener('input', () => {
        audio.currentTime = (progressBar.value / 100) * audio.duration;
        updateSliderBackground(progressBar, progressBar.value);
    });

    volumeBar.addEventListener('input', () => {
        audio.volume = volumeBar.value / 100;
        updateSliderBackground(volumeBar, volumeBar.value);
    });

    function updateSliderBackground(slider, percentage) {
        slider.style.background = `linear-gradient(to right, #ff6bc1 ${percentage}%, rgba(255, 255, 255, 0.1) ${percentage}%)`;
    }

    function formatTime(seconds) {
        const min = Math.floor(seconds / 60);
        const sec = Math.floor(seconds % 60);
        return `${min}:${sec < 10 ? '0' : ''}${sec}`;
    }

    function toggleActive(el) {
        el.style.color = el.style.color === 'rgb(255, 107, 193)' ? '' : '#ff6bc1';
    }

    function checkFavoriteStatus(filename) {
        let favs = JSON.parse(localStorage.getItem('vestra_favorites')) || [];
        if (favs.includes(filename)) {
            playerHeart.classList.replace('bi-heart', 'bi-heart-fill');
            playerHeart.style.color = '#ff6bc1';
        } else {
            playerHeart.classList.replace('bi-heart-fill', 'bi-heart');
            playerHeart.style.color = '';
        }
    }

    function toggleFavoritePlayer() {
        if (currentPlaylist.length === 0) return;
        const filename = currentPlaylist[currentTrackIndex].filename;
        let favs = JSON.parse(localStorage.getItem('vestra_favorites')) || [];
        
        if (favs.includes(filename)) {
            favs = favs.filter(f => f !== filename);
            playerHeart.classList.replace('bi-heart-fill', 'bi-heart');
            playerHeart.style.color = '';
        } else {
            favs.push(filename);
            playerHeart.classList.replace('bi-heart', 'bi-heart-fill');
            playerHeart.style.color = '#ff6bc1';
        }
        localStorage.setItem('vestra_favorites', JSON.stringify(favs));
    }

    function showComingSoon() {
        alert("Ця функція з'явиться після підключення Бази Даних MySQL! Залишилося зовсім трохи 😉");
    }

    // ==========================================
    // ЧАСТИНА 2: БЕЗШОВНА НАВІГАЦІЯ (AJAX)
    // ==========================================
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;

        const href = link.getAttribute('href');
        
        // Ігноруємо зовнішні лінки та системні файли
        if (!href || href.startsWith('http') || href.startsWith('#') || href.includes('logout.php') || href.includes('delete_track.php') || href.includes('upload')) {
            return;
        }

        e.preventDefault(); 
        loadPage(href);
    });

    window.addEventListener('popstate', function() {
        loadPage(location.pathname.split('/').pop() || 'index.php');
    });

    async function loadPage(url) {
        try {
            const response = await fetch(url);
            const html = await response.text();

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newMain = doc.querySelector('main');
            const currentMain = document.querySelector('main');

            if (newMain && currentMain) {
                // Підміняємо контент
                currentMain.innerHTML = newMain.innerHTML;
                
                history.pushState({}, '', url);
                document.title = doc.title;

                // Перезапускаємо скрипти на новій сторінці
                const scripts = currentMain.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    newScript.textContent = oldScript.textContent;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                window.location.href = url; 
            }
        } catch (error) {
            window.location.href = url; 
        }
    }
</script>