<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeuroDev - Plataforma de Apoio ao Autismo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #5e72e4;
            --accent-color: #4cc9f0;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light-color);
            color: var(--dark-color);
            margin: 0;
            line-height: 1.6;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 25px;
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.25rem;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        .section {
            padding: 80px 0;
            text-align: center;
        }

        .section-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 30px;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }

        .mission-content {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
            color: var(--gray-color);
        }

        .cta {
            background: var(--primary-color);
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .cta h2 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        footer {
            background: var(--dark-color);
            color: #ccc;
            padding: 40px 0 20px;
            text-align: center;
        }

        .footer-logo {
            font-weight: 700;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 20px;
            display: inline-block;
        }

        .copyright {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">NeuroDev</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Sobre Nós</a>
                    </li>
                </ul>
                <a href="login/login.php" class="btn btn-primary ms-lg-3">Entrar</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Juntos no desenvolvimento de habilidades especiais</h1>
            <p>NeuroDev é a plataforma digital que apoia o desenvolvimento de pessoas com autismo através de recursos personalizados e acompanhamento especializado.</p>
            <a href="login/login.php" class="btn btn-primary btn-lg">Acessar Plataforma</a>
        </div>
    </section>

    <!-- Mission Section -->
    <section id="about" class="section">
        <div class="container">
            <h2 class="section-title">Nossa Missão</h2>
            <div class="mission-content">
                <p>O NeuroDev foi criado para fornecer suporte acessível, personalizado e eficiente a indivíduos com Transtorno do Espectro Autista (TEA), familiares e profissionais da área.</p>
                <p>Nossa plataforma foi desenvolvida por uma equipe multidisciplinar especializada em autismo, combinando conhecimento técnico com experiência prática no atendimento a pessoas com TEA.</p>
                <p>Acreditamos que cada indivíduo é único e merece ferramentas adaptadas às suas necessidades específicas para alcançar seu pleno potencial.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Pronto para começar?</h2>
            <p class="mb-4">Acesse agora nossa plataforma e descubra como podemos apoiar o desenvolvimento de habilidades especiais.</p>
            <a href="login/login.php" class="btn btn-light btn-lg">Acessar Plataforma</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <a href="#" class="footer-logo">NeuroDev</a>
            <p>A plataforma digital de apoio ao desenvolvimento de pessoas com autismo.</p>
            <div class="copyright">
                <p>&copy; <?= date('Y') ?> NeuroDev. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>