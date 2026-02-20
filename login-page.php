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
            <h1>
                
                <img src=assets/img/uema-logo.png class="logo">
            
            </h1>

            <?php if (isset($_GET['erro'])): ?>

                <div class="alert alert-danger" role="alert" style="color: red; margin-bottom: 15px;">
                    Usuário ou senha incorretos!
                </div>

            <?php endif; ?>

            <div class="input-box">
                <input placeholder="Usuário" type="email" name="email">

            </div>

            <div class="input-box">
                <input placeholder="Senha" type="password" name="senha">
            </div>

                <button type="submit" class="login">Entrar</button>

            <div class="create">

                <p> <a href="#" >Cadastre-se</a></p>
                <p> <a href="#" >Redefinir senha </a></p>


            </div>

        </form>

    </main>


</body>
</html>