<?php

$c = file_get_contents(dirname(__DIR__, 2) . '/resources/views/public/index.blade.php');
preg_match_all('/href="[^"]*visa[^"]*"/', $c, $m);
print_r($m[0]);