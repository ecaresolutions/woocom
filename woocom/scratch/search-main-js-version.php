<?php
$lines = file(__DIR__ . '/../functions.php');
foreach ($lines as $num => $line) {
    if (strpos($line, 'main_js_version') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
