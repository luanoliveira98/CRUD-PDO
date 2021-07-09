<?php


namespace App\Models;

class Consulta extends Base {

    protected $tabela = 'consultas';
    protected $campos = ['dt_agendamento', 'horario', 'status', 'especialidade_id', 'paciente_id'];

    public function getRules(string $type = 'insert'): array
    {
        return array(
            'dt_agendamento'    => 'required|date|date_min:today',
            'horario'           => 'required|time',
            'status'            => 'required|enum:executado,pendente',
            'especialidade_id'  => 'required|exists:especialidade',
            'paciente_id'       => 'required|exists:paciente',
        );
    }

    /**
     * Define o select padrão inicial para qualquer query
     * 
     * @return  string                              Query padrão
     */
    public static function setDefaultSelectQuery(): string 
    {
        return "SELECT consultas.id, dt_agendamento, horario, status, especialidade_id, especialidades.nome as especialidade, paciente_id, pacientes.nome as paciente FROM ".self::getTable()."
        JOIN pacientes ON pacientes.id = consultas.paciente_id JOIN especialidades ON especialidades.id = consultas.especialidade_id WHERE consultas.dt_exclusao IS NULL";
    }
}
