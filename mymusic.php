<?php
session_start(); 
require_once 'function/db_helpers.php';
ensureUserSessionFields();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

$userId = getUserId();
$playlists = getPlaylistsForUser($userId);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Моя музика - Vestra</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <style>
        .dropdown-menu-dark {
            background: rgba(16, 12, 38, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            transition: 0.2s;
        }
        .dropdown-item:hover {
            background: rgba(209, 34, 143, 0.2);
            color: #fff;
        }
    </style>
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container mt-5 pt-3 mb-5 position-relative">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold text-white mb-0">Моя музика</h2>
                <p class="text-white-50 mt-1 mb-0" style="font-size: 0.85rem;">Треки, які ви завантажили</p>
            </div>
            <a href="upload.php" class="btn btn-gradient rounded-pill px-4 py-2 text-white fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Додати трек
            </a>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert text-white rounded-3 mb-4" style="background: rgba(50, 200, 255, 0.2); border: 1px solid rgba(50, 200, 255, 0.4);">
                <i class="bi bi-check-circle-fill me-2"></i> Трек успішно завантажено!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert text-white rounded-3 mb-4" style="background: rgba(255, 107, 193, 0.2); border: 1px solid rgba(255, 107, 193, 0.4);">
                <i class="bi bi-trash-fill me-2"></i> Трек успішно видалено!
            </div>
        <?php endif; ?>

        <div class="tracks-list" style="max-width: 100%;">
            <?php
            $songs = getSongsForUser();

            if (count($songs) > 0) {
                
                // === ГЕНЕРУЄМО JAVASCRIPT МАСИВ УСІХ ТРЕКІВ ===
                echo "<script>\nconst pageTracks = [\n";
                foreach ($songs as $index => $song) {
                    $filename = basename($song['url']);
                    $song_name = $song['title'];
                    $file_url = $song['url'];
                    echo "{ id: '" . (int)$song['id'] . "', url: '$file_url', title: '" . addslashes($song_name) . "', artist: '" . addslashes($song['composer'] ?: 'Vestra') . "', filename: '" . addslashes($filename) . "' },\n";
                }
                echo "];\n</script>\n";

                // === ВИВОДИМО СПИСОК HTML ===
                foreach ($songs as $index => $song) {
                    $filename = basename($song['url']);
                    $song_name = $song['title'];
                    $track_data = json_encode([
                        'id' => (int)$song['id'],
                        'title' => $song_name,
                        'artist' => $song['composer'] ?: 'Vestra',
                        'filename' => $filename,
                        'url' => $song['url']
                    ], JSON_UNESCAPED_UNICODE);
                    
                    echo '
                    <div class="track-item d-flex align-items-center p-2 rounded-3 mb-2" style="background: rgba(255,255,255,0.03); transition: 0.3s; cursor: pointer;" data-track-index="' . $index . '">
                        <div class="text-white-50 text-center fw-bold" style="width: 50px;">' . ($index + 1) . '</div>
                        
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #d1228f, #8a43f2); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                <i class="bi bi-music-note text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-white">' . htmlspecialchars($song_name) . '</h6>
                                <small class="text-white-50" style="font-size: 0.75rem;">' . htmlspecialchars($song['composer'] ?: 'Локальний файл') . '</small>
                                ' . (!empty($song['genre']) ? '<small class="d-block text-info-ish" style="font-size: 0.7rem; color: #32c8ff;">🎵 ' . htmlspecialchars(formatGenres($song['genre'])) . '</small>' : '') . '
                            </div>
                        </div>
                        
                        

                        <div class="dropdown">
                            <i class="bi bi-three-dots-vertical text-white-50 fs-5" style="cursor: pointer;" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end rounded-3 mt-2">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center track-action-favorite" href="#" data-track-index="' . $index . '">
                                        <i class="bi bi-heart me-3"></i> <span class="track-favorite-label">Додати в улюблене</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center track-playlist-toggle" href="#" data-track-index="' . $index . '">
                                        <i class="bi bi-collection-play me-3"></i> <span class="track-playlist-label">Додати в плейлист</span>
                                    </a>
                                    <div class="px-2 pb-2 d-none playlist-submenu" data-track-index="' . $index . '">
                                        ' . ($playlists ? '' : '<div class="text-white-50 small px-2">Плейлисти відсутні</div>') . '
                                        ' . implode(array_map(function($playlist) use ($song, $index) {
                                            return '<button type="button" class="btn btn-sm w-100 text-start rounded-3 mt-1 playlist-option" style="--bs-btn-color: rgba(255, 255, 255, 0.9);" data-playlist-id="' . (int)$playlist['id'] . '" data-track-id="' . (int)$song['id'] . '" data-track-index="' . $index . '">+ ' . htmlspecialchars($playlist['title']) . '</button>';
                                        }, $playlists)) . '
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-danger track-action-delete" href="#" data-track-index="' . $index . '" data-filename="' . htmlspecialchars($filename, ENT_QUOTES) . '" data-track-name="' . htmlspecialchars($song_name, ENT_QUOTES, 'UTF-8') . '">
                                        <i class="bi bi-trash3 me-3"></i> Видалити трек
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>';
                }
            } else {
                echo '
                <div class="text-center py-5">
                    <i class="bi bi-music-note-list text-white-50 mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-white">У вас поки немає завантажених треків</h5>
                    <p class="text-white-50">Натисніть "Додати трек", щоб завантажити свою першу пісню.</p>
                </div>';
            }
            ?>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
    <?php include 'php/player.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.playlists = <?php echo json_encode($playlists, JSON_UNESCAPED_UNICODE); ?>;
        window.getPlaylists = function () {
            return window.playlists || [];
        };

        function updateTrackMenuState() {
            if (!window.isUserLoggedIn) return;

            document.querySelectorAll('.track-action-favorite').forEach(function (link) {
                const track = pageTracks[Number(link.dataset.trackIndex)];
                const label = link.querySelector('.track-favorite-label');
                const icon = link.querySelector('i');
                if (!track || !label || !icon) return;

                const formData = new FormData();
                formData.append('song_id', track.id);
                formData.append('check_only', '1');

                fetch('favorite_actions.php', {
                    method: 'POST',
                    body: formData
                }).then(function (response) {
                    return response.text();
                }).then(function (text) {
                    const favorite = text === 'favorite';
                    label.textContent = favorite ? '✓ Улюблене' : 'Додати в улюблене';
                    icon.className = favorite ? 'bi bi-heart-fill me-3' : 'bi bi-heart me-3';
                    icon.style.color = favorite ? '#ff6bc1' : '';
                });
            });
        }

        function renderPlaylistSubmenu(trackIndex) {
            const submenu = document.querySelector('.playlist-submenu[data-track-index="' + trackIndex + '"]');
            if (!submenu) return;
            submenu.classList.remove('d-none');
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateTrackMenuState();

            document.querySelectorAll('.track-item[data-track-index]').forEach(function (row) {
                row.addEventListener('click', function (event) {
                    if (event.target.closest('.dropdown, .dropdown-menu, .dropdown-item')) return;
                    loadAndPlay(pageTracks, Number(row.dataset.trackIndex));
                });
            });

            document.querySelectorAll('.track-play-btn').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    loadAndPlay(pageTracks, Number(button.dataset.trackIndex));
                });
            });

            document.querySelectorAll('.track-action-favorite').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const track = pageTracks[Number(link.dataset.trackIndex)];
                    if (!track) return;
                    if (!window.isUserLoggedIn) {
                        if (window.showAuthMessage) {
                            window.showAuthMessage('Щоб додати трек до улюблених, увійдіть у свій акаунт');
                        } else {
                            alert('Щоб додати трек до улюблених, увійдіть у свій акаунт');
                        }
                        return;
                    }

                    const formData = new FormData();
                    formData.append('song_id', track.id);

                    fetch('favorite_actions.php', {
                        method: 'POST',
                        body: formData
                    }).then(function (response) {
                        if (!response.ok) {
                            if (window.showAuthMessage) {
                                window.showAuthMessage('Щоб додати трек до улюблених, увійдіть у свій акаунт');
                            } else {
                                alert('Щоб додати трек до улюблених, увійдіть у свій акаунт');
                            }
                            return;
                        }
                        updateTrackMenuState();
                    });
                });
            });

            document.querySelectorAll('.track-playlist-toggle').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const trackIndex = link.dataset.trackIndex;
                    const submenu = document.querySelector('.playlist-submenu[data-track-index="' + trackIndex + '"]');
                    if (submenu) {
                        const isHidden = submenu.classList.contains('d-none');
                        document.querySelectorAll('.playlist-submenu').forEach(function (item) {
                            item.classList.add('d-none');
                        });
                        if (isHidden) {
                            submenu.classList.remove('d-none');
                            renderPlaylistSubmenu(trackIndex);
                        }
                    }
                });
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.playlist-option');
                if (!button) return;
                event.preventDefault();
                event.stopPropagation();
                const playlistId = button.getAttribute('data-playlist-id');
                const trackId = button.getAttribute('data-track-id');
                const formData = new FormData();
                formData.append('track_id', trackId);
                formData.append('playlist_id', playlistId);

                fetch('playlist_actions.php', {
                    method: 'POST',
                    body: formData
                }).then(function (response) {
                    return response.text();
                }).then(function () {
                    document.querySelectorAll('.playlist-option').forEach(function (option) {
                        option.classList.remove('btn-gradient');
                        option.classList.add('btn-outline-light');
                        option.style.color = '';
                    });

                    button.classList.remove('btn-outline-light');
                    button.classList.add('btn-gradient');
                    button.style.color = '#fff';

                    const label = button.closest('.dropdown').querySelector('.track-playlist-label');
                    if (label) {
                        label.textContent = '✓ Додано в плейлист';
                    }
                });
            });

            document.querySelectorAll('.track-action-delete').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const trackName = this.getAttribute('data-track-name');
                    const filename = this.getAttribute('data-filename');
                    
                    const confirmed = confirm('Видалити трек "' + trackName + '" назавжди?');
                    if (!confirmed) return;

                    const formData = new FormData();
                    formData.append('file', filename);

                    fetch('php/delete_track.php', {
                        method: 'POST',
                        body: formData
                    }).then(function (response) {
                        if (response.ok) {
                            // Перезавантажуємо сторінку зі статусом успіху
                            window.location.href = 'mymusic.php?deleted=1';
                        } else {
                            alert('Помилка при видаленні треку');
                        }
                    }).catch(function (error) {
                        console.error('Помилка:', error);
                        alert('Помилка при видаленні треку');
                    });
                });
            });
        });
    </script>
</body>
</html>