<?php
require_once("../classes/Triangulo.class.php");
require_once("../classes/Database.class.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']); // Converte para inteiro

    try {
        $conexao = Database::getInstance();
        
        // Deletar o triângulo com o ID fornecido
        $sql = "DELETE FROM triangulos_equilateros WHERE id = :id"; 

        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: index3.php");
            exit(); // Adiciona exit para evitar que o script continue
        } else {
            echo "Erro ao excluir o registro.";
            // Adicione esta linha para depurar o erro
            echo "Erro: " . implode(", ", $stmt->errorInfo());
        }
    } catch (PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
} else {
    echo "ID inválido.";
}
?>
