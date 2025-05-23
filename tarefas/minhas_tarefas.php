<?php
// Iniciar a sessão
session_start();
require_once('../includes/conexao.php'); // Conexão com o banco de dados

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['user_id'];

// Variável para mensagens de erro ou sucesso
$erro = '';
$sucesso = '';

// Processar o formulário de criação de tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $prazo = $_POST['prazo']; // Recebe a data e hora do formulário
    $usuario_id = $_SESSION['user_id'];

    // Converter a data e hora para o formato do MySQL
    $prazo_formatado = date('Y-m-d H:i:s', strtotime($prazo));

    // Inserir a tarefa no banco de dados
    $sql = "INSERT INTO tarefas (titulo, descricao, prazo, usuario_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $titulo, $descricao, $prazo_formatado, $usuario_id);

    if ($stmt->execute()) {
        header('Location: minhas_tarefas.php?sucesso=Tarefa criada com sucesso!');
        exit();
    } else {
        header('Location: criar_tarefa.php?erro=Erro ao criar tarefa.');
        exit();
    }

    $stmt->close();
}

// Buscar as tarefas do usuário
$sql = "SELECT * FROM tarefas WHERE usuario_id = ? ORDER BY prazo ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);

if (!$stmt->execute()) {
    die("Erro ao executar a consulta: " . $stmt->error);
}

$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Tarefas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset básico e variáveis de cores */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-color: #f5f7fa;
            --dark-color: #333;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
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
            content: "📋";
        }

        /* Botões */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            background-color: var(--primary-color);
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            margin-bottom: 20px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-success {
            background-color: var(--success-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
        }

        .btn-warning {
            background-color: var(--warning-color);
        }

        /* Mensagens */
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

        /* Tabela - ESTILO IGUAL AO PRIMEIRO CÓDIGO */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        table th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 15px;
            position: sticky;
            top: 0;
        }

        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
            cursor: pointer;
            transition: background-color 0.10s ease;
        }

        /* Links de ação */
        .action-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-links a {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-links a:hover {
            transform: translateY(-1px);
        }

        .edit-link {
            color: var(--primary-color);
            background-color: rgba(52, 152, 219, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .edit-link:hover {
            background-color: rgba(52, 152, 219, 0.2);
        }

        .delete-link {
            color: var(--danger-color);
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .delete-link:hover {
            background-color: rgba(220, 53, 69, 0.2);
        }

        .send-link {
            color: var(--success-color);
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .send-link:hover {
            background-color: rgba(40, 167, 69, 0.2);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            .action-links {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-links a {
                width: 100%;
                justify-content: center;
            }
        }

        /* Ícones */
        .fas {
            font-size: 14px;
        }

        /* Mensagem de lista vazia */
        .empty-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            background-color: #f9f9f9;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Minhas Tarefas</h2>

        <!-- Mensagens de sucesso ou erro -->
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['sucesso']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_GET['erro']); ?>
            </div>
        <?php endif; ?>

        <!-- Botões de ação -->
        <a href="criarTarefa.html" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Criar Nova Tarefa
        </a>
        <a href="../dashboard/professor/index.php" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Voltar ao Dashboard
        </a>

        <!-- Listar as tarefas -->
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Prazo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['prazo'])); ?></td>
                            <td class="action-links">
                                <a href="editar_tarefa.php?id=<?php echo $row['id']; ?>" class="edit-link">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="excluir_tarefa.php?id=<?php echo $row['id']; ?>" class="delete-link" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">
                                    <i class="fas fa-trash-alt"></i> Excluir
                                </a>
                                <a href="vincular_tarefa.php?id=<?php echo $row['id']; ?>" class="send-link">
                                    <i class="fas fa-share"></i> Enviar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-message">
                            <i class="fas fa-inbox fa-3x"></i>
                            <h5>Você não tem tarefas cadastradas</h5>
                            <p>Clique no botão "Criar Nova Tarefa" para começar</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Fechar automaticamente as mensagens de alerta após 5 segundos
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>