<?php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'arun_portfolio';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$res = $conn->query("SELECT section, content FROM site_content WHERE section = 'hero'");
if ($row = $res->fetch_assoc()) {
    echo "HERO SECTION:\n";
    print_r(json_decode($row['content'], true));
}
$conn->close();
?>
