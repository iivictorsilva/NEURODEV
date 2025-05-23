<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");  // Diretório relativo
    exit();
}

// Verificar se o usuário é administrador
if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../verifica_acesso.php");  // Diretório relativo
    exit();
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

// Buscar informações para o painel, por exemplo, contagem de alunos, professores, etc.
$result_alunos = $conn->query("SELECT COUNT(*) AS total_alunos FROM usuarios WHERE tipo_usuario = 'aluno'");
$result_professores = $conn->query("SELECT COUNT(*) AS total_professores FROM usuarios WHERE tipo_usuario = 'professor'");
$result_eventos = $conn->query("SELECT COUNT(*) AS total_eventos FROM eventos"); // Exemplo de eventos

$alunos = $result_alunos->fetch_assoc();
$professores = $result_professores->fetch_assoc();
$eventos = $result_eventos->fetch_assoc();
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Painel de Administração">
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../src/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky">
        <h4 class="text-center mt-3">Administração</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="index.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="alunos.php">Alunos (<?php echo $alunos['total_alunos']; ?>)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="professores.php">Professores (<?php echo $professores['total_professores']; ?>)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tarefas.php">Tarefas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="eventos.php">Eventos (<?php echo $eventos['total_eventos']; ?>)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="relatorios.php">Relatórios</a>
            </li>

            <!-- Nova opção de comunicação, acima de configurações -->
            <li class="nav-item">
                <a class="nav-link" href="comunicacao.php">Comunicação</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="configuracoes.php">Configurações</a>
            </li>
        </ul>

        <!-- Botão de sair, abaixo de configurações -->
        <div class="mt-3">
            <a href="../../logout.php" class="btn btn-danger w-100">Sair</a>
        </div>
    </div>
</nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <h1 class="h2">Painel de Administração</h1>
                <p>Bem-vindo ao painel administrativo! Aqui você pode gerenciar os dados da plataforma.</p>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-header">Alunos</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $alunos['total_alunos']; ?></h5>
                                <p class="card-text">Total de alunos cadastrados.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-header">Professores</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $professores['total_professores']; ?></h5>
                                <p class="card-text">Total de professores cadastrados.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-header">Eventos</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $eventos['total_eventos']; ?></h5>
                                <p class="card-text">Total de eventos cadastrados.</p>
                            </div>
                        </div>
                    </div>
                </div>

                
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
