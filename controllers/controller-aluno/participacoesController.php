<?php

require_once __DIR__ . '/../../model/Aluno.php';

class ParticipacaoAlunoController {

    private $pdo;
    private $id_usuario;

    public function __construct($conexao, $id_usuario) {
        $this->pdo        = $conexao;
        $this->id_usuario = $id_usuario;
    }

    public function index() {
        try {
            $alunoModel    = new Aluno($this->pdo);
            $estatisticas  = $alunoModel->obterEstatisticasProjetos($this->id_usuario);
            $participacoes = $alunoModel->obterParticipacoes($this->id_usuario);

            require __DIR__ . '/../../views/view-aluno/participacoes.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
        }
    }
}
?>
