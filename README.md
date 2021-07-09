## Sequência de comandos para rodar o sistema

Instalar as dependências

### composer install

Importar o banco de dados, o arquivo está em **Database/database.sql**

Configurar as variáveis de ambiente em **Config/app.php**

* Opcional: arquivo com as rotas no insomnia em **Routes/insomnia.json**
## Rotas

Rotas da API criada:

### Pacientes

* **Listar Pacientes:** [GET] /pacientes/
* **Cadastrar Paciente:** [POST] /pacientes/
* **Listar Paciente:** [GET] /pacientes/:id
* **Atualizar Paciente:** [PUT] /pacientes/:id
* **Excluir Paciente:** [DELETE] /pacientes/:id

### Consultas

* **Listar Consultas:** [GET] /consultas/
* **Agendar Consulta:** [POST] /consultas/
* **Listar Consulta:** [GET] /consultas/:id
* **Atualizar Consulta:** [PUT] /consultas/:id
* **Concluir Consulta:** [PATCH] /consultas/:id
* **Excluir Consulta:** [DELETE] /consultas/:id
* **Listar Consultas do Paciente:** [GET] /consultas/paciente/:paciente_id
* **Listar Consultas de Hoje:** [GET] /consultas/agendadas/hoje

### Especialidades

* **Listar Especialidades:** [GET] /especialidades/
* **Cadastrar Especialidade:** [POST] /especialidades/
* **Listar Especialidade:** [GET] /especialidades/:id
* **Atualizar Especialidade:** [PUT] /especialidades/:id
* **Excluir Especialidade:** [DELETE] /especialidades/:id
