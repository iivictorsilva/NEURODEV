<?php
// Incluindo o arquivo de verificação de acesso
include('../verifica_acesso.php');

// Conectar ao banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');

// Verificar a conexão
if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Inicializar a variável do nome do usuário
$user_name = 'Usuário'; // Nome padrão caso o nome do professor não seja encontrado

// Verificar se o ID do professor está na sessão (o login deve ter atribuído um ID)
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Obtém o ID do usuário da sessão
} else {
    $user_id = 1; // Defina um valor padrão caso não esteja logado (apenas para testes)
}

// Consultar o nome do professor
$result_nome = $mysqli->query("SELECT nome FROM usuarios WHERE id = '$user_id' AND tipo_usuario = 'professor' LIMIT 1");
if ($result_nome && $result_nome->num_rows > 0) {
    $row = $result_nome->fetch_assoc();
    $user_name = $row['nome']; // Atribui o nome do professor
} else {
    $user_name = 'Professor não encontrado'; // Mensagem de erro caso o nome não seja encontrado
}

// Consultar as mensagens recebidas, agora com o nome do remetente
$query_mensagens = "
    SELECT m.*, u.nome AS remetente_nome
    FROM mensagens m
    JOIN usuarios u ON m.remetente_id = u.id
    WHERE m.destinatario_id = '$user_id'
    ORDER BY m.data_envio DESC
";
$result_mensagens = $mysqli->query($query_mensagens);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunicações - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
            padding-top: 20px;
            width: 250px;
            transition: width 0.3s ease;
        }

        .sidebar .nav-item {
            margin-bottom: 10px;
        }

        .sidebar .nav-link {
            font-size: 18px;
            color: #333;
        }

        .sidebar .nav-link:hover {
            background-color: #007bff;
            color: #fff;
        }

        .sidebar .nav-item.active .nav-link {
            background-color: #007bff;
            color: #fff;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .toggle-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 25px;
            cursor: pointer;
            z-index: 1;
        }

        .card-custom {
            margin-bottom: 20px;
        }

        .message-container {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .message-box {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .message-box p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="bg-light sidebar p-3" id="sidebar">
            
        <h2 class="text-center" id="site-name">
                    Neuro<span id="dev">Dev</span>
            </h2>

            <style>
            @keyframes colorChange {
            0% { color: rgb(255, 0, 0); }   /* Vermelho */
            25% { color: rgb(255, 255, 0); } /* Amarelo */
            50% { color: rgb(0, 0, 255); }   /* Azul */
            75% { color: rgb(0, 255, 0); }   /* Verde */
            100% { color: rgb(255, 0, 0); }  /* Vermelho */
            }

            #dev {
                animation: colorChange 5s infinite;
            }
            </style>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="../professor/index.php"><i class="fas fa-tachometer-alt"></i> <span>Início</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../professor/comunicacao.php"><i class="fas fa-comments"></i> <span>Comunicações</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../video_chamada/video_chamada.php"><i class="fas fa-video"></i> <span>Video Chamada</span></a>
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
            <span class="toggle-btn" onclick="toggleSidebar()">&#9776;</span>

            <div class="container-fluid">
                <h1 class="my-4">Comunicações - Professor <?php echo htmlspecialchars($user_name); ?></h1>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title">Mensagens Recentes</h5>
                                <div class="message-container">
                                    <?php
                                    if ($result_mensagens && $result_mensagens->num_rows > 0) {
                                        while ($msg = $result_mensagens->fetch_assoc()) {
                                            // Verificar se o campo "conteudo" está disponível
                                            $conteudo = isset($msg['conteudo']) ? $msg['conteudo'] : 'Conteúdo não disponível';

                                            echo '<div class="message-box">';
                                            echo '<p><strong>' . htmlspecialchars($msg['remetente_nome']) . ':</strong> ' . htmlspecialchars($conteudo) . '</p>';
                                            echo '<p><small>Enviada em: ' . date('d/m/Y H:i', strtotime($msg['data_envio'])) . '</small></p>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<p>Não há mensagens recentes.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-custom">
                            <div class="card-body">
                                <h5 class="card-title">Enviar Nova Mensagem</h5>
                                <form action="envia_mensagem.php" method="POST">
                                    <div class="mb-3">
                                        <label for="destinatario" class="form-label">Destinatário</label>
                                        <input type="text" class="form-control" id="destinatario" name="destinatario" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="conteudo" class="form-label">Mensagem</label>
                                        <textarea class="form-control" id="conteudo" name="conteudo" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Enviar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("collapsed");
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
