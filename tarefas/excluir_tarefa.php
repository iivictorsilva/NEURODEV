<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php'); // Redireciona para o login se não estiver autenticado
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
require_once('../includes/conexao.php'); // Ajuste o caminho conforme necessário

// Verificar se o ID da tarefa foi passado via GET
if (isset($_GET['id'])) {
    // Sanitizar o ID da tarefa (garantir que é um número inteiro)
    $tarefa_id = intval($_GET['id']);

    // Verificar se o ID é válido
    if ($tarefa_id <= 0) {
        die("ID da tarefa inválido.");
    }

    // Preparar a query para excluir a tarefa
    $sql = "DELETE FROM tarefas WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Verificar se a query foi preparada corretamente
    if (!$stmt) {
        die("Erro ao preparar a query: " . $conn->error);
    }

    // Vincular o parâmetro (ID da tarefa) à query
    $stmt->bind_param("i", $tarefa_id);

    // Executar a query
    $stmt->execute();

    // Verificar se a tarefa foi excluída com sucesso
    if ($stmt->affected_rows > 0) {
        // Redirecionar com mensagem de sucesso
        header("Location: ../tarefas/minhas_tarefas.php?sucesso=Tarefa excluída com sucesso!");
    } else {
        // Redirecionar com mensagem de erro
        header("Location: ../tarefas/minhas_tarefas.php?erro=Erro ao excluir a tarefa.");
    }

    // Fechar a declaração e a conexão
    $stmt->close();
    $conn->close();
} else {
    // Se não houver ID, redirecionar de volta com mensagem de erro
    header("Location: ../tarefas/minhas_tarefas.php?erro=ID da tarefa não fornecido.");
}
?>