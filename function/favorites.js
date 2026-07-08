(function () {
    const FAVORITES_STORAGE_KEY = 'vestra_favorites';

    function getFavorites() {
        try {
            const raw = localStorage.getItem(FAVORITES_STORAGE_KEY);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            return [];
        }
    }

    function saveFavorites(items) {
        localStorage.setItem(FAVORITES_STORAGE_KEY, JSON.stringify(items));
    }

    function normalizeTrack(track) {
        if (!track) return null;
        if (typeof track === 'string') {
            return { filename: track, title: track, artist: 'Vestra', url: '' };
        }

        return {
            filename: track.filename || '',
            title: track.title || 'Без назви',
            artist: track.artist || 'Vestra',
            url: track.url || '',
            duration: track.duration || ''
        };
    }

    function getTrackKey(track) {
        const normalized = normalizeTrack(track);
        if (!normalized) return '';
        return normalized.filename || normalized.url || `${normalized.title}-${normalized.artist}`;
    }

    function isFavorite(track) {
        const key = getTrackKey(track);
        if (!key) return false;
        return getFavorites().some(item => getTrackKey(item) === key);
    }

    function updateFavoriteHeartUI(isFavorite) {
        const heart = document.getElementById('player-heart');
        if (!heart) return;
        heart.classList.toggle('bi-heart-fill', isFavorite);
        heart.classList.toggle('bi-heart', !isFavorite);
        heart.style.color = isFavorite ? '#ff6bc1' : '';
    }

    function toggleFavoriteTrack(track) {
        const normalized = normalizeTrack(track);
        if (!normalized) return false;

        const key = getTrackKey(normalized);
        const favorites = getFavorites();
        const exists = favorites.some(item => getTrackKey(item) === key);

        const nextFavorites = exists
            ? favorites.filter(item => getTrackKey(item) !== key)
            : [...favorites, normalized];

        saveFavorites(nextFavorites);
        updateFavoriteHeartUI(!exists);
        renderFavoritePlaylistCard();
        renderFavoritesPage();
        return !exists;
    }

    function checkFavoriteStatus(track) {
        const normalized = normalizeTrack(track);
        updateFavoriteHeartUI(isFavorite(normalized));
    }

    function toggleFavoritePlayer() {
        const track = window.currentPlaylist?.[window.currentTrackIndex];
        if (!track) return;
        toggleFavoriteTrack(track);
    }

    function renderFavoritePlaylistCard() {
        const card = document.getElementById('favorites-playlist-card');
        if (!card) return;

        const favorites = getFavorites();
        const count = favorites.length;
        const titleEl = card.querySelector('[data-favorites-title]');
        const countEl = card.querySelector('[data-favorites-count]');
        const badgeEl = card.querySelector('[data-favorites-badge]');

        if (titleEl) titleEl.textContent = 'Улюблені';
        if (countEl) countEl.textContent = count === 0 ? 'Поки порожній' : `${count} ${count === 1 ? 'трек' : 'треки'}`;
        if (badgeEl) badgeEl.textContent = 'Не можна видалити';
    }

    function renderFavoritesPage() {
        const list = document.getElementById('favorites-tracks-list');
        if (!list) return;

        const favorites = getFavorites();
        const countEl = document.getElementById('favorites-count');
        const durationEl = document.getElementById('favorites-duration');

        if (countEl) {
            countEl.textContent = `${favorites.length} ${favorites.length === 1 ? 'трек' : 'треки'}`;
        }

        if (durationEl) {
            const minutes = favorites.length * 3 + 30;
            durationEl.textContent = `${Math.floor(minutes / 60)} хв ${minutes % 60} сек`;
        }

        if (!favorites.length) {
            list.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-heart text-white-50 mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-white">Поки що тут пусто</h5>
                    <p class="text-white-50">Натисніть на сердечко біля треку, щоб додати його в цей плейлист.</p>
                </div>`;
            return;
        }

        list.innerHTML = favorites.map((track, index) => `
            <div class="track-item d-flex align-items-center p-2 rounded-3 mb-2" style="background: rgba(255,255,255,0.03); transition: 0.3s;">
                <div class="text-white-50 text-center fw-bold" style="width: 50px;">${index + 1}</div>
                <div class="d-flex align-items-center flex-grow-1">
                    <div class="rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #d1228f, #8a43f2); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                        <i class="bi bi-music-note text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-white">${track.title}</h6>
                        <small class="text-white-50" style="font-size: 0.75rem;">${track.artist}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-heart-fill me-3" style="color: #ff6bc1; font-size: 0.9rem;"></i>
                    <button class="btn btn-sm rounded-pill text-white" style="background: rgba(255,255,255,0.08);" data-play-track="${index}">Відтворити</button>
                </div>
            </div>`).join('');

        list.querySelectorAll('[data-play-track]').forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.getAttribute('data-play-track'));
                if (window.loadAndPlay) {
                    window.loadAndPlay(getFavorites(), index);
                }
            });
        });
    }

    function initFavoritesUI() {
        if (window.currentPlaylist?.length) {
            checkFavoriteStatus(window.currentPlaylist[window.currentTrackIndex]);
        }
        renderFavoritePlaylistCard();
        renderFavoritesPage();
    }

    document.addEventListener('DOMContentLoaded', initFavoritesUI);
    window.addEventListener('storage', initFavoritesUI);

    window.FAVORITES_STORAGE_KEY = FAVORITES_STORAGE_KEY;
    window.getFavorites = getFavorites;
    window.toggleFavoriteTrack = toggleFavoriteTrack;
    window.toggleFavoritePlayer = toggleFavoritePlayer;
    window.checkFavoriteStatus = checkFavoriteStatus;
    window.renderFavoritesPage = renderFavoritesPage;
    window.renderFavoritePlaylistCard = renderFavoritePlaylistCard;
    window.initFavoritesUI = initFavoritesUI;
})();
