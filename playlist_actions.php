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

$trackId = isset($_POST['track_id']) ? (int)$_POST['track_id'] : 0;
$playlistId = isset($_POST['playlist_id']) ? (int)$_POST['playlist_id'] : 0;

if ($trackId <= 0 || $playlistId <= 0) {
    exit('Invalid data');
}

$conn = getDbConnection();
$checkStmt = $conn->prepare('SELECT 1 FROM playlist_songs WHERE id_playlist = ? AND id_song = ? LIMIT 1');
$checkStmt->bind_param('ii', $playlistId, $trackId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$exists = $checkResult->num_rows > 0;
$checkStmt->close();

if ($exists) {
    echo 'exists';
    exit;
}

$insertStmt = $conn->prepare('INSERT INTO playlist_songs (id_playlist, id_song) VALUES (?, ?)');
$insertStmt->bind_param('ii', $playlistId, $trackId);
$success = $insertStmt->execute();
$insertStmt->close();

echo $success ? 'ok' : 'error';
