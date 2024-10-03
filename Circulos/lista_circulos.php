<?php
require_once("../classes/Circulo.class.php");
require_once("../classes/Database.class.php");
require_once("../classes/Unidade.class.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'salvar') {
    // Captura os dados do formulário
    $raio = $_POST['raio'];
    $cor = $_POST['cor'];
    $id_unidade = $_POST['medida']; // Certifique-se de que o campo 'medida' é enviado

    // Criação de um novo objeto Unidade
    $unidade = new Unidade($id_unidade); // Você deve garantir que o id é válido
    $circulo = new Circulo(0, $raio, $cor, $unidade);

    var_dump($_POST);
    // Tente incluir o círculo no banco de dados
   try {
        $circulo->incluir();
        // Redirecionar ou mostrar mensagem de sucesso
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        echo "Erro ao salvar: " . $e->getMessage();
   }
}
?>