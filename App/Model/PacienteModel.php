<?php


namespace App\Model;

class PacienteModel extends BaseModel {

    protected $tabela = 'pacientes';
    protected $campos = ['nome', 'dt_nascimento', 'endereco', 'sexo', 'telefone', 'email'];

    public function read() {

    }

    public function update(object $p) {

    }

    public function delete(int $id) {

    }
}
