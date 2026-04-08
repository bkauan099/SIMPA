<?php
// Backend/controllers/controller-adm/dashboardController.php

require_once __DIR__ . '/../../model/model-adm/Projeto.php';

class DashboardController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $projetoModel = new Projeto($this->pdo);

            // Aqui as variáveis nascem
            $estatisticas = $projetoModel->obterEstatisticas();
            $projetosAtivos = $projetoModel->listarProjetosAtivos();

            // Usamos REQUIRE em vez de REQUIRE_ONCE
            require __DIR__ . '/../../views/view-adm/pagina-inicial.view.php';

        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
        }
    }
}
?>