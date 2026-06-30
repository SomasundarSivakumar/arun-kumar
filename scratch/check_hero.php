<?php
require_once __DIR__ . '/../admin/api/db.php';
$db = getDB();
$res = $db->query("SELECT content FROM site_content WHERE section = 'hero'");
if ($row = $res->fetch_assoc()) {
    echo $row['content'] . "\n";
} else {
    echo "Hero not found.\n";
}
$db->close();
?>
