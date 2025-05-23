<?php
require_once('../config/db.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar se o token existe no banco de dados
    $sql = "SELECT id FROM usuarios WHERE token_redefinicao = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $senha = $_POST['senha'];

            // Atualizar a senha no banco de dados
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET senha = ?, token_redefinicao = NULL WHERE token_redefinicao = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $senha_hash, $token);
            $stmt->execute();

            echo "Senha redefinida com sucesso!";
        }
    } else {
        echo "Token inválido ou expirado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Defina uma nova senha para sua conta.">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="../src/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="first-column">
            <h2 class="title title-primary">Redefina sua Senha</h2>
            <form action="redefinir_senha.php?token=<?php echo $_GET['token']; ?>" method="POST" class="form">
                <label class="label-input" for="senha">
                    <i class="fas fa-lock"></i>
                    <input id="senha" type="password" name="senha" placeholder="Nova Senha" required>
                </label>
                <button type="submit" class="btn btn-second">Redefinir Senha</button>
            </form>
        </div>
    </div>
</body>
</html>
