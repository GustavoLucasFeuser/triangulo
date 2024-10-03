<?php
require_once("Database.class.php");

class Circulo
{
    private $id;
    private $raio;
    private $cor;
    private $unidade;

    public function __construct($id, $raio, $cor, $unidade)
    {
        $this->id = $id;
        $this->raio = $raio;
        $this->cor = $cor;
        $this->unidade = $unidade;
    }

    public function incluir()
    {
        $sql = 'INSERT INTO circulos (id_unidade, raio, cor) VALUES (:id_unidade, :raio, :cor)';
        $parametros = array(':id_unidade' => $this->unidade->getIdUnidade(), ':raio' => $this->raio, ':cor' => $this->cor);

        return Database::executar($sql, $parametros);
    }

    public function setId($id)
    {
        if ($id < 0) {
            throw new Exception("Erro: id inválido!");
        } else {
            $this->id = $id;
        }
    }

    public function setRaio($raio)
    {
        if ($raio < 1) {
            throw new Exception("Erro, raio indefinido");
        } else {
            $this->raio = $raio;
        }
    }

    public function setCor($cor)
    {
        if ($cor == "") {
            throw new Exception("Erro, cor indefinida");
        } else {
            $this->cor = $cor;
        }
    }

    public function setUnidade(Unidade $unidade)
    {
        $this->unidade = $unidade;
    }


    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getRaio()
    {
        return $this->raio;
    }

    public function getCor()
    {
        return $this->cor;
    }

    public function getUnidade()
    {
        return $this->unidade;
    }

    public static function listar($tipo = 0, $busca = "")
    {
        // SQL para selecionar círculos e suas informações, agora referenciando a tabela 'unidades'
        $sql = "SELECT circulos.*, unidades.unidade, unidades.tipo FROM circulos 
            JOIN unidades ON circulos.id_unidade = unidades.id";

        // Verificação do tipo de busca
        if ($tipo > 0) {
            switch ($tipo) {
                case 1:
                    $sql .= " WHERE circulos.id = :busca";
                    break;
                case 2:
                    $sql .= " WHERE circulos.raio LIKE :busca";
                    $busca = "%{$busca}%";
                    break;
                case 3:
                    $sql .= " WHERE circulos.cor LIKE :busca";
                    $busca = "%{$busca}%";
                    break;
                case 4:
                    $sql .= " WHERE unidades.tipo LIKE :busca";
                    $busca = "%{$busca}%";
                    break;
            }
        }

        // Array para parâmetros da consulta
        $parametros = [];
        if ($tipo > 0) {
            $parametros = array(':busca' => $busca);
        }

        // Executar a consulta
        $comando = Database::executar($sql, $parametros);
        $circulos = array();

        // Percorrer os resultados e criar objetos Circulo
        while ($circuloData = $comando->fetch(PDO::FETCH_ASSOC)) {
            $unidade = new Unidade($circuloData['id_unidade'], $circuloData['unidade'], $circuloData['tipo']); // Agora referenciando 'unidades'
            $circulo = new Circulo($circuloData['id'], $circuloData['raio'], $circuloData['cor'], $unidade);
            array_push($circulos, $circulo);
        }

        return $circulos;
    }
}
