<?php 
session_start(); 
require_once 'function/db_helpers.php';
ensureUserSessionFields();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

$userId = getUserId();
$playlists = getPlaylistsForUser($userId);
$customPlaylists = [];
foreach ($playlists as $playlist) {
    $normalizedTitle = normalizePlaylistTitle($playlist['title']);
    if ($normalizedTitle === 'улюблені' || $normalizedTitle === 'улюблене' || $normalizedTitle === 'favorites' || $normalizedTitle === 'favourites') {
        continue;
    }
    $customPlaylists[] = $playlist;
}

$favoritesPlaylistId = getOrCreateFavoritesPlaylist($userId);
$favoritesSongs = getPlaylistSongs($favoritesPlaylistId);
$favoritesCount = count($favoritesSongs);
$userSongs = getSongsForUser();
$hasMyMusicPlaylist = count($userSongs) > 0;

// Precompute counts for custom playlists
$playlistCounts = [];
foreach ($customPlaylists as $p) {
    $plistSongs = getPlaylistSongs((int)$p['id']);
    $playlistCounts[(int)$p['id']] = count($plistSongs);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['playlist_name']) && trim($_POST['playlist_name']) !== '') {
        $title = trim($_POST['playlist_name']);
        createPlaylist($userId, $title);
        header('Location: playlists.php');
        exit();
    }

    if (isset($_POST['delete_playlist_id'])) {
        $playlistIdToDelete = (int)$_POST['delete_playlist_id'];
        deletePlaylist($playlistIdToDelete, $userId);
        header('Location: playlists.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мої Плейлисти - Vestra</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <style>
        .playlist-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            overflow: hidden;
        }
        .playlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(209, 34, 143, 0.3);
            border-color: rgba(209, 34, 143, 0.5);
        }
        .playlist-img {
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container mt-5 pt-3 mb-5 position-relative">
        <h2 class="fw-bold text-white mb-4">Мої Плейлисти</h2>
        
        <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="playlist-card h-100 d-flex flex-column align-items-center justify-content-center p-4" style="border: 2px dashed rgba(255,255,255,0.2); background: transparent;" data-bs-toggle="modal" data-bs-target="#createPlaylistModal">
                    <i class="bi bi-plus-circle text-white-50 mb-2" style="font-size: 2.5rem;"></i>
                    <h6 class="text-white-50 fw-bold">Створити новий</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="playlist-card h-100" id="favorites-playlist-card" onclick="window.location.href='favorites.php'" style="cursor: pointer;">
                    <div class="playlist-img" style="background: linear-gradient(135deg, #ff6bc1, #d1228f);">
                        <i class="bi bi-heart-fill text-white" style="font-size: 3rem; opacity: 0.9;"></i>
                    </div>
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white fw-bold mb-1" data-favorites-title>Улюблені</h6>
                                <small class="text-white-50" data-favorites-count><?php echo $favoritesCount; ?> треків</small>
                            </div>
                            <span class="badge rounded-pill text-white-50" style="background: rgba(255,255,255,0.08);" data-favorites-badge>Не можна видалити</span>
                        </div>
                        <div class="text-white-50 small mt-2">Сума тривалості: <span id="favorites-duration">0 хв 0 сек</span></div>
                    </div>
                </div>
            </div>
            
            <?php if ($hasMyMusicPlaylist): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="playlist-card-wrapper h-100 position-relative">
                        <a href="mymusic.php" class="text-decoration-none d-block h-100">
                            <div class="playlist-card h-100">
                                <div class="playlist-img" style="background: linear-gradient(135deg, #d1228f, #8a43f2);">
                                    <i class="bi bi-music-note-list text-white" style="font-size: 3rem; opacity: 0.6;"></i>
                                </div>
                                <div class="p-3">
                                    <h6 class="text-white fw-bold mb-1">Моя музика</h6>
                                    <small class="text-white-50 d-block"><?php echo count($userSongs); ?> треків</small>
                                </div>
                            </div>
                        </a>
                        <button type="button" class="btn btn-sm btn-light position-absolute my-music-play-btn" style="top:10px; right:10px; opacity:0.9;">
                            <i class="bi bi-play-fill"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($customPlaylists as $playlist): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="playlist-card-wrapper h-100 position-relative">
                        <a href="playlist.php?id=<?php echo (int)$playlist['id']; ?>" class="text-decoration-none d-block h-100">
                            <div class="playlist-card h-100">
                                <div class="playlist-img" style="background: linear-gradient(135deg, #2b1055, #7597de);">
                                    <i class="bi bi-music-note-list text-white" style="font-size: 3rem; opacity: 0.5;"></i>
                                </div>
                                        <div class="p-3">
                                                <h6 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($playlist['title']); ?></h6>
                                                <small class="text-white-50 d-block"><?php echo (isset($playlistCounts[(int)$playlist['id']]) ? $playlistCounts[(int)$playlist['id']] : 0) . ' треків'; ?></small>
                                                <small class="text-white-50 d-block mt-1">Сума тривалості: <span class="playlist-duration" data-playlist-id="<?php echo (int)$playlist['id']; ?>">0 хв 0 сек</span></small>
                                            </div>
                            </div>
                        </a>
                        <div class="position-absolute d-flex gap-2" style="top:10px; right:10px;">
                            <button type="button" class="btn btn-sm btn-light play-playlist-btn" data-playlist-id="<?php echo (int)$playlist['id']; ?>" style="opacity:0.9;">
                                <i class="bi bi-play-fill"></i>
                            </button>
                            <form method="POST" onsubmit="return confirm('Видалити цей плейлист?');" class="m-0">
                                <input type="hidden" name="delete_playlist_id" value="<?php echo (int)$playlist['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-light" style="opacity:0.9;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div class="modal fade" id="createPlaylistModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: rgba(10, 8, 24, 0.95); border: 1px solid rgba(255,255,255,0.1);">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Створити плейлист</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <label class="form-label text-white-50">Назва плейлиста</label>
                        <input type="text" name="playlist_name" class="form-control bg-transparent text-white" placeholder="Наприклад: Вечірній вайб" required>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Скасувати</button>
                        <button type="submit" class="btn btn-gradient text-white">Створити</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'php/footer.php'; ?>
    <?php include 'php/player.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const favoritesPlaylistId = <?php echo (int)$favoritesPlaylistId; ?>;
        const myMusicTracks = <?php echo json_encode(array_map(function($song) {
            return [
                'id' => (int)$song['id'],
                'title' => $song['title'],
                'artist' => $song['composer'] ?: 'Vestra',
                'filename' => basename($song['url']),
                'url' => $song['url']
            ];
        }, $userSongs), JSON_UNESCAPED_UNICODE); ?>;

        function formatDuration(totalSeconds) {
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = Math.floor(totalSeconds % 60);
            return minutes + ' хв ' + (seconds < 10 ? '0' : '') + seconds + ' сек';
        }

        function computeAndRenderPlaylistDuration(playlistId, el) {
            if (!playlistId || !el) return;
            fetch('playlist_json.php?id=' + encodeURIComponent(playlistId))
                .then(function (response) { return response.json(); })
                .then(function (tracks) {
                    if (!Array.isArray(tracks) || !tracks.length) {
                        el.textContent = '0 хв 0 сек';
                        return;
                    }

                    let index = 0;
                    let total = 0;
                    function next() {
                        if (index >= tracks.length) {
                            el.textContent = formatDuration(total);
                            return;
                        }
                        const track = tracks[index];
                        const audio = document.createElement('audio');
                        audio.preload = 'metadata';
                        audio.src = track.url;
                        audio.addEventListener('loadedmetadata', function () {
                            total += isFinite(audio.duration) ? audio.duration : 0;
                            index += 1;
                            setTimeout(next, 30);
                        });
                        audio.addEventListener('error', function () {
                            index += 1;
                            setTimeout(next, 30);
                        });
                    }
                    next();
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.play-playlist-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = btn.getAttribute('data-playlist-id');
                    if (!id) return;
                    fetch('playlist_json.php?id=' + encodeURIComponent(id))
                        .then(r => r.json())
                        .then(function (tracks) {
                            if (Array.isArray(tracks) && tracks.length) {
                                if (window.loadAndPlay) window.loadAndPlay(tracks, 0);
                            } else {
                                alert('У цьому плейлисті поки немає треків');
                            }
                        }).catch(function () {
                            alert('Не вдалося завантажити плейлист');
                        });
                });
            });

            document.querySelectorAll('.playlist-duration').forEach(function (el) {
                const playlistId = el.getAttribute('data-playlist-id');
                computeAndRenderPlaylistDuration(playlistId, el);
            });

            const myMusicBtn = document.querySelector('.my-music-play-btn');
            if (myMusicBtn && window.loadAndPlay && myMusicTracks.length) {
                myMusicBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.loadAndPlay(myMusicTracks, 0);
                });
            }

            const favoritesDurationEl = document.getElementById('favorites-duration');
            if (favoritesDurationEl) {
                computeAndRenderPlaylistDuration(favoritesPlaylistId, favoritesDurationEl);
            }
        });
    </script>
</body>
</html>