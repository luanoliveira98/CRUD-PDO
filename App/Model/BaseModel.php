<?php

namespace App\Model;

class BaseModel {
    
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
        $auxiliares = $this->getAuxiliares();

        $sql = "INSERT INTO $this->tabela (".$auxiliares['campos'].") VALUES (".$auxiliares['values'].")";
        $stmt = self::getConn()->prepare($sql);
        $this->bindValues($stmt, $auxiliares['campos']);
        $stmt->execute();
        return;
    }

    public function bindValues($stmt, $campos) {
        $count = explode(",", $campos);
        for ($i=0; $i < count($count); $i++) {
            $stmt->bindValue($i+1, $this->{$count[$i]});
        }
        return;
    }

    public function getAuxiliares() {
        $camposArray = [];
        $valuesArray = [];
        foreach ($this->campos as $campo) {
            if (isset($this->{$campo})) {
                $camposArray[] = $campo;
                $valuesArray[] = '?';
            }
        }
        return array(
            'campos' => implode(',',$camposArray), 
            'values' => implode(',',$valuesArray)
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
        echo $this->dt_insercao;
        return;
    }
}