<?php
require_once __DIR__ . '/../admin/api/db.php';
$db = getDB();
$res = $db->query("SELECT * FROM theme_settings");
while ($row = $res->fetch_assoc()) {
    echo $row['setting_key'] . ": " . $row['setting_value'] . "\n";
}
$db->close();
?>
