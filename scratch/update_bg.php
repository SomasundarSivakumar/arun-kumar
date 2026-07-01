<?php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'arun_portfolio';

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Update theme_settings
    $stmt = $conn->prepare("UPDATE theme_settings SET setting_value = ? WHERE setting_key = 'bg_color'");
    $newBg = '#061022';
    $stmt->bind_param('s', $newBg);
    if ($stmt->execute()) {
        echo "Successfully updated bg_color to " . $newBg . "\n";
    } else {
        echo "Error updating bg_color: " . $stmt->error . "\n";
    }
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
