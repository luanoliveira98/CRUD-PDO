<?php

namespace App\Models;

class Base {
    
    private static $instance;
    public $datas = ['dt_insercao', 'dt_alteracao', 'dt_exclusao'];

    /**
     * Faz conexão com o banco de dados
     * 
     * @return  PDO                            Conexão feita com o banco de dados
     */
    public static function getConn(): \PDO 
    {
        
        if(!isset(self::$instance)) {
            self::$instance = new \PDO(DB.":host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,DB_USER,DB_PASSWORD);

        }

        return self::$instance;
    }

    /**
     * Método para inserção de dado no banco de dados
     * 
     * @return  bool                            Inserção ou não de novo registro
     */
    public function save(): bool
    {
        $this->setTimestamps();
        $query = $this->getQuery();

        $sql = "INSERT INTO $this->tabela (".$query['campos'].") VALUES (".$query['values'].")";
        $stmt = self::getConn()->prepare($sql);
        $this->bindValues($stmt, $query['campos']);
        return $stmt->execute();
    }

    /**
     * Atribui os dados para utilização na query sql
     * 
     * @param   PDOStatement    $stmt           Query criada
     * @param   string          $campos         Campos a serem utilizados na query
     * @param   int             $id             ID do registro
     * 
     * @return  Void
     */
    public function bindValues(\PDOStatement $stmt, string $campos, int $id = null): Void
    {
        $count = explode(",", $campos);
        for ($i=0; $i < count($count); $i++) {
            $stmt->bindValue($i+1, $this->{$count[$i]});
        }

        if($id) {
            $stmt->bindValue($i+1, $id);
        }
        return;
    }

    /**
     * Cria os auxiliares que serão utilizados para criação da query
     * 
     * @param   string          $type           Tipo da query a ser criada (insert, update, delete)
     * 
     * @return  array                           Auxiliares para criação da query ['campos, 'values']
     */
    public function getQuery(string $type = 'insert'): array
    {
        $campos = [];
        $values = [];
        foreach ($this->campos as $campo) {
            if (isset($this->{$campo})) {
                $campos[] = $campo;
                $values[] = ($type == 'insert') ? '?' : "$campo = ?";
            }
        }
        return array(
            'campos' => implode(',',$campos), 
            'values' => implode(',',$values)
        );
    }

    /**
     * Cria as datas de inserção, alteração e exclusão para servirem de log nos registros
     * 
     * @param   string          $type           Tipo da query a ser criada (insert, update, delete)
     * 
     * @return  Void
     */
    public function setTimestamps(string $type = 'insert'): Void
    {
        $this->campos = array_merge($this->campos, $this->datas);

        switch ($type) {
            case 'insert':
                $this->dt_insercao = date("Y-m-d H:i:sa");
                break;
            case 'delete':
                $this->dt_exclusao = date("Y-m-d H:i:sa");
                break;
            default:
                break;
        }
        $this->dt_alteracao = date("Y-m-d H:i:sa");
        return;
    }

    /**
     * Método para seleção de registros
     * 
     * @param   array               $where          Condicionais da query
     * @param   array               $orderBy        Ordenação da query
     * 
     * @return  array                               Registros selecionados
     */
    public static function select(array $where = null, array $orderBy = null): array
    {
        $sql = self::getDefaultSelectQuery();
        $sql .= self::addQueryOptions($where, $orderBy);

        $stmt = self::getConn()->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    /**
     * Define o select padrão inicial para qualquer query
     * 
     * @return  string                              Query padrão
     */
    public static function setDefaultSelectQuery(): string 
    {
        return "SELECT * FROM ".self::getTable()." WHERE dt_exclusao IS NULL";
    }

    /**
     * Define o select padrão inicial para qualquer query
     * 
     * @return  string                              Query padrão
     */
    public static function getDefaultSelectQuery(): string 
    {
        $class = self::getClass();
        $inst = new $class();
        return $inst::setDefaultSelectQuery();
    }

    /**
     * Adiciona condicionais e/ou ordenação na query
     * 
     * @param   array               $where          Condicionais da query
     * @param   array               $orderBy        Ordenação da query
     * 
     * @return  string              $sql            Adicionais para query
     */
    public static function addQueryOptions(array $where = null, array $orderBy = null): string
    {
        $sql = '';
        $inst = new self();
        if ($where) $sql .= $inst->getWhere($where);
        if ($orderBy) $sql .= $inst->getOrderBy($orderBy);

        return $sql;
    }

    /**
     * Adiciona condicionais na query
     * 
     * @param   array               $where          Condicionais da query
     * 
     * @return  string              $sql            Condicionais para query
     */
    public function getWhere(array $orderBy): string
    {
        $sql = '';

        foreach($orderBy as $key => $value) {
            switch ($value) {
                case null:
                case 'null':
                case 'NULL':
                    $sql .= " AND $key IS NULL";
                    break;
                    $sql .= " AND $key IS NULL";
                    break;
                case 'not null':
                case 'NOT NULL':
                    $sql .= " AND $key IS NOT NULL";
                    break;
                default:
                    $sql .= " AND $key = '$value'";
                    break;
            }
        }

        return $sql;
    }

    /**
     * Adiciona ordenação na query
     * 
     * @param   array               $orderBy        Ordenação da query
     * 
     * @return  string              $sql            Ordenação para query
     */
    public function getOrderBy(array $orderBy): string
    {
        $sql = ' ORDER BY';

        foreach($orderBy as $key => $value) {
            if ($key !== array_key_first($orderBy)) {
                $sql .= ",";
            }

            $value = ($value) ? $value : 'asc';
            $sql .= " $key $value";
        }

        return $sql;
    }

    /**
     * Método para seleção de um registro específico
     * 
     * @param   int             $id             ID do registro
     * 
     * @return  array                           Registros selecionados
     */
    public static function find(int $id): array
    {
        $sql = self::getDefaultSelectQuery()." AND ".self::getTable().".id = ?";

        $stmt = self::getConn()->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC)[0];
        }
        return [];
    }

