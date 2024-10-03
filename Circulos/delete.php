<?php
// Inclua o arquivo de configuração do banco de dados ou as classes necessárias
require_once '../classes/Database.class.php'; // Ou o caminho correto para seu arquivo de conexão

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Garantindo que o ID é um número inteiro

    // Prepare a consulta SQL para excluir o círculo
    $sql = "DELETE FROM circulos WHERE id = :id";
    $parametros = array(':id' => $id);

    // Executar a consulta
    try {
        Database::executar($sql, $parametros);
        header("Location: index.php"); // Redireciona de volta para a página principal
        exit();
    } catch (Exception $e) {
        echo "Erro ao excluir o círculo: " . $e->getMessage();
    }
} else {
    echo "ID não especificado.";
}
?>
