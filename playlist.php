<?php
session_start();
require_once 'function/db_helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = getUserId();
$playlistId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($playlistId <= 0) {
    header('Location: playlists.php');
    exit();
}

$playlist = null;
$playlists = getPlaylistsForUser($userId);
foreach ($playlists as $item) {
    if ((int)$item['id'] === $playlistId) {
        $playlist = $item;
        break;
    }
}

if (!$playlist) {
    header('Location: playlists.php');
    exit();
}

$songs = getPlaylistSongs($playlistId);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($playlist['title']); ?> - Vestra</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'php/header.php'; ?>

    <main class="body container mt-5 pt-3 mb-5 position-relative">
        <div class="playlist-header p-4 rounded-4 mb-5 d-flex align-items-center" style="background: linear-gradient(135deg, rgba(43, 16, 85, 0.25), rgba(117, 151, 222, 0.15));">
            <div class="rounded-4 d-flex justify-content-center align-items-center me-4" style="width: 120px; height: 120px; background: linear-gradient(135deg, #2b1055, #7597de); box-shadow: 0 0 20px rgba(117, 151, 222, 0.35);">
                <i class="bi bi-music-note-list text-white" style="font-size: 3rem;"></i>
            </div>
            <div class="flex-grow-1">
                <p class="text-white-50 text-uppercase mb-1" style="font-size: 0.8rem; letter-spacing: 2px;">Плейлист</p>
                <h1 class="display-5 fw-bold text-white mb-2"><?php echo htmlspecialchars($playlist['title']); ?></h1>
                <p class="text-white-50 mb-0">
                    <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    <span class="mx-2">•</span> <?php echo count($songs); ?> треків <span class="mx-2">•</span> <span id="playlist-duration">0 хв 0 сек</span>
                </p>
            </div>
            <div class="ms-auto d-none d-md-block">
                <button id="play-all-playlist-btn" class="btn btn-gradient rounded-pill px-4 py-2 text-white fw-bold d-flex align-items-center">
                    <i class="bi bi-play-fill fs-4 me-1"></i> Слухати все
                </button>
            </div>
        </div>

        <div class="tracks-list" style="max-width: 100%;">
            <?php if (count($songs) > 0): ?>
                <?php foreach ($songs as $index => $song): ?>
                    <div class="track-item d-flex align-items-center p-2 rounded-3 mb-2" style="background: rgba(255,255,255,0.03); transition: 0.3s; cursor: pointer;" data-track-index="<?php echo $index; ?>">
                        <div class="text-white-50 text-center fw-bold" style="width: 50px;"><?php echo $index + 1; ?></div>
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #d1228f, #8a43f2); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                <i class="bi bi-music-note text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-white"><?php echo htmlspecialchars($song['title']); ?></h6>
                                <small class="text-white-50" style="font-size: 0.75rem;"><?php echo htmlspecialchars($song['composer'] ?: 'Локальний файл'); ?></small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-light remove-from-playlist-btn ms-2" data-playlist-id="<?php echo (int)$playlistId; ?>" data-song-id="<?php echo (int)$song['id']; ?>">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-music-note-list text-white-50 mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-white">Цей плейлист поки порожній</h5>
                    <p class="text-white-50">Додайте треки з «Моя музика», щоб вони з’явилися тут.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
    <?php include 'php/player.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const playlistTracks = <?php echo json_encode(array_map(function($song) {
            return [
                'id' => (int)$song['id'],
                'title' => $song['title'],
                'artist' => $song['composer'] ?: 'Vestra',
                'filename' => basename($song['url']),
                'url' => $song['url']
            ];
        }, $songs), JSON_UNESCAPED_UNICODE); ?>;

        document.addEventListener('DOMContentLoaded', function () {
            const playAllBtn = document.getElementById('play-all-playlist-btn');
            if (playAllBtn) {
                playAllBtn.addEventListener('click', function () {
                    if (window.loadAndPlay && playlistTracks.length) {
                        window.loadAndPlay(playlistTracks, 0);
                    }
                });
            }

            document.querySelectorAll('.track-item').forEach(function (row, index) {
                row.addEventListener('click', function (event) {
                    if (event.target.closest('.remove-from-playlist-btn')) return;
                    if (window.loadAndPlay && playlistTracks[index]) {
                        window.loadAndPlay(playlistTracks, index);
                    }
                });
            });

            document.querySelectorAll('.remove-from-playlist-btn').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const formData = new FormData();
                    formData.append('playlist_id', button.dataset.playlistId);
                    formData.append('song_id', button.dataset.songId);

                    fetch('remove_from_playlist.php', {
                        method: 'POST',
                        body: formData
                    }).then(function () {
                        button.closest('.track-item').remove();
                    });
                });
            });
        });
        // compute total duration for playlist
        function computeTotalDuration(tracks, callback) {
            let idx = 0;
            let total = 0;
            function next() {
                if (idx >= tracks.length) { callback(total); return; }
                const a = document.createElement('audio');
                a.preload = 'metadata';
                a.src = tracks[idx].url;
                a.addEventListener('loadedmetadata', function () {
                    total += isFinite(a.duration) ? a.duration : 0;
                    idx++;
                    setTimeout(next, 50);
                });
                a.addEventListener('error', function () { idx++; setTimeout(next, 50); });
            }
            next();
        }

        if (playlistTracks && playlistTracks.length) {
            computeTotalDuration(playlistTracks, function (secs) {
                const min = Math.floor(secs / 60);
                const sec = Math.floor(secs % 60);
                const el = document.getElementById('playlist-duration');
                if (el) el.innerText = min + ' хв ' + (sec < 10 ? '0' : '') + sec + ' сек';
            });
        }
    </script>
</body>
</html>
