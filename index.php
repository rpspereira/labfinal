<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuração da base de dados
$host = '10.0.1.4'; // IP da tua VM MySQL
$user = 'mysqladmin';
$pass = '@pass123!';
$db   = 'frutaria';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de ligação: " . $conn->connect_error);
}

// Adicionar fruta
if (isset($_POST['add'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);
    $conn->query("INSERT INTO frutas (nome, quantidade, preco) VALUES ('$nome', $quantidade, $preco)");
}

// Remover fruta
if (isset($_GET['remover'])) {
    $id = intval($_GET['remover']);
    $conn->query("DELETE FROM frutas WHERE id=$id");
}

// Buscar frutas
$result = $conn->query("SELECT * FROM frutas ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Loja de Frutas</title>
</head>
<body>
    <h1>Loja de Frutas</h1>
    <form method="post">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="number" name="quantidade" placeholder="Quantidade" required>
        <input type="number" step="0.01" name="preco" placeholder="Preço" required>
        <button type="submit" name="add">Adicionar</button>
    </form>
    <table border="1">
        <tr><th>Nome</th><th>Quantidade</th><th>Preço</th><th>Ação</th></tr>
        <?php while($f = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($f['nome']) ?></td>
            <td><?= $f['quantidade'] ?></td>
            <td><?= $f['preco'] ?></td>
            <td><a href="?remover=<?= $f['id'] ?>" onclick="return confirm('Remover?')">Remover</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
