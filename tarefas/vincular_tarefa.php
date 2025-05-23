<?php
session_start();
require_once('../includes/conexao.php'); // Conexão com o banco de dados

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php'); // Redireciona para o login se não estiver autenticado
    exit();
}

// Verificar se o ID da tarefa foi passado
if (isset($_GET['id'])) {
    $tarefa_id = intval($_GET['id']); // Garantir que o ID da tarefa seja um número inteiro

    // Verificar se o ID da tarefa é válido
    if ($tarefa_id <= 0) {
        header("Location: ../tarefas/minhas_tarefas.php?erro=ID da tarefa inválido.");
        exit();
    }

    // Verificar se a tarefa existe no banco de dados
    $sql_verificar_tarefa = "SELECT id FROM tarefas WHERE id = ?";
    $stmt_verificar_tarefa = $conn->prepare($sql_verificar_tarefa);

    if (!$stmt_verificar_tarefa) {
        die("Erro ao preparar a query de verificação: " . $conn->error);
    }

    $stmt_verificar_tarefa->bind_param("i", $tarefa_id);
    $stmt_verificar_tarefa->execute();
    $stmt_verificar_tarefa->store_result();

    if ($stmt_verificar_tarefa->num_rows === 0) {
        header("Location: ../tarefas/minhas_tarefas.php?erro=Tarefa não encontrada.");
        exit();
    }

    $stmt_verificar_tarefa->close();

    // Buscar todos os alunos na tabela `usuarios`
    $sql_buscar_alunos = "SELECT id FROM usuarios WHERE tipo_usuario = 'aluno'";
    $result_alunos = $conn->query($sql_buscar_alunos);

    if (!$result_alunos) {
        die("Erro ao buscar alunos: " . $conn->error);
    }

    // Vincular a tarefa a cada aluno
    $sql_vincular_tarefa = "INSERT INTO tarefas_alunos (tarefa_id, aluno_id) VALUES (?, ?)";
    $stmt_vincular_tarefa = $conn->prepare($sql_vincular_tarefa);

    if (!$stmt_vincular_tarefa) {
        die("Erro ao preparar a query de vinculação: " . $conn->error);
    }

    $vinculacoes_realizadas = 0;

    while ($aluno = $result_alunos->fetch_assoc()) {
        $aluno_id = $aluno['id'];
        $stmt_vincular_tarefa->bind_param("ii", $tarefa_id, $aluno_id);

        if ($stmt_vincular_tarefa->execute()) {
            $vinculacoes_realizadas++;
        } else {
            // Log de erro (opcional)
            error_log("Erro ao vincular tarefa ao aluno ID: $aluno_id");
        }
    }

    $stmt_vincular_tarefa->close();
    $conn->close();

    if ($vinculacoes_realizadas > 0) {
        // Redirecionar com mensagem de sucesso
        header("Location: ../tarefas/minhas_tarefas.php?sucesso=Tarefa vinculada a $vinculacoes_realizadas aluno(s) com sucesso!");
    } else {
        // Redirecionar com mensagem de erro
        header("Location: ../tarefas/minhas_tarefas.php?erro=Nenhum aluno encontrado para vincular a tarefa.");
    }
} else {
    // Se não houver ID da tarefa, redirecionar de volta
    header("Location: ../tarefas/minhas_tarefas.php?erro=Parâmetros faltando.");
}
?>