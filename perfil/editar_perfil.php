<?php
// Iniciar a sessão
session_start();

// Verificar o conteúdo da variável de sessão
var_dump($_SESSION['usuario_id']); // Verifica o valor armazenado na sessão
exit; // Para garantir que o código não continue sendo executado enquanto debugamos

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Conectar ao banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "autismo_plataforma";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter o ID do usuário logado
$user_id = $_SESSION['usuario_id'];

// Buscar as informações do usuário no banco de dados
$sql = "SELECT * FROM usuarios WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "Usuário não encontrado!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = isset($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : $user['senha'];
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : $user['cpf'];
    $matricula = isset($_POST['matricula']) ? $_POST['matricula'] : $user['matricula'];

    // Atualizar as informações no banco de dados
    $sql_update = "UPDATE usuarios SET nome='$nome', email='$email', senha='$senha', cpf='$cpf', matricula='$matricula' WHERE id='$user_id'";

    if ($conn->query($sql_update) === TRUE) {
        echo "Perfil atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar perfil: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="src/styles.css">
</head>
<body>
    <div class="container">
        <h2>Editar Perfil</h2>

        <form action="editar_perfil.php" method="POST" class="form">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" placeholder="Nova senha (deixe em branco para não alterar)">

            <!-- Exibir CPF ou matrícula dependendo do tipo de usuário -->
            <?php if ($user['tipo_usuario'] != 'aluno'): ?>
                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" value="<?php echo htmlspecialchars($user['cpf']); ?>" required>
            <?php endif; ?>

            <?php if ($user['tipo_usuario'] == 'aluno'): ?>
                <label for="matricula">Matrícula:</label>
                <input type="text" name="matricula" value="<?php echo htmlspecialchars($user['matricula']); ?>" required>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>

        <div class="mt-3">
            <a href="perfil.php" class="btn btn-secondary">Voltar para o Perfil</a>
        </div>
    </div>
</body>
</html>
