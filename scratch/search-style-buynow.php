<?php
$lines = file(__DIR__ . '/../style.css');
foreach ($lines as $num => $line) {
    if (preg_match('/(buy-now|buy_now|buy-btn)/i', $line)) {
        echo ($num + 1) . ': ' . trim($line) . "\n";
    }
}
