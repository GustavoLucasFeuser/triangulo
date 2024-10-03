<!DOCTYPE html>
<html lang="en">
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('../navbar.php');
require_once("../classes/Database.class.php");
require_once("../classes/Unidade.class.php");
require_once("../classes/Circulo.class.php");

$busca = isset($_GET['busca']) ? $_GET['busca'] : "";
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 0;
$lista = Circulo::listar($tipo, $busca); // Busca os triângulos conforme a busca

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'salvar') {
    // Captura os dados do formulário
    $raio = $_POST['raio'];
    $cor = $_POST['cor'];
    $id_unidade = $_POST['medida'];

    // Criação de um novo objeto Unidade
    $unidade = new Unidade($id_unidade);
    $circulo = new Circulo(0, $raio, $cor, $unidade);

    // Tente incluir o círculo no banco de dados
    try {
        $circulo->incluir();
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        echo "Erro ao salvar: " . $e->getMessage();
    }
}

// Inicializar a variável $circulos
$circulos = [];
try {
    $circulos = Circulo::listar();

} catch (Exception $e) {
    echo "Erro ao listar círculos: " . $e->getMessage();
}
?>

<head>
    <title>Cadastro de Círculos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Cadastro de Círculos</h2>
        <form action="lista_circulos.php" method="post" class="row g-3">
            <div class="col-md-4">
                <label for="raio" class="form-label">Raio:</label>
                <input type="number" name="raio" id="raio" class="form-control" placeholder="Digite o raio" required>
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

        <h2>Círculos Desenhados</h2>
        <div class="row" style="display: flex; flex-wrap: wrap;">
            <?php
            foreach ($circulos as $circulo) {
                $raio = $circulo->getRaio();
                $cor = $circulo->getCor();
                $unidade = $circulo->getUnidade()->getNome(); // Exemplo: 'px', 'cm', etc.
                $id = $circulo->getId(); // Obtendo o ID do círculo

                // Definindo o diâmetro com a unidade de medida selecionada
                $diametro = $raio * 2 . $unidade;

                // Exibir o círculo com a unidade de medida aplicada ao raio e cor de fundo correta
                echo "<div class='col-md-4' style='margin-bottom: 20px;'>";
                echo "<svg width='$diametro' height='$diametro'>";
                echo "<circle cx='$raio$unidade' cy='$raio$unidade' r='$raio$unidade' style='fill:$cor;stroke:black;stroke-width:1' />";
                echo "</svg>";
                echo "<p>Raio: $raio $unidade</p>"; // Exibir o raio com a unidade de medida
                echo "<p>Cor: $cor</p>";
                echo "<a href='delete.php?id=$id' class='btn btn-danger btn-sm'>Excluir</a> "; // Passando o ID correto
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>

</html>