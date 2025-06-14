<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuração da base de dados
$host = '10.0.1.4'; // IP privado da VM MySQL (ajusta se for diferente)
$user = 'frutaria_user';
$pass = 'frutaria_pass';
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
    $observacoes = $conn->real_escape_string($_POST['observacoes'] ?? '');
    $conn->query("INSERT INTO frutas (nome, quantidade, preco, observacoes) VALUES ('$nome', $quantidade, $preco, '$observacoes')");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Remover fruta
if (isset($_GET['remover'])) {
    $id = intval($_GET['remover']);
    $conn->query("DELETE FROM frutas WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Atualizar fruta
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);
    $observacoes = $conn->real_escape_string($_POST['observacoes'] ?? '');
    $conn->query("UPDATE frutas SET quantidade=$quantidade, preco=$preco, observacoes='$observacoes' WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Buscar frutas
$result = $conn->query("SELECT * FROM frutas ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍎 Loja de Conveniência de Frutas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background: #3498db; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .add-form { background: #ecf0f1; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        input[type="text"], input[type="number"], textarea { padding: 8px; margin: 5px; border: 1px solid #ccc; border-radius: 4px; }
        textarea { resize: vertical; }
        button { padding: 10px 15px; margin: 5px; cursor: pointer; background: #3498db; color: white; border: none; border-radius: 4px; }
        button:hover { background: #2980b9; }
        .remove-btn { background: #e74c3c; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; }
        .remove-btn:hover { background: #c0392b; }
        .update-btn { background: #27ae60; }
        .update-btn:hover { background: #229954; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍎 Loja de Conveniência de Frutas</h1>
        
        <div class="add-form">
            <h2>➕ Adicionar Nova Fruta</h2>
            <form method="post">
                <input type="text" name="nome" placeholder="Nome da fruta" required>
                <input type="number" name="quantidade" placeholder="Quantidade" min="0" required>
                <input type="number" step="0.01" name="preco" placeholder="Preço (€)" min="0" required>
                <textarea name="observacoes" placeholder="Observações" rows="2" cols="30"></textarea>
                <button type="submit" name="add">Adicionar Fruta</button>
            </form>
        </div>
        
        <h2>📋 Inventário Atual</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>🍏 Fruta</th>
                    <th>📦 Quantidade</th>
                    <th>💰 Preço (€)</th>
                    <th>📝 Observações</th>
                    <th>⚙️ Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($fruta = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="post" style="display: contents;">
                            <td><?= $fruta['id'] ?></td>
                            <td><strong><?= htmlspecialchars($fruta['nome']) ?></strong></td>
                            <td>
                                <input type="number" name="quantidade" value="<?= $fruta['quantidade'] ?>" min="0" required style="width: 70px;">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="preco" value="<?= $fruta['preco'] ?>" min="0" required style="width: 80px;">
                            </td>
                            <td>
                                <textarea name="observacoes" rows="2" cols="30"><?= htmlspecialchars($fruta['observacoes']) ?></textarea>
                            </td>
                            <td>
                                <input type="hidden" name="id" value="<?= $fruta['id'] ?>">
                                <button type="submit" name="update" class="update-btn">✏️ Atualizar</button>
                                <a href="?remover=<?= $fruta['id'] ?>" class="remove-btn" onclick="return confirm('Remover <?= htmlspecialchars($fruta['nome']) ?>?')">🗑️ Remover</a>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">Nenhuma fruta encontrada no inventário.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p style="text-align: center; margin-top: 30px; color: #7f8c8d;"><em>✅ Base de dados configurada automaticamente via Terraform!</em></p>
    </div>
</body>
</html>
