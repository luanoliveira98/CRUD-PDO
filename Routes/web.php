<?php

use CoffeeCode\Router\Router;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET,PUT,PATCH,POST,DELETE");

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
 * Paciente - API
 */
$router->group("pacientes");
$router->get("/", "PacienteController:index");
$router->post("/", "PacienteController:store");
$router->get("/{id}", "PacienteController:show");
$router->put("/{id}", "PacienteController:update");
$router->delete("/{id}", "PacienteController:destroy");

/**
 * Consulta - API
 */
$router->group("consultas");
$router->get("/", "ConsultaController:index");
$router->get("/paciente/{paciente_id}", "ConsultaController:getByPacienteId");
$router->get("/agendadas/hoje", "ConsultaController:getScheduledToday");
$router->post("/", "ConsultaController:store");
$router->get("/{id}", "ConsultaController:show");
$router->put("/{id}", "ConsultaController:update");
$router->patch("/{id}", "ConsultaController:execute");
$router->delete("/{id}", "ConsultaController:destroy");

/**
 * Especialidade - API
 */
$router->group("especialidades");
$router->get("/", "EspecialidadeController:index");
$router->post("/", "EspecialidadeController:store");
$router->get("/{id}", "EspecialidadeController:show");
$router->put("/{id}", "EspecialidadeController:update");
$router->delete("/{id}", "EspecialidadeController:destroy");

$router->dispatch();