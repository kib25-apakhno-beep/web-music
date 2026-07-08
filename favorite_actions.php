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

$songId = isset($_POST['song_id']) ? (int)$_POST['song_id'] : 0;
if ($songId <= 0) {
    exit('Invalid data');
}

$userId = getUserId();
if (!empty($_POST['check_only'])) {
    $playlistId = getOrCreateFavoritesPlaylist($userId);
    $conn = getDbConnection();
    $checkStmt = $conn->prepare('SELECT 1 FROM playlist_songs WHERE id_playlist = ? AND id_song = ? LIMIT 1');
    $checkStmt->bind_param('ii', $playlistId, $songId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    echo $checkResult->num_rows > 0 ? 'favorite' : 'not-favorite';
    $checkStmt->close();
    exit;
}

$result = toggleFavoriteSong($userId, $songId);
echo $result ? 'added' : 'removed';
