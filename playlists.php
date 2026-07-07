<?php 
session_start(); 
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
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
                <div class="playlist-card h-100 d-flex flex-column align-items-center justify-content-center p-4" style="border: 2px dashed rgba(255,255,255,0.2); background: transparent;">
                    <i class="bi bi-plus-circle text-white-50 mb-2" style="font-size: 2.5rem;"></i>
                    <h6 class="text-white-50 fw-bold">Створити новий</h6>
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <div class="playlist-card h-100">
                    <div class="playlist-img" style="background: linear-gradient(135deg, #2b1055, #7597de);">
                        <i class="bi bi-moon-stars text-white" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <div class="p-3">
                        <h6 class="text-white fw-bold mb-1">Нічний вайб</h6>
                        <small class="text-white-50">12 треків</small>
                    </div>
                </div>
            </div>
            
            <div class="col-6 col-md-4 col-lg-3">
                <div class="playlist-card h-100">
                    <div class="playlist-img" style="background: linear-gradient(135deg, #ff6bc1, #f6416c);">
                        <i class="bi bi-fire text-white" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <div class="p-3">
                        <h6 class="text-white fw-bold mb-1">Тренування</h6>
                        <small class="text-white-50">24 треки</small>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'php/footer.php'; ?>
    <?php include 'php/player.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>