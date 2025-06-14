<?php
// Ativar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração MySQL - IP será substituído automaticamente pelo Terraform
$mysql_host = '10.0.1.4'; // Este IP será substituído pelo script Terraform
$mysql_user = 'mysqladmin';
$mysql_pass = '@pass123!';
$mysql_db   = 'motos';

// Configurar relatórios de erro MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Criar ligação com charset correto
    $conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    $conn->set_charset('utf8mb4');
    echo "<!-- Conectado com sucesso ao MySQL em $mysql_host -->\n";
} catch (Exception $e) {
    die("<div style='color: red; font-weight: bold;'>Erro na ligação à base de dados: " . $e->getMessage() . "</div>");
}

$message = '';

// Função para limpar input (segurança básica)
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Criar novo registo (com prepared statements para segurança)
if (isset($_POST['submit_create'])) {
    $marca = clean_input($_POST['marca']);
    $modelo = clean_input($_POST['modelo']);
    $cilindrada = (int)$_POST['cilindrada'];
    $velocidade_maxima = (int)$_POST['velocidade_maxima'];
    
    $stmt = $conn->prepare("INSERT INTO motos (marca, modelo, cilindrada, velocidade_maxima) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $marca, $modelo, $cilindrada, $velocidade_maxima);
    
    if ($stmt->execute()) {
        $message = "<div class='success'>Novo registo criado com sucesso!</div>";
    } else {
        $message = "<div class='error'>Erro ao criar registo: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Eliminar registo
if (isset($_POST['submit_delete'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM motos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = "<div class='success'>Registo eliminado com sucesso!</div>";
    } else {
        $message = "<div class='error'>Erro ao eliminar registo: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Atualizar registo
if (isset($_POST['submit_update'])) {
    $id = (int)$_POST['id'];
    $marca = clean_input($_POST['marca']);
    $modelo = clean_input($_POST['modelo']);
    $cilindrada = (int)$_POST['cilindrada'];
    $velocidade_maxima = (int)$_POST['velocidade_maxima'];
    
    $stmt = $conn->prepare("UPDATE motos SET marca=?, modelo=?, cilindrada=?, velocidade_maxima=? WHERE id=?");
    $stmt->bind_param("ssiii", $marca, $modelo, $cilindrada, $velocidade_maxima, $id);
    
    if ($stmt->execute()) {
        $message = "<div class='success'>Registo atualizado com sucesso!</div>";
    } else {
        $message = "<div class='error'>Erro ao atualizar registo: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Obter todos os registos
$result = $conn->query("SELECT * FROM motos ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Dados Motos - Projeto 7</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498db;
        }
        
        h2 {
            color: #34495e;
            margin: 25px 0 15px 0;
            padding-left: 10px;
            border-left: 4px solid #3498db;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .form-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            align-items: center;
        }
        
        label {
            flex: 0 0 150px;
            font-weight: bold;
            color: #495057;
            margin-right: 10px;
        }
        
        input[type="text"], 
        input[type="number"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
            min-width: 200px;
        }
        
        input[type="text"]:focus, 
        input[type="number"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
            outline: none;
        }
        
        button {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        button:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
        
        button.edit {
            background: #f39c12;
        }
        
        button.edit:hover {
            background: #e67e22;
        }
        
        button.delete {
            background: #e74c3c;
        }
        
        button.delete:hover {
            background: #c0392b;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th {
            background: #3498db;
            color: white;
            padding: 15px 10px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tr:nth-child(even):hover {
            background: #e9ecef;
        }
        
        .actions {
            white-space: nowrap;
        }
        
        .actions form {
            display: inline-block;
            margin: 0 2px;
        }
        
        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }
        
        .info-box {
            background: #e1f5fe;
            border: 1px solid #81d4fa;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #0277bd;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            label {
                flex: none;
                margin-bottom: 5px;
            }
            
            input[type="text"], 
            input[type="number"] {
                min-width: auto;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏍️ Base de Dados Motos - Projeto 7</h1>
        
        <div class="info-box">
            <strong>Ligação MySQL:</strong> Conectado a <?php echo $mysql_host; ?> | 
            <strong>Base de Dados:</strong> <?php echo $mysql_db; ?> | 
            <strong>Utilizador:</strong> <?php echo $mysql_user; ?>
        </div>

        <?php echo $message; ?>

        <!-- Formulário para criar novo registo -->
        <h2>➕ Criar Registo de Mota</h2>
        <div class="form-container">
            <form method="post">
                <div class="form-row">
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" required maxlength="50" placeholder="Ex: Honda, Yamaha, BMW...">
                </div>
                <div class="form-row">
                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" required maxlength="50" placeholder="Ex: CBR600RR, R1, S1000RR...">
                </div>
                <div class="form-row">
                    <label for="cilindrada">Cilindrada (cc):</label>
                    <input type="number" id="cilindrada" name="cilindrada" required min="50" max="2000" placeholder="Ex: 600, 1000...">
                </div>
                <div class="form-row">
                    <label for="velocidade_maxima">Velocidade Máxima (km/h):</label>
                    <input type="number" id="velocidade_maxima" name="velocidade_maxima" required min="50" max="400" placeholder="Ex: 180, 250...">
                </div>
                <div class="form-row">
                    <label></label>
                    <button type="submit" name="submit_create">✅ Criar Registo</button>
                </div>
            </form>
        </div>

        <!-- Formulário de edição -->
        <?php
        if (isset($_POST['submit_edit'])) {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("SELECT * FROM motos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $edit_result = $stmt->get_result();
            
            if ($edit_row = $edit_result->fetch_assoc()):
        ?>
        <h2>✏️ Editar Registo #<?php echo $edit_row['id']; ?></h2>
        <div class="form-container">
            <form method="post">
                <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
                <div class="form-row">
                    <label for="edit_marca">Marca:</label>
                    <input type="text" id="edit_marca" name="marca" value="<?php echo htmlspecialchars($edit_row['marca']); ?>" required maxlength="50">
                </div>
                <div class="form-row">
                    <label for="edit_modelo">Modelo:</label>
                    <input type="text" id="edit_modelo" name="modelo" value="<?php echo htmlspecialchars($edit_row['modelo']); ?>" required maxlength="50">
                </div>
                <div class="form-row">
                    <label for="edit_cilindrada">Cilindrada (cc):</label>
                    <input type="number" id="edit_cilindrada" name="cilindrada" value="<?php echo $edit_row['cilindrada']; ?>" required min="50" max="2000">
                </div>
                <div class="form-row">
                    <label for="edit_velocidade">Velocidade Máxima (km/h):</label>
                    <input type="number" id="edit_velocidade" name="velocidade_maxima" value="<?php echo $edit_row['velocidade_maxima']; ?>" required min="50" max="400">
                </div>
                <div class="form-row">
                    <label></label>
                    <button type="submit" name="submit_update">💾 Atualizar Registo</button>
                    <button type="submit" name="cancel" style="background: #6c757d;">❌ Cancelar</button>
                </div>
            </form>
        </div>
        <?php 
            endif;
            $stmt->close();
        }
        ?>

        <!-- Tabela de registos -->
        <h2>📋 Lista de Motos Registadas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Cilindrada (cc)</th>
                    <th>Velocidade Máxima (km/h)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $row['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['marca']); ?></td>
                            <td><?php echo htmlspecialchars($row['modelo']); ?></td>
                            <td><?php echo number_format($row['cilindrada']); ?> cc</td>
                            <td><?php echo number_format($row['velocidade_maxima']); ?> km/h</td>
                            <td class="actions">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="submit_edit" class="edit">✏️ Editar</button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="submit_delete" class="delete" onclick="return confirm('Tem certeza que deseja eliminar este registo?')">🗑️ Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">
                            📭 Nenhum registo encontrado. Crie o primeiro registo de mota!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="info-box">
            <strong>Total de registos:</strong> <?php echo $result ? $result->num_rows : 0; ?> motos registadas
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>