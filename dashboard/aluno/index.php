<?php 
// Incluir o arquivo de verificação de acesso
$pagina_permitida_para = 'aluno'; // Definindo que apenas o aluno pode acessar esta página
include('../verifica_acesso.php'); // Verifica o acesso do usuário

// Verificar se a sessão já foi iniciada, caso contrário, iniciar a sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conectar ao banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');

// Verificar a conexão
if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Inicializar as variáveis
$total_tarefas = 0;
$total_eventos = 0;
$total_jogos = 0;
$user_name = 'Aluno';

// Obter dados do aluno
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = 1; // Valor padrão para testes
}

// Consultar nome do aluno
$result_nome = $mysqli->query("SELECT nome FROM usuarios WHERE id = '$user_id' AND tipo_usuario = 'aluno' LIMIT 1");
if ($result_nome && $result_nome->num_rows > 0) {
    $row = $result_nome->fetch_assoc();
    $user_name = $row['nome'];
} else {
    $user_name = 'Aluno não encontrado';
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

$result_jogos = $mysqli->query("SELECT COUNT(*) AS total FROM jogos WHERE usuario_id = '$user_id'");
if ($result_jogos) {
    $row = $result_jogos->fetch_assoc();
    $total_jogos = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NeuroDev</title>
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
                    <a class="nav-link" href="../../video_chamada/video_chamada.php"><i class="fas fa-video"></i> <span>Video Chamada</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./tarefas.php"><i class="fas fa-tasks"></i> <span>Tarefas</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../aluno/JOGODAVELHA/index.html"><i class="fas fa-gamepad"></i> <span>Jogos</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./eventos.php"><i class="fas fa-calendar"></i> <span>Eventos</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./equipe.html"><i class="fas fa-users"></i> <span>Nossa Equipe</span></a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="./contato.html"><i class="fas fa-envelope"></i> <span>Contato</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./sobre.html"><i class="fas fa-info-circle"></i> <span>Sobre a Clínica</span></a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="./perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
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
                            <img src="../../assets/img/banner2.jpeg" class="d-block w-100" alt="Banner 1">
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
                                <p class="card-text"><?php echo $total_tarefas; ?> tarefa(s) pendente(s) para realizar.</p>
                                <a href="./tarefas.php" class="btn btn-primary">
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
                                <a href="./eventos.php" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i> Ver Eventos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-gamepad me-2"></i>Jogos Disponíveis</h5>
                                <p class="card-text"><?php echo $total_jogos; ?> jogo(s) disponível(is) para você.</p>
                                <a href="../aluno/JOGODAVELHA/index.html" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-1"></i> Ver Jogos
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