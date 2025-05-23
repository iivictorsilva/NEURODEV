<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conectar ao banco de dados
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "autismo_plataforma";
    
    $conn = new mysqli($host, $user, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }
    
    // Recuperar o ID do usuário da sessão
    $usuario_id = $_SESSION['user_id'];
    
    // Validar e sanitizar os dados do formulário
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Verificar se os campos obrigatórios estão preenchidos
    if (empty($nome) || empty($email)) {
        $_SESSION['erro_atualizacao'] = "Por favor, preencha todos os campos obrigatórios.";
        header("Location: perfil.php");
        exit();
    }
    
    // Verificar se o email já está em uso por outro usuário
    $sql_check_email = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check_email);
    $stmt_check->bind_param("si", $email, $usuario_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows > 0) {
        // Email já está em uso por outro usuário
        $_SESSION['erro_atualizacao'] = "Este email já está sendo usado por outro usuário.";
        header("Location: perfil.php");
        exit();
    }
    
    // Atualizar os dados do usuário no banco de dados
    $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nome, $email, $usuario_id);
    
    if ($stmt->execute()) {
        // Atualização bem-sucedida
        $_SESSION['sucesso_atualizacao'] = "Perfil atualizado com sucesso!";
        
        // Atualizar os dados na sessão
        $_SESSION['user_name'] = $nome;
        $_SESSION['user_email'] = $email;
    } else {
        // Erro na atualização
        $_SESSION['erro_atualizacao'] = "Erro ao atualizar o perfil. Por favor, tente novamente.";
    }
    
    $stmt->close();
    $conn->close();
    
    // Redirecionar de volta para a página de perfil
    header("Location: perfil.php");
    exit();
} else {
    // Se alguém tentar acessar diretamente este arquivo sem enviar o formulário
    header("Location: perfil.php");
    exit();
}
?>