<?php
$lines = file(__DIR__ . '/../style.css');
foreach ($lines as $num => $line) {
    if (preg_match('/(blockUI|blockOverlay|processing|loading)/i', $line)) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
