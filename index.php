<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Usa as credenciais dos outputs do Terraform
$host = '10.0.1.4'; // Será o IP privado da VM MySQL
$user = 'frutaria_user';
$pass = 'frutaria_pass';
$db   = 'frutaria';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de ligação: " . $conn->connect_error);
}

// Criar pasta uploads se não existir
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Função para fazer upload da imagem
function uploadImage($file) {
    global $upload_dir;
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if ($file['size'] > $max_size) {
        return false;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    $new_filename = uniqid() . '.' . $file_extension;
    $target_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $new_filename;
    }
    
    return false;
}

// Adicionar fruta
if (isset($_POST['add'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);
    $imagem = '';
    
    // Processar upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploaded_image = uploadImage($_FILES['imagem']);
        if ($uploaded_image) {
            $imagem = $uploaded_image;
        }
    }
    
    $conn->query("INSERT INTO frutas (nome, quantidade, preco, imagem) VALUES ('$nome', $quantidade, $preco, '$imagem')");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Remover fruta
if (isset($_GET['remover'])) {
    $id = intval($_GET['remover']);
    
    // Obter nome da imagem antes de apagar o registo
    $result = $conn->query("SELECT imagem FROM frutas WHERE id=$id");
    if ($row = $result->fetch_assoc()) {
        if ($row['imagem'] && file_exists($upload_dir . $row['imagem'])) {
            unlink($upload_dir . $row['imagem']); // Apagar ficheiro da imagem
        }
    }
    
    $conn->query("DELETE FROM frutas WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Atualizar fruta
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);
    
    $update_query = "UPDATE frutas SET quantidade=$quantidade, preco=$preco";
    
    // Se foi enviada uma nova imagem
    if (isset($_FILES['nova_imagem']) && $_FILES['nova_imagem']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploaded_image = uploadImage($_FILES['nova_imagem']);
        if ($uploaded_image) {
            // Apagar imagem antiga
            $result = $conn->query("SELECT imagem FROM frutas WHERE id=$id");
            if ($row = $result->fetch_assoc() && $row['imagem']) {
                if (file_exists($upload_dir . $row['imagem'])) {
                    unlink($upload_dir . $row['imagem']);
                }
            }
            
            $update_query .= ", imagem='$uploaded_image'";
        }
    }
    
    $update_query .= " WHERE id=$id";
    $conn->query($update_query);
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
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background: #3498db; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .add-form { background: #ecf0f1; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        input[type="text"], input[type="number"], input[type="file"] { padding: 8px; margin: 5px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; margin: 5px; cursor: pointer; background: #3498db; color: white; border: none; border-radius: 4px; }
        button:hover { background: #2980b9; }
        .remove-btn { background: #e74c3c; color: white; text-decoration: none; padding: 5px 10px; border-radius: 3px; }
        .remove-btn:hover { background: #c0392b; }
        .update-btn { background: #27ae60; }
        .update-btn:hover { background: #229954; }
        .fruit-image { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .no-image { width: 60px; height: 60px; background: #ecf0f1; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #7f8c8d; font-size: 12px; }
        .image-upload { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍎 Loja de Conveniência de Frutas</h1>
        
        <div class="add-form">
            <h2>➕ Adicionar Nova Fruta</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="nome" placeholder="Nome da fruta" required>
                <input type="number" name="quantidade" placeholder="Quantidade" min="0" required>
                <input type="number" step="0.01" name="preco" placeholder="Preço (€)" min="0" required>
                <div class="image-upload">
                    <label>📷 Imagem da fruta:</label>
                    <input type="file" name="imagem" accept="image/*">
                    <small style="color: #7f8c8d;">(JPG, PNG, GIF, WEBP - máx. 5MB)</small>
                </div>
                <button type="submit" name="add">Adicionar Fruta</button>
            </form>
        </div>
        
        <h2>📋 Inventário Atual</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>📷 Imagem</th>
                    <th>🍏 Fruta</th>
                    <th>📦 Quantidade</th>
                    <th>💰 Preço (€)</th>
                    <th>⚙️ Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($fruta = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="post" enctype="multipart/form-data" style="display: contents;">
                            <td><?= $fruta['id'] ?></td>
                            <td>
                                <?php if ($fruta['imagem'] && file_exists($upload_dir . $fruta['imagem'])): ?>
                                    <img src="<?= $upload_dir . htmlspecialchars($fruta['imagem']) ?>" class="fruit-image" alt="<?= htmlspecialchars($fruta['nome']) ?>">
                                <?php else: ?>
                                    <div class="no-image">Sem imagem</div>
                                <?php endif; ?>
                                <div class="image-upload">
                                    <input type="file" name="nova_imagem" accept="image/*" style="font-size: 11px;">
                                </div>
                            </td>
                            <td><strong><?= htmlspecialchars($fruta['nome']) ?></strong></td>
                            <td>
                                <input type="number" name="quantidade" value="<?= $fruta['quantidade'] ?>" min="0" required style="width: 70px;">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="preco" value="<?= $fruta['preco'] ?>" min="0" required style="width: 80px;">
                            </td>
                            <td>
                                <input type="hidden" name="id" value="<?= $fruta['id'] ?>">
                                <button type="submit" name="update" class="update-btn">✏️ Atualizar</button>
                                <a href="?remover=<?= $fruta['id'] ?>" class="remove-btn" onclick="return confirm('Remover <?= htmlspecialchars($fruta['nome']) ?>? A imagem também será apagada.')">🗑️ Remover</a>
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
