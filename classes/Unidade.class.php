<?php
require_once("../classes/Database.class.php"); // Inclui a classe que lida com o banco de dados

class Unidade
{
    // Atributos da classe que representam as colunas da tabela "unidades" no banco de dados
    private $id;
    private $tipo;
    private $nome;


    public function getFatorConversao()
    {
        // Fatores de conversão de várias unidades para pixels
        switch ($this->nome) {
            case 'mm':
                return 3.77953; // 1 mm = 3.77953 px
            case 'cm':
                return 37.7953; // 1 cm = 37.7953 px
            case 'in':
                return 96; // 1 polegada = 96 px
            case 'pt':
                return 1.33333; // 1 ponto = 1.33333 px
            case 'pc':
                return 16; // 1 pica = 16 px
            case 'px':
                return 1; // pixels
            case 'em':
                return 16; // 1 em (base 16px) = 16 px (ajuste conforme necessário)
            case 'rem':
                return 16; // 1 rem (base 16px) = 16 px (ajuste conforme necessário)
            default:
                return 1; // Retorna 1 se a unidade não for reconhecida
        }
    }

    // Construtor da classe: inicializa os atributos com os valores passados como parâmetro
    private function carregarDados()
    {
        $sql = "SELECT * FROM unidades WHERE id = :id_unidade";
        $parametros = [':id_unidade' => $this->id]; // Assume que $this->id já foi definido
        $conexao = Database::conectar(); // Conecta ao banco de dados
        $stmt = $conexao->prepare($sql); // Prepara a consulta SQL

        // Executa a consulta com os parâmetros
        $stmt->execute($parametros);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC); // Busca os dados

        if ($dados) {
            // Se os dados foram encontrados, atribui os valores às propriedades da classe
            $this->nome = $dados['unidade'];
            $this->tipo = $dados['tipo'];
        } else {
            // Se não encontrar a unidade, lança uma exceção
            throw new Exception("Unidade não encontrada para o ID: " . $this->id);
        }
    }

    public function __construct($id = 0, $nome = '', $tipo = '')
    {
        $this->setIdUnidade($id);
        if ($id > 0) {
            // Carregar dados se o ID for válido
            $this->carregarDados();
        } else {
            // Defina valores padrão ou lance uma exceção se necessário
            $this->nome = $nome;
            $this->tipo = $tipo;
        }
    }

    // Métodos "setters" para definir os valores dos atributos, com validações para garantir a integridade dos dados

    public function setIdUnidade($id)
    {
        if ($id < 0) {
            throw new Exception("Erro: id inválido!"); // Lança uma exceção se o ID for negativo
        } else {
            $this->id = $id;
        }
    }

    public function setTipo($tipo)
    {
        if ($tipo == "") {
            throw new Exception("Erro, Tipo indefinido"); // Lança uma exceção se o tipo for vazio
        } else {
            $this->tipo = $tipo;
        }
    }

    public function setNome($nome)
    {
        if ($nome == "") {
            throw new Exception("Erro, nome indefinido"); // Lança uma exceção se o nome for vazio
        } else {
            $this->nome = $nome;
        }
    }

    // Métodos "getters" para acessar os valores dos atributos

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function getIdUnidade()
    {
        return $this->id;
    }

    // Métodos para interagir com o banco de dados (CRUD - Create, Read, Update, Delete)

    // Método para inserir uma nova unidade no banco de dados
    public function incluir()
    {
        $sql = 'INSERT INTO unidades (unidade, tipo, id) VALUES (:unidade, :tipo, :id)'; // Consulta SQL para inserção

        // Array com os parâmetros da consulta, associando os nomes dos parâmetros aos valores dos atributos
        $parametros = array(':tipo' => $this->nome, ':unidade' => $this->tipo, ':id' => $this->id);

        // Chama o método estático da classe Database para executar a consulta com os parâmetros
        return Database::executar($sql, $parametros);
    }

    // Método para excluir uma unidade do banco de dados com base no ID
    public function excluir()
    {
        $conexao = Database::getInstance(); // Obtém uma instância da conexão com o banco de dados
        $sql = 'DELETE FROM unidades WHERE id = :id'; // Consulta SQL para exclusão

        $comando = $conexao->prepare($sql); // Prepara a consulta SQL
        $comando->bindValue(':id', $this->id); // Associa o valor do ID ao parâmetro da consulta

        return $comando->execute(); // Executa a consulta e retorna o resultado (true para sucesso, false para falha)
    }

    // Método para atualizar os dados de uma unidade no banco de dados com base no ID
    public function alterar()
    {
        $sql = 'UPDATE unidades SET unidade = :unidade, tipo = :tipo WHERE id = :id'; // Consulta SQL para atualização

        // Array com os parâmetros da consulta
        $parametros = array(':tipo' => $this->nome, ':unidade' => $this->tipo, ':id' => $this->id);

        // Chama o método estático da classe Database para executar a consulta com os parâmetros
        return Database::executar($sql, $parametros);
    }

    // Método estático para listar unidades do banco de dados, com opções de filtro por tipo ou busca
    public static function listar($tipo = 0, $busca = "")
    {
        $sql = "SELECT * FROM unidades"; // Consulta SQL básica para selecionar todas as unidades

        // Adiciona condições à consulta com base nos parâmetros de filtro
        if ($tipo > 0) {
            switch ($tipo) {
                case 1: // Filtra por ID exato
                    $sql .= " WHERE id = :busca";
                    break;
                case 2: // Filtra por tipo, permitindo busca parcial
                    $sql .= " WHERE tipo LIKE :busca";
                    $busca = "%{$busca}%"; // Adiciona "%" para busca parcial
                    break;
            }
        }

        $parametros = []; // Inicializa o array de parâmetros

        // Adiciona o parâmetro de busca ao array se necessário
        if ($tipo > 0) {
            $parametros = array(':busca' => $busca);
        }

        // Executa a consulta com os parâmetros e armazena o resultado em $comando
        $comando = Database::executar($sql, $parametros);

        $unidades = array(); // Inicializa o array que armazenará os objetos Unidade

        // Itera sobre os resultados da consulta, criando objetos Unidade e adicionando-os ao array
        while ($forma = $comando->fetch(PDO::FETCH_ASSOC)) {
            $unidade = new Unidade($forma['id'], $forma['tipo'], $forma['unidade']);
            array_push($unidades, $unidade);
        }

        return $unidades; // Retorna o array de objetos Unidade
    }
}
