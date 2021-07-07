<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Validator;

class PacienteController extends Controller {

    public $model = 'App\Models\Paciente';

    /**
     * Listar os registros
     */
    public function index()
    {
        $pacientes = Paciente::select(null, array('nome' => ''));
        return $this->response('success', null, $pacientes);
    }

    /**
     * Inserir novo registro
     */
    public function store()
    {
        if(!$data = $this->hasData()) {
            return $this->response('error', 'NO DATA', null, 400);
        }

        if($validator = Validator::validate($this->model, $data)) {
            return $this->response('error', 'ERROR VALIDATOR', $validator, 400);
        }

        $paciente = new Paciente();
        $paciente->nome = $data['nome'];
        $paciente->dt_nascimento = $data['dt_nascimento'];
        $paciente->endereco = $data['endereco'];
        $paciente->sexo = $data['sexo'];
        $paciente->telefone = $data['telefone'];
        $paciente->email = $data['email'];
        if (!$paciente->save()) {
            return $this->response('error', null, null, 500);
        }

        return $this->response('success', 'INSERTED', $data, 201);
    }

    /**
     * Listar um registro em específico
     * 
     * @param   array               $data           Dados vindos da URL ($data['id'])
     */
    public function show(array $data)
    {
        $id = $data['id'];
        $paciente = Paciente::find($id);
        if(!$paciente) {
            return $this->response('error', 'NOT FOUND', null, 404);
        }
        
        return $this->response('success', null, $paciente);
    }

    /**
     * Atualizar um registro em específico
     * 
     * @param   array               $data           Dados vindos da URL ($data['id'])
     */
    public function update(array $data)
    {
        if(!$id = $this->exists($data, 'Paciente')) {
            return $this->response('error', 'NOT FOUND', null, 404);
        }

        if(!$data = $this->hasData()) {
            return $this->response('error', 'NO DATA', null, 400);
        }

        if($validator = Validator::validate($this->model, $data, 'update')) {
            return $this->response('error', 'ERROR VALIDATOR', $validator, 400);
        }

        $paciente = new Paciente();
        foreach ($data as $key => $value) {
            $paciente->{$key} = $value;
        }

        if (!$paciente->update($id)) {
            return $this->response('error', null, null, 500);
        }

        return $this->response('success', null, null, 204);
    }

    /**
     * Excluir um registro em específico
     * 
     * @param   array               $data           Dados vindos da URL ($data['id'])
     */
    public function destroy(array $data)
    {
        if(!$id = $this->exists($data)) {
            return $this->response('error', 'NOT FOUND', null, 404);
        }

        if(!Paciente::delete($id)) {
            return $this->response('error', null, null, 500);
        }

        return $this->response('success', null, null, 204);
    }
}