<?php


namespace App\Model;

class Paciente extends Base {

    protected $tabela = 'pacientes';
    protected $campos = ['nome', 'dt_nascimento', 'endereco', 'sexo', 'telefone', 'email'];
}
