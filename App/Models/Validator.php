<?php


namespace App\Models;

class Validator extends Base {

    private static array $data = [];
    private static array $erros = [];

    /**
     * Busca regras aplicadas na model
     * 
     * @param   string              $model          Model para buscar as regras de validação
     * @param   string              $type           Tipo da requisição
     * 
     * @return  array                               Regras de validação da model
     */
    public static function getRules(string $model, string $type): array
    {
        
        $model =  self::getModel($model);
        $modelValidate = new $model();
        return $modelValidate->getRules($type);
    }

    /**
     * Método para validar dados da requisição de acordo com as regras aplicadas na model
     * 
     * @param   string              $model          Model para buscar as regras de validação
     * @param   array               $data           Dados para serem validados
     * @param   string              $type           Tipo da requisição
     * 
     * @return  object               $erros         Erros retornados da validação
     */
    public static function validate(string $model, array $data, string $type = 'insert'): array
    {
        self::$data = $data;
        $regras = self::getRules($model, $type);

        foreach ($regras as $key => $value) {
            foreach (explode('|', $value) as $explodeValue) {
                $opcoes = explode(':', $explodeValue);
                $regra = $opcoes[0];
                switch ($regra) {
                    case 'date':
                        if (!self::date($key)) self::setMessage($key, $regra);
                        break;
                    case 'time':
                        if (!self::time($key)) self::setMessage($key, $regra);
                        break;
                    case 'enum':
                        if (!self::enum($key, $opcoes[1])) self::setMessage($key, $regra);
                        break;
                    case 'size':
                        if (!self::size($key, $opcoes[1])) self::setMessage($key, $regra, $opcoes[1]);
                        break;
                    case 'exists':
                        if (!self::exists($key, $opcoes[1])) self::setMessage($key, $regra, $opcoes[1]);
                        break;
                    case 'number':
                        if (!self::number($key)) self::setMessage($key, $regra);
                        break;
                    case 'email':
                        if (!self::email($key)) self::setMessage($key, $regra);
                        break;
                    default:
                        if (!self::required($key)) self::setMessage($key, $regra);
                        break;
                }
            }
            
        }
        return self::$erros;
    }

    /**
     * Atribui as mensagens de erro
     * 
     * @param   string              $campo          Campo com o erro
     * @param   string              $regra          Regra do erro
     * @param   string              $extra          Dados extra para mensagem
     * 
     * @return  Void
     */
    public static function setMessage(string $campo, string $regra, string $extra = null): Void
    {
        $mensagem = "O campo $campo ";

        switch ($regra) {
            case 'date':
                $mensagem .= "deve ser uma data válida!";
                break;
            case 'time':
                $mensagem .= "deve ser um horário válido!";
                break;
            case 'enum':
                $mensagem .= "deve ser uma opção válida!";
                break;
            case 'exists':
                $mensagem .= "deve ser referente a um registro!";
                break;
            case 'number':
                $mensagem .= "deve ser um número válido!";
                break;
            case 'email':
                $mensagem .= "deve ser um email válido!";
                break;
            case 'size':
                $mensagem .= "deve ter $extra caracteres!";
                break;
            default:
                $mensagem .= "é obrigatório!";
                break;
        }
        self::$erros[] = array(
            "field" => $campo,
            "message" => $mensagem
        );
    }

    /**
     * Regra de parametro obrigatório
     * 
     * @param   string              $key            Parametro para ser validado
     * 
     * @return  bool                                Resultado da validação
     */
    public static function required(string $key): bool
    {
        if (!array_key_exists($key, self::$data)) return false;
        return (!empty(self::$data[$key]));
    }

    /**
     * Regra de data válida
     * 
     * @param   string              $key            Parametro para ser validado
     * 
     * @return  bool                                Resultado da validação
     */
    public static function date(string $key): bool
    {
        if (!self::required($key)) return true;
        return date('Y-m-d', strtotime(self::$data[$key])) == self::$data[$key];
    }

    /**
     * Regra de horário válido
     * 
     * @param   string              $key            Parametro para ser validado
     * 
     * @return  bool                                Resultado da validação
     */
    public static function time(string $key): bool
    {
        if (!self::required($key)) return true;
        return date('H:i:s', strtotime(self::$data[$key])) || date('H:i', strtotime(self::$data[$key])) == self::$data[$key];
    }

    /**
     * Regra de opção válida
     * 
     * @param   string              $key            Parametro para ser validado
     * @param   string              $opcoes         Opções para validação
     * 
     * @return  bool                                Resultado da validação
     */
    public static function enum(string $key, string $opcoes): bool
    {
        if (!self::required($key)) return true;
        return in_array(self::$data[$key], explode(',',$opcoes));
    }

    /**
     * Regra de número de caracteres
     * 
     * @param   string              $key            Parametro para ser validado
     * @param   string              $tamanho        Tamanho em números de caracteres
     * 
     * @return  bool                                Resultado da validação
     */
    public static function size(string $key, string $tamanho): bool
    {
        if (!self::required($key)) return true;
        return strlen(self::$data[$key]) == $tamanho;
    }

    /**
     * Regra de existência do id na tabela definida
     * 
     * @param   string              $key            Parametro para ser validado
     * @param   string              $tabela         Tabela para comparação
     * 
     * @return  bool                                Resultado da validação
     */
    public static function exists(string $key, string $tabela): bool
    {
        if (!self::required($key)) return true;
        $model =  self::getModel($tabela);
        return count($model::find(self::$data[$key])) > 0;
    }

    /**
     * Regra de número válido
     * 
     * @param   string              $key            Parametro para ser validado
     * 
     * @return  bool                                Resultado da validação
     */
    public static function number(string $key): bool
    {
        if (!self::required($key)) return true;
        return is_numeric(self::$data[$key]);
    }

    /**
     * Regra de email válido
     * 
     * @param   string              $key            Parametro para ser validado
     * 
     * @return  bool                                Resultado da validação
     */
    public static function email(string $key): bool
    {
        if (!self::required($key)) return true;
        return filter_var(self::$data[$key], FILTER_VALIDATE_EMAIL);
    }
}