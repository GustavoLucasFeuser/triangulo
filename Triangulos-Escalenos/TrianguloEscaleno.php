<?php
require_once("../classes/Database.class.php");
require_once("../classes/Unidade.class.php");
require_once("../classes/TrianguloEscaleno.class.php");
require_once("../classes/Triangulo.class.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'salvar') {
    // Captura os dados do formulário
    // Captura os dados do formulário
    $lado1 = $_POST['lado1'];
    $lado2 = $_POST['lado2'];
    $lado3 = $_POST['lado3'];
    $cor = $_POST['cor'];
    $id_unidade = $_POST['medida']; // Certifique-se de que o campo 'medida' é enviado

    // Criação de um novo objeto Unidade
    $unidade = new Unidade($id_unidade);
    $triangulo = new Triangulo(0, $lado1, $lado2, $lado3, $cor, $unidade); // Certifique-se de que Unidade recebe o ID corretamente

    try {
        $triangulo->incluir();
        $id_triangulo = Database::$lastId; // Obtenha o ID do triângulo que foi inserido


        $trianguloEscaleno = new TrianguloEscaleno(0, $lado1, $lado2, $lado3, $cor, $unidade); // Passa o ID do triângulo
        $trianguloEscaleno->incluir($id_triangulo);
        // Redirecionar ou mostrar mensagem de sucesso
        header("Location: index5.php"); // Altere para a página desejada
        exit();
    } catch (Exception $e) {
        echo "Erro ao salvar: " . $e->getMessage();
    }
}
