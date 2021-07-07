<?php

namespace App\Model;

class Base {
    
    private static $instance;
    public $datas = ['dt_insercao', 'dt_alteracao', 'dt_exclusao'];

    public static function getConn() {
        
        if(!isset(self::$instance)) {
            self::$instance = new \PDO('mysql:host=localhost;dbname=hygia;charset=utf8','root','');
        }

        return self::$instance;
    }

    public function create() {
        $this->setTimestamps();
        $query = $this->getQuery();

        $sql = "INSERT INTO $this->tabela (".$query['campos'].") VALUES (".$query['values'].")";
        $stmt = self::getConn()->prepare($sql);
        $this->bindValues($stmt, $query['campos']);
        $stmt->execute();
        return;
    }

    public function bindValues($stmt, $campos, $id = null) {
        $count = explode(",", $campos);
        for ($i=0; $i < count($count); $i++) {
            $stmt->bindValue($i+1, $this->{$count[$i]});
        }

        if($id) {
            $stmt->bindValue($i+1, $id);
        }
        return;
    }

    public function getQuery(string $type = 'insert') {
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

    public function setTimestamps(string $type = 'insert') {
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

    public static function select(int $id = null) {
        
        $sql = "SELECT * FROM ".self::getTabela();

        if ($id) {
            $sql .= " WHERE id = ?";
        }

        $stmt = self::getConn()->prepare($sql);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }
        return [];
    }

    public static function getTabela() {
        $class = self::getClass();
        $inst = new $class();
        return $inst->tabela;
    }

    public static function getClass() {
        return get_called_class();
    }

    public function update(int $id) {
        $this->setTimestamps('update');
        $query = $this->getQuery('update');

        $sql = "UPDATE $this->tabela SET ".$query['values']." WHERE id = ?";
        $stmt = self::getConn()->prepare($sql);

        $this->bindValues($stmt, $query['campos'], $id);
        $stmt->execute();
        return;
    }
}