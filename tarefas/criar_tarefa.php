<?php
// Inicia a sessão para acessar variáveis de sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados
require_once('../includes/conexao.php'); // Certifique-se de que o caminho da conexão está correto

// Verifica se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    // Se o usuário não estiver autenticado, redireciona para a página de login
    header('Location: login.php');
    exit(); // Encerra a execução do script
}

 // Verifica se o método de requisição é POST
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $prazo = $_POST['prazo'];
    $usuario_id = $_SESSION['user_id']; // Obtém o ID do usuário da sessão

    // Prepara a query SQL para inserir uma nova tarefa no banco de dados
    $sql = "INSERT INTO tarefas (titulo, descricao, prazo, usuario_id) 
            VALUES ('$titulo', '$descricao', '$prazo', '$usuario_id')";

    // Executa a query e verifica se foi bem-sucedida
    if ($conn->query($sql) === TRUE) {
        // Se a inserção for bem-sucedida, redireciona para a página de tarefas
        header("Location: minhas_tarefas.php");
        exit(); // Encerra a execução do script
    } else {
        // Se houver um erro na execução da query, exibe uma mensagem de erro
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>


