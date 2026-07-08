<?php
session_start();
require_once 'function/db_helpers.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$playlistId = isset($_POST['playlist_id']) ? (int)$_POST['playlist_id'] : 0;
$songId = isset($_POST['song_id']) ? (int)$_POST['song_id'] : 0;

if ($playlistId <= 0 || $songId <= 0) {
    exit('Invalid data');
}

$conn = getDbConnection();
$stmt = $conn->prepare('DELETE FROM playlist_songs WHERE id_playlist = ? AND id_song = ?');
$stmt->bind_param('ii', $playlistId, $songId);
$success = $stmt->execute();
$stmt->close();

echo $success ? 'ok' : 'error';
