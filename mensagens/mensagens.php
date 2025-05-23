<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Conectar ao banco de dados
$host = "localhost";
$user = "root"; 
$password = "";
$dbname = "autismo_plataforma";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter o ID do usuário logado
$usuario_id = $_SESSION['user_id'];

// Variáveis para mensagens
$erro = '';
$sucesso = '';

// Processar envio de mensagens
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensagem'], $_POST['destinatario_id'])) {
    $mensagem = $conn->real_escape_string($_POST['mensagem']);
    $destinatario_id = $conn->real_escape_string($_POST['destinatario_id']);

    if (!empty($mensagem)) {
        $sql = "INSERT INTO mensagens (remetente_id, destinatario_id, mensagem, data_envio) 
                VALUES ('$usuario_id', '$destinatario_id', '$mensagem', NOW())";
        
        if ($conn->query($sql)) {
            $sucesso = "Mensagem enviada com sucesso!";
        } else {
            $erro = "Erro ao enviar a mensagem: " . $conn->error;
        }
    } else {
        $erro = "Por favor, digite uma mensagem.";
    }
}

// Carregar mensagens
$sql_mensagens = "SELECT m.mensagem, m.data_envio, u.nome AS nome_remetente,
                  CASE WHEN m.remetente_id = '$usuario_id' THEN 'enviada' ELSE 'recebida' END AS tipo
                  FROM mensagens m
                  JOIN usuarios u ON m.remetente_id = u.id
                  WHERE m.destinatario_id = '$usuario_id' OR m.remetente_id = '$usuario_id'
                  ORDER BY m.data_envio DESC";
$result = $conn->query($sql_mensagens);

// Carregar lista de usuários
$sql_usuarios = "SELECT id, nome FROM usuarios WHERE id != '$usuario_id'";
$usuarios_result = $conn->query($sql_usuarios);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background-color: var(--secondary-color);
            padding-top: 20px;
            width: 250px;
            transition: all 0.3s ease;
            position: fixed;
            z-index: 1000;
        }
        
        .sidebar .nav-item {
            margin-bottom: 5px;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .site-title {
            color: white;
            text-align: center;
            padding: 15px 0;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .site-title span {
            color: var(--primary-color);
        }
        
        @keyframes colorChange {
            0% { color: #3498db; }
            25% { color: #e74c3c; }
            50% { color: #9b59b6; }
            75% { color: #2ecc71; }
            100% { color: #3498db; }
        }
        
        .animated-text {
            animation: colorChange 8s infinite;
        }
        
        /* Mensagens Styles */
        .messages-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .messages-header {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .message-form {
            margin-bottom: 30px;
        }
        
        .message-textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: none;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .message-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .message-list {
            max-height: 500px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
        }
        
        .message-received {
            background-color: #f0f8ff;
            border-left: 4px solid var(--primary-color);
        }
        
        .message-sent {
            background-color: #e6ffe6;
            border-left: 4px solid #2ecc71;
            margin-left: 20%;
        }
        
        .message-sender {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .message-text {
            margin-bottom: 5px;
        }
        
        .message-time {
            font-size: 0.8rem;
            color: #666;
            text-align: right;
        }
        
        .btn-send {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-send:hover {
            background-color: #2980b9;
        }
        
        .alert-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-error {
            background-color: #ffdddd;
            color: #721c24;
            border-left: 4px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #ddffdd;
            color: #155724;
            border-left: 4px solid #c3e6cb;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .message-sent {
                margin-left: 10%;
            }
        }
        
        @media (max-width: 768px) {
            .messages-container {
                padding: 20px;
            }
            
            .message {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <button class="toggle-btn d-lg-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="d-flex">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar">
            <div class="site-title">
                <h4>Neuro<span class="animated-text">Dev</span></h4>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard/index.php"><i class="fas fa-tachometer-alt"></i> <span>Início</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../comunicacao/comunicacao.php"><i class="fas fa-comments"></i> <span>Comunicações</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../video_chamada/video_chamada.php"><i class="fas fa-video"></i> <span>Video Chamada</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../tarefas/minhas_tarefas.php"><i class="fas fa-tasks"></i> <span>Tarefas</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../eventos/eventos.php"><i class="fas fa-calendar"></i> <span>Eventos</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="mensagens.php"><i class="fas fa-envelope"></i> <span>Mensagens</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../notificacoes/notificacoes_pagina.php"><i class="fas fa-bell"></i> <span>Notificações</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../perfil/perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <div class="messages-container">
                <h1 class="messages-header">Mensagens</h1>
                
                <!-- Alertas -->
                <?php if ($erro): ?>
                    <div class="alert-message alert-error">
                        <strong>Erro: </strong> <?php echo $erro; ?>
                    </div>
                <?php endif; ?>
                <?php if ($sucesso): ?>
                    <div class="alert-message alert-success">
                        <strong>Sucesso: </strong> <?php echo $sucesso; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulário de envio -->
                <form id="form-mensagem" method="POST" class="message-form">
                    <textarea name="mensagem" id="mensagem" class="message-textarea" 
                              placeholder="Escreva sua mensagem..." required></textarea>
                    
                    <select name="destinatario_id" id="destinatario_id" class="message-select" required>
                        <option value="">Selecione o destinatário</option>
                        <?php while ($user = $usuarios_result->fetch_assoc()): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo $user['nome']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    
                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i> Enviar Mensagem
                    </button>
                </form>
                
                <!-- Lista de mensagens -->
                <div class="message-list" id="mensagens-container">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="message message-<?php echo $row['tipo']; ?>">
                                <div class="message-sender"><?php echo $row['nome_remetente']; ?></div>
                                <div class="message-text"><?php echo $row['mensagem']; ?></div>
                                <div class="message-time">
                                    <?php echo date('d/m/Y H:i', strtotime($row['data_envio'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Nenhuma mensagem encontrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        // AJAX para envio de mensagens
        $(document).ready(function() {
            $('#form-mensagem').submit(function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                
                $.ajax({
                    url: 'mensagens.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Recarregar apenas as mensagens
                        $('#mensagens-container').load(' #mensagens-container > *');
                        $('#mensagem').val(''); // Limpar campo
                        
                        // Rolar para baixo automaticamente
                        $('.message-list').scrollTop($('.message-list')[0].scrollHeight);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
            
            // Rolar para a última mensagem ao carregar
            $('.message-list').scrollTop($('.message-list')[0].scrollHeight);
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>