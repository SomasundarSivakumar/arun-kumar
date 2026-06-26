<?php
$c = new mysqli('localhost', 'root', '', 'arun_portfolio');
if ($c->connect_error) {
    die("Connection failed: " . $c->connect_error);
}
$r = $c->query("SELECT content FROM site_content WHERE section = 'services'");
if ($row = $r->fetch_assoc()) {
    echo "=== SERVICES CONTENT ===\n";
    echo $row['content'] . "\n";
}
$c->close();
