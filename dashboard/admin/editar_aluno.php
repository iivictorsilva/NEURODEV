<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Verificar se o usuário é um administrador, caso contrário, redireciona para verifica_acesso.php
if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../verifica_acesso.php");
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

// Obter os dados do aluno para edição
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT nome, email, matricula FROM usuarios WHERE id = $id AND tipo_usuario = 'aluno'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
    } else {
        echo "Aluno não encontrado.";
        exit();
    }
} else {
    echo "ID de aluno não fornecido.";
    exit();
}

// Atualizar os dados do aluno
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $matricula = $_POST['matricula'];

    $sql_update = "UPDATE usuarios SET nome = '$nome', email = '$email', matricula = '$matricula' WHERE id = $id";
    
    if ($conn->query($sql_update) === TRUE) {
        header("Location: alunos.php");
        exit();
    } else {
        echo "Erro ao atualizar aluno: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Editar Aluno</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($aluno['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($aluno['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="matricula" class="form-label">Matrícula</label>
                <input type="text" name="matricula" id="matricula" class="form-control" value="<?= htmlspecialchars($aluno['matricula']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="alunos.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>

<?php
// Fechar conexão
$conn->close();
?>
