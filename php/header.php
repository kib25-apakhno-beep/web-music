<?php
// Перевіряємо, чи користувач авторизований
$isLoggedIn = isset($_SESSION['user_id']); 
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark pt-4 pb-3" style="background-color: transparent;">
        <div class="container">
            
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <div class="rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 30px; height: 30px; background: linear-gradient(135deg, #d1228f, #8a43f2);">
                    <span class="fw-bold text-white" style="font-size: 0.9rem;">M</span>
                </div>
                <span class="fw-bold text-uppercase" style="font-size: 1.1rem; letter-spacing: 1px;">Melodica</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                
                <?php if ($isLoggedIn): ?>
                    <ul class="navbar-nav ms-auto" style="font-size: 0.85rem;">
                        <li class="nav-item px-2"><a class="nav-link text-white" href="index.php">Головна</a></li>
                        <li class="nav-item px-2"><a class="nav-link text-white-50" href="catalog.php">Каталог</a></li>
                        <li class="nav-item px-2"><a class="nav-link text-white-50" href="favorites.php">Улюблене</a></li>
                        <li class="nav-item px-2"><a class="nav-link text-white-50" href="playlists.php">Плейлисти</a></li>
                        <li class="nav-item px-2"><a class="nav-link text-white-50" href="mymusic.php">Моя музика</a></li>
                    </ul>
                    
                    <div class="d-flex align-items-center mt-3 mt-lg-0 ms-lg-4">
                        <a href="upload.php" class="text-white-50 text-decoration-none me-4" style="font-size: 0.85rem;">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Додати MP3
                        </a>
                        <a href="profile.php" class="text-white text-decoration-none d-flex align-items-center me-4" style="font-size: 0.85rem;">
                            <i class="bi bi-person-circle fs-5 me-2 text-secondary"></i> Софія Богданова
                        </a>
                        <a href="php/logout.php" class="btn btn-outline-light btn-sm rounded-pill px-4 py-1" style="font-size: 0.8rem; border-color: rgba(255,255,255,0.2);">Вийти</a>
                    </div>

                <?php else: ?>
                    <ul class="navbar-nav ms-auto align-items-lg-center" style="font-size: 0.85rem;">
                        <li class="nav-item px-2"><a class="nav-link text-white" href="index.php">Головна</a></li>
                        <li class="nav-item px-2"><a class="nav-link text-white-50" href="catalog.php">Каталог</a></li>
                    </ul>
                    
                    <div class="d-flex align-items-center mt-3 mt-lg-0 ms-lg-4">
                        <a href="login.php" class="text-white text-decoration-none me-4 fw-bold" style="font-size: 0.9rem;">Увійти</a>
                        <a href="register.php" class="btn rounded-pill px-4 py-2 text-white fw-bold btn-gradient" style="font-size: 0.9rem;">Реєстрація</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </nav>

</header>