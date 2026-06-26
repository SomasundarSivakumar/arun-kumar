<?php
require_once __DIR__ . '/../admin/api/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');

$db = getDB();

// Fetch content
$contentResult = $db->query('SELECT section, content FROM site_content');
$content = [];
while ($row = $contentResult->fetch_assoc()) {
    $content[$row['section']] = json_decode($row['content'], true);
}

// Fetch theme
$themeResult = $db->query('SELECT setting_key, setting_value FROM theme_settings');
$theme = [];
while ($row = $themeResult->fetch_assoc()) {
    $theme[$row['setting_key']] = $row['setting_value'];
}

$db->close();

echo json_encode([
    'success' => true,
    'content' => $content,
    'theme' => $theme,
    'timestamp' => time()
]);
?>
