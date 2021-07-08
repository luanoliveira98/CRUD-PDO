<?php

namespace App\Http\Controllers;

use App\Models\Especialidade;
use App\Models\Validator;

class EspecialidadeController extends Controller {

    public $model = 'Especialidade';

    /**
     * Listar os registros
     */
    public function index()
    {
        $especialidades = Especialidade::select(null, array('nome' => ''));
        return $this->response('success', null, $especialidades);
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

        $especialidade = new Especialidade();
        $especialidade->nome = $data['nome'];
        if (!$especialidade->save()) {
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
        $especialidade = Especialidade::find($id);
        if(!$especialidade) {
            return $this->response('error', 'NOT FOUND', null, 404);
        }
        
        return $this->response('success', null, $especialidade);
    }

    /**
     * Atualizar um registro em específico
     * 
     * @param   array               $data           Dados vindos da URL ($data['id'])
     */
    public function update(array $data)
    {
        if(!$id = $this->exists($data)) {
            return $this->response('error', 'NOT FOUND', null, 404);
        }

        if(!$data = $this->hasData()) {
            return $this->response('error', 'NO DATA', null, 400);
        }

        if($validator = Validator::validate($this->model, $data, 'update')) {
            return $this->response('error', 'ERROR VALIDATOR', $validator, 400);
        }

        $especialidade = new Especialidade();
        foreach ($data as $key => $value) {
            $especialidade->{$key} = $value;
        }

        if (!$especialidade->update($id)) {
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

        if(!Especialidade::delete($id)) {
            return $this->response('error', null, null, 500);
        }

        return $this->response('success', null, null, 204);
    }
}