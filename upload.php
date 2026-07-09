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
                    <label class="text-white-50 mb-2" style="font-size: 0.9rem;">Назва музики <span class="text-danger">*</span></label>
                    <input type="text" name="track_title" class="form-control bg-transparent text-white shadow-none" style="border: 2px solid rgba(255, 107, 193, 0.3); padding: 12px;" placeholder="Введіть назву пісні" required>
                </div>

                <div class="mb-4 text-start">
                    <label class="text-white-50 mb-2" style="font-size: 0.9rem;">Автор / Виконавець <span class="text-danger">*</span></label>
                    <input type="text" name="composer" class="form-control bg-transparent text-white shadow-none" style="border: 2px solid rgba(255, 107, 193, 0.3); padding: 12px;" placeholder="Введіть ім'я виконавця" required>
                </div>

                <div class="mb-4 text-start">
                    <label class="text-white-50 mb-2" style="font-size: 0.9rem;">Жанри (можна вибрати декілька) <span class="text-danger">*</span></label>
                    
                    <!-- Скриту форму для відправлення -->
                    <input type="hidden" name="genre" id="genre_input" required>
                    
                    <!-- Вибрані жанри (бейджи) -->
                    <div id="selected_genres" class="mb-3" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                    
                    <!-- Dropdown кнопка -->
                    <div class="position-relative">
                        <button type="button" class="form-control text-start" id="genre_dropdown_btn" style="border: 2px solid rgba(209, 34, 143, 0.5); padding: 12px; color: rgba(255, 255, 255, 0.8); background: transparent; display: flex; align-items: center; justify-content: space-between;">
                            <span>Виберіть жанри...</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        
                        <!-- Список жанрів -->
                        <div id="genre_dropdown_menu" class="position-absolute w-100 mt-2 rounded-3 shadow-lg" style="display: none; background: rgba(16, 12, 38, 0.95); backdrop-filter: blur(15px); border: 1px solid rgba(255, 107, 193, 0.3); z-index: 1000; max-height: 250px; overflow-y: auto;">
                            <div class="p-2">
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Rock">Rock</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Pop">Pop</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Jazz">Jazz</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Electronic">Electronic</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Classical">Classical</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Hip-Hop">Hip-Hop</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="R&B">R&B</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Country">Country</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Metal">Metal</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Folk">Folk</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Latin">Latin</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Reggae">Reggae</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Blues">Blues</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Soul">Soul</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Indie">Indie</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Podcast">Podcast</div>
                                <div class="genre-option p-2 rounded-2" style="cursor: pointer; color: #ffffff; transition: 0.2s;" data-value="Інший">Інший</div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    let selectedGenres = [];

                    const genreDropdownBtn = document.getElementById('genre_dropdown_btn');
                    const genreDropdownMenu = document.getElementById('genre_dropdown_menu');
                    const genreOptions = document.querySelectorAll('.genre-option');
                    const genreInput = document.getElementById('genre_input');
                    const selectedGenresDiv = document.getElementById('selected_genres');

                    // Відкривання/закривання dropdown
                    genreDropdownBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        genreDropdownMenu.style.display = genreDropdownMenu.style.display === 'none' ? 'block' : 'none';
                    });

                    // Вибір жанру
                    genreOptions.forEach(option => {
                        option.addEventListener('click', function () {
                            const value = this.getAttribute('data-value');
                            
                            if (selectedGenres.includes(value)) {
                                selectedGenres = selectedGenres.filter(g => g !== value);
                            } else {
                                selectedGenres.push(value);
                            }
                            
                            updateSelectedGenres();
                        });

                        option.addEventListener('mouseenter', function () {
                            this.style.background = 'rgba(209, 34, 143, 0.2)';
                        });

                        option.addEventListener('mouseleave', function () {
                            this.style.background = 'transparent';
                        });
                    });

                    function updateSelectedGenres() {
                        // Оновлюємо hidden input
                        genreInput.value = JSON.stringify(selectedGenres);

                        // Оновлюємо бейджи
                        selectedGenresDiv.innerHTML = '';
                        selectedGenres.forEach(genre => {
                            const badge = document.createElement('div');
                            badge.className = 'badge rounded-pill d-inline-flex align-items-center';
                            badge.style.background = 'linear-gradient(90deg, #d1228f, #8a43f2)';
                            badge.style.color = '#fff';
                            badge.style.padding = '8px 12px';
                            badge.style.fontSize = '0.9rem';
                            badge.innerHTML = `${genre} <i class="bi bi-x-lg ms-2" style="cursor: pointer; font-size: 0.8rem;"></i>`;
                            
                            badge.querySelector('i').addEventListener('click', function () {
                                selectedGenres = selectedGenres.filter(g => g !== genre);
                                updateSelectedGenres();
                            });

                            selectedGenresDiv.appendChild(badge);
                        });

                        // Оновлюємо текст на кнопці
                        if (selectedGenres.length === 0) {
                            genreDropdownBtn.querySelector('span').textContent = 'Виберіть жанри...';
                        } else {
                            genreDropdownBtn.querySelector('span').textContent = `Вибрано: ${selectedGenres.length}`;
                        }
                    }

                    // Закривання dropdown при кліку поза ним
                    document.addEventListener('click', function (e) {
                        if (!genreDropdownBtn.contains(e.target) && !genreDropdownMenu.contains(e.target)) {
                            genreDropdownMenu.style.display = 'none';
                        }
                    });
                </script>
                
                <div class="mb-4 text-start">
                    <label class="text-white-50 mb-2" style="font-size: 0.9rem;">Оберіть файл з комп'ютера (.mp3) <span class="text-danger">*</span></label>
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