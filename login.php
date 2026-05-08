<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIRPV</title>

    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100..900&display=swap" rel="stylesheet">
</head>
<body class="login-page">

    <div class="login-box">
        <img src="img/logo-azul.svg" alt="Logo PGE" class="login-logo">

        <h1>SIRPV</h1>
        <p>Controle de Solicitações de Pagamento - RPV</p>

            <?php if(isset($_GET['erro'])): ?>
                <div class="msg-erro-login">
                    Usuário ou senha inválidos!
                </div>
            <?php endif; ?>

        <form action="valida_login.php" method="POST" class="login-form">
            <input type="text" name="usuario" placeholder="Usuário">
            <input type="password" name="senha" placeholder="Senha">

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>
</body>
</html>