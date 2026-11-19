<?php

$html = file_get_contents(dirname(__DIR__, 2) . '/../usstate/visa-status.html');
preg_match_all('/<option value="([^"]+)">/', $html, $matches);
$opts = array_values(array_filter($matches[1]));
$dir = dirname(__DIR__) . '/data';
if (! is_dir($dir)) {
    mkdir($dir, 0777, true);
}
file_put_contents($dir . '/locations_raw.txt', implode(PHP_EOL, $opts));
echo 'Extracted ' . count($opts) . ' locations' . PHP_EOL;