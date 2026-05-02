<?php
// controllers/controller-adm/acessoController.php

require_once __DIR__ . '/../../model/AcessoModel.php';

class AcessoController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $dias = isset($_GET['dias']) ? (int)$_GET['dias'] : 30;
            if (!in_array($dias, [7, 30, 90])) $dias = 30;

            $acessoModel  = new AcessoModel($this->pdo);
            $estatisticas = $acessoModel->obterEstatisticas($dias);
            $listaAcessos = $acessoModel->listarRecentes($dias, 200);
            $graficoDias  = $acessoModel->acessosPorDia($dias);
            $diasAtivos   = $dias;

            require __DIR__ . '/../../views/view-adm/visitas.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro ao carregar acessos: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>
