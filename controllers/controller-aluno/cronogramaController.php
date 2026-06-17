<?php
require_once __DIR__ . '/../../model/Aluno.php';

class CronogramaAlunoController {
    private $pdo;
    private $id_usuario;

    public function __construct($conexao, $id_usuario) {
        $this->pdo        = $conexao;
        $this->id_usuario = $id_usuario;
    }

    public function index() {
        try {
            $alunoModel   = new Aluno($this->pdo);
            $estatisticas = $alunoModel->obterEstatisticasCronograma($this->id_usuario);
            $itens        = $alunoModel->listarCronograma($this->id_usuario);

            require __DIR__ . '/../../views/view-aluno/cronograma.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>
