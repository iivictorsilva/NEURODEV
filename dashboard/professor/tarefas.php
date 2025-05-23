<?php
session_start();
require_once('../../includes/conexao.php'); // Certifique-se de que o caminho da conexão está correto

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login/login.php');
    exit();
}

// Verificar se o usuário é do tipo "professor" (ou outro tipo, conforme o caso)
if ($_SESSION['tipo_usuario'] !== 'professor') {
    header('Location: ../verifica_acesso.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coletar os dados do formulário e fazer a validação
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $prazo = trim($_POST['prazo']);
    $usuario_id = $_SESSION['user_id'];

    // Validação simples dos campos
    if (empty($titulo) || empty($descricao) || empty($prazo)) {
        echo "Todos os campos são obrigatórios.";
        exit();
    }

    // Preparar a query para inserir a tarefa usando prepared statements (protege contra injeção de SQL)
    $sql = "INSERT INTO tarefas (titulo, descricao, prazo, usuario_id) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Vincular os parâmetros
        $stmt->bind_param("sssi", $titulo, $descricao, $prazo, $usuario_id);

        // Executar a query
        if ($stmt->execute()) {
            header("Location: minhas_tarefas.php"); // Redireciona para a página de tarefas
            exit();
        } else {
            echo "Erro: " . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro ao preparar a query: " . $conn->error;
    }
}

// Fechar a conexão com o banco de dados
$conn->close();
?>


// Fechar a conexão com o banco
$conn->close();
?>

