<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeuroDev - Plataforma de Apoio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --accent-color: #4cc9f0;
            --dark-color: #212529;
            --light-color: #f5f7fa;
            --border-radius: 15px;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light-color);
            margin: 0;
        }

        .hero {
            background: var(--primary-color);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            margin-top: 10px;
        }

        .section {
            padding: 60px 20px;
            text-align: center;
        }

        .section h2 {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .section p {
            font-size: 1.05rem;
            color: #444;
            max-width: 800px;
            margin: 0 auto 20px;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }

        .feature {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            padding: 30px;
            max-width: 300px;
            text-align: left;
        }

        .feature h4 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn-login {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 30px;
            font-size: 1.1rem;
            border: none;
            border-radius: var(--border-radius);
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 30px;
        }

        .btn-login:hover {
            background-color: var(--accent-color);
        }

        footer {
            background: #212529;
            color: #ccc;
            text-align: center;
            padding: 30px 20px;
            margin-top: 60px;
        }
    </style>
</head>
<body>

    <section class="hero">
        <h1>Bem-vindo ao NeuroDev</h1>
        <p>Plataforma digital de apoio ao desenvolvimento de pessoas com autismo.</p>
        <a href="login.php" class="btn-login">Entrar na Plataforma</a>
    </section>

    <section class="section">
        <h2>Nossa Missão</h2>
        <p>O NeuroDev foi criado para fornecer suporte acessível, personalizado e eficiente a indivíduos com Transtorno do Espectro Autista (TEA), familiares e profissionais da área.</p>
    </section>

    <section class="section">
        <h2>Recursos da Plataforma</h2>
        <div class="features">
            <div class="feature">
                <h4>Cadastro Personalizado</h4>
                <p>Perfis adaptados para diferentes tipos de usuários: pais, educadores, terapeutas e pessoas autistas.</p>
            </div>
            <div class="feature">
                <h4>Biblioteca de Atividades</h4>
                <p>Atividades categorizadas por nível de habilidade e objetivos de aprendizagem.</p>
            </div>
            <div class="feature">
                <h4>Monitoramento de Progresso</h4>
                <p>Acompanhe o desenvolvimento por meio de gráficos, relatórios e sugestões baseadas em IA.</p>
            </div>
            <div class="feature">
                <h4>Interação Segura</h4>
                <p>Ambiente seguro com fóruns e comunicação supervisionada para troca de experiências.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <h2>Comece Agora</h2>
        <p>Cadastre-se e tenha acesso a uma jornada personalizada de desenvolvimento e apoio.</p>
        <a href="login/login.php" class="btn-login">Fazer Login</a>
    </section>

    <footer>
        <p>&copy; <?= date('Y') ?> NeuroDev. Todos os direitos reservados.</p>
    </footer>

</body>
</html>
