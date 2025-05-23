<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redirecionar para a página de login
    header("Location: ../login.php");
    exit(); // Garantir que o script não continue
}

// Verificar se o usuário é professor
if ($_SESSION['tipo_usuario'] != 'professor') {
    // Se não for professor, redirecionar para a página de acesso negado
    header("Location: ../verifica_acesso.php");
    exit(); // Garantir que o script não continue
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

// Verificar se a mensagem foi enviada
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensagem']) && !empty($_POST['mensagem'])) {
    $mensagem = $conn->real_escape_string($_POST['mensagem']);
    $remetente_id = $_SESSION['user_id'];
    $destinatario_id = 1; // Defina o ID do destinatário de acordo com a lógica desejada (ID do administrador, por exemplo)

    // Usando prepared statements para evitar SQL Injection
    $sql = "INSERT INTO comunicacao (remetente_id, destinatario_id, mensagem, data)
            VALUES (?, ?, ?, NOW())";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $remetente_id, $destinatario_id, $mensagem);

    // Executar a consulta e verificar se foi bem-sucedida
    if ($stmt->execute()) {
        header("Location: comunicacao.php");
        exit();
    } else {
        echo "Erro ao enviar mensagem: " . $stmt->error;
    }

    // Fechar a declaração
    $stmt->close();
}

// Fechar a conexão com o banco de dados
$conn->close();
?>
