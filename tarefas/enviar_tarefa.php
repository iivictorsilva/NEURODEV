<?php
session_start();
require_once('../includes/conexao.php'); // Ajuste o caminho conforme necessário

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php'); // Redireciona para o login se não estiver autenticado
    exit();
}

// Verificar se o ID da tarefa foi passado
if (isset($_GET['id'])) {
    $tarefa_id = $_GET['id'];
    $professor_id = $_SESSION['user_id']; // ID do professor logado

    // Substitua $aluno_id pelo ID do aluno para quem a tarefa será enviada
    $aluno_id = $_SESSION['user_id']; // Exemplo: ID do aluno (substitua pelo valor correto)

    // Atualizar a tarefa para vincular ao aluno
    $sql = "UPDATE tarefas SET aluno_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $aluno_id, $tarefa_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirecionar com mensagem de sucesso
        header("Location: ../tarefas/minhas_tarefas.php?sucesso=Tarefa enviada com sucesso!");
    } else {
        // Redirecionar com mensagem de erro
        header("Location: ../tarefas/minhas_tarefas.php?erro=Erro ao enviar a tarefa.");
    }

    $stmt->close();
    $conn->close();
} else {
    // Se não houver ID, redirecionar de volta
    header("Location: ../tarefas/minhas_tarefas.php");
}
?>