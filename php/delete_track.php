<?php
session_start();
require_once '../function/db_helpers.php';

// Захист
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $songId = (int)$_GET['id'];
    $conn = getDbConnection();

    $stmt = $conn->prepare('SELECT url FROM songs WHERE id_songs = ? AND id_user = ?');
    $stmt->bind_param('ii', $songId, getUserId());
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();
    $stmt->close();

    if ($song) {
        $filename = basename($song['url']);
        $filepath = '../uploads/' . $filename;

        $conn->query('DELETE FROM playlist_songs WHERE id_song = ' . (int)$songId);
        $conn->query('DELETE FROM songs WHERE id_songs = ' . (int)$songId);

        if (file_exists($filepath) && strtolower(pathinfo($filepath, PATHINFO_EXTENSION)) == 'mp3') {
            unlink($filepath);
        }

        header('Location: ../mymusic.php?deleted=1');
        exit();
    }
}

header('Location: ../mymusic.php');
exit();
?>