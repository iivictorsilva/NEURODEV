<?php
// Incluir o arquivo de verificação de acesso
$pagina_permitida_para = 'professor'; // Definindo que apenas o professor pode acessar esta página
include('../verifica_acesso.php'); // Verifica o acesso do usuário

// Conectar ao banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');
if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Consultar a lista de alunos
$alunos = [];
$result = $mysqli->query("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno'");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $alunos[] = $row;
    }
}

// Obter a última atividade do aluno
$ultima_atividade = null;
if (isset($_GET['aluno_id'])) {
    $aluno_id = $_GET['aluno_id'];
    $atividade_result = $mysqli->query("SELECT * FROM tarefas WHERE usuario_id = '$aluno_id' ORDER BY data_limite DESC LIMIT 1");

    if ($atividade_result && $atividade_result->num_rows > 0) {
        $ultima_atividade = $atividade_result->fetch_assoc();
    }
}

// Se o formulário for enviado para agendar a chamada ou adicionar anotações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agendar_chamada'])) {
        $aluno_id = $_POST['aluno_id'];
        $data_chamada = $_POST['data_chamada'];
        $hora_chamada = $_POST['hora_chamada'];

        // Agendar a videochamada no banco de dados
        $agendar_query = "INSERT INTO video_chamadas (professor_id, aluno_id, data, hora) VALUES ('$user_id', '$aluno_id', '$data_chamada', '$hora_chamada')";
        if ($mysqli->query($agendar_query) === TRUE) {
            echo "<script>alert('Chamada agendada com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao agendar a chamada.');</script>";
        }
    } elseif (isset($_POST['adicionar_anotacao'])) {
        $anotacao = $_POST['anotacao'];
        $aluno_id = $_POST['aluno_id'];

        // Adicionar anotação no banco de dados
        $anotacao_query = "INSERT INTO anotacoes (professor_id, aluno_id, anotacao, data) VALUES ('$user_id', '$aluno_id', '$anotacao', NOW())";
        if ($mysqli->query($anotacao_query) === TRUE) {
            echo "<script>alert('Anotação adicionada com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao adicionar anotação.');</script>";
        }
    }
}

// Obter histórico de videochamadas
$historico_chamadas = [];
if (isset($aluno_id)) {
    $historico_result = $mysqli->query("SELECT * FROM video_chamadas WHERE aluno_id = '$aluno_id' ORDER BY data DESC");

    if ($historico_result) {
        while ($row = $historico_result->fetch_assoc()) {
            $historico_chamadas[] = $row;
        }
    }
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
            <iframe src="https://meet.jit.si/OrientacaoProfessor<?php echo uniqid(); ?>" frameborder="0" allow="camera; microphone; fullscreen; display-capture" sandbox="allow-forms allow-scripts allow-same-origin" class="embed-responsive-item"></iframe>
        </div>

        <hr>

        <h3>Última Atividade Passada</h3>
        <?php if ($ultima_atividade): ?>
            <p><strong>Título:</strong> <?= $ultima_atividade['titulo'] ?></p>
            <p><strong>Data de Entrega:</strong> <?= $ultima_atividade['data_limite'] ?></p>
            <p><strong>Descrição:</strong> <?= $ultima_atividade['descricao'] ?></p>
        <?php else: ?>
            <p>Não há atividades passadas para este aluno.</p>
        <?php endif; ?>

        <hr>

        <h3>Adicionar Anotação</h3>
        <form method="POST" action="">
            <input type="hidden" name="aluno_id" value="<?= $aluno_id ?>">
            <div class="form-group">
                <textarea class="form-control" name="anotacao" rows="4" placeholder="Escreva suas anotações aqui..." required></textarea>
            </div>
            <button type="submit" name="adicionar_anotacao" class="btn btn-success">Adicionar Anotação</button>
        </form>

        <hr>

        <h3>Histórico de Videochamadas</h3>
        <?php if (count($historico_chamadas) > 0): ?>
            <ul class="list-group">
                <?php foreach ($historico_chamadas as $chamada): ?>
                    <li class="list-group-item">
                        <strong>Data:</strong> <?= $chamada['data'] ?><br>
                        <strong>Duração:</strong> <?= $chamada['duracao'] ?> minutos<br>
                        <strong>Status:</strong> <?= $chamada['status'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Não há histórico de chamadas com este aluno.</p>
        <?php endif; ?>

        <hr>

        <h3>Agendar Video Chamada</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="aluno_id">Selecione o Aluno:</label>
                <select class="form-control" id="aluno_id" name="aluno_id">
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= $aluno['id'] ?>"><?= $aluno['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="data_chamada">Data:</label>
                <input type="date" class="form-control" id="data_chamada" name="data_chamada" required>
            </div>
            <div class="form-group">
                <label for="hora_chamada">Hora:</label>
                <input type="time" class="form-control" id="hora_chamada" name="hora_chamada" required>
            </div>
            <button type="submit" name="agendar_chamada" class="btn btn-success">Agendar Chamada</button>
        </form>

        <div class="mt-3">
            <a href="index.php" class="btn btn-primary">Voltar ao Dashboard</a>
        </div>
    </div>

    <!-- Scripts do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
