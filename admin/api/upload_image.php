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

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No image file uploaded or upload error occurred']);
    exit;
}

$file = $_FILES['image'];
$fileName = basename($file['name']);
$fileTmp = $file['tmp_name'];

// Validate file type
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExtensions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file extension. Only JPG, PNG, GIF, WEBP, and SVG are allowed.']);
    exit;
}

// Ensure target directory exists
$targetDir = __DIR__ . '/../../public/assets/images/uploads/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Generate unique filename to avoid overwrites
$uniqueName = uniqid('img_', true) . '.' . $ext;
$targetFilePath = $targetDir . $uniqueName;

if (move_uploaded_file($fileTmp, $targetFilePath)) {
    // Also copy to dist if it exists
    $distDir = __DIR__ . '/../../dist/assets/images/uploads/';
    if (is_dir(__DIR__ . '/../../dist/')) {
        if (!is_dir($distDir)) {
            mkdir($distDir, 0755, true);
        }
        copy($targetFilePath, $distDir . $uniqueName);
    }
    // Return relative URL to the asset
    $relativeUrl = '/assets/images/uploads/' . $uniqueName;
    echo json_encode(['success' => true, 'filePath' => $relativeUrl]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save the uploaded file.']);
}
?>
