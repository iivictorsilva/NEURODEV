<?php
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Conectar ao banco de dados
    $conn = new mysqli("localhost", "root", "", "meu_projeto");
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Verificar se o usuário existe
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Gerar um token único e definir o prazo de validade
        $token = bin2hex(random_bytes(50));
        $expiracao = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token válido por 1 hora

        // Atualizar o token e prazo no banco de dados
        $update_sql = "UPDATE usuarios SET token_redefinicao = '$token', token_expiracao = '$expiracao' WHERE email = '$email'";
        if ($conn->query($update_sql) === TRUE) {

            // Gerar o link de redefinição de senha
            $link = "http://localhost/AutismoProjeto/redefinicao_senha/redefinir_senha.php?token=$token";

            // Configurar o PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'felipevenneza2@gmail.com'; // Substituído pelo seu e-mail
                $mail->Password = 'roselita123';             // Substituído pela sua senha
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                
                $mail->setFrom('felipevenneza2@gmail.com', 'Felipe'); // Atualizado com seu e-mail
                $mail->addAddress($email, 'Nome do Usuário');
                

                // Corpo do e-mail
                $mail->isHTML(true);
                $mail->Subject = 'Redefinição de Senha';
                $mail->Body    = "Clique no link abaixo para redefinir sua senha:<br><a href='$link'>Redefinir Senha</a>";

                // Enviar o e-mail
                $mail->send();
                echo 'Mensagem de redefinição de senha enviada!';
            } catch (Exception $e) {
                echo "Erro ao enviar a mensagem: {$mail->ErrorInfo}";
            }
        } else {
            echo "Erro ao atualizar o token no banco de dados!";
        }
    } else {
        echo "Nenhum usuário encontrado com esse e-mail!";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Redefinição de Senha">
    <title>Esqueceu a Senha</title>
    <link rel="stylesheet" href="../src/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="second-column">
            <h2 class="title title-second">Esqueceu a Senha?</h2>
            <p class="description description-primary">Digite seu e-mail para receber o link de redefinição de senha.</p>
            <form action="enviar_link.php" method="POST" class="form">
                <label class="label-input" for="email">
                    <i class="far fa-envelope"></i>
                    <input id="email" type="email" name="email" placeholder="E-mail" required>
                </label>
                <button type="submit" class="btn btn-second">Enviar Link de Redefinição</button>
            </form>
            <a href="../login.php" class="btn btn-secondary">Voltar para o Login</a>
        </div>
    </div>
</body>
</html>
