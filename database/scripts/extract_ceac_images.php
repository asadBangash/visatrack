<?php

$source = dirname(__DIR__, 2).'/resources/views/public/visa-status.blade.php';
$target = dirname(__DIR__, 2).'/resources/views/partials/ceac-flag-seal.blade.php';

$content = file_get_contents($source);
preg_match('/<img[^>]+class="flag"[^>]+>/', $content, $flag);
preg_match('/<img[^>]+class="seal-img"[^>]+>/', $content, $seal);

$flagImg = str_replace('class="flag"', 'class="ds160-masthead-flag-img"', $flag[0]);
$sealImg = str_replace('class="seal-img"', 'class="ds160-masthead-seal-img"', $seal[0]);

file_put_contents($target, $flagImg."\n".$sealImg."\n");
echo 'Wrote partial ('.strlen($flagImg.$sealImg)." bytes)\n";