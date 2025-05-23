<?php
// Incluir o arquivo de verificação de acesso
$pagina_permitida_para = 'aluno';
include('../verifica_acesso.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Conectar ao banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');

if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Inicializar as variáveis
$user_id = $_SESSION['user_id'];
$user_name = '';
$user_email = '';

// Consultar dados do aluno
$result = $mysqli->query("SELECT nome, email FROM usuarios WHERE id = '$user_id' AND tipo_usuario = 'aluno' LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_name = $row['nome'];
    $user_email = $row['email'];
}

// Consultar estatísticas do aluno
$total_tarefas = 0;
$total_eventos = 0;
$total_jogos = 0;

$result_tarefas = $mysqli->query("SELECT COUNT(*) AS total FROM tarefas t 
                                 INNER JOIN tarefas_alunos ta ON t.id = ta.tarefa_id 
                                 WHERE ta.aluno_id = '$user_id' AND t.status = 'pendente'");
if ($result_tarefas) $total_tarefas = $result_tarefas->fetch_assoc()['total'];

$result_eventos = $mysqli->query("SELECT COUNT(*) AS total FROM eventos e
                                 INNER JOIN aluno_eventos ae ON e.id = ae.evento_id
                                 WHERE ae.aluno_id = '$user_id' AND e.data_evento >= CURDATE()");
if ($result_eventos) $total_eventos = $result_eventos->fetch_assoc()['total'];

$result_jogos = $mysqli->query("SELECT COUNT(*) AS total FROM jogos WHERE usuario_id = '$user_id'");
if ($result_jogos) $total_jogos = $result_jogos->fetch_assoc()['total'];

// Atualizar informações se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $mysqli->real_escape_string($_POST['nome']);
    $new_email = $mysqli->real_escape_string($_POST['email']);

    $update_query = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
    $stmt = $mysqli->prepare($update_query);
    
    if ($stmt) {
        $stmt->bind_param("ssi", $new_name, $new_email, $user_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Perfil atualizado com sucesso!');</script>";
            $user_name = $new_name;
            $user_email = $new_email;
        } else {
            echo "<script>alert('Erro ao atualizar o perfil: " . $stmt->error . "');</script>";
        }
        
        $stmt->close();
    } else {
        echo "<script>alert('Erro ao preparar a atualização: " . $mysqli->error . "');</script>";
    }
}
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--secondary);
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
            background-color: var(--primary);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 20px;
        }
        
        .profile-name {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }
        
        .profile-email {
            color: #6c757d;
            font-size: 1.2rem;
        }
        
        .stats-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s;
            height: 100%;
            text-align: center;
            padding: 25px;
            background-color: white;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .info-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .info-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }
        
        .info-title i {
            margin-right: 15px;
        }
        
        .info-item {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--secondary);
            min-width: 150px;
            display: flex;
            align-items: center;
        }
        
        .info-label i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        .info-value {
            color: var(--dark);
        }
        
        .btn-edit {
            background-color: var(--primary);
            border: none;
            padding: 10px 25px;
            font-size: 1.1rem;
        }
        
        .btn-edit:hover {
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
            color: var(--primary);
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
        
        @media (max-width: 992px) {
            .main-content {
                padding: 25px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px 15px;
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
                    <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> <span>Início</span></a>
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
                    <a class="nav-link active" href="./perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
                </li>
            </ul>
        </div>

        <div class="main-content" id="main-content">
            <div class="profile-container">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                    </div>
                    <h1 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h1>
                    <p class="profile-email"><?php echo htmlspecialchars($user_email); ?></p>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-5">
                    <div class="col-md-4 mb-4">
                        <div class="stats-card">
                            <i class="fas fa-tasks stats-icon"></i>
                            <div class="stats-number"><?php echo $total_tarefas; ?></div>
                            <div class="stats-label">Tarefas Pendentes</div>
                            <a href="./tarefas.php" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Visualizar
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="stats-card">
                            <i class="fas fa-calendar stats-icon"></i>
                            <div class="stats-number"><?php echo $total_eventos; ?></div>
                            <div class="stats-label">Eventos Futuros</div>
                            <a href="./eventos.php" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Visualizar
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="stats-card">
                            <i class="fas fa-gamepad stats-icon"></i>
                            <div class="stats-number"><?php echo $total_jogos; ?></div>
                            <div class="stats-label">Jogos Disponíveis</div>
                            <a href="../aluno/JOGODAVELHA/index.html" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Visualizar
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Info Card -->
                <div class="info-card">
                    <h2 class="info-title">
                        <i class="fas fa-info-circle"></i>
                        Informações do Perfil
                    </h2>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Nome:
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($user_name); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            Email:
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($user_email); ?></div>
                    </div>
                </div>
                
                <!-- Edit Profile Card -->
                <div class="info-card">
                    <h2 class="info-title">
                        <i class="fas fa-user-edit"></i>
                        Editar Perfil
                    </h2>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="nome" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    Nome
                                </label>
                                <input type="text" class="form-control" id="nome" name="nome" 
                                       value="<?php echo htmlspecialchars($user_name); ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user_email); ?>" required>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-edit">
                                <i class="fas fa-save me-2"></i>
                                Salvar Alterações
                            </button>
                            
                            <a href="alterar_senha.php" class="btn btn-outline-secondary">
                                <i class="fas fa-lock me-2"></i>
                                Alterar Senha
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
            }
        }
        
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
        handleResize();
    </script>
</body>
</html>