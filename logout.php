<?php
session_start();

// Destruir a sessão e redirecionar para a página de login
session_unset();
session_destroy();

// Redirecionar para a página de login
header("Location: login/login.php"); // Agora o logout vai levar para a página de login no diretório correto
exit();
?>
