<?php
// Garantir que nenhum output seja enviado antes do cabeçalho
if (ob_get_length()) ob_clean();

// Configurações iniciais
header('Content-Type: application/json');
error_reporting(0); // Desativar erros na resposta (use 1 apenas para desenvolvimento)

try {
    // Iniciar sessão se ainda não estiver ativa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar autenticação
    if (empty($_SESSION['user_id'])) {
        throw new Exception("Acesso não autorizado", 401);
    }

    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método não permitido", 405);
    }

    // Verificar parâmetro ID
    if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
        throw new Exception("ID inválido", 400);
    }

    // Incluir arquivo de conexão
    require_once __DIR__.'/../includes/conexao.php';
    
    // Obter IDs
    $tarefa_id = (int)$_GET['id'];
    $aluno_id = (int)$_SESSION['user_id'];

    // Verificar relação tarefa-aluno
    $sql = "UPDATE tarefas t
            JOIN tarefas_alunos ta ON t.id = ta.tarefa_id
            SET t.status = 'concluida', t.data_conclusao = NOW()
            WHERE t.id = ? AND ta.aluno_id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no preparo da consulta", 500);
    }

    $stmt->bind_param("ii", $tarefa_id, $aluno_id);
    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar atualização", 500);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("Tarefa não encontrada ou não pertence ao aluno", 404);
    }

    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Tarefa marcada como concluída!',
        'data' => [
            'task_id' => $tarefa_id,
            'completed_at' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    // Resposta de erro
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    
} finally {
    // Fechar conexões
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>