<?php
session_start();

// Se o usuário já estiver logado, redireciona para a dashboard de acordo com o tipo de usuário
if (isset($_SESSION['user_id'])) {
    // Verifica o tipo de usuário e redireciona para a página adequada
    if ($_SESSION['tipo_usuario'] == 'admin') {
        header("Location: ../dashboard/admin/index.php");
    } elseif ($_SESSION['tipo_usuario'] == 'professor') {
        header("Location: ../dashboard/professor/index.php");
    } else {
        header("Location: ../dashboard/aluno/index.php");
    }
    exit();
}

// Conectar ao banco de dados
$host = "localhost";
$user = "root"; 
$password = "";
$dbname = "autismo_plataforma"; 

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}

// Variável para armazenar a mensagem de erro
$erro = "";

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  // Filtra e-mail
    $senha = $_POST['senha']; // Senha

    // Consulta segura usando prepared statements
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar a senha usando a função password_verify
        if (password_verify($senha, $user['senha'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id']; // Armazenar ID do usuário na sessão
            $_SESSION['user_name'] = $user['nome']; // Armazenar nome do usuário na sessão
            $_SESSION['tipo_usuario'] = $user['tipo_usuario']; // Armazenar tipo de usuário

            // Redireciona de acordo com o tipo de usuário
            if ($user['tipo_usuario'] == 'admin') {
                header("Location: ../dashboard/admin/index.php");
            } elseif ($user['tipo_usuario'] == 'professor') {
                header("Location: ../dashboard/professor/index.php");
            } else {
                header("Location: ../dashboard/aluno/index.php");
            }
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="styleTelaLogin.css">
    
    <title>Responsive login and registration form - Bedimcode</title>
    <style>
        /* Estilos para a mensagem de erro */
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #ef9a9a;
            text-align: center;
            font-size: 14px;
            animation:  0.3s ;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Estilos para a notificação */
        .notification {
            display: none;
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            animation: slideDown 2s ease-out, fadeOut 7s ease-out 7s;
        }

        @keyframes slideDown {
            from { top: -100px; }
            to { top: 20px; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>


<script>
    // Função para esconder a mensagem de erro após 5 segundos
    function hideErrorMessage() {
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.animation = 'fadeOut 0.5s ease-out';
                setTimeout(() => {
                    errorMessage.remove();
                }, 200);
            }, 2000); // 5000ms = 5 segundos
        }
    }

    // Executa quando a página carrega
    document.addEventListener('DOMContentLoaded', hideErrorMessage);
</script>
</head>
<body>
    <!--=============== LOGIN IMAGE ===============-->
    <svg class="login__blob" viewBox="0 0 566 840" xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0" mask-type="alpha">
            <path d="M342.407 73.6315C388.53 56.4007 394.378 17.3643 391.538 
            0H566V840H0C14.5385 834.991 100.266 804.436 77.2046 707.263C49.6393 
            591.11 115.306 518.927 176.468 488.873C363.385 397.026 156.98 302.824 
            167.945 179.32C173.46 117.209 284.755 95.1699 342.407 73.6315Z"/>
        </mask>
    
        <g mask="url(#mask0)">
            <path d="M342.407 73.6315C388.53 56.4007 394.378 17.3643 391.538 
            0H566V840H0C14.5385 834.991 100.266 804.436 77.2046 707.263C49.6393 
            591.11 115.306 518.927 176.468 488.873C363.385 397.026 156.98 302.824 
            167.945 179.32C173.46 117.209 284.755 95.1699 342.407 73.6315Z"/>
    
            <!-- Insert your image (recommended size: 1000 x 1200) -->
            <image class="login__img" href="img/bg-img.png"/>
        </g>
    </svg>      

    <!--=============== LOGIN ===============-->
    <div class="login container grid" id="loginAccessRegister">
        <!--===== LOGIN ACCESS =====-->
        <div class="login__access">
            <h1 class="login__title">Faça login em sua conta.</h1>
            
            <div class="login__area">
                <!-- Mensagem de erro -->
                <?php if (!empty($erro)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" class="login__form" method="POST">
                    <div class="login__content grid">
                        <div class="login__box">
                            <input type="email" id="email" name="email" required placeholder=" " class="login__input">
                            <label for="email" class="login__label">Email</label>
                
                            <i class="ri-mail-fill login__icon"></i>
                        </div>
            
                        <div class="login__box">
                            <input type="password" id="password" name="senha" required placeholder=" " class="login__input">
                            <label for="password" class="login__label">Password</label>
                
                            <i class="ri-eye-off-fill login__icon login__password" id="loginPassword"></i>
                        </div>
                    </div>
            
                    <a href="#" class="login__forgot">Esqueceu sua senha?</a>
            
                    <button type="submit" class="login__button">Login</button>
                </form>
    
                <p class="login__switch">
                    Don't have an account? 
                    <button id="loginButtonRegister">Criar uma conta</button>
                </p>
            </div>
        </div>

        <!--===== LOGIN REGISTER =====-->
        <div class="login__register">
            <h1 class="login__title">Crie uma nova conta.</h1>
        
            <div class="login__area">
                <form action="cadastro.php" class="login__form" method="POST">
                    <div class="login__content grid">
                        <div class="login__group grid">
                            <div class="login__box">
                                <input type="text" id="names" name="nome" required placeholder=" " class="login__input">
                                <label for="names" class="login__label">Names</label>
                                <i class="ri-id-card-fill login__icon"></i>
                            </div>
        
                            <div class="login__box">
                                <input type="text" id="surnames" name="surnames" required placeholder=" " class="login__input">
                                <label for="surnames" class="login__label">Surnames</label>
                                <i class="ri-id-card-fill login__icon"></i>
                            </div>
                        </div>
        
                        <div class="login__box">
                            <input type="email" id="emailCreate" name="email" required placeholder=" " class="login__input">
                            <label for="emailCreate" class="login__label">Email</label>
                            <i class="ri-mail-fill login__icon"></i>
                        </div>
        
                        <div class="login__box">
                            <input type="password" id="passwordCreate" name="senha" required placeholder=" " class="login__input">
                            <label for="passwordCreate" class="login__label">Password</label>
                            <i class="ri-eye-off-fill login__icon login__password" id="loginPasswordCreate"></i>
                        </div>
        
                        <!-- Novo campo para selecionar tipo de usuário -->
                        <div class="login__box">
                            <select id="tipo_usuario" class="login__input" name="tipo_usuario" required data-live-search="false" onchange="toggleExtraFields()">
                                <option value="" disabled selected hidden>Escolha o tipo de usuário</option>
                                <option value="aluno">Aluno</option>
                                <option value="professor">Professor</option>
                            </select>
                            <label for="tipo_usuario" class="login__label">Tipo de usuario</label>
                        </div>
                    </div>
        
                    <button type="submit" class="login__button">Criar uma conta</button>
                </form>
        
                <p class="login__switch">
                    Already have an account? 
                    <button id="loginButtonAccess">Log In</button>
                </p>
            </div>
        </div>
    </div>
    
    <!--=============== MAIN JS ===============-->
    <script src="script.js"></script>
    <script>
        function toggleExtraFields() {
            const tipoUsuario = document.getElementById("tipo_usuario").value;

            // Mostrar campo CPF apenas para "professor"
            document.getElementById("cpf-field").style.display = (tipoUsuario === "professor") ? "block" : "none";
            // Mostrar campo Documento Profissional apenas para "professor"
            document.getElementById("doc-professor-field").style.display = (tipoUsuario === "professor") ? "block" : "none";
            // Mostrar campo Matrícula apenas para "aluno"
            document.getElementById("matricula-field").style.display = (tipoUsuario === "aluno") ? "block" : "none";
            // Mostrar campo Comprovante de Matrícula apenas para "aluno"
            document.getElementById("comprovante-matricula-field").style.display = (tipoUsuario === "aluno") ? "block" : "none";
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

    <!-- Notificação de sucesso -->
    <div id="successNotification" class="notification">
        <p>Cadastro realizado com sucesso!</p>
    </div>
</body>
</html>