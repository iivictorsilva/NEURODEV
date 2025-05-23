<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilo da Sidebar */
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
            width: 250px;
            transition: width 0.3s ease;
        }

        .sidebar .nav-item {
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            font-size: 18px;
            color: #fff;
        }

        .sidebar .nav-link:hover {
            background-color: #007bff;
            color: #fff;
        }

        .sidebar .nav-item.active .nav-link {
            background-color: #007bff;
            color: #fff;
        }

        /* Estilo para quando a sidebar estiver contraída */
        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            text-align: center;
        }

        /* Botão para alternar a sidebar */
        .toggle-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 25px;
            cursor: pointer;
            z-index: 1;
        }

        .sidebar-header {
            color: #fff;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            margin: 0;
        }

        /* Título de boas-vindas */
        .sidebar-header p {
            margin-top: 10px;
            font-size: 14px;
        }

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="bg-dark sidebar p-3" id="sidebar">
        <div class="sidebar-header">
            <h3>NeuroDev</h3>
            <p class="text-light">Bem-vindo, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Visitante'; ?></p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Início</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="video_chamada.php"><i class="fas fa-video"></i> <span>Video Chamada</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tarefas/minhas_tarefas.php"><i class="fas fa-tasks"></i> <span>Tarefas</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="eventos.php"><i class="fas fa-calendar"></i> <span>Eventos</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="mensagens.php"><i class="fas fa-envelope"></i> <span>Mensagens</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="notificacoes_pagina.php"><i class="fas fa-bell"></i> <span>Notificações</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
            </li>
        </ul>
    </div>

    <script>
        // Função para alternar a sidebar
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>
</body>
</html>
