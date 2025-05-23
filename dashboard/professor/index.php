<?php 
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado e se é do tipo "professor"
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

if ($_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../verifica_acesso.php");
    exit();
}

// Conectar ao banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');

if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Inicializar as variáveis
$total_tarefas = 0;
$total_eventos = 0;
$total_notificacoes = 0;
$user_name = 'Usuário';

// Obter dados do professor
$user_id = $_SESSION['user_id'];
$result_nome = $mysqli->query("SELECT nome FROM usuarios WHERE id = '$user_id' AND tipo_usuario = 'professor' LIMIT 1");
if ($result_nome && $result_nome->num_rows > 0) {
    $row = $result_nome->fetch_assoc();
    $user_name = $row['nome'];
} else {
    $user_name = 'Professor não encontrado';
}

// Consultar estatísticas
$result_tarefas = $mysqli->query("SELECT COUNT(*) AS total FROM tarefas WHERE status = 'pendente' AND usuario_id = '$user_id'");
if ($result_tarefas) {
    $row = $result_tarefas->fetch_assoc();
    $total_tarefas = $row['total'];
}

$result_eventos = $mysqli->query("SELECT COUNT(*) AS total FROM eventos WHERE data_evento >= CURDATE() AND usuario_id = '$user_id'");
if ($result_eventos) {
    $row = $result_eventos->fetch_assoc();
    $total_eventos = $row['total'];
}

$result_notificacoes = $mysqli->query("SELECT COUNT(*) AS total FROM notificacoes WHERE lida = 0 AND usuario_id = '$user_id'");
if ($result_notificacoes) {
    $row = $result_notificacoes->fetch_assoc();
    $total_notificacoes = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
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
        }
        
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
        
        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            background-color: white;
            height: 100%;
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .card-custom .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .card-custom .card-title {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .card-custom .card-text {
            flex-grow: 1;
        }
        
        .card-custom .btn {
            align-self: flex-start;
            margin-top: auto;
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
        
        .welcome-title {
            color: var(--secondary-color);
            margin-bottom: 30px;
        }
        
        .welcome-title span {
            color: var(--primary-color);
        }
        
        .carousel {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.collapsed {
                margin-left: 0;
                width: 70px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <button class="toggle-btn d-lg-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="d-flex">
        <div class="sidebar" id="sidebar">
            <div class="site-title">
                <h4>Neuro<span class="animated-text">Dev</span></h4>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php"><i class="fas fa-tachometer-alt"></i> <span>Início</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="comunicacao.php"><i class="fas fa-comments"></i> <span>Comunicações</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="video_chamada.php"><i class="fas fa-video"></i> <span>Video Chamada</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../tarefas/minhas_tarefas.php"><i class="fas fa-tasks"></i> <span>Tarefas</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../eventos/eventos.php"><i class="fas fa-calendar"></i> <span>Eventos</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../mensagens/mensagens.php"><i class="fas fa-envelope"></i> <span>Mensagens</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../notificacoes/notificacoes_pagina.php"><i class="fas fa-bell"></i> <span>Notificações</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../perfil/perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
                </li>
            </ul>
        </div>

        <div class="main-content" id="main-content">
            <div class="container-fluid">
                <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="banner1.jpg" class="d-block w-100" alt="Banner 1">
                        </div>
                        <div class="carousel-item">
                            <img src="banner2.jpg" class="d-block w-100" alt="Banner 2">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

                <h1 class="welcome-title my-4">Bem-vindo à NeuroDev, <span><?php echo htmlspecialchars($user_name); ?></span>!</h1>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-tasks me-2"></i>Tarefas Pendentes</h5>
                                <p class="card-text"><?php echo $total_tarefas; ?> tarefa(s) pendente(s) para revisar.</p>
                                <a href="../../tarefas/minhas_tarefas.php" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i> Ver Tarefas
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-calendar me-2"></i>Eventos Futuros</h5>
                                <p class="card-text"><?php echo $total_eventos; ?> evento(s) futuro(s) programado(s).</p>
                                <a href="../../eventos/eventos.php" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i> Ver Eventos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-bell me-2"></i>Notificações</h5>
                                <p class="card-text"><?php echo $total_notificacoes; ?> notificação(ns) não lida(s).</p>
                                <a href="../../notificacoes/notificacoes_pagina.php" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i> Ver Notificações
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
            }
        }
        
        // Ajustar sidebar em telas pequenas
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (window.innerWidth < 992) {
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('show');
                mainContent.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Executar no carregamento inicial
    </script>
</body>
</html>
<?php
$mysqli->close();
?>