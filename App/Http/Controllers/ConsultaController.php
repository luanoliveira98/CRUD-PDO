<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Validator;

class ConsultaController extends Controller {

    public $model = 'Consulta';

    /**
     * Listar os registros
     */
    public function index()
    {
        $consultas = Consulta::select(null, array('dt_agendamento' => '', 'horario' => ''));
        return $this->response('success', null, $consultas);
    }

    /**
     * Listar os registros vínculados a um paciente
     * 
     * @param   array               $data           Dados vindos da URL ($data['paciente_id'])
     */
    public function getByPacienteId(array $data)
    {
        $consultas = Consulta::select(array('paciente_id' => $data['paciente_id']), array('dt_agendamento' => ''));
        return $this->response('success', null, $consultas);
    }

    public function isScheduled(string $date, string $time): bool
    {
        return count(Consulta::select(array('dt_agendamento' => $date, 'horario' => $time), null)) > 0;
    }

    /**
     * Inserir novo registro
     */
    public function store()
    {
        if(!$data = $this->hasData()) {
            return $this->response('error', 'NO DATA', null, 400);
        }

        $data['status'] = (isset($data['status'])) ? $data['status'] : 'pendente';

        if($validator = Validator::validate($this->model, $data)) {
            return $this->response('error', 'ERROR VALIDATOR', $validator, 400);
        }

        if($this->isScheduled($data['dt_agendamento'], $data['horario'])) {
            return $this->response('error', 'Data e horário já reservados para outra consulta!', 400);
        }

        $consulta = new Consulta();
        $consulta->dt_agendamento = $data['dt_agendamento'];
        $consulta->horario = $data['horario'];
        $consulta->status = $data['status'];
        $consulta->especialidade_id = $data['especialidade_id'];
        $consulta->paciente_id = $data['paciente_id'];
        if (!$consulta->save()) {
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
        $consulta = Consulta::find($id);
        if(!$consulta) {
            return $this->response('error', 'NOT FOUND', null, 404);
        }
        
        return $this->response('success', null, $consulta);
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

        if($this->isScheduled($data['dt_agendamento'], $data['horario'])) {
            return $this->response('error', 'Data e horário já reservados para outra consulta!', 400);
        }

        $consulta = new Consulta();
        foreach ($data as $key => $value) {
            $consulta->{$key} = $value;
        }

        if (!$consulta->update($id)) {
            return $this->response('error', null, null, 500);
        }

        return $this->response('success', null, null, 204);
    }

    /**
     * Muda status para Executado
     * 
     * @param   array               $data           Dados vindos da URL ($data['id'])
     */
    public function execute(array $data)
    {
        if(!$id = $this->exists($data)) {
            return $this->response('error', 'NOT FOUND', null, 404);
        }

        $consulta = new Consulta();
        $consulta->status = 'Executado';

        if (!$consulta->update($id)) {
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

        if(!Consulta::delete($id)) {
            return $this->response('error', null, null, 500);
        }

        return $this->response('success', null, null, 204);
    }

    /**
     * Listar as consultas registradas para hoje
     */
    public function getScheduledToday()
    {
        $consultas = Consulta::select(array('dt_agendamento' => date('Y-m-d')), array('horario' => ''));
        return $this->response('success', null, $consultas);
    }
}