<?php


namespace App\Models;

class Paciente extends Base {

    protected $tabela = 'pacientes';
    protected $campos = ['nome', 'dt_nascimento', 'endereco', 'sexo', 'telefone', 'email'];

    public function getRules(string $type = 'insert'): array
    {
        switch ($type) {
            case 'update':
                return array(
                    'dt_nascimento' => 'date',
                    'sexo'          => 'enum:masculino,feminino',
                    'telefone'      => 'number|size:11',
                    'email'         => 'email'
                );
                break;
            
            default:
                return array(
                    'nome'          => 'required',
                    'dt_nascimento' => 'required|date',
                    'endereco'      => 'required',
                    'sexo'          => 'required|enum:masculino,feminino',
                    'telefone'      => 'required|number|size:11',
                    'email'         => 'required|email'
                );
                break;
        }
    }
}
