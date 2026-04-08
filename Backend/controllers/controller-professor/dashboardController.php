<?php
// Backend/controllers/controller-professor/dashboardController.php

require_once __DIR__ . '/../../model/model-professor/Projeto.php';

class DashboardController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            // VERIFICAÇÃO DE LOGIN / ID
            // Se existir id_usuario na SESSION, usa ele. Caso contrário, assume o ID 2.
            if (isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario'])) {
                $id_professor = $_SESSION['id_usuario'];
            } else {
                $id_professor = 2; // ID padrão de teste
            }

            $projetoModel = new Projeto($this->pdo);

            // Chamada dos métodos dinâmicos
            $estatisticas = $projetoModel->obterEstatisticasProfessor($id_professor);
            $distribuicaoTipos = $projetoModel->obterDadosGrafico($id_professor);

            // Dados da agenda (ainda simulados, mas preparados para o ID atual)
            $agenda = [
                ['titulo' => 'Reunião de Orientação', 'projeto' => 'Projeto Social', 'hora' => '09:00', 'quando' => 'Hoje', 'cor' => '#8b5cf6'],
                ['titulo' => 'Prazo Entrega Relatório', 'projeto' => 'SIMPA UEMA', 'hora' => '14:00', 'quando' => 'Hoje', 'cor' => '#f59e0b']
            ];

            // Caminho da View
            require __DIR__ . '/../../views/view-professor/pagina-inicial.view.php';

        } catch (Exception $e) {
            echo "Erro no Controller: " . $e->getMessage();
        }
    }
}