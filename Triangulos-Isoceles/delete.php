<?php
// Conectar ao banco de dados
try {
    $pdo = new PDO("mysql:host=localhost;dbname=geometria2", "root", ""); // Substitua 'usuario' e 'senha' conforme necessário
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verificar se o ID foi passado
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Excluir o triângulo isósceles
        $stmt = $pdo->prepare("DELETE FROM triangulos_isosceles WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Excluir o triângulo relacionado
        $stmt = $pdo->prepare("DELETE FROM triangulos WHERE id = (SELECT id_triangulo FROM triangulos_isosceles WHERE id = :id)");
        $stmt->execute([':id' => $id]);

        echo "Triângulo isósceles excluído com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao excluir triângulo: " . $e->getMessage();
    }
}

header("Location: index4.php");
exit;
