<?php
session_start();
require_once '../function/db_helpers.php';

// Захист
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Приймаємо як GET (з посилань) так і POST (з AJAX)
$filename = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'])) {
    $filename = basename($_POST['file']);
} elseif (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
}

if ($filename) {
    $filepath = '../uploads/' . $filename;

    // Знаходимо пісню за ім'ям файлу
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT id_songs FROM songs WHERE url LIKE ?');
    $searchUrl = '%' . $filename;
    $stmt->bind_param('s', $searchUrl);
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();
    $stmt->close();

    if ($song) {
        $songId = (int)$song['id_songs'];

        // Видаляємо всі зв'язки цієї пісні з плейлистами
        $conn->query('DELETE FROM playlist_songs WHERE id_song = ' . $songId);
        
        // Видаляємо саму пісню
        $conn->query('DELETE FROM songs WHERE id_songs = ' . $songId);

        // Видаляємо файл з сервера
        if (file_exists($filepath) && strtolower(pathinfo($filepath, PATHINFO_EXTENSION)) == 'mp3') {
            @unlink($filepath);
        }

        // Якщо це AJAX запрос - просто повертаємо OK
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            http_response_code(200);
            echo 'OK';
            exit();
        }

        // Якщо це звичайне посилання - перенаправляємо
        header('Location: ../mymusic.php?deleted=1');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    http_response_code(400);
    echo 'Error';
    exit();
}

header('Location: ../mymusic.php');
exit();
?>