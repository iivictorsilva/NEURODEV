<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Notificações</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .notification-item {
            border-bottom: 1px solid #eaeaea;
            padding: 15px 0;
        }
        .notification-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card p-4">
                <h2 class="text-center mb-4">Minhas Notificações</h2>
                <div class="text-center mb-4">
                    <a href="../dashboard/professor/index.php" class="btn btn-primary">Voltar ao Dashboard</a>
                </div>
                <!-- Área onde as notificações serão carregadas -->
                <div id="notificacoes">
                    <div class="alert alert-info text-center" role="alert">
                        Carregando notificações...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    // Função para carregar notificações usando AJAX
    function carregarNotificacoes() {
        fetch('notificacoes.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('notificacoes').innerHTML = data;
            })
            .catch(error => console.log('Erro ao carregar notificações:', error));
    }

    // Carregar notificações assim que a página carregar
    document.addEventListener('DOMContentLoaded', carregarNotificacoes);

    // Atualizar notificações a cada 5 segundos
    setInterval(carregarNotificacoes, 5000);
</script>
</body>
</html>
