<?php
/**
 * lib/Guard.php
 * Proteção de rotas do SIMPA.
 * Impede acesso direto a qualquer página sem sessão válida.
 */
class Guard {

    /**
     * Exige sessão ativa. Se não tiver, redireciona para o login.
     * @param string $redirecionarPara  Caminho relativo ao login
     */
    public static function autenticar(string $redirecionarPara = '../login-page.php'): void {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        if (empty($_SESSION['id_usuario'])) {
            // Requisição AJAX? Retorna JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erro' => 'Sessão expirada. Faça login novamente.', 'redirect' => $redirecionarPara]);
                exit;
            }
            header("Location: $redirecionarPara");
            exit;
        }
    }

    /**
     * Exige perfil de administrador.
     */
    public static function apenasAdmin(string $redirecionarPara = '../login-page.php'): void {
        self::autenticar($redirecionarPara);

        $perfil = strtolower($_SESSION['perfil'] ?? '');
        if (!str_contains($perfil, 'admin')) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
                str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['erro' => 'Acesso restrito a administradores.']);
                exit;
            }
            header("Location: $redirecionarPara");
            exit;
        }
    }

    /**
     * Exige perfil de professor ou admin.
     */
    public static function apenasProfesor(string $redirecionarPara = '../login-page.php'): void {
        self::autenticar($redirecionarPara);

        $perfil = strtolower($_SESSION['perfil'] ?? '');
        if (!str_contains($perfil, 'admin') &&
            !str_contains($perfil, 'professor') &&
            !str_contains($perfil, 'orientador')) {
            header("Location: $redirecionarPara");
            exit;
        }
    }
}
?>
