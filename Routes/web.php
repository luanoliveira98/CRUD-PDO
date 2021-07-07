<?php

use CoffeeCode\Router\Router;

$router = new Router(URL_BASE);

/**
 * Controllers
 */
$router->namespace("App\Http\Controllers");

/**
 * Home
 */
$router->group(null);
$router->get("/", "HomeController:index");

/**
 * Erro
 */
$router->group("ooops");
$router->get("/{errcode}", "ErroController:index");

/**
 * Paciente - API
 */
$router->group("pacientes");
$router->get("/", "PacienteController:index");
$router->post("/", "PacienteController:store");
$router->get("/{id}", "PacienteController:show");
$router->put("/{id}", "PacienteController:update");
$router->delete("/{id}", "PacienteController:destroy");

$router->dispatch();

if($router->error()) {
    $router->redirect("/ooops/{$router->error()}");
}