<?php
// Iniciar a sessão
session_start();
require_once('../includes/conexao.php');

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];
$erro = '';
$sucesso = '';

// Verificar se o ID da tarefa foi passado na URL
if (!isset($_GET['id'])) {
    header('Location: minhas_tarefas.php?erro=Tarefa não encontrada');
    exit();
}

$tarefa_id = $_GET['id'];

// Buscar os dados da tarefa no banco de dados
$sql = "SELECT * FROM tarefas WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $tarefa_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: minhas_tarefas.php?erro=Tarefa não encontrada ou você não tem permissão para editá-la');
    exit();
}

$tarefa = $result->fetch_assoc();
$stmt->close();

// Processar o formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $prazo = $_POST['prazo'];
    
    // Converter a data para o formato do MySQL
    $prazo_formatado = date('Y-m-d H:i:s', strtotime($prazo));

    // Atualizar a tarefa no banco de dados
    $sql = "UPDATE tarefas SET titulo = ?, descricao = ?, prazo = ? WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $titulo, $descricao, $prazo_formatado, $tarefa_id, $usuario_id);

    if ($stmt->execute()) {
        $sucesso = "Tarefa atualizada com sucesso!";
    } else {
        $erro = "Erro ao atualizar tarefa: " . $stmt->error;
    }
    
    $stmt->close();
    
    // Atualizar os dados locais após a edição
    $tarefa['titulo'] = $titulo;
    $tarefa['descricao'] = $descricao;
    $tarefa['prazo'] = $prazo_formatado;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarefa</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-color: #f5f7fa;
            --dark-color: #333;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            margin-bottom: 25px;
            font-size: 28px;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        h2:before {
            content: "✏️";
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--secondary-color);
        }
        
        input[type="text"],
        textarea,
        input[type="datetime-local"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        textarea:focus,
        input[type="datetime-local"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            background-color: var(--primary-color);
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Tarefa</h2>
        
        <!-- Mensagens de erro ou sucesso -->
        <?php if (!empty($erro)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulário de edição -->
        <form method="POST" action="editar_tarefa.php?id=<?php echo $tarefa_id; ?>">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($tarefa['titulo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" required><?php echo htmlspecialchars($tarefa['descricao']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="prazo">Prazo:</label>
                <input type="datetime-local" id="prazo" name="prazo" 
                       value="<?php echo date('Y-m-d\TH:i', strtotime($tarefa['prazo'])); ?>" required>
            </div>
            
            <div class="action-buttons">
                <button type="submit" class="btn">Salvar Alterações</button>
                <a href="minhas_tarefas.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>