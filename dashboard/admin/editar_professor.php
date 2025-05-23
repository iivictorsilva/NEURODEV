<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Verificar se o usuário não é administrador, e redirecionar para verifica_acesso.php
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

// Obter os dados do professor para edição
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT nome, email, cpf FROM usuarios WHERE id = $id AND tipo_usuario = 'professor'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $professor = $result->fetch_assoc();
    } else {
        echo "Professor não encontrado.";
        exit();
    }
} else {
    echo "ID de professor não fornecido.";
    exit();
}

// Atualizar os dados do professor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];

    $sql_update = "UPDATE usuarios SET nome = '$nome', email = '$email', cpf = '$cpf' WHERE id = $id";
    
    if ($conn->query($sql_update) === TRUE) {
        header("Location: professores.php");
        exit();
    } else {
        echo "Erro ao atualizar professor: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Professor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Editar Professor</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" name="nome" id="nome" class="form-control" value="<?= htmlspecialchars($professor['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($professor['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-control" value="<?= htmlspecialchars($professor['cpf']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="professores.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>

<?php
// Fechar conexão
$conn->close();
?>
