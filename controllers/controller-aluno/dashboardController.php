<?php

// Importa o Model que acabamos de criar
require_once __DIR__ . '/../../model/Aluno.php';

class DashboardAlunoController {

    private $pdo;
    private $id_usuario;

    // Recebe a conexão com o banco E o ID do aluno logado
    public function __construct($conexao, $id_usuario) {
        $this->pdo = $conexao;
        $this->id_usuario = $id_usuario;
    }

    public function index() {
        try {
            // 1. Cria o "funcionário" (Model) passando a conexão
            $alunoModel = new Aluno($this->pdo);

            // 2. Pede os dados ao Model — cada variável guarda um resultado
            $cargaHoraria   = $alunoModel->obterCargaHorariaTotal($this->id_usuario);
            $projetoAtivo   = $alunoModel->obterProjetoAtivo($this->id_usuario);
            $agenda         = $alunoModel->obterAgenda($this->id_usuario);
            $tarefas        = $agenda['tarefas'];
            $eventos        = $agenda['eventos'];

            // 3. Chama a View — ela vai usar essas variáveis para montar o HTML
            require __DIR__ . '/../../views/view-aluno/pagina-inicial.view.php';

        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
        }
    }
}
?>
