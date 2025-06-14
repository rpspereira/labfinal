<?php
// Configuração da ligação à base de dados
$host = '10.0.1.4'; // IP privado da tua VM MySQL
$user = 'mysqladmin';
$pass = '@pass123!';
$db   = 'frutaria';

// Cria ligação
$conn = new mysqli($host, $user, $pass, $db);

// Verifica ligação
if ($conn->connect_error) {
    die("Falha na ligação: " . $conn->connect_error);
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

// Atualizar fruta
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);
    $conn->query("UPDATE frutas SET quantidade=$quantidade, preco=$preco WHERE id=$id");
}

// Buscar frutas
$result = $conn->query("SELECT * FROM frutas ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Loja de Conveniência de Frutas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 60%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f4f4f4; }
        form { margin: 0; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Loja de Conveniência de Frutas</h1>
    <h2>Adicionar Nova Fruta</h2>
    <form method="post">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="number" name="quantidade" placeholder="Quantidade" min="0" required>
        <input type="number" step="0.01" name="preco" placeholder="Preço (€)" min="0" required>
        <button type="submit" name="add">Adicionar</button>
    </form>
    <h2>Inventário</h2>
    <table>
        <tr>
            <th>Nome</th>
            <th>Quantidade</th>
            <th>Preço (€)</th>
            <th>Ações</th>
        </tr>
        <?php while($fruta = $result->fetch_assoc()): ?>
        <tr>
            <form method="post">
                <td><?= htmlspecialchars($fruta['nome']) ?></td>
                <td>
                    <input type="number" name="quantidade" value="<?= $fruta['quantidade'] ?>" min="0" required>
                </td>
                <td>
                    <input type="number" step="0.01" name="preco" value="<?= $fruta['preco'] ?>" min="0" required>
                </td>
                <td class="actions">
                    <input type="hidden" name="id" value="<?= $fruta['id'] ?>">
                    <button type="submit" name="update">Atualizar</button>
                    <a href="?remover=<?= $fruta['id'] ?>" onclick="return confirm('Remover esta fruta?')">Remover</a>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
