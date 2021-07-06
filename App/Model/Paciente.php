<?php

namespace App\Model;

class Paciente {

    private $id, $nome, $dt_nascimento, $endereco, $sexo, $telefone, $email, $dt_insercao, $dt_alteracao, $dt_exclusao;

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome(string $nome) {
        $this->nome = $nome;
    }

    public function getDtNascimento() {
        return $this->dt_nascimento;
    }

    public function setDtNascimento(string $dt_nascimento) {
        $this->dt_nascimento = $dt_nascimento;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function setEndereco(string $endereco) {
        $this->endereco = $endereco;
    }

    public function getSexo() {
        return $this->sexo;
    }

    public function setSexo(string $sexo) {
        $this->sexo = $sexo;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function setTelefone(string $telefone) {
        $this->telefone = $telefone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail(string $email) {
        $this->email = $email;
    }

    public function getDtInsercao() {
        return $this->dt_insercao;
    }

    public function setDtInsercao(string $dt_insercao) {
        $this->dt_insercao = $dt_insercao;
    }

    public function getDtAlteracao() {
        return $this->dt_alteracao;
    }

    public function setDtAlteracao(string $dt_alteracao) {
        $this->dt_alteracao = $dt_alteracao;
    }

    public function getDtExclusao() {
        return $this->dt_exclusao;
    }

    public function setDtExclusao(string $dt_exclusao) {
        $this->dt_exclusao = $dt_exclusao;
    }
}