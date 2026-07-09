<?php
session_start();
require_once 'function/db_helpers.php';

$tracks = getAllSongs();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог - Vestra</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            border-color: rgba(209, 34, 143, 0.5); 
        }
        .genre-box {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-weight: bold;
            font-size: 1.2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        /* --- Магія кнопки Play --- */
        .album-cover {
            position: relative;
            overflow: hidden;
        }
        .play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.5); /* Спочатку зменшена */
            background: #d1228f;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0; /* Невидима */
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(209, 34, 143, 0.6);
        }
        /* При наведенні на всю картку кнопка плавно з'являється */
        .glass-card:hover .play-btn {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    </style>
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container mt-5 pt-3 mb-5 position-relative">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-white mb-0">Каталог</h2>
            
            <div class="search-box d-flex align-items-center p-1 rounded-pill" style="background: rgba(255, 255, 255, 0.08); width: 300px;">
                <i class="bi bi-search text-white ms-3"></i>
                <input type="text" id="search-input" class="form-control bg-transparent border-0 text-white shadow-none ms-2" style="font-size: 0.85rem;" placeholder="Пошук у каталозі...">
            </div>
        </div>
        
        <h5 class="text-white-50 mb-3 mt-5">Фільтр за жанрами</h5>
        <div class="genre-filter d-flex flex-wrap gap-2 mb-5">
            <button type="button" class="btn btn-outline-light rounded-pill genre-filter-btn" data-genre="all" style="border: 2px solid rgba(255, 255, 255, 0.3);">
                <i class="bi bi-funnel me-2"></i> Усі треки
            </button>
            <?php 
                // Збираємо унікальні жанри з усіх треків
                $allGenres = [];
                foreach ($tracks as $track) {
                    if (!empty($track['genre'])) {
                        $genres = parseGenres($track['genre']);
                        foreach ($genres as $genre) {
                            if (!in_array($genre, $allGenres)) {
                                $allGenres[] = $genre;
                            }
                        }
                    }
                }
                sort($allGenres);
            ?>
            <?php foreach ($allGenres as $genre): ?>
                <button type="button" class="btn btn-outline-light rounded-pill genre-filter-btn" data-genre="<?php echo htmlspecialchars($genre); ?>" style="border: 2px solid rgba(209, 34, 143, 0.3); transition: 0.2s;">
                    <?php echo htmlspecialchars($genre); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <h5 class="text-white-50 mb-3">Вся музика з бази</h5>
        <div class="row g-4">
            <?php if (!empty($tracks)): ?>
                <?php foreach ($tracks as $index => $track): ?>
                    <?php $trackData = [
                        'id' => (int)$track['id'],
                        'title' => $track['title'],
                        'artist' => $track['composer'] ?: 'Vestra',
                        'filename' => basename($track['url']),
                        'url' => $track['url']
                    ]; 
                    $trackGenres = !empty($track['genre']) ? parseGenres($track['genre']) : ['Інший'];
                    $genresJson = json_encode($trackGenres, JSON_UNESCAPED_UNICODE);
                    ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="glass-card p-3 rounded-4 text-center h-100 track-card" data-track-index="<?php echo $index; ?>" data-track='<?php echo htmlspecialchars(json_encode($trackData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>' data-genres="<?php echo htmlspecialchars($genresJson, ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="album-cover rounded-3 mb-3" style="width: 100%; aspect-ratio: 1; background: linear-gradient(135deg, #d1228f, #8a43f2); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-music-note-beamed fs-1 text-white-50"></i>
                                <div class="play-btn">
                                    <i class="bi bi-play-fill fs-3 ms-1"></i>
                                </div>
                            </div>
                            <h6 class="text-white mb-1 text-truncate"><?php echo htmlspecialchars($track['title']); ?></h6>
                            <p class="text-white-50 mb-2" style="font-size: 0.75rem;"><?php echo htmlspecialchars($track['composer'] ?: 'Vestra'); ?></p>
                            <small class="text-info-ish" style="color: #32c8ff; font-size: 0.7rem;"><?php echo htmlspecialchars(formatGenres($track['genre'] ?? '["Інший"]')); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 text-white-50">
                        <i class="bi bi-music-note-list fs-1 mb-3"></i>
                        <p class="mb-0">У базі поки немає треків.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
    
    <?php include 'php/player.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterBtns = document.querySelectorAll('.genre-filter-btn');
            const trackCards = document.querySelectorAll('.track-card');
            const searchInput = document.getElementById('search-input');
            let activeFilter = 'all';
            let searchQuery = '';

            // Обробка кліків на фільтри жанрів
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    activeFilter = this.getAttribute('data-genre');
                    
                    // Оновлюємо стиль кнопок
                    filterBtns.forEach(b => {
                        if (b.getAttribute('data-genre') === activeFilter) {
                            b.classList.remove('btn-outline-light');
                            b.classList.add('btn-gradient');
                            b.style.borderColor = 'transparent';
                        } else {
                            b.classList.remove('btn-gradient');
                            b.classList.add('btn-outline-light');
                            b.style.borderColor = 'rgba(209, 34, 143, 0.3)';
                        }
                    });

                    // Фільтруємо треки
                    filterTracks();
                });
            });

            // Обробка пошуку
            searchInput.addEventListener('input', function () {
                searchQuery = this.value.toLowerCase().trim();
                filterTracks();
            });

            function filterTracks() {
                let visibleCount = 0;
                
                trackCards.forEach(card => {
                    const trackData = JSON.parse(card.getAttribute('data-track'));
                    const title = (trackData.title || '').toLowerCase();
                    const artist = (trackData.artist || '').toLowerCase();
                    
                    const genresAttr = card.getAttribute('data-genres');
                    let genres = [];
                    
                    if (genresAttr) {
                        try {
                            genres = JSON.parse(genresAttr);
                            if (!Array.isArray(genres)) {
                                genres = [genres];
                            }
                        } catch (e) {
                            genres = ['Інший'];
                        }
                    } else {
                        genres = ['Інший'];
                    }

                    // Перевіряємо жанр
                    const genreMatches = activeFilter === 'all' || genres.some(g => g === activeFilter);
                    
                    // Перевіряємо пошук по назві та автору
                    const searchMatches = !searchQuery || title.includes(searchQuery) || artist.includes(searchQuery);
                    
                    // Показуємо трек тільки якщо проходить обидва фільтри
                    const matches = genreMatches && searchMatches;
                    
                    if (matches) {
                        card.parentElement.style.display = '';
                        card.style.display = '';
                        visibleCount++;
                        // Анімація появи
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            card.parentElement.style.display = 'none';
                        }, 150);
                    }
                });

                // Повідомлення якщо нема результатів
                let noResultsDiv = document.getElementById('no-results-message');
                if (visibleCount === 0) {
                    if (!noResultsDiv) {
                        noResultsDiv = document.createElement('div');
                        noResultsDiv.id = 'no-results-message';
                        noResultsDiv.className = 'col-12 text-center py-5 text-white-50';
                        noResultsDiv.innerHTML = '<i class="bi bi-music-note-list fs-1 mb-3"></i><p class="mb-0">Немає треків що відповідають пошуку</p>';
                        document.querySelector('.row.g-4').appendChild(noResultsDiv);
                    }
                    noResultsDiv.style.display = 'flex';
                    noResultsDiv.classList.add('d-flex', 'flex-column', 'align-items-center');
                } else if (noResultsDiv) {
                    noResultsDiv.style.display = 'none';
                }
            }

            // Обробка кліків на картки для проигрывання
            trackCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    try {
                        const track = JSON.parse(card.getAttribute('data-track'));
                        if (window.loadAndPlay && track && track.url) {
                            window.loadAndPlay([track], 0);
                        }
                    } catch (e) {}
                });
            });

            // Додаємо плавність переходу
            trackCards.forEach(card => {
                card.style.transition = 'all 0.15s ease';
            });
        });
    </script>
</body>
</html>