<?php
session_start();
require_once '../function/db_helpers.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $tracks = getAllSongs();
    
    if (empty($tracks)) {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $formattedTracks = [];
    foreach ($tracks as $track) {
        $formattedTracks[] = [
            'id' => (int)$track['id'],
            'title' => $track['title'],
            'artist' => $track['composer'] ?: 'Vestra',
            'filename' => basename($track['url']),
            'url' => $track['url']
        ];
    }

    echo json_encode($formattedTracks, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error'], JSON_UNESCAPED_UNICODE);
}
?>
