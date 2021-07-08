<?php


namespace App\Models;

class Especialidade extends Base {

    protected $tabela = 'especialidades';
    protected $campos = ['nome'];

    public function getRules(string $type = 'insert'): array
    {
        switch ($type) {
            case 'update':
                return array(
                    'nome'    => 'required',
                );
                break;
            
            default:
                return array(
                    'nome'    => 'required',
                );
                break;
        }
    }
}
