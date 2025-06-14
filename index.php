<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuração da base de dados
$host = '10.0.1.4'; // IP privado da tua VM MySQL (ajusta conforme o output do Terraform)
$user = 'frutaria_user'; // Username criado no provisionamento
$pass = 'frutaria_pass'; // Password criada no provisionamento
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
        table { border-collapse: collapse; width: 80%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f4f4f4; }
        form { margin: 20px 0; }
        input[type="text"], input[type="number"] { padding: 5px; margin: 5px; }
        button { padding: 8px 15px; margin: 5px; cursor: pointer; }
        .add-form { background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>🍎 Loja de Conveniência de Frutas</h1>
    
    <div class="add-form">
        <h2>Adicionar Nova Fruta</h2>
        <form method="post">
            <input type="text" name="nome" placeholder="Nome da fruta" required>
            <input type="number" name="quantidade" placeholder="Quantidade" min="0" required>
            <input type="number" step="0.01" name="preco" placeholder="Preço (€)" min="0" required>
            <button type="submit" name="add">Adicionar Fruta</button>
        </form>
    </div>
    
    <h2>📋 Inventário</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Quantidade</th>
                <th>Preço (€)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($fruta = $result->fetch_assoc()): ?>
            <tr>
                <form method="post" style="display: contents;">
                    <td><?= $fruta['id'] ?></td>
                    <td><?= htmlspecialchars($fruta['nome']) ?></td>
                    <td>
                        <input type="number" name="quantidade" value="<?= $fruta['quantidade'] ?>" min="0" required style="width: 60px;">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="preco" value="<?= $fruta['preco'] ?>" min="0" required style="width: 70px;">
                    </td>
                    <td>
                        <input type="hidden" name="id" value="<?= $fruta['id'] ?>">
                        <button type="submit" name="update">Atualizar</button>
                        <a href="?remover=<?= $fruta['id'] ?>" onclick="return confirm('Remover <?= htmlspecialchars($fruta['nome']) ?>?')" style="color: red; text-decoration: none;">❌ Remover</a>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <p><em>Base de dados atualizada automaticamente via Terraform!</em></p>
</body>
</html>
