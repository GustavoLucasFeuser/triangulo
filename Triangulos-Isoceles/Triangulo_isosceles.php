<?php
require_once("../classes/Database.class.php");
require_once("../classes/Unidade.class.php");
require_once("../classes/Triangulo.class.php");
require_once("../classes/TrianguloIsosceles.class.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'salvar') {
    $lado1 = $_POST['lado'];
    $lado2 = $_POST['base'];
    $cor = $_POST['cor'];
    $id_unidade = $_POST['medida'];

    $unidade = new Unidade($id_unidade);
    $triangulo = new Triangulo(0, $lado1, $lado2, $lado2, $cor, $unidade);

    try {
        $triangulo->incluir();
        $id_triangulo = Database::$lastId; // Armazena o ID retornado após a inclusão

        $trianguloIsosceles = new TrianguloIsosceles(0, $lado1, $lado2, $cor, $unidade);
        $trianguloIsosceles->incluir($id_triangulo);
        
        header("Location: index4.php");
        exit();
    } catch (Exception $e) {
        echo "Erro ao salvar: " . $e->getMessage();
    }
}
?>
