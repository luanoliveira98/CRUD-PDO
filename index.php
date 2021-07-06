<?php

require_once 'vendor/autoload.php';

use App\Model\PacienteModel;

$paciente = new PacienteModel();
$paciente->nome = 'Luan Oliveira';
$paciente->create();