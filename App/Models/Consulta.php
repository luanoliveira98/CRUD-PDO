<?php


namespace App\Models;

class Consulta extends Base {

    protected $tabela = 'consultas';
    protected $campos = ['dt_agendamento', 'horario', 'status', 'especialidade_id', 'paciente_id'];

    public function getRules(string $type = 'insert'): array
    {
        switch ($type) {
            case 'update':
                return array(
                    'dt_agendamento'    => 'date',
                    'horario'           => 'time',
                    'status'            => 'enum:executado,pendente',
                    'especialidade_id'  => 'exists:especialidade',
                    'paciente_id'       => 'exists:paciente',
                );
                break;
            
            default:
                return array(
                    'dt_agendamento'    => 'required|date',
                    'horario'           => 'required|time',
                    'status'            => 'required|enum:executado,pendente',
                    'especialidade_id'  => 'required|exists:especialidade',
                    'paciente_id'       => 'required|exists:paciente',
                );
                break;
        }
    }
}
