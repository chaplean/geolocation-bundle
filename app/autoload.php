<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__  . '/../vendor/autoload.php';

require_once __DIR__ . '/AppKernel.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
