<?php

/**
 * Fallback when the web server document root is the project root (not /public).
 * Preferred: set the vhost document root to the public/ directory instead.
 */
require __DIR__.'/public/index.php';