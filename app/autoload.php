<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = null;
$autoloader = __DIR__.'/../vendor/autoload.php';

if (file_exists($autoloader)) {
    $loader = require $autoloader;
} else {
    $loader = require getcwd() . "/vendor/autoload.php";
}

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
