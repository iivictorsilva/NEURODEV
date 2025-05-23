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

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consultar relatórios
$sql = "SELECT r.id, u.nome AS aluno_nome, r.data, r.nota, r.comentarios 
        FROM relatorios r 
        JOIN usuarios u ON r.aluno_id = u.id";
$result = $conn->query($sql);

// Verificar se a consulta retornou resultados
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Relatórios</title>
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
                            <a class="nav-link" href="professores.php">Professores</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tarefas.php">Tarefas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="eventos.php">Eventos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="relatorios.php">Relatórios</a>
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
                <h1>Gerenciar Relatórios</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Aluno</th>
                            <th>Data</th>
                            <th>Nota</th>
                            <th>Comentários</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['aluno_nome'] . "</td>";
                                echo "<td>" . $row['data'] . "</td>";
                                echo "<td>" . $row['nota'] . "</td>";
                                echo "<td>" . $row['comentarios'] . "</td>";
                                echo "<td>
                                        <a href='editar_relatorio.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Editar</a>
                                        <a href='deletar_relatorio.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Deletar</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Nenhum relatório encontrado</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
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
