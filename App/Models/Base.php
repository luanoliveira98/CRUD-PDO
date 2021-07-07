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
            self::$instance = new \PDO('mysql:host=localhost;dbname=hygia;charset=utf8','root','');
        }

        return self::$instance;
    }

    /**
     * Método para criação/inserção de dado no banco de dados
     * 
     * @return  bool                            Inserção ou não de novo registro
     */
    public function create(): bool
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
                $this->dt_insercao = date("Y-m-d h:i:sa");
                break;
            case 'delete':
                $this->dt_exclusao = date("Y-m-d h:i:sa");
                break;
            default:
                break;
        }
        $this->dt_alteracao = date("Y-m-d h:i:sa");
        return;
    }

    /**
     * Método para seleção de registros
     * 
     * @return  array                           Registros selecionados
     */
    public static function select(): array
    {
        $sql = "SELECT * FROM ".self::getTabela()." WHERE dt_exclusao IS NULL";

        $stmt = self::getConn()->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
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
        $sql = "SELECT * FROM ".self::getTabela()." WHERE dt_exclusao IS NULL AND id = ?";

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
    public static function getTabela(): string
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