<?php


// Create the router
$router = new \Phalcon\Mvc\Router();

$router->add(
    "/sitemap",
    array(
        "controller" => 'sitemap',
        "action"     => 'index'
    )
);


$router->handle();

return $router;