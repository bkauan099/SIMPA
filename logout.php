<?php
session_start();
session_unset();
session_destroy();

// Expira o cookie de sessão no navegador também (boa prática recomendada
// pelo manual do PHP, além de destruir os dados no servidor)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

header('Location: login-page.php');
exit;
