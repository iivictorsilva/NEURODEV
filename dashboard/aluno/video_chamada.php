<?php 
// Incluir o arquivo de verificação de acesso
$pagina_permitida_para = 'aluno'; // Definindo que apenas o aluno pode acessar esta página
include('../verifica_acesso.php'); // Verifica o acesso do usuário

// Definir o user_id a partir da sessão
$user_id = $_SESSION['user_id']; // Aqui assumimos que o ID do usuário está salvo na sessão

// Conectar ao banco de dados para buscar última atividade
$mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');
if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Buscar última atividade ou tarefa do aluno
$tarefa_ultima = null;
$tarefa_resultado = $mysqli->query("SELECT * FROM tarefas WHERE usuario_id = '$user_id' ORDER BY data_limite DESC LIMIT 1");
if ($tarefa_resultado && $tarefa_resultado->num_rows > 0) {
    $tarefa_ultima = $tarefa_resultado->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Chamada - Orientação</title>
    <!-- Link para o Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de navegação ou cabeçalho -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Plataforma de Orientação</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notificacoes_pagina.php">Notificações</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Sair</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Iniciar Video Chamada</h2>

        <div class="alert alert-info">
            <strong>Instruções:</strong> A videochamada será iniciada abaixo. Verifique sua conexão.
        </div>

        <div class="embed-responsive embed-responsive-16by9">
            <!-- Jitsi Meet iframe -->
            <iframe src="https://meet.jit.si/OrientacaoAluno<?php echo uniqid(); ?>" frameborder="0" allow="camera; microphone; fullscreen; display-capture" sandbox="allow-forms allow-scripts allow-same-origin" class="embed-responsive-item"></iframe>
        </div>

        <div class="mt-5">
            <h4>Última Atividade</h4>
            <?php if ($tarefa_ultima): ?>
                <div class="alert alert-secondary">
                    <strong><?php echo $tarefa_ultima['titulo']; ?></strong><br>
                    <small>Data de entrega: <?php echo date('d/m/Y', strtotime($tarefa_ultima['data_limite'])); ?></small><br>
                    <p><?php echo $tarefa_ultima['descricao']; ?></p>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    Nenhuma atividade recente encontrada.
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-5">
            <h4>Faça Anotações Durante a Video Chamada</h4>
            <textarea class="form-control" rows="5" placeholder="Digite suas anotações aqui..."></textarea>
        </div>

        <div class="mt-3">
            <h4>Feedback Após a Chamada</h4>
            <form action="enviar_feedback.php" method="POST">
                <textarea name="feedback" class="form-control" rows="3" placeholder="Compartilhe sua opinião sobre a videochamada..."></textarea>
                <button type="submit" class="btn btn-success mt-2">Enviar Feedback</button>
            </form>
        </div>

        <!-- Botão de voltar ao dashboard posicionado abaixo de todas as opções -->
        <div class="mt-3">
            <a href="index.php" class="btn btn-primary">Voltar ao Dashboard</a>
        </div>
    </div>

    <!-- Scripts do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
