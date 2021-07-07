<?php

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

$router = new Router(URL_BASE);

$router->group(null);
$router->get("/", function($data) {
    echo "SALVE";
    return;
});
$router->get("/teste", function($data) {
    echo "TESTE";
    return;
});

$router->dispatch();