<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPA - Login UEMA</title>
    <link rel="stylesheet" href="assets/css/login-page.css">
</head>
<body>
    <main class="container">
        <form action="processa_login.php" method="POST">
            <div class="uema">
                <img src="assets/img/uema-logo.png" alt="UEMA" class="logo">
                <img src="assets/img/Proexae.png" alt="Proexae" class="logo">
            </div>

            <?php if (isset($_GET['erro'])): ?>
                <div style="color:red; margin-bottom:15px; font-size:0.9rem;">
                    Usuário ou senha incorretos!
                </div>
            <?php endif; ?>

            <div class="input-box">
                <input placeholder="E-mail" type="email" name="email" required>
            </div>

            <div class="input-box">
                <input placeholder="Senha" type="password" name="senha" required>
            </div>

            <button type="submit" class="login">Entrar</button>

            <div class="create">
                <a href="#">Redefinir senha</a>
            </div>
        </form>
    </main>
</body>
</html>
