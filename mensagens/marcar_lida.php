<?php
// Conectar ao banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "autismo_plataforma"; // Alterado para o banco de dados correto
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Iniciar sessão e verificar login
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o ID da notificação foi fornecido
if (isset($_GET['id'])) {
    $id_notificacao = $_GET['id'];

    // Atualizar a notificação para marcada como lida
    $sql = "UPDATE notificacoes SET lida = TRUE WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_notificacao, $_SESSION['user_id']);
    $stmt->execute();

    $stmt->close();
}

$conn->close();

// Redirecionar de volta para a página de notificações
header("Location: notificacoes.php");
exit();
?>
