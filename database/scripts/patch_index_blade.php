<?php

$f = dirname(__DIR__, 2) . '/resources/views/public/index.blade.php';
$c = file_get_contents($f);
$c = str_replace('visa-status.html?type=NIV', "{{ route('visa-status') }}?type=NIV", $c);
$c = str_replace('visa-status.html?type=IV', "{{ route('visa-status') }}?type=IV", $c);
$c = str_replace('href="visa-status.html"', 'href="{{ route(\'visa-status\') }}"', $c);
file_put_contents($f, $c);
echo "Patched index blade\n";