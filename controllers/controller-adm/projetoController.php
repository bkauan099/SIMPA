<?php
// controllers/controller-adm/projetoController.php

require_once __DIR__ . '/../../model/ProjetoModel.php';

class ProjetoController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $projetoModel = new ProjetoModel($this->pdo);
            $estatisticas = $projetoModel->obterEstatisticas();
            $listaProjetos = $projetoModel->listarTodos();
            $tiposProjeto  = $projetoModel->listarTipos();

            require __DIR__ . '/../../views/view-adm/projetos.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro ao carregar projetos: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $projetoModel = new ProjetoModel($this->pdo);
            $campos = ['id_tipo','titulo','area','descricao','data_inicio','data_fim'];
            $dados = [];
            foreach ($campos as $c) {
                $dados[$c] = $_POST[$c] ?? '';
            }
            $dados['status'] = $_POST['status'] ?? 'pendente';

            if (empty($dados['titulo']) || empty($dados['data_inicio'])) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Título e data de início são obrigatórios.']);
                return;
            }

            $ok = $projetoModel->criar($dados);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Projeto criado com sucesso!' : 'Erro ao criar projeto.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function atualizar() {
        try {
            $id = (int)($_POST['id_projeto'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }

            $projetoModel = new ProjetoModel($this->pdo);
            $dados = [
                'id_tipo'    => $_POST['id_tipo']    ?? '',
                'titulo'     => $_POST['titulo']     ?? '',
                'area'       => $_POST['area']       ?? '',
                'descricao'  => $_POST['descricao']  ?? '',
                'data_inicio'=> $_POST['data_inicio']?? '',
                'data_fim'   => $_POST['data_fim']   ?? null,
                'status'     => $_POST['status']     ?? 'pendente',
            ];

            $ok = $projetoModel->atualizar($id, $dados);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Projeto atualizado!' : 'Erro ao atualizar.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_projeto'] ?? 0);
            $status = $_POST['status'] ?? '';
            $statusValidos = ['ativo','pendente','concluido','inativo'];

            if (!$id || !in_array($status, $statusValidos)) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Dados inválidos.']); return;
            }

            $projetoModel = new ProjetoModel($this->pdo);
            $ok = $projetoModel->alterarStatus($id, $status);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Status alterado!' : 'Erro ao alterar status.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function buscarPorId() {
        try {
            $id = (int)($_GET['id'] ?? 0);
            $projetoModel = new ProjetoModel($this->pdo);
            $projeto = $projetoModel->buscarPorId($id);
            echo json_encode($projeto ?: ['erro' => 'Projeto não encontrado.']);
        } catch (Exception $e) {
            echo json_encode(['erro' => $e->getMessage()]);
        }
    }
}
?>
