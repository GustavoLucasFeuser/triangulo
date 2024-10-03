<?php
require_once("../classes/Database.class.php");
require_once("../classes/Unidade.class.php");

class TrianguloEquilatero
{
    private $id;
    private $lado;
    private $cor;
    private $unidade;

    public function __construct($id, $lado, $cor, Unidade $unidade)
    {
        $this->id = $id;
        $this->lado = $lado;
        $this->cor = $cor;
        $this->unidade = $unidade;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLado()
    {
        return $this->lado;
    }

    public function getCor()
    {
        return $this->cor;
    }

    public function getUnidade()
    {
        return $this->unidade;
    }
    
    public function incluir()
    {
        try {
            $sql_triangulo = "INSERT INTO triangulos_equilateros (id_triangulo, lado, cor) VALUES (:id_triangulo, :lado, :cor)";
            $parametros_triangulo = [
                ':id_triangulo' => $this->unidade->getIdUnidade(),
                ':lado' => $this->lado,
                ':cor' => $this->cor // Incluindo a cor nos parâmetros
            ];

            return Database::executar($sql_triangulo, $parametros_triangulo);
        } catch (Exception $e) {
            throw new Exception("Erro ao incluir triângulo equilatero: " . $e->getMessage());
        }
    }

    public static function listar($tipo, $busca)
    {
        $sql = "SELECT te.*, u.id as id_unidade, u.tipo 
                FROM triangulos_equilateros te 
                JOIN unidades u ON te.id_triangulo = u.id";

        $conexao = Database::conectar();
        $stmt = $conexao->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $lista = [];
        foreach ($result as $row) {
            $id_unidade = $row['id_unidade'];
            $unidade = new Unidade($id_unidade, $row['tipo']);
            $lista[] = new TrianguloEquilatero($row['id'], $row['lado'], $row['cor'], $unidade);
        }
        return $lista;
    }
}
