<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recupere sua senha!">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="../src/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="first-column">
            <h2 class="title title-primary">Esqueceu sua senha?</h2>
            <p class="description description-primary">Digite seu e-mail para receber o link de redefinição.</p>
            <form action="enviar_link.php" method="POST" class="form">
                <label class="label-input" for="email">
                    <i class="far fa-envelope"></i>
                    <input id="email" type="email" name="email" placeholder="E-mail" required>
                </label>
                <button type="submit" class="btn btn-second">Enviar Link</button>
            </form>
        </div>
    </div>
</body>
</html>