    /**
     * Busca a tabela da classe que chamou o método (usado para funções estáticas)
     * 
     * @return  string                           Nome da tabela
     */
    public static function getTable(): string
    {
        $class = self::getClass();
        $inst = new $class();
        return $inst->tabela;
    }

    /**
     * Busca a classe que chamou o método
     * 
     * @return  string                           Classe
     */
    public static function getClass(): string
    {
        return get_called_class();
    }

    /**
     * Busca a model
     * 
     * @return  string                           Model
     */
    public static function getModel(string $model): string
    {
        return str_replace("|", "", "App\Models\|".ucfirst($model));
    }

    /**
     * Método para atualização de um registro específico
     * 
     * @param   int             $id             ID do registro
     * 
     * @return  bool                            Atualização ou não do registro
     */
    public function update(int $id) 
    {
        $this->setTimestamps('update');
        $query = $this->getQuery('update');

        $sql = "UPDATE $this->tabela SET ".$query['values']." WHERE id = ?";
        $stmt = self::getConn()->prepare($sql);
        $this->bindValues($stmt, $query['campos'], $id);
        return $stmt->execute();
    }

    /**
     * Método para exclusão de um registro específico
     * 
     * @param   int             $id             ID do registro
     * 
     * @return  bool                            Exclusão ou não do registro
     */
    public static function delete(int $id) {
        $class = self::getClass();
        $inst = new $class();
        $inst->setTimestamps('delete');
        $query = $inst->getQuery('delete');

        $sql = "UPDATE $inst->tabela SET ".$query['values']." WHERE id = ?";
        $stmt = self::getConn()->prepare($sql);
        $inst->bindValues($stmt, $query['campos'], $id);
        return $stmt->execute();
    }
}