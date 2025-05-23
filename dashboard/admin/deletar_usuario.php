<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/login.php");
    exit();
}

// Verificar se o usuário está logado e redirecionar para verifica_acesso.php caso não seja admin
if ($_SESSION['tipo_usuario'] != 'admin') {
    header("Location: ../verifica_acesso.php");
    exit();
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

// Verificar se os parâmetros foram enviados
if (isset($_GET['id']) && isset($_GET['tipo'])) {
    $id = intval($_GET['id']);
    $tipo_usuario = $_GET['tipo'];

    // Validar o tipo de usuário
    if (in_array($tipo_usuario, ['professor', 'aluno'])) {
        // Verificar se o ID é do tipo correto (professor ou aluno)
        $sql_check = "SELECT id FROM usuarios WHERE id = $id AND tipo_usuario = '$tipo_usuario'";
        $result_check = $conn->query($sql_check);

        if ($result_check->num_rows > 0) {
            // Executar o DELETE com base no tipo
            $sql_delete = "DELETE FROM usuarios WHERE id = $id AND tipo_usuario = '$tipo_usuario'";

            if ($conn->query($sql_delete) === TRUE) {
                // Redirecionar para a página correspondente
                $redirect_page = ($tipo_usuario === 'professor') ? 'professores.php' : 'alunos.php';
                header("Location: $redirect_page");
                exit();
            } else {
                echo "Erro ao deletar $tipo_usuario: " . $conn->error;
            }
        } else {
            echo "Usuário não encontrado ou inválido.";
        }
    } else {
        echo "Tipo de usuário inválido.";
    }
} else {
    echo "Parâmetros inválidos.";
}
?>



