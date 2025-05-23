<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar se há mensagens de sucesso ou erro
$sucesso_atualizacao = '';
$erro_atualizacao = '';

if (isset($_SESSION['sucesso_atualizacao'])) {
    $sucesso_atualizacao = $_SESSION['sucesso_atualizacao'];
    unset($_SESSION['sucesso_atualizacao']);
}

if (isset($_SESSION['erro_atualizacao'])) {
    $erro_atualizacao = $_SESSION['erro_atualizacao'];
    unset($_SESSION['erro_atualizacao']);
}

// Conectar ao banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "autismo_plataforma";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperar o ID do usuário da sessão
$usuario_id = $_SESSION['user_id'];

// Consultar informações do usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
} else {
    echo "Usuário não encontrado.";
    exit();
}

// Consultar estatísticas
$total_tarefas = 0;
$total_eventos = 0;
$total_jogos = 0;

$result_tarefas = $conn->query("SELECT COUNT(*) AS total FROM tarefas WHERE status = 'pendente' AND usuario_id = '$usuario_id'");
if ($result_tarefas) {
    $row = $result_tarefas->fetch_assoc();
    $total_tarefas = $row['total'];
}

$result_eventos = $conn->query("SELECT COUNT(*) AS total FROM eventos WHERE data_evento >= CURDATE() AND usuario_id = '$usuario_id'");
if ($result_eventos) {
    $row = $result_eventos->fetch_assoc();
    $total_eventos = $row['total'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - <?php echo htmlspecialchars($usuario['nome']); ?></title>
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
            overflow-x: hidden;
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
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.2rem;
        }
        
        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 10px 5px;
        }
        
        .toggle-btn {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1100;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .toggle-btn:hover {
            background-color: #2980b9;
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
        
        /* Profile Content Styles */
        .profile-header {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .profile-name {
            font-size: 2rem;
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .profile-email {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        
        .stat-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .stat-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .profile-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #666;
        }
        
        .edit-form {
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 5px;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .alert {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
            
            .stats-container {
                flex-direction: column;
            }
            
            .stat-card {
                min-width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .profile-header, .profile-section {
                padding: 20px;
            }
            
            .profile-name {
                font-size: 1.8rem;
            }
            
            .action-buttons {
                flex-direction: column;
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
                    <a class="nav-link" href="../dashboard/professor/index.php"><i class="fas fa-tachometer-alt"></i> <span>Início</span></a>
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
                    <a class="nav-link" href="../mensagens/mensagens.php"><i class="fas fa-envelope"></i> <span>Mensagens</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../notificacoes/notificacoes_pagina.php"><i class="fas fa-bell"></i> <span>Notificações</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <div class="container py-5">
                <!-- Cabeçalho do Perfil -->
                <div class="profile-header">
                    <h1 class="profile-name"><?php echo htmlspecialchars(strtoupper($usuario['nome'])); ?></h1>
                    <p class="profile-email"><?php echo htmlspecialchars($usuario['email']); ?></p>
                    
                    <!-- Estatísticas -->
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_tarefas; ?></div>
                            <div class="stat-label">Tarefas Pendentes</div>
                            <button class="stat-button" onclick="window.location.href='../tarefas/minhas_tarefas.php'">Visualizar</button>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_eventos; ?></div>
                            <div class="stat-label">Eventos Futuros</div>
                            <button class="stat-button" onclick="window.location.href='../eventos/eventos.php'">Visualizar</button>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_jogos; ?></div>
                            <div class="stat-label">Jogos Disponíveis</div>
                            <button class="stat-button">Visualizar</button>
                        </div>
                    </div>
                </div>
                
                <!-- Informações do Perfil -->
                <div class="profile-section">
                    <h2 class="section-title">Informações do Perfil</h2>
                    
                    <div class="info-item">
                        <div class="info-label">Nome:</div>
                        <div class="info-value"><?php echo htmlspecialchars(strtoupper($usuario['nome'])); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email:</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></div>
                    </div>
                </div>
                
                <!-- Editar Perfil -->
                <div class="profile-section">
                    <h2 class="section-title">Editar Perfil</h2>
                    
                    <!-- Mensagens de feedback -->
                    <?php if ($sucesso_atualizacao): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($sucesso_atualizacao); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($erro_atualizacao): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($erro_atualizacao); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form class="edit-form" action="atualizar_perfil.php" method="POST">
                        <div class="form-group">
                            <label class="form-label" for="nome">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn-primary">Salvar Alterações</button>
                            <a href="alterar_senha.php" class="btn-secondary">Alterar Senha</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        // Ajustar sidebar em telas pequenas
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (window.innerWidth < 992) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Executar no carregamento inicial
        
        // Adicionar funcionalidade aos botões de visualização
        document.querySelectorAll('.stat-button').forEach(button => {
            button.addEventListener('click', function() {
                // Esta função já está implementada via onclick nos botões
            });
        });

        // Fechar alertas automaticamente após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>