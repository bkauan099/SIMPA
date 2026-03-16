<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UEMA Login</title>
    <link rel="stylesheet" href="assets/css/login-page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <main class="container">

        <form action="processa_login.php" method="POST">
            <h1 class="uema">
                <img src=assets/img/uema-logo.png class="logo">
                <img src=assets/img/Proexae.png class="logo">

            </h1>

            <?php if (isset($_GET['erro'])): ?>

                <div class="alert alert-danger" role="alert" style="color: red; margin-bottom: 15px;">
                    Usuário ou senha incorretos!
                </div>

            <?php endif; ?>

            <div class="campo">

                <span id="erro-email" class="erro">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span class="msg">Digite o email</span>
                </span>

                <div class="input-box">
                    <input id="email" placeholder="Usuário" type="email" name="email">
                </div>

            </div>
            <div class="campo">

                <span id="erro-senha" class="erro">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span class="msg">Digite a senha</span>
                </span>

                <div class="input-box">
                    <input id="senha" placeholder="Senha" type="password" name="senha">
                </div>

            </div>
            <button type="submit" class="login" href="aluno-page.php">Entrar</a></button>

            <a href="#" class="btn-redefinir">
                Redefinir senha
                </a>

        </form>

    </main>
    <script src="assets/js/verificacao-login.js"></script>

</body>

</html>