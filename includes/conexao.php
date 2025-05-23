<?php
// Definir as configurações de conexão com o banco de dados
$host = 'localhost';        // Endereço do servidor de banco de dados (geralmente 'localhost')
$user = 'root';             // Nome de usuário do banco de dados (normalmente 'root' no XAMPP)
$password = '';             // Senha do banco de dados (deixe vazio se não houver senha)
$dbname = 'autismo_plataforma'; // Nome do banco de dados que você está utilizando

// Criando a conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verificando se houve algum erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error); // Exibe erro e interrompe o script se falhar
}

// Caso a conexão seja bem-sucedida, o script continua normalmente
?>
