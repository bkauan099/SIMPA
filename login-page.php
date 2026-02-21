<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UEMA Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">

</head>
<body>
    
    <main class="container">
    
        <form action="processa_login.php" method="POST">
           <h1 class="uema">
                <img src=assets/img/uema-logo.png  class="logo">
                <img src=assets/img/Proexae.png  class="logo">
                
           </h1>

            <?php if (isset($_GET['erro'])): ?>

                <div class="alert alert-danger" role="alert" style="color: red; margin-bottom: 15px;">
                    Usuário ou senha incorretos!
                </div>

            <?php endif; ?>

            <div class="input-box">
                <input id="email" placeholder="Usuário" type="email" name="email">
                <span id="erro-email" class="erro">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#e74c3c" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm.75 14.25h-1.5v-1.5h1.5v1.5zm0-3h-1.5v-6h1.5v6z"/>
                </svg>
                Preencha o e-mail
            </span>
            </div>

            <div class="input-box">
                <input id="senha" placeholder="Senha" type="password" name="senha">
                <span id="erro-senha" class="erro">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#e74c3c" d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm.75 14.25h-1.5v-1.5h1.5v1.5zm0-3h-1.5v-6h1.5v6z"/>
                </svg>
                Preencha a senha
            </span>
            </div>

                <button type="submit" class="login">Entrar</button>

            <div class="create">



                <p> <a href="#" >Redefinir senha </a><p>


            </div>

        </form>

    </main>
    <script src="assets/js/verificacaologin.js"></script>
</body>
</html>
