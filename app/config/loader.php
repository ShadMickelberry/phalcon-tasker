<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of namespaces taken from the configuration file
 */

$loader->registerNamespaces(
    array(
        'App\Controllers' => $config->application->controllersDir,
        'App\Models'      => $config->application->modelsDir,
        'App\Library'     => $config->application->libraryDir
    )
);
$loader->register();

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../vendor/autoload.php';
