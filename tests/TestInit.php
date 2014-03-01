<?php
$root = dirname(__DIR__);
$autoloader = $root . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    die("Unable to find autoload." . PHP_EOL);
}
require $autoloader;
