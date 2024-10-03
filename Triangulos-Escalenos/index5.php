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
    $trianguloEscaleno = new TrianguloEscaleno(0, $lado1, $lado2, $lado3, $unidade, $cor);

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
    <title>Formulário de criação de triângulos escalenos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">

        <h2>Cadastro de Triângulos Escalenos</h2>
        <form action="TrianguloEscaleno.php" method="post" class="row g-3" onsubmit="return validarEntradas()">
            <div class="col-md-4">
                <label for="lado1" class="form-label">Lado 1:</label>
                <input type="number" name="lado1" id="lado1" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="lado2" class="form-label">Lado 2:</label>
                <input type="number" name="lado2" id="lado2" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="lado3" class="form-label">Lado 3:</label>
                <input type="number" name="lado3" id="lado3" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="cor" class="form-label">Cor:</label>
                <input type="color" name="cor" id="cor" class="form-control form-control-color" value="#000000">
            </div>
            <div class="col-md-4">
                <label for="medida" class="form-label">Unidade de medida:</label>
                <select name='medida' id='medida' class="form-select" required>
                    <option value="0">Selecione</option>
                    <?php
                    $uniLista = Unidade::listar();
                    foreach ($uniLista as $unidade) {
                        echo "<option value='{$unidade->getIdUnidade()}'>{$unidade->getNome()}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" name="acao" value="salvar" class="btn btn-primary">Salvar</button>
                <button type="reset" class="btn btn-secondary">Resetar</button>
            </div>
        </form>

        <script>
            function validarEntradas() {
                const lado1 = parseFloat(document.getElementById('lado1').value);
                const lado2 = parseFloat(document.getElementById('lado2').value);
                const lado3 = parseFloat(document.getElementById('lado3').value);

                if (lado1 <= 0 || lado2 <= 0 || lado3 <= 0) {
                    alert("Os lados devem ser maiores que zero.");
                    return false;
                }

                if (lado1 === lado2 || lado1 === lado3 || lado2 === lado3) {
                    alert("Os lados devem ser diferentes.");
                    return false;
                }

                return true; // Permite o envio do formulário
            }
        </script>


        <hr>

        <h2>Pesquisar</h2>
        <form action="" method="get" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="busca" id="busca" class="form-control" placeholder="Procurar">
            </div>
            <div class="col-md-3">
                <select name="tipo" id="tipo" class="form-select">
                    <option value="1">ID</option>
                    <option value="2">Lado 1</option>
                    <option value="3">Lado 2</option>
                    <option value="4">Lado 3</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-success">Buscar</button>
            </div>
        </form>

        <br>

        <h2>Triângulos Escalenos Desenhados</h2>
        <div class="row" style="display: flex; flex-wrap: wrap;">
            <?php
            foreach ($lista as $trianguloEscaleno) {
                $lado1 = $trianguloEscaleno->getLado1();
                $lado2 = $trianguloEscaleno->getLado2();
                $lado3 = $trianguloEscaleno->getLado3();
                $unidade = $trianguloEscaleno->getUnidade()->getNome();
                $cor = $trianguloEscaleno->getCor();

                // Usar a fórmula de Herão para calcular a área do triângulo
                $s = ($lado1 + $lado2 + $lado3) / 2; // semi-perímetro
                $area = sqrt($s * ($s - $lado1) * ($s - $lado2) * ($s - $lado3));

                // Altura baseada na área e na base (lado2)
                $altura = (2 * $area) / $lado2;

                // Calcular o fator de conversão baseado na unidade selecionada
                $fatorConversao = match ($unidade) {
                    'mm' => 3.77953,
                    'cm' => 37.7953,
                    'in' => 96,
                    'pt' => 1.33333,
                    'pc' => 16,
                    'px' => 1,
                    'em' => 16, // ajuste conforme necessário
                    'rem' => 16, // ajuste conforme necessário
                    'vw' => 1, // porcentagem da viewport
                    'vh' => 1, // porcentagem da viewport
                    'vmin' => 1, // porcentagem da viewport
                    'vmax' => 1, // porcentagem da viewport
                    default => 1, // px
                };

                // Converter todas as dimensões para pixels
                $lado1Px = $lado1 * $fatorConversao;
                $lado2Px = $lado2 * $fatorConversao;
                $alturaPx = $altura * $fatorConversao;

                // Calcular os pontos do triângulo escaleno
                $points = "0,$alturaPx $lado2Px,0 " . (($lado2Px - $lado1Px) * 0.5) . ",0";

                echo "<div class='col-md-4' style='margin-bottom: 20px;'>"; // Adiciona margem inferior
                echo "<svg width='100%' height='" . ($alturaPx + 20) . "px'>"; // A altura do SVG se ajusta à altura do triângulo
                echo "<polygon points='$points' style='fill:$cor;stroke:black;stroke-width:1' />";
                echo "</svg>";
                echo "<p>Lados: $lado1 $unidade, $lado2 $unidade, $lado3 $unidade</p>";
                echo "<p>Cor: $cor</p>";
                echo "<a href='delete.php?id=" . $trianguloEscaleno->getId() . "' class='btn btn-danger btn-sm'>Excluir</a> ";
                echo "</div>";
            }
            ?>
        </div>

    </div>
</body>

</html>