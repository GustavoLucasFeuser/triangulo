<?php
require_once("../classes/Database.class.php");
require_once("../classes/Unidade.class.php");

class TrianguloIsosceles
{
    private $id;
    private $lado1;
    private $lado2;
    private $cor;
    private $unidade;

    public function __construct($id, $lado1, $lado2, $cor, Unidade $unidade)
    {
        $this->id = $id;
        $this->lado1 = $lado1;
        $this->lado2 = $lado2;
        $this->cor = $cor;
        $this->unidade = $unidade;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getLado1()
    {
        return $this->lado1;
    }
    
    public function getLado2()
    {
        return $this->lado2;
    }
    
    public function getCor()
    {
        return $this->cor;
    }
    
    public function getUnidade()
    {
        return $this->unidade;
    }

    public static function listar($tipo, $busca)
    {
        $sql = "SELECT ti.*, u.id as id_unidade, u.tipo 
                FROM triangulos_isosceles ti 
                JOIN unidades u ON ti.id_triangulo = u.id";

        if ($tipo == 1) {
            $sql .= " WHERE ti.id = :busca";
        } elseif ($tipo == 2) {
            $sql .= " WHERE ti.lado1 = :busca";
        } elseif ($tipo == 3) {
            $sql .= " WHERE u.tipo = :busca";
        }

        $conexao = Database::conectar();
        $stmt = $conexao->prepare($sql);

        if ($tipo == 1 || $tipo == 2 || $tipo == 3) {
            $stmt->bindParam(':busca', $busca);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $lista = [];
        foreach ($result as $row) {
            $id_unidade = $row['id_unidade'];
            $unidade = new Unidade($id_unidade, $row['tipo']);
            $lista[] = new TrianguloIsosceles($row['id'], $row['lado1'], $row['lado2'], $row['cor'], $unidade);
        }
        return $lista;
    }

    public function incluir()
    {
        try {
            $sql_triangulo = "INSERT INTO triangulos_isosceles (id_triangulo, lado1, lado2, cor) VALUES (:id_triangulo, :lado1, :lado2, :cor)";
            $parametros_triangulo = [
                ':id_triangulo' => $this->unidade->getIdUnidade(),
                ':lado1' => $this->lado1,
                ':lado2' => $this->lado2,
                ':cor' => $this->cor
            ];

            return Database::executar($sql_triangulo, $parametros_triangulo);
        } catch (Exception $e) {
            throw new Exception("Erro ao incluir triângulo isósceles: " . $e->getMessage());
        }
    }

    public function desenhar()
    {
        // Desenho do triângulo isósceles
        echo "<div style='width: 0; height: 0; border-left: {$this->lado1}px solid transparent; border-right: {$this->lado1}px solid transparent; border-bottom: {$this->lado2}px solid {$this->cor};'></div>";
    }

    public function ehTrianguloValido()
    {
        // Para ser um triângulo, a soma de dois lados deve ser maior que o terceiro
        return ($this->lado1 + $this->lado2 > $this->lado1);
    }

    public function calcularPerimetro()
    {
        return $this->lado1 + $this->lado2 + $this->lado1; // lado1 é igual ao lado2 em um triângulo isósceles
    }

    public function calcularArea()
    {
        // Fórmula da área de um triângulo: A = (base * altura) / 2
        // Precisamos calcular a altura usando o Teorema de Pitágoras
        $altura = sqrt(($this->lado1 * $this->lado1) - ($this->lado1 / 2) * ($this->lado1 / 2));
        return ($this->lado1 * $altura) / 2;
    }
}
}
