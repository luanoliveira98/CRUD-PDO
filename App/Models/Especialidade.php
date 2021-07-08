<?php


namespace App\Models;

class Especialidade extends Base {

    protected $tabela = 'especialidades';
    protected $campos = ['nome'];

    public function getRules(string $type = 'insert'): array
    {
        return array(
            'nome'    => 'required',
        );
    }
}
