<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/AutismoProjeto2/login.php"); // Redireciona para a página de login
    exit();
}

// Verifica se a variável que define o tipo de usuário foi definida
if (!isset($pagina_permitida_para)) {
    // Caso não tenha sido definida, redireciona com uma mensagem de erro
    header("Location: http://localhost/AutismoProjeto2/erro403.php");
    exit();
}

// Verifica o tipo de usuário permitido para acessar a página
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== $pagina_permitida_para) {
    header("Location: http://localhost/AutismoProjeto2/erro403.php"); // Página de erro de acesso negado
    exit();
}
