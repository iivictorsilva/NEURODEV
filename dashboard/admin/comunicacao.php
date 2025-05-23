<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../../login/login.php");
    exit();
}

// Se o usuário estiver logado, redirecionar para verifica_acesso.php
if (isset($_SESSION['user_id']) && $_SESSION['tipo_usuario'] != 'admin') {
    // Se for logado, mas não for admin, redireciona para a página de verificação de acesso
    header("Location: ../verifica_acesso.php");
    exit();
}

// Se o usuário for admin, permitir acesso à página
$user_id = $_SESSION['user_id']; // ID do usuário logado
$tipo_usuario = $_SESSION['tipo_usuario']; // Tipo de usuário (admin)

// Conectar ao banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "autismo_plataforma";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consultar todos os professores
$sql_professores = "SELECT id, nome FROM usuarios WHERE tipo_usuario = 'professor'";
$result_professores = $conn->query($sql_professores);

// Consultar mensagens
$sql = "SELECT mensagens.id, mensagens.mensagem, mensagens.assunto, mensagens.data_envio, mensagens.lida, usuarios.nome AS remetente
        FROM mensagens
        INNER JOIN usuarios ON mensagens.remetente_id = usuarios.id
        WHERE mensagens.destinatario_id = $user_id OR mensagens.remetente_id = $user_id
        ORDER BY mensagens.data_envio ASC";
$result = $conn->query($sql);

// Marcar mensagem como lida
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensagem_id'])) {
    $mensagem_id = $_POST['mensagem_id'];
    $sql_update = "UPDATE mensagens SET lida = TRUE WHERE id = $mensagem_id";
    $conn->query($sql_update);
}

// Enviar uma nova mensagem
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['mensagem_id'])) {
    $mensagem = $_POST['mensagem'];
    $assunto = $_POST['assunto'];
    $destinatario_id = $_POST['destinatario_id']; // ID do professor escolhido

    // Inserir mensagem no banco
    $sql_insert = "INSERT INTO mensagens (mensagem, remetente_id, destinatario_id, assunto)
                   VALUES ('$mensagem', '$user_id', '$destinatario_id', '$assunto')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "Mensagem enviada com sucesso!";
    } else {
        echo "Erro ao enviar mensagem: " . $conn->error;
    }
}
?>





<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunicação - Professor/Administrador</title>
    <link rel="stylesheet" href="../src/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <a class="nav-link" href="index.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="alunos.php">Alunos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="professores.php">Professores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tarefas.php">Tarefas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="eventos.php">Eventos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="relatorios.php">Relatórios</a>
                        </li>
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
                <h2 class="my-4">Comunicação</h2>

                <!-- Exibir mensagens -->
                <div class="messages-box">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='message" . ($row['lida'] ? " lida" : " nao-lida") . "'>";
                            echo "<strong>" . $row['remetente'] . ":</strong> " . $row['mensagem'];
                            echo "<br><small>Assunto: " . $row['assunto'] . " | Enviado em: " . $row['data_envio'] . "</small>";

                            // Formulário para marcar a mensagem como lida
                            if ($row['lida'] == 0) {
                                echo "<form method='POST'>
                                        <input type='hidden' name='mensagem_id' value='" . $row['id'] . "'>
                                        <button type='submit' class='btn btn-success btn-sm mt-2'>Marcar como lida</button>
                                      </form>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Nenhuma mensagem encontrada.</p>";
                    }
                    ?>
                </div>

                <!-- Formulário para enviar novas mensagens -->
                <form method="POST">
                    <div class="form-group">
                        <label for="destinatario_id">Selecione o Professor</label>
                        <select name="destinatario_id" id="destinatario_id" class="form-control" required>
                            <option value="">-- Escolha um professor --</option>
                            <?php
                            if ($result_professores->num_rows > 0) {
                                while ($professor = $result_professores->fetch_assoc()) {
                                    echo "<option value='" . $professor['id'] . "'>" . $professor['nome'] . "</option>";
                                }
                            } else {
                                echo "<option value=''>Nenhum professor disponível</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="assunto">Assunto</label>
                        <select name="assunto" id="assunto" class="form-control" required>
                            <option value="reclamacao">Reclamação</option>
                            <option value="elogio">Elogio</option>
                            <option value="sugestao">Sugestão</option>
                            <option value="duvida">Dúvida</option>
                            <option value="comum">Comum</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem</label>
                        <textarea name="mensagem" id="mensagem" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Enviar</button>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
