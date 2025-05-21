<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre a Clínica - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Atualizando o FontAwesome -->
    <style>
        /* Estilo do modo escuro */
        body.dark-mode {
            background-color: #121212;
            color: white;
        }

        .dark-mode .navbar {
            background-color: #1f1f1f;
        }

        .dark-mode .card {
            background-color: #2c2c2c;
            color: white;
        }

        .dark-mode .footer {
            background-color: #1f1f1f;
            color: white;
        }

        /* Estilo do cabeçalho */
        .header {
            background-color: #007bff;
            color: white;
            padding: 50px 0;
            text-align: center;
            position: relative;
        }

        .header h1, .header p {
            z-index: 1;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.4); /* Sombra atrás do texto */
            z-index: 0;
        }

        /* Alternar a visibilidade do ícone e estado do modo escuro */
        .mode-toggle {
            cursor: pointer;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                Neuro<span class="rgb-text">Dev</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Clínica</a></li>
                    <li class="nav-item"><a class="nav-link" href="equipe.php">Equipe</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
                <div class="d-flex ms-lg-3">
                    <a href="login/login.php" class="btn btn-primary me-2">Login</a>
                    <a href="login/cadastro.php" class="btn btn-custom">Cadastro</a>
                    <!-- Ícone de alternância para o modo escuro -->
                    <i class="fas fa-moon mode-toggle" onclick="toggleDarkMode()"></i> 
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="header">
        <h1>Sobre a Clínica NeuroDev</h1>
        <p>Transformando vidas através do cuidado e da educação especializada.</p>
    </header>

    <!-- Conteúdo Principal -->
    <div class="container my-5">
        <div class="row">
            <!-- Imagem da Clínica -->
            <div class="col-md-6">
                <img src="https://via.placeholder.com/600x400" alt="Imagem da Clínica" class="img-fluid rounded">
            </div>
            <!-- Informações da Clínica -->
            <div class="col-md-6">
                <h2 class="text-center">Quem Somos</h2>
                <p>
                    A Clínica NeuroDev é dedicada ao cuidado de crianças com transtornos de neurodesenvolvimento, 
                    oferecendo suporte especializado para promover o aprendizado, a integração social e a autonomia. 
                    Com uma equipe altamente qualificada e comprometida, nosso objetivo é proporcionar um ambiente acolhedor 
                    e terapêutico para as famílias que confiam em nosso trabalho.
                </p>
            </div>
        </div>

        <!-- Missão, Visão e Valores -->
        <div class="mission-section my-5">
            <h2 class="text-center">Nossa Missão</h2>
            <p class="text-center">
                Oferecer cuidado especializado e educação inclusiva para crianças com transtornos de neurodesenvolvimento, 
                transformando desafios em oportunidades de crescimento.
            </p>
        </div>
        <div class="vision-section my-5">
            <h2 class="text-center">Nossa Visão</h2>
            <p class="text-center">
                Ser referência nacional no cuidado e desenvolvimento de crianças com necessidades especiais, 
                promovendo inclusão e transformação social.
            </p>
        </div>
        <div class="values-section my-5">
            <h2 class="text-center">Nossos Valores</h2>
            <ul class="values-list text-center">
                <li><strong>Empatia:</strong> Entendemos e respeitamos as necessidades de cada criança e sua família.</li>
                <li><strong>Compromisso:</strong> Dedicamos esforços constantes para oferecer o melhor cuidado.</li>
                <li><strong>Inclusão:</strong> Promovemos igualdade de oportunidades e aceitação.</li>
                <li><strong>Inovação:</strong> Buscamos sempre as melhores práticas e métodos terapêuticos.</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="text-center">&copy; 2024 NeuroDev. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para alternar modo escuro -->
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const icon = document.querySelector('.mode-toggle');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
    </script>
</body>
</html>
