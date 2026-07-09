
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
    const shuffleBtn = document.getElementById('shuffle-btn');
    const repeatBtn = document.getElementById('repeat-btn');
    const volumeIcon = document.getElementById('volume-icon');

    let isPlaying = false;
    let currentPlaylist = []; 
    let currentTrackIndex = 0; 
    let allAvailableTracks = [];
    let isShuffleEnabled = false;
    let repeatMode = 'off';
    let isMuted = false;
    let previousVolume = 0.7;
    let shuffleOrder = [];
    let shuffleCursor = 0;
    const PLAYER_STATE_KEY = 'vestra_player_state';

    window.currentPlaylist = currentPlaylist;
    window.currentTrackIndex = currentTrackIndex;

    audio.volume = volumeBar.value / 100;
    updateSliderBackground(volumeBar, volumeBar.value);

    // Завантажуємо всі треки при ініціалізації
    fetch('php/get_all_tracks.php')
        .then(r => r.json())
        .then(data => {
            allAvailableTracks = data;
        })
        .catch(e => console.log('Не вдалось завантажити треки:', e));

    function savePlayerState() {
        const state = {
            playlist: currentPlaylist,
            trackIndex: currentTrackIndex,
            isPlaying: isPlaying && audio.src,
            currentTime: audio.currentTime || 0,
            volume: audio.muted ? previousVolume : audio.volume,
            isMuted: audio.muted,
            repeatMode: repeatMode,
            isShuffleEnabled: isShuffleEnabled,
            shuffleOrder: shuffleOrder,
            shuffleCursor: shuffleCursor
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
            repeatMode = state.repeatMode === 'track' || state.repeatMode === 'playlist' ? state.repeatMode : 'off';
            isShuffleEnabled = Boolean(state.isShuffleEnabled);
            shuffleOrder = Array.isArray(state.shuffleOrder) ? state.shuffleOrder : [];
            shuffleCursor = Number(state.shuffleCursor) || 0;
            isMuted = Boolean(state.isMuted);
            previousVolume = typeof state.volume === 'number' ? state.volume : volumeBar.value / 100;
            audio.volume = previousVolume;
            audio.muted = isMuted;
            volumeBar.value = isMuted ? 0 : Math.round(previousVolume * 100);
            updateSliderBackground(volumeBar, volumeBar.value);
            updateRepeatButtonUI();
            updateShuffleButtonUI();
            updateMuteButtonUI();

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

    function initializeShuffleState(force = false) {
        if (currentPlaylist.length === 0) {
            shuffleOrder = [];
            shuffleCursor = 0;
            return;
        }

        const needsInit = force || shuffleOrder.length !== currentPlaylist.length || shuffleCursor < 0 || shuffleCursor >= currentPlaylist.length;
        if (!needsInit) return;

        if (!isShuffleEnabled) {
            shuffleOrder = currentPlaylist.map((_, index) => index);
            shuffleCursor = currentTrackIndex >= 0 && currentTrackIndex < currentPlaylist.length ? currentTrackIndex : 0;
            return;
        }

        const order = currentPlaylist.map((_, index) => index);
        for (let i = order.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [order[i], order[j]] = [order[j], order[i]];
        }

        shuffleOrder = order;
        shuffleCursor = order.indexOf(currentTrackIndex);
        if (shuffleCursor === -1) {
            shuffleCursor = 0;
        }
    }

    function loadAndPlay(playlist, startIndex) {
        currentPlaylist = playlist;
        currentTrackIndex = startIndex;
        window.currentPlaylist = currentPlaylist;
        window.currentTrackIndex = currentTrackIndex;
        initializeShuffleState(true);
        playCurrentTrack();
    }

    window.loadAndPlay = loadAndPlay;

    function playCurrentTrack() {
        if (currentPlaylist.length === 0) return;
        initializeShuffleState();
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

    function getNextTrackIndex() {
        if (currentPlaylist.length === 0) return 0;
        initializeShuffleState();
        if (isShuffleEnabled && currentPlaylist.length > 1) {
            shuffleCursor = (shuffleCursor + 1) % shuffleOrder.length;
            return shuffleOrder[shuffleCursor];
        }
        return (currentTrackIndex + 1) % currentPlaylist.length;
    }

    function getPreviousTrackIndex() {
        if (currentPlaylist.length === 0) return 0;
        initializeShuffleState();
        if (isShuffleEnabled && currentPlaylist.length > 1) {
            shuffleCursor = (shuffleCursor - 1 + shuffleOrder.length) % shuffleOrder.length;
            return shuffleOrder[shuffleCursor];
        }
        return (currentTrackIndex - 1 + currentPlaylist.length) % currentPlaylist.length;
    }

    nextBtn.addEventListener('click', () => {
        if (currentPlaylist.length <= 1 && allAvailableTracks.length > 0) {
            currentPlaylist = [...allAvailableTracks];
            currentTrackIndex = 0;
        }

        if (currentPlaylist.length > 1) {
            currentTrackIndex = getNextTrackIndex();
            playCurrentTrack();
        } else if (currentPlaylist.length === 1) {
            currentTrackIndex = 0;
            playCurrentTrack();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentPlaylist.length <= 1 && allAvailableTracks.length > 0) {
            currentPlaylist = [...allAvailableTracks];
            currentTrackIndex = allAvailableTracks.length - 1;
        }

        if (currentPlaylist.length > 1) {
            currentTrackIndex = getPreviousTrackIndex();
            playCurrentTrack();
        } else if (currentPlaylist.length === 1) {
            currentTrackIndex = 0;
            playCurrentTrack();
        }
    });

    audio.addEventListener('ended', () => {
        if (repeatMode === 'track') {
            audio.currentTime = 0;
            audio.play();
            return;
        }

        if (repeatMode === 'playlist') {
            if (currentPlaylist.length <= 1 && allAvailableTracks.length > 0) {
                currentPlaylist = [...allAvailableTracks];
            }

            if (currentPlaylist.length > 1) {
                currentTrackIndex = getNextTrackIndex();
                playCurrentTrack();
            } else if (currentPlaylist.length === 1) {
                currentTrackIndex = 0;
                playCurrentTrack();
            }
            return;
        }

        isPlaying = false;
        updatePlayerUI();
        savePlayerState();
    });

    playPauseBtn.addEventListener('click', () => {
        if (!audio.src || !currentPlaylist.length) {
            // Якщо немає звуку, але є доступні треки - завантажуємо перший
            if (allAvailableTracks.length > 0) {
                currentPlaylist = [...allAvailableTracks];
                currentTrackIndex = 0;
                playCurrentTrack();
            }
            return;
        }

        if (isPlaying) {
            audio.pause();
        } else {
            const playPromise = audio.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(function (e) {
                    console.error('Помилка відтворення:', e);
                    isPlaying = false;
                    updatePlayerUI();
                });
            }
        }
        isPlaying = !isPlaying;
        updatePlayerUI();
        savePlayerState();
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
        previousVolume = volumeBar.value / 100;
        audio.volume = previousVolume;
        audio.muted = false;
        isMuted = false;
        updateMuteButtonUI();
        updateSliderBackground(volumeBar, volumeBar.value);
        savePlayerState();
    });

    function updateMuteButtonUI() {
        if (isMuted || volumeBar.value == 0) {
            volumeIcon.classList.remove('bi-volume-up', 'bi-volume-down');
            volumeIcon.classList.add('bi-volume-mute');
        } else if (previousVolume < 0.5) {
            volumeIcon.classList.remove('bi-volume-up', 'bi-volume-mute');
            volumeIcon.classList.add('bi-volume-down');
        } else {
            volumeIcon.classList.remove('bi-volume-down', 'bi-volume-mute');
            volumeIcon.classList.add('bi-volume-up');
        }
    }

    function toggleMute() {
        if (isMuted) {
            isMuted = false;
            audio.muted = false;
            volumeBar.value = Math.round(previousVolume * 100);
            audio.volume = previousVolume;
        } else {
            isMuted = true;
            audio.muted = true;
            volumeBar.value = 0;
        }
        updateMuteButtonUI();
        updateSliderBackground(volumeBar, volumeBar.value);
        savePlayerState();
    }

    volumeIcon.addEventListener('click', toggleMute);

    function updateRepeatButtonUI() {
        repeatBtn.classList.remove('bi-repeat', 'bi-repeat-1');
        repeatBtn.classList.remove('text-white', 'text-white-50');
        repeatBtn.style.color = '';

        if (repeatMode === 'track') {
            repeatBtn.classList.add('bi-repeat-1', 'text-white');
            repeatBtn.style.color = '#ff6bc1';
        } else if (repeatMode === 'playlist') {
            repeatBtn.classList.add('bi-repeat', 'text-white');
            repeatBtn.style.color = '#ff6bc1';
        } else {
            repeatBtn.classList.add('bi-repeat', 'text-white-50');
        }
    }

    function updateShuffleButtonUI() {
        if (isShuffleEnabled) {
            shuffleBtn.classList.remove('text-white-50');
            shuffleBtn.classList.add('text-white');
            shuffleBtn.style.color = '#ff6bc1';
        } else {
            shuffleBtn.classList.remove('text-white');
            shuffleBtn.classList.add('text-white-50');
            shuffleBtn.style.color = '';
        }
    }

    repeatBtn.addEventListener('click', () => {
        if (repeatMode === 'off') {
            repeatMode = 'playlist';
        } else if (repeatMode === 'playlist') {
            repeatMode = 'track';
        } else {
            repeatMode = 'off';
        }
        updateRepeatButtonUI();
        savePlayerState();
    });

    shuffleBtn.addEventListener('click', () => {
        isShuffleEnabled = !isShuffleEnabled;
        initializeShuffleState(true);
        updateShuffleButtonUI();
        savePlayerState();
    });

    updateRepeatButtonUI();
    updateShuffleButtonUI();
    updateMuteButtonUI();

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

    function getTrackId(track) {
        if (!track) return null;
        if (typeof track.id !== 'undefined' && track.id !== null && track.id !== '') {
            return Number(track.id);
        }
        if (typeof track.song_id !== 'undefined' && track.song_id !== null && track.song_id !== '') {
            return Number(track.song_id);
        }
        return null;
    }

    function setFavoriteHeartState(isFavorite) {
        if (isFavorite) {
            playerHeart.classList.replace('bi-heart', 'bi-heart-fill');
            playerHeart.style.color = '#ff6bc1';
        } else {
            playerHeart.classList.replace('bi-heart-fill', 'bi-heart');
            playerHeart.style.color = '';
        }
    }

    function updateLocalFavorites(track, isFavorite) {
        const key = getFavoriteKey(track);
        if (!key) return;

        let favs = JSON.parse(localStorage.getItem('vestra_favorites')) || [];
        const exists = favs.some(item => {
            if (typeof item === 'string') return item === key;
            return getFavoriteKey(item) === key;
        });

        if (isFavorite && !exists) {
            favs.push(track);
        } else if (!isFavorite && exists) {
            favs = favs.filter(item => getFavoriteKey(item) !== key);
        }

        localStorage.setItem('vestra_favorites', JSON.stringify(favs));
    }

    async function checkFavoriteStatus(trackOrFilename) {
        const track = trackOrFilename && typeof trackOrFilename === 'object' ? trackOrFilename : null;
        const key = getFavoriteKey(trackOrFilename);
        if (!key) {
            setFavoriteHeartState(false);
            return;
        }

        const trackId = getTrackId(track);
        if (trackId) {
            try {
                const formData = new FormData();
                formData.append('song_id', trackId);
                formData.append('check_only', '1');

                const response = await fetch('favorite_actions.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const result = await response.text();
                    setFavoriteHeartState(result.trim() === 'favorite');
                    updateLocalFavorites(track || trackOrFilename, result.trim() === 'favorite');
                    return;
                }
            } catch (error) {
                console.error('Не вдалося перевірити улюблене:', error);
            }
        }

        let favs = JSON.parse(localStorage.getItem('vestra_favorites')) || [];
        const isFavorite = favs.some(item => {
            if (typeof item === 'string') return item === key;
            return getFavoriteKey(item) === key;
        });

        setFavoriteHeartState(isFavorite);
    }

    async function toggleFavoritePlayer() {
        const currentTrack = currentPlaylist[currentTrackIndex] || allAvailableTracks.find(track => track.url === audio.src) || null;
        if (!currentTrack) return;

        const trackId = getTrackId(currentTrack);
        if (trackId) {
            try {
                const formData = new FormData();
                formData.append('song_id', trackId);

                const response = await fetch('favorite_actions.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const result = await response.text();
                    const isFavorite = result.trim() === 'added';
                    setFavoriteHeartState(isFavorite);
                    updateLocalFavorites(currentTrack, isFavorite);
                    if (window.renderFavoritesPage) window.renderFavoritesPage();
                    if (window.renderFavoritePlaylistCard) window.renderFavoritePlaylistCard();
                    return;
                }
            } catch (error) {
                console.error('Не вдалося змінити стан улюбленого:', error);
            }
        }

        const key = getFavoriteKey(currentTrack);
        let favs = JSON.parse(localStorage.getItem('vestra_favorites')) || [];
        const isFavorite = favs.some(item => {
            if (typeof item === 'string') return item === key;
            return getFavoriteKey(item) === key;
        });

        if (isFavorite) {
            favs = favs.filter(item => getFavoriteKey(item) !== key);
            setFavoriteHeartState(false);
        } else {
            favs.push(currentTrack);
            setFavoriteHeartState(true);
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