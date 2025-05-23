<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado - 403</title>
    <!-- Link do Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            padding: 50px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.25rem;
            color: #6c757d;
            margin-bottom: 30px;
        }

        .btn-custom {
            background-color: #007bff;
            color: #fff;
            font-size: 1.1rem;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>403 - Acesso Negado</h1>
        <p>Você não tem permissão para acessar esta página.</p>
        <a href="http://localhost/AutismoProjeto2/login/login.php" class="btn-custom">Voltar ao login</a>
    </div>

    <!-- Script do Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
