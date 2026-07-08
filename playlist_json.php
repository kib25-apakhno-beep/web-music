<?php
session_start();
require_once 'function/db_helpers.php';

header('Content-Type: application/json; charset=utf-8');

$playlistId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($playlistId <= 0) {
    echo json_encode([]);
    exit;
}

$songs = getPlaylistSongs($playlistId);
$out = array_map(function($s) {
    return [
        'id' => (int)$s['id'],
        'title' => $s['title'],
        'artist' => $s['composer'] ?: 'Vestra',
        'filename' => basename($s['url']),
        'url' => $s['url']
    ];
}, $songs);

echo json_encode($out, JSON_UNESCAPED_UNICODE);
