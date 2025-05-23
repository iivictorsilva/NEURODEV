<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {  // Alterado para 'user_id'
    header("Location: login.php");
    exit;
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

        <!-- Instruções e espaço para a videochamada -->
        <div class="alert alert-info">
            <strong>Instruções:</strong> A videochamada será iniciada abaixo. Verifique sua conexão.
        </div>

        <div class="embed-responsive embed-responsive-16by9">
            <!-- Jitsi Meet iframe -->
            <iframe src="https://meet.jit.si/OrientacaoAluno<?php echo uniqid(); ?>" frameborder="0" allow="camera; microphone; fullscreen; display-capture" sandbox="allow-forms allow-scripts allow-same-origin" class="embed-responsive-item"></iframe>
        </div>
        
        <div class="mt-3">
            <a href="../dashboard/aluno/index.php" class="btn btn-primary">Voltar ao Dashboard</a>
        </div>
    </div>

    <!-- Scripts do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
