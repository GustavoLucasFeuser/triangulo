<!DOCTYPE html>
<html lang="en">
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('../navbar.php');
include_once('TrianguloEscaleno.php');
require_once("../classes/Unidade.class.php");

// Verifica se a requisição GET foi feita para buscar triângulos escalenos
$busca = isset($_GET['busca']) ? $_GET['busca'] : "";
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 0;
$lista = TrianguloEscaleno::listar($tipo, $busca);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'salvar') {
    // Captura os dados do formulário
    $lado1 = $_POST['lado1'];
    $lado2 = $_POST['lado2'];
    $lado3 = $_POST['lado3'];
    $cor = $_POST['cor'];
    $id_unidade = $_POST['medida'];

    // Criação de um novo objeto Unidade
    $unidade = new Unidade($id_unidade);

    // Criação de um novo objeto TrianguloEscaleno
    $trianguloEscaleno = new TrianguloEscaleno(0, $lado1, $lado2, $lado3, $cor, $unidade);

    // Tente incluir o triângulo escaleno no banco de dados
    try {
        $trianguloEscaleno->incluir();
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        echo "Erro ao salvar: " . $e->getMessage();
    }
}
?>

<head>
    <title>Triângulos Escalenos</title>
</head>

<body>

    <h2>Cadastro de Triângulos Escalenos</h2>
    <form action="TrianguloEscaleno.php" method="post">
        <label>Lado 1:</label>
        <input type="number" name="lado1" required>
        <label>Lado 2:</label>
        <input type="number" name="lado2" required>
        <label>Lado 3:</label>
        <input type="number" name="lado3" required>
        <label>Cor:</label>
        <input type="color" name="cor" value="#000000">
        <label>Unidade de medida:</label>
        <select name='medida' required>
            <option value="0">Selecione</option>
            <?php
            $uniLista = Unidade::listar();
            foreach ($uniLista as $unidade) {
                echo "<option value='{$unidade->getIdUnidade()}'>{$unidade->getNome()}</option>";
            }
            ?>
        </select>
        <button type="submit" name="acao" value="salvar">Salvar</button>
        <button type="reset">Resetar</button>
    </form>

    <h2>Pesquisar</h2>
    <form action="" method="get">
        <input type="text" name="busca" placeholder="Procurar">
        <select name="tipo">
            <option value="1">ID</option>
            <option value="2">Lado 1</option>
            <option value="3">Lado 2</option>
            <option value="4">Lado 3</option>
        </select>
        <button type="submit">Buscar</button>
    </form>

    <h2>Triângulos Escalenos Desenhados</h2>
    <div>
        <?php
        foreach ($lista as $trianguloEscaleno) {
            $lado1 = $trianguloEscaleno->getLado1();
            $lado2 = $trianguloEscaleno->getLado2();
            $lado3 = $trianguloEscaleno->getLado3();
            $unidade = $trianguloEscaleno->getUnidade()->getNome();
            $cor = $trianguloEscaleno->getCor();

            // Usar a fórmula de Heron para calcular a área do triângulo
            $area = $trianguloEscaleno->calcularArea();
            $perimetro = $trianguloEscaleno->calcularPerimetro();

            // Calcular altura
            $altura = (2 * $area) / $lado2;

            // Exibir informações
            echo "<div style='margin: 10px;'>";
            echo "<svg width='200' height='100'>";
            echo "<polygon points='0,$altura 100,0 50,0' style='fill:$cor;stroke:black;stroke-width:1' />";
            echo "</svg>";
            echo "Lados: $lado1 $unidade, $lado2 $unidade, $lado3 $unidade<br>";
            echo "Perímetro: " . round($perimetro, 2) . " $unidade<br>";
            echo "Área: " . round($area, 2) . " $unidade<sup>2</sup><br>";
            echo "Cor: $cor<br>";
            echo "<a href='delete.php?id=" . $trianguloEscaleno->getId() . "'>Excluir</a>";
            echo "</div>";
        }
        ?>
    </div>

</body>

</html>
