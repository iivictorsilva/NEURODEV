<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Verificar se o usuário está logado e redirecionar para verifica_acesso.php caso não seja admin
if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../verifica_acesso.php");
    exit();
}

// Se o usuário for admin, permitir acesso à página

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

// Consultar as configurações atuais (exemplo simples)
$sql = "SELECT * FROM configuracoes LIMIT 1"; // Presumindo que você tenha uma tabela de configurações
$result = $conn->query($sql);
$config = $result->fetch_assoc();

// Se o formulário de atualização for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_plataforma = $_POST['nome_plataforma'];
    $email_contato = $_POST['email_contato'];

    // Atualizar as configurações no banco
    $update_sql = "UPDATE configuracoes SET nome_plataforma = ?, email_contato = ? WHERE id = 1";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ss', $nome_plataforma, $email_contato);
    if ($stmt->execute()) {
        $message = "Configurações atualizadas com sucesso!";
    } else {
        $message = "Erro ao atualizar as configurações.";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
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
                <h1>Configurações da Plataforma</h1>

                <?php if (isset($message)) { ?>
                    <div class="alert alert-info">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>

                <form action="configuracoes.php" method="POST">
                    <div class="mb-3">
                        <label for="nome_plataforma" class="form-label">Nome da Plataforma</label>
                        <input type="text" class="form-control" id="nome_plataforma" name="nome_plataforma" value="<?php echo $config['nome_plataforma']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email_contato" class="form-label">E-mail de Contato</label>
                        <input type="email" class="form-control" id="email_contato" name="email_contato" value="<?php echo $config['email_contato']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Fechar a conexão
$conn->close();
?>
