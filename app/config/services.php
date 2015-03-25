<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Cache\Frontend\Output as OutputFrontend;
use Phalcon\Cache\Backend\Memcache as MemcacheBackend;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * Start router
 */
$di->set('router', function(){
    return require "router.php";

});

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

                $volt = new VoltEngine($view, $di);

                $volt->setOptions(array(
                    'compiledPath' => $config->application->cacheDir,
                    'compiledSeparator' => '_'
                ));

                return $volt;
            },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Dispatcher use default namespace
 */

$di->setShared('dispatcher', function() {
    //Create/Get an EventManager
    $eventsManager = new Phalcon\Events\Manager();

    //Attach a listener
  /*  $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) {


        //Alternative way, controller or action doesn't exist
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'controller' => 'errors',
                        'action' => 'notfound'
                    ));
                    return false;
            }
        }
    });*/

    $dispatcher = new Dispatcher();

    //Bind the EventsManager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);
    $dispatcher->setDefaultNamespace('App\Controllers');
    return $dispatcher;

});

/**
 * Set security component
 */
$di->set('security', function(){

    $security = new Phalcon\Security();

    //Set the password hashing factor to 12 rounds
    $security->setWorkFactor(12);

    return $security;
}, true);

//caching

$di->set('viewCache', function() use ($config) {

    //Cache data for one day by default
    $frontCache = new OutputFrontend(array(
        "lifetime" => 206400
    ));

    $cache = new Phalcon\Cache\Backend\File($frontCache, array(
        "cacheDir" => $config->application->cacheDir,
    ));

    return $cache;
});

/**
 * Set Flash service
 */
$di->set('flash', function() {
        return new \Phalcon\Flash\Direct();
});

/**
 * Register the global configuration as config
// */
$di->set('config', $config);

/**
 * Model manager
 */
$di->setShared('modelsManager', function() {
       return new Phalcon\Mvc\Model\Manager();
    });

/**
 * Models Cache
 */
$di->set('modelsCache', function() use ($config) {

        //Cache data for one day by default
        $frontCache = new OutputFrontend(array(
            "lifetime" => 3600
        ));

        $cache = new Phalcon\Cache\Backend\File($frontCache, array(
            "cacheDir" => $config->application->cacheDir,
        ));

        return $cache;
    });

$di->setShared('assets', 'Phalcon\Assets\Manager');
