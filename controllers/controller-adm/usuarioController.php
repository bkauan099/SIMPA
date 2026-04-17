<?php
// controllers/UsuarioController.php

require_once __DIR__ . '/../../model/Usuario.php';

class UsuarioController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $usuarioModel = new Usuario($this->pdo);

            // Gera as variáveis que a View vai usar
            $estatisticas = $usuarioModel->obterEstatisticas();
            $listaUsuarios = $usuarioModel->listarUsuarios();

            // Chama a View (usando require simples para evitar o erro de cache de arquivo)
            require __DIR__ . '/../../views/view-adm/usuarios.view.php';

        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro ao carregar usuários: " . $e->getMessage() . "</div>";
        }
    }
}
?>