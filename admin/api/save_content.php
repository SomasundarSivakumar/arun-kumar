<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$section = trim($input['section'] ?? '');
$data = $input['data'] ?? null;
$type = $input['type'] ?? 'content'; // 'content' or 'theme'

if (empty($section) || $data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Section and data are required']);
    exit;
}

$db = getDB();

if ($type === 'theme') {
    // Save theme settings
    $stmt = $db->prepare('INSERT INTO theme_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?');
    foreach ($data as $key => $value) {
        $stmt->bind_param('sss', $key, $value, $value);
        $stmt->execute();
    }
    $stmt->close();
} else {
    // Save content section
    $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $stmt = $db->prepare('INSERT INTO site_content (section, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = ?, updated_at = CURRENT_TIMESTAMP');
    $stmt->bind_param('sss', $section, $jsonData, $jsonData);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['success' => true, 'message' => 'Content saved successfully']);
$db->close();
?>
