<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$db = getDB();

// Fetch all content sections
$result = $db->query('SELECT section, content, updated_at FROM site_content');
$content = [];
while ($row = $result->fetch_assoc()) {
    $content[$row['section']] = [
        'data' => json_decode($row['content'], true),
        'updated_at' => $row['updated_at']
    ];
}

// Fetch theme settings
$themeResult = $db->query('SELECT setting_key, setting_value FROM theme_settings');
$theme = [];
while ($row = $themeResult->fetch_assoc()) {
    $theme[$row['setting_key']] = $row['setting_value'];
}

echo json_encode([
    'success' => true,
    'content' => $content,
    'theme' => $theme
]);

$db->close();
?>
