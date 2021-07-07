<?php


namespace App\Models;

class Validator extends Base {

    private static array $data = [];
    private static array $erros = [];

    /**
     * Busca regras aplicadas na model
     * 
     * @param   string              $model          Model para buscar as regras de validação
     * 
     * @return  array                               Regras de validação da model
     */
    public static function getRules(string $model): array
    {
        $modelValidate = new $model();
        return $modelValidate->getRules();
    }

    /**
     * Método para validar dados da requisição de acordo com as regras aplicadas na model
     * 
     * @param   string              $model          Model para buscar as regras de validação
     * @param   array               $data           Dados para serem validados
     * 
     * @return  object               $erros          Erros retornados da validação
     */
    public static function validate(string $model, array $data): array
    {
        self::$data = $data;
        $regras = self::getRules($model);

        foreach ($regras as $key => $value) {
            foreach (explode('|', $value) as $explodeValue) {
                $opcoes = explode(':', $explodeValue);
                $regra = $opcoes[0];
                switch ($regra) {
                    case 'date':
                        if (!self::date($key)) self::setMessage($key, $regra);
                        break;
                    case 'enum':
                        if (!self::enum($key, $opcoes[1])) self::setMessage($key, $regra);
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
        echo json_encode(self::$erros);
        die();
        return self::$erros;
    }

    /**
     * Atribui as mensagens de erro
     * 
     * @param   string              $campo          Campo com o erro
     * @param   string              $regra          Regra do erro
     * 
     * @return  Void
     */
    public static function setMessage(string $campo, string $regra): Void
    {
        $mensagem = "O campo $campo ";

        switch ($regra) {
            case 'date':
                $mensagem .= "deve ser uma data válida!";
                break;
            case 'enum':
                $mensagem .= "deve ser uma opção válida!";
                break;
            case 'number':
                $mensagem .= "deve ser um número válido!";
                break;
            case 'email':
                $mensagem .= "deve ser um email válido!";
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
        return array_key_exists($key, self::$data);
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