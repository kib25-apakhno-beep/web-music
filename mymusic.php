<?php 
session_start(); 
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
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
            $upload_dir = 'uploads/';
            $mp3_files = [];
            if (is_dir($upload_dir)) {
                $mp3_files = glob($upload_dir . "*.mp3");
            }

            if (count($mp3_files) > 0) {
                
                // === ГЕНЕРУЄМО JAVASCRIPT МАСИВ УСІХ ТРЕКІВ ===
                echo "<script>\nconst pageTracks = [\n";
                foreach ($mp3_files as $file) {
                    $filename = basename($file);
                    $song_name = basename($file, ".mp3");
                    $file_url = 'uploads/' . rawurlencode($filename);
                    echo "{ url: '$file_url', title: '" . addslashes($song_name) . "', artist: 'Vestra', filename: '" . addslashes($filename) . "' },\n";
                }
                echo "];\n</script>\n";

                // === ВИВОДИМО СПИСОК HTML ===
                foreach ($mp3_files as $index => $file) {
                    $filename = basename($file);
                    $song_name = basename($file, ".mp3");
                    $track_data = json_encode([
                        'title' => $song_name,
                        'artist' => 'Vestra',
                        'filename' => $filename,
                        'url' => 'uploads/' . rawurlencode($filename)
                    ], JSON_UNESCAPED_UNICODE);
                    
                    echo '
                    <div class="track-item d-flex align-items-center p-2 rounded-3 mb-2" style="background: rgba(255,255,255,0.03); transition: 0.3s;">
                        <div class="text-white-50 text-center fw-bold" style="width: 50px;">' . ($index + 1) . '</div>
                        
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="rounded-3 d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #d1228f, #8a43f2); box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                <i class="bi bi-music-note text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-white">' . htmlspecialchars($song_name) . '</h6>
                                <small class="text-white-50" style="font-size: 0.75rem;">Локальний файл</small>
                            </div>
                        </div>
                        
                        <div class="pe-3" style="cursor: pointer;" onclick="loadAndPlay(pageTracks, ' . $index . ')">
                            <i class="bi bi-play-circle-fill fs-3" style="color: #ff6bc1; transition: 0.3s;" onmouseover="this.style.transform=\'scale(1.2)\'" onmouseout="this.style.transform=\'scale(1)\'"></i>
                        </div>

                        <div class="dropdown">
                            <i class="bi bi-three-dots-vertical text-white-50 fs-5" style="cursor: pointer;" data-bs-toggle="dropdown"></i>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end rounded-3 mt-2">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#" onclick="toggleFavoriteTrack(' . $track_data . '); return false;">
                                        <i class="bi bi-heart me-3"></i> Додати в улюблене
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#" onclick="showComingSoon(); return false;">
                                        <i class="bi bi-collection-play me-3"></i> Додати в плейлист
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-danger" href="php/delete_track.php?file=' . rawurlencode($filename) . '" onclick="return confirm(\'Ви точно хочете видалити цей трек назавжди?\')">
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
</body>
</html>