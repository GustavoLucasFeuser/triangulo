<?php
require_once("../classes/Triangulo.class.php");
require_once("../classes/Database.class.php");

$id = $_GET['id'];

try {
    $conexao = Database::getInstance();

    // Excluir primeiro os registros da tabela triangulos_equilateros, por exemplo:
    $sqlDeleteEquilateros = "DELETE FROM triangulos_equilateros WHERE id_triangulo = :id";
    $stmtEquilateros = $conexao->prepare($sqlDeleteEquilateros);
    $stmtEquilateros->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtEquilateros->execute();

    // Agora excluir da tabela triangulos
    $sqlDeleteTriangulo = "DELETE FROM triangulos WHERE id = :id";
    $stmtTriangulo = $conexao->prepare($sqlDeleteTriangulo);
    $stmtTriangulo->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmtTriangulo->execute()) {
        header("Location: index2.php");
    } else {
        echo "Erro ao excluir o triângulo.";
    }
} catch (PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}
