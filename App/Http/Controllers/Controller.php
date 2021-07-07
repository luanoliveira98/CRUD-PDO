<?php

namespace App\Http\Controllers;

class Controller {

    /**
     * Método para retorno padrão da API
     * 
     * @param   string              $status             Status da requisição ('success', 'error')
     * @param   string              $message            Mensagem da requisição
     * @param   mixed               $data               Dados da requisição
     * @param   int                 $statusCode         Status da requisição
     * 
     * @return  Void
     */
    public function response(string $status, string $message = null, $data = null, int $statusCode = null): Void
    {
        switch ($statusCode) {
            case '201':
                $statusCode .= " Created";
                break;
            case '204':
                $statusCode .= " No Content";
                break;
            case '400':
                $statusCode .= " Bad Request";
                break;
            case '404':
                $statusCode .= " Not Found";
                break;
            case '500':
                $statusCode .= " Internal Server Error";
                break;
            default:
                $statusCode = "200 OK";
                break;
        }
        header("HTTP/1.1 $statusCode");
        
        $response = (object) array(
            "status"    => $status,
            "message"   => ($message) ? $this->getMessage($message) : null,
            "data"      => $data
        );

        echo json_encode($response);
    }

    /**
     * Busca as mensagens correspondentes ao código de mensagem enviado
     * 
     * @param   string              $message            Mensagem da requisição
     * 
     * @return  string                                  Mensagem correspondente
     */
    public function getMessage(string $message): string
    {
        switch ($message) {
            case 'NOT FOUND':
                return 'Nenhum registro encontrado!';
                break;
            case 'INSERTED':
                return 'Novo registro inserido com sucesso!';
                break;
            case 'NO DATA':
                return 'Não foram enviados os dados necessários para requisição!';
                break;
            case 'ERROR VALIDATOR':
                return 'Erro ao validar dados da requisição!';
                break;
            default:
                return $message;
                break;
        }
        return $message;
    }

    /**
     * Verifica se o registro existe no banco de dados para
     * 
     * @param   array               $data           Dados vindos da URL ($data['id'])
     * 
     * @return  int                 $id             ID do registro, caso o mesmo exista
     */
    public function exists(array $data): int
    {
        $id = $data['id'];
        return ($this->model::find($id)) ? $id : null;
    }
    
    /**
     * Verifica se o registro existe no banco de dados para
     * 
     * @param   array               $data           Dados vindos da URL ($data['id'])
     * 
     * @return  int                 $id             ID do registro, caso o mesmo exista
     */
    public function hasData(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        return ($data) ? $data : [];
    }
}