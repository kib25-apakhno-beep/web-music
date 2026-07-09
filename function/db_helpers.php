<?php
require_once __DIR__ . '/../db/db.php';

function getDbConnection() {
    global $conn;
    return $conn;
}

function getUserId() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
}

function ensureUserSessionFields() {
    if (!isset($_SESSION['user_name']) && isset($_SESSION['username'])) {
        $_SESSION['user_name'] = $_SESSION['username'];
    }
}

function normalizePlaylistTitle($title) {
    $title = trim((string)$title);
    $title = mb_strtolower($title, 'UTF-8');
    $title = preg_replace('/\s+/u', '', $title);
    return $title;
}

function parseGenres($genreData) {
    if (empty($genreData)) {
        return ['Інший'];
    }
    
    $decoded = json_decode($genreData, true);
    if (is_array($decoded) && !empty($decoded)) {
        return $decoded;
    }
    
    // Якщо не JSON, повертаємо як масив з одного елемента
    return [trim($genreData)];
}

function formatGenres($genreData) {
    $genres = parseGenres($genreData);
    return implode(', ', $genres);
}

function getAllSongs() {
    $conn = getDbConnection();
    $result = $conn->query('SELECT id_songs AS id, title, url, composer, genre FROM songs ORDER BY id_songs DESC');
    $songs = [];

    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }

    return $songs;
}

function getSongsForUser() {
    return getAllSongs();
}

function saveSongToDb($title, $url, $composer, $genre = 'Інший') {
    $conn = getDbConnection();
    $stmt = $conn->prepare('INSERT INTO songs (title, url, composer, genre) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $title, $url, $composer, $genre);
    $success = $stmt->execute();
    $insertId = $stmt->insert_id;
    $stmt->close();
    return $success ? $insertId : false;
}

function getOrCreateFavoritesPlaylist($userId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT id, title FROM play_list WHERE id_user = ? ORDER BY id ASC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $playlists = [];

    while ($row = $result->fetch_assoc()) {
        $playlists[] = $row;
    }

    $stmt->close();

    $favoriteIds = [];
    foreach ($playlists as $playlist) {
        $normalizedTitle = normalizePlaylistTitle($playlist['title']);
        if ($normalizedTitle === 'улюблені' || $normalizedTitle === 'улюблене' || $normalizedTitle === 'favorites' || $normalizedTitle === 'favourites') {
            $favoriteIds[] = (int)$playlist['id'];
        }
    }

    if (!empty($favoriteIds)) {
        $canonicalId = $favoriteIds[0];

        if (count($favoriteIds) > 1) {
            foreach (array_slice($favoriteIds, 1) as $duplicateId) {
                $sourceStmt = $conn->prepare('SELECT id_song FROM playlist_songs WHERE id_playlist = ?');
                $sourceStmt->bind_param('i', $duplicateId);
                $sourceStmt->execute();
                $sourceResult = $sourceStmt->get_result();

                while ($songRow = $sourceResult->fetch_assoc()) {
                    $songId = (int)$songRow['id_song'];
                    $insertStmt = $conn->prepare('INSERT IGNORE INTO playlist_songs (id_playlist, id_song) VALUES (?, ?)');
                    $insertStmt->bind_param('ii', $canonicalId, $songId);
                    $insertStmt->execute();
                    $insertStmt->close();
                }

                $sourceStmt->close();
                $conn->query('DELETE FROM playlist_songs WHERE id_playlist = ' . (int)$duplicateId);
                $conn->query('DELETE FROM play_list WHERE id = ' . (int)$duplicateId);
            }
        }

        return $canonicalId;
    }

    $title = 'Улюблені';
    $stmt = $conn->prepare('INSERT INTO play_list (id_user, title) VALUES (?, ?)');
    $stmt->bind_param('is', $userId, $title);
    $stmt->execute();
    $playlistId = $stmt->insert_id;
    $stmt->close();
    return (int)$playlistId;
}

function toggleFavoriteSong($userId, $songId) {
    $playlistId = getOrCreateFavoritesPlaylist($userId);
    $conn = getDbConnection();

    $existsStmt = $conn->prepare('SELECT 1 FROM playlist_songs WHERE id_playlist = ? AND id_song = ? LIMIT 1');
    $existsStmt->bind_param('ii', $playlistId, $songId);
    $existsStmt->execute();
    $existsResult = $existsStmt->get_result();
    $exists = $existsResult->num_rows > 0;
    $existsStmt->close();

    if ($exists) {
        $deleteStmt = $conn->prepare('DELETE FROM playlist_songs WHERE id_playlist = ? AND id_song = ?');
        $deleteStmt->bind_param('ii', $playlistId, $songId);
        $deleteStmt->execute();
        $deleteStmt->close();
        return false;
    }

    $insertStmt = $conn->prepare('INSERT INTO playlist_songs (id_playlist, id_song) VALUES (?, ?)');
    $insertStmt->bind_param('ii', $playlistId, $songId);
    $insertStmt->execute();
    $insertStmt->close();
    return true;
}

function getFavoriteSongs($userId) {
    $playlistId = getOrCreateFavoritesPlaylist($userId);
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT s.id_songs AS id, s.title, s.url, s.composer FROM playlist_songs ps JOIN songs s ON ps.id_song = s.id_songs WHERE ps.id_playlist = ? ORDER BY s.id_songs DESC');
    $stmt->bind_param('i', $playlistId);
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = [];

    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }

    $stmt->close();
    return $songs;
}

function getPlaylistsForUser($userId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT id, title FROM play_list WHERE id_user = ? ORDER BY id ASC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $playlists = [];

    while ($row = $result->fetch_assoc()) {
        $playlists[] = $row;
    }

    $stmt->close();
    return $playlists;
}

function createPlaylist($userId, $title) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('INSERT INTO play_list (id_user, title) VALUES (?, ?)');
    $stmt->bind_param('is', $userId, $title);
    $success = $stmt->execute();
    $insertId = $stmt->insert_id;
    $stmt->close();
    return $success ? $insertId : false;
}

function addSongToPlaylist($playlistId, $songId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('INSERT IGNORE INTO playlist_songs (id_playlist, id_song) VALUES (?, ?)');
    $stmt->bind_param('ii', $playlistId, $songId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function deletePlaylist($playlistId, $userId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('DELETE FROM play_list WHERE id = ? AND id_user = ?');
    $stmt->bind_param('ii', $playlistId, $userId);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function getPlaylistNamesForUser($userId) {
    return getPlaylistsForUser($userId);
}

function getPlaylistSongs($playlistId) {
    $conn = getDbConnection();
    $stmt = $conn->prepare('SELECT s.id_songs AS id, s.title, s.url, s.composer FROM playlist_songs ps JOIN songs s ON ps.id_song = s.id_songs WHERE ps.id_playlist = ? ORDER BY s.id_songs DESC');
    $stmt->bind_param('i', $playlistId);
    $stmt->execute();
    $result = $stmt->get_result();
    $songs = [];

    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }

    $stmt->close();
    return $songs;
}
