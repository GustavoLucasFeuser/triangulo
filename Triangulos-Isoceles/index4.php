<?php
// Conectar ao banco de dados
try {
    $pdo = new PDO("mysql:host=localhost;dbname=geometria2", "root", ""); // Substitua 'usuario' e 'senha' conforme necessário
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $lado1 = $_POST['lado1'];
    $lado2 = $_POST['lado2'];
    $base = $_POST['base'];
    $cor = $_POST['cor'];
    $id_unidade = $_POST['id_unidade']; // Assume que você tem uma unidade selecionada

    try {
        // Inserir uma nova forma
        $stmt = $pdo->prepare("INSERT INTO formas (cor, fundo, id_unidade) VALUES (:cor, '#FFFFFF', :id_unidade)");
        $stmt->execute([':cor' => $cor, ':id_unidade' => $id_unidade]);

        // Inserir um triângulo
        $stmt = $pdo->prepare("INSERT INTO triangulos (id_forma, lado1, lado2, lado3) VALUES (LAST_INSERT_ID(), :lado1, :lado2, :base)");
        $stmt->execute([':lado1' => $lado1, ':lado2' => $lado2, ':base' => $base]);

        // Inserir um triângulo isósceles
        $stmt = $pdo->prepare("INSERT INTO triangulos_isosceles (id_triangulo, lado1, lado2, base, cor) VALUES (LAST_INSERT_ID(), :lado1, :lado2, :base, :cor)");
        $stmt->execute([':lado1' => $lado1, ':lado2' => $lado2, ':base' => $base, ':cor' => $cor]);

        echo "Triângulo isósceles inserido com sucesso!";
    } catch (PDOException $e) {
        echo "Erro ao incluir triângulo isósceles: " . $e->getMessage();
    }
}

// Obter os triângulos isósceles cadastrados
$stmt = $pdo->prepare("SELECT ti.id, ti.lado1, ti.lado2, ti.base, ti.cor FROM triangulos_isosceles ti JOIN triangulos t ON ti.id_triangulo = t.id");
$stmt->execute();
$triangulos_isosceles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Triângulos Isósceles</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclua o seu CSS -->
    <style>
        canvas {
            border: 1px solid #000;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Cadastro de Triângulos Isósceles</h1>
    <form method="POST">
        <label for="lado1">Lado 1:</label>
        <input type="number" name="lado1" required>

        <label for="lado2">Lado 2:</label>
        <input type="number" name="lado2" required>

        <label for="base">Base:</label>
        <input type="number" name="base" required>

        <label for="cor">Cor:</label>
        <input type="color" name="cor" required>

        <label for="id_unidade">Unidade de medida:</label>
        <select name="id_unidade" required>
            <option value="">Selecione</option>
            <option value="1">px</option>
            <!-- Adicione mais opções conforme necessário -->
        </select>

        <button type="submit">Salvar</button>
        <button type="reset">Resetar</button>
    </form>

    <h2>Triângulos Isósceles Cadastrados</h2>
    <ul>
        <?php foreach ($triangulos_isosceles as $triangulo): ?>
            <li>
                Lados: <?= $triangulo['lado1'] ?> px, <?= $triangulo['lado2'] ?> px, <?= $triangulo['base'] ?> px
                <br>
                Cor: <?= $triangulo['cor'] ?>
                <br>
                <canvas id="canvas-<?= $triangulo['id'] ?>" width="200" height="200"></canvas>
                <script>
    const canvas = document.getElementById('canvas-<?= $triangulo['id'] ?>');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpa o canvas antes de desenhar

    const larguraCanvas = canvas.width;
    const alturaCanvas = canvas.height;

    // Aumente o fator de escala se necessário
    const fatorEscala = Math.min(larguraCanvas / <?= $triangulo['base'] ?>, alturaCanvas / (<?= $triangulo['lado1'] ?> + <?= $triangulo['lado2'] ?>));

    ctx.fillStyle = '<?= $triangulo['cor'] ?>';
    const altura = Math.sqrt(Math.pow(<?= $triangulo['lado1'] ?>, 2) - Math.pow(<?= $triangulo['base'] ?> / 2, 2)); // Cálculo da altura

    ctx.beginPath();
    ctx.moveTo(larguraCanvas / 2, (alturaCanvas / 2) - (altura * fatorEscala)); // Topo do triângulo
    ctx.lineTo((larguraCanvas / 2) - (<?= $triangulo['base'] ?> / 2 * fatorEscala), (alturaCanvas / 2)); // Base esquerda
    ctx.lineTo((larguraCanvas / 2) + (<?= $triangulo['base'] ?> / 2 * fatorEscala), (alturaCanvas / 2)); // Base direita
    ctx.closePath();
    ctx.fill();
</script>


                <form action="delete.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $triangulo['id'] ?>">
                    <button type="submit">Excluir</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
