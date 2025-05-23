<?php
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

// Iniciar sessão e verificar login
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Você não está logado.";
    exit();
}

// Buscar as notificações não lidas para o usuário logado
$usuario_id = $_SESSION['user_id'];
$sql = "SELECT tipo, mensagem, data_criacao FROM notificacoes WHERE usuario_id = ? AND lida = FALSE ORDER BY data_criacao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Gerar HTML apenas para as notificações
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="notification-item">';
        echo '<p><strong>Tipo:</strong> ' . htmlspecialchars($row['tipo']) . '</p>';
        echo '<p><strong>Mensagem:</strong> ' . htmlspecialchars($row['mensagem']) . '</p>';
        echo '<p><strong>Data:</strong> ' . htmlspecialchars($row['data_criacao']) . '</p>';
        echo '</div>';
    }
} else {
    echo '<div class="alert alert-info text-center" role="alert">Você não tem notificações.</div>';
}

$stmt->close();
$conn->close();
?>
