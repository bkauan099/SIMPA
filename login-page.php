<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UEMA Login</title>
    <link rel="stylesheet" href="assets/css/login-page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

    <main class="container">

        <form id="formLogin">
            <h1 class="uema">
                <img src=assets/img/uema-logo2.png class="logo">
                <img src=assets/img/Proexae.png class="logo">

            </h1>

            <div class="campo">

                <span id="erro-email" class="erro">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span class="msg">Digite o email</span>
                </span>

                <div class="input-box">
                    <input id="email" placeholder="Email" type="email" name="email">
                </div>

            </div>
            <div class="campo">

                <span id="erro-senha" class="erro">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span class="msg">Digite a senha</span>
                </span>

                <div class="input-box senha-box">
                    <input id="senha" placeholder="Senha" type="password" name="senha">

                    <button type="button" id="toggleSenha" class="toggle-senha">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

            </div>

            <button type="submit" class="login">

                <span class="texto">Entrar</span>
                <span class="loader"></span>

            </button>

            <a href="#" class="btn-redefinir">
                Redefinir senha
            </a>

        </form>

    </main>

    <script src="assets/js/verificacao-login.js"></script>
</body>

</html>