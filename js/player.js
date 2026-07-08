
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
    const PLAYER_STATE_KEY = 'vestra_player_state';

    window.currentPlaylist = currentPlaylist;
    window.currentTrackIndex = currentTrackIndex;

    audio.volume = volumeBar.value / 100;
    updateSliderBackground(volumeBar, volumeBar.value);

    function savePlayerState() {
        const state = {
            playlist: currentPlaylist,
            trackIndex: currentTrackIndex,
            isPlaying: isPlaying && audio.src,
            currentTime: audio.currentTime || 0,
            volume: audio.volume
        };
        localStorage.setItem(PLAYER_STATE_KEY, JSON.stringify(state));
    }

    function restorePlayerState() {
        try {
            const raw = localStorage.getItem(PLAYER_STATE_KEY);
            if (!raw) return;
            const state = JSON.parse(raw);
            if (!state || !Array.isArray(state.playlist) || state.playlist.length === 0) return;

            currentPlaylist = state.playlist;
            currentTrackIndex = Number(state.trackIndex) || 0;
            window.currentPlaylist = currentPlaylist;
            window.currentTrackIndex = currentTrackIndex;
            audio.volume = typeof state.volume === 'number' ? state.volume : volumeBar.value / 100;
            volumeBar.value = Math.round(audio.volume * 100);
            updateSliderBackground(volumeBar, volumeBar.value);

            const track = currentPlaylist[currentTrackIndex];
            if (!track || !track.url) return;

            playerTitle.innerText = track.title;
            playerArtist.innerText = track.artist;
            audio.src = track.url;
            audio.load();
            audio.currentTime = Number(state.currentTime) || 0;
            checkFavoriteStatus(track.filename);

            if (state.isPlaying) {
                const playPromise = audio.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(function () {
                        isPlaying = false;
                        updatePlayerUI();
                    });
                }
                isPlaying = true;
                updatePlayerUI();
            } else {
                isPlaying = false;
                updatePlayerUI();
            }
        } catch (error) {
            console.error(error);
        }
    }

    function loadAndPlay(playlist, startIndex) {
        currentPlaylist = playlist;
        currentTrackIndex = startIndex;
        window.currentPlaylist = currentPlaylist;
        window.currentTrackIndex = currentTrackIndex;
        playCurrentTrack();
    }

    window.loadAndPlay = loadAndPlay;

    function playCurrentTrack() {
        if (currentPlaylist.length === 0) return;
        const track = currentPlaylist[currentTrackIndex];
        if (!track || !track.url) return;

        audio.src = track.url;
        audio.load();
        playerTitle.innerText = track.title;
        playerArtist.innerText = track.artist;

        checkFavoriteStatus(track.filename);

        const playPromise = audio.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(function () {
                isPlaying = false;
                updatePlayerUI();
            });
        }
        isPlaying = true;
        updatePlayerUI();
        savePlayerState();
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
            savePlayerState();
        }
    });

    audio.addEventListener('play', () => {
        isPlaying = true;
        updatePlayerUI();
        savePlayerState();
    });

    audio.addEventListener('pause', () => {
        isPlaying = false;
        updatePlayerUI();
        savePlayerState();
    });

    audio.addEventListener('loadedmetadata', () => {
        savePlayerState();
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

    window.addEventListener('beforeunload', savePlayerState);
    window.addEventListener('DOMContentLoaded', restorePlayerState);

    function getFavoriteKey(trackOrFilename) {
        if (typeof trackOrFilename === 'string') {
            return trackOrFilename;
        }
        if (!trackOrFilename) return '';
        return trackOrFilename.filename || trackOrFilename.url || '';
    }

    function checkFavoriteStatus(trackOrFilename) {
        const key = getFavoriteKey(trackOrFilename);
        let favs = JSON.parse(localStorage.getItem('vestra_favorites')) || [];
        const isFavorite = favs.some(item => {
            if (typeof item === 'string') return item === key;
            return getFavoriteKey(item) === key;
        });

        if (isFavorite) {
            playerHeart.classList.replace('bi-heart', 'bi-heart-fill');
            playerHeart.style.color = '#ff6bc1';
        } else {
            playerHeart.classList.replace('bi-heart-fill', 'bi-heart');
            playerHeart.style.color = '';
        }
    }

    function toggleFavoritePlayer() {
        if (currentPlaylist.length === 0) return;
        const track = currentPlaylist[currentTrackIndex];
        const key = getFavoriteKey(track);
        let favs = JSON.parse(localStorage.getItem('vestra_favorites')) || [];
        const isFavorite = favs.some(item => {
            if (typeof item === 'string') return item === key;
            return getFavoriteKey(item) === key;
        });

        if (isFavorite) {
            favs = favs.filter(item => getFavoriteKey(item) !== key);
            playerHeart.classList.replace('bi-heart-fill', 'bi-heart');
            playerHeart.style.color = '';
        } else {
            favs.push(track);
            playerHeart.classList.replace('bi-heart', 'bi-heart-fill');
            playerHeart.style.color = '#ff6bc1';
        }

        localStorage.setItem('vestra_favorites', JSON.stringify(favs));
        if (window.renderFavoritesPage) window.renderFavoritesPage();
        if (window.renderFavoritePlaylistCard) window.renderFavoritePlaylistCard();
    }

    window.toggleFavoritePlayer = toggleFavoritePlayer;
    window.checkFavoriteStatus = checkFavoriteStatus;

    function showComingSoon() {
        alert("Ця функція з'явиться після підключення Бази Даних MySQL! Залишилося зовсім трохи 😉");
    }

    // ==========================================
    // ЧАСТИНА 2: НАВІГАЦІЯ ПО СТОРІНКАХ
    // ==========================================
    // Залишаємо стандартну навігацію браузера, щоб сторінки не ламалися
    // під час переходів між розділами сайту.