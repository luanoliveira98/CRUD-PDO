<?php

namespace App\Http\Controllers;

class ErroController extends Controller {

    public function index($data)
    {
        echo "Erro {$data["errcode"]}";
    }
}