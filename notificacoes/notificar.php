<?php
// Conectar ao banco de dados autismo_plataforma
$host = "localhost";
$user = "root";
$password = "";
$dbname = "autismo_plataforma"; // Alterado para o banco de dados correto
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Função para adicionar uma notificação
function adicionarNotificacao($usuario_id, $tipo, $mensagem) {
    global $conn;
    $sql = "INSERT INTO notificacoes (usuario_id, tipo, mensagem) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $usuario_id, $tipo, $mensagem);
    $stmt->execute();
    $stmt->close();
}

// Exemplo de notificação
$usuario_id = 1; // ID do usuário que receberá a notificação
$tipo = "tarefa"; // Tipo da notificação (exemplo: tarefa, evento)
$mensagem = "Você tem uma nova tarefa disponível."; // Mensagem da notificação

adicionarNotificacao($usuario_id, $tipo, $mensagem);

$conn->close();
?>
