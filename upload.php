<?php 
session_start(); 
// Захист: якщо гість, перекидаємо на сторінку входу
if (!isset($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Додати MP3 - Vestra</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include 'php/header.php'; ?>

    <main class="body container d-flex justify-content-center align-items-center mt-5 mb-5 position-relative">
        
        <div class="p-5 rounded-4 text-center" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); width: 100%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            
            <div class="mb-4">
                <i class="bi bi-cloud-arrow-up text-white" style="font-size: 3.5rem; text-shadow: 0 0 20px #d1228f;"></i>
            </div>
            <h3 class="fw-bold text-white mb-4">Завантажити MP3</h3>
            
            <form action="php/upload_handler.php" method="POST" enctype="multipart/form-data">
                
                <div class="mb-4 text-start">
                    <label class="text-white-50 mb-2" style="font-size: 0.9rem;">Оберіть файл з комп'ютера (.mp3)</label>
                    <input type="file" name="mp3_file" accept=".mp3" class="form-control bg-transparent text-white shadow-none" style="border: 2px dashed rgba(255, 107, 193, 0.5); padding: 15px; cursor: pointer;" required>
                </div>
                
                <button type="submit" class="btn btn-gradient rounded-pill w-100 py-3 fw-bold text-white mt-2" style="font-size: 1.1rem;">
                    <i class="bi bi-upload me-2"></i> Завантажити на сервер
                </button>
            </form>
            
        </div>
        
    </main>

    <?php include 'php/footer.php'; ?>
    <?php include 'php/player.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>