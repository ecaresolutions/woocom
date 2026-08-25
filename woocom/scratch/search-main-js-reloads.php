<?php
$lines = file(__DIR__ . '/../assets/js/main.js');
foreach ($lines as $num => $line) {
    if (strpos($line, 'location.reload') !== false || strpos($line, 'window.location') !== false) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
