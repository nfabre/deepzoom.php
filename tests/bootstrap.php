<?php 
require_once __DIR__.'/../src/vendor/Symfony/Foundation/UniversalClassLoader.php';

$loader = new Symfony\Foundation\UniversalClassLoader();
$loader->registerNamespace('Symfony', __DIR__.'/../src/vendor');
$loader->registerNamespace('Zend', __DIR__.'/../src/vendor/zf/library');
$loader->registerNamespace('Imagine', __DIR__.'/../src/vendor/imagine/lib');
$loader->registerNamespace('Deepzoom\\Tests', __DIR__);
$loader->registerNamespace('Deepzoom', __DIR__.'/../src');

$loader->register();
