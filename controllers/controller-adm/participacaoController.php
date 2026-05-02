<?php
// controllers/controller-adm/participacaoController.php

require_once __DIR__ . '/../../model/ParticipacaoModel.php';
require_once __DIR__ . '/../../model/UsuarioModel.php';
require_once __DIR__ . '/../../model/ProjetoModel.php';

class ParticipacaoController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $participacaoModel = new ParticipacaoModel($this->pdo);
            $usuarioModel      = new UsuarioModel($this->pdo);
            $projetoModel      = new ProjetoModel($this->pdo);

            $estatisticas      = $participacaoModel->obterEstatisticas();
            $listaParticipacoes= $participacaoModel->listarTodas();
            $listaUsuariosAtivos = $usuarioModel->listarAtivos();
            $listaProjetos     = $projetoModel->listarTodos();

            require __DIR__ . '/../../views/view-adm/participacoes.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro ao carregar participações: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $participacaoModel = new ParticipacaoModel($this->pdo);
            $dados = [
                'id_projeto'    => (int)($_POST['id_projeto'] ?? 0),
                'id_usuario'    => (int)($_POST['id_usuario'] ?? 0),
                'funcao'        => $_POST['funcao']        ?? '',
                'carga_horaria' => $_POST['carga_horaria'] ?? null,
                'data_entrada'  => $_POST['data_entrada']  ?? '',
                'data_saida'    => $_POST['data_saida']    ?? null,
                'status'        => $_POST['status']        ?? 'ativo',
            ];

            if (!$dados['id_projeto'] || !$dados['id_usuario'] || empty($dados['funcao'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Projeto, usuário e função são obrigatórios.']); return;
            }

            $ok = $participacaoModel->criar($dados);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Participação criada com sucesso!' : 'Erro ao criar participação.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function atualizar() {
        try {
            $id = (int)($_POST['id_participacao'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }

            $participacaoModel = new ParticipacaoModel($this->pdo);
            $dados = [
                'id_projeto'    => (int)($_POST['id_projeto'] ?? 0),
                'id_usuario'    => (int)($_POST['id_usuario'] ?? 0),
                'funcao'        => $_POST['funcao']        ?? '',
                'carga_horaria' => $_POST['carga_horaria'] ?? null,
                'data_entrada'  => $_POST['data_entrada']  ?? '',
                'data_saida'    => $_POST['data_saida']    ?? null,
                'status'        => $_POST['status']        ?? 'ativo',
            ];

            $ok = $participacaoModel->atualizar($id, $dados);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Participação atualizada!' : 'Erro ao atualizar.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_participacao'] ?? 0);
            $status = $_POST['status'] ?? '';
            $statusValidos = ['ativo','inativo'];

            if (!$id || !in_array($status, $statusValidos)) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Dados inválidos.']); return;
            }

            $participacaoModel = new ParticipacaoModel($this->pdo);
            $ok = $participacaoModel->alterarStatus($id, $status);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Status alterado!' : 'Erro.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function excluir() {
        try {
            $id = (int)($_POST['id_participacao'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }

            $participacaoModel = new ParticipacaoModel($this->pdo);
            $ok = $participacaoModel->excluir($id);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Participação removida!' : 'Erro ao remover.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function listarPorProjeto() {
        try {
            $id_projeto = (int)($_GET['id_projeto'] ?? 0);
            if (!$id_projeto) { echo json_encode(['erro' => 'ID inválido.']); return; }
            $participacaoModel = new ParticipacaoModel($this->pdo);
            $dados = $participacaoModel->listarPorProjeto($id_projeto);
            echo json_encode(['sucesso' => true, 'participantes' => $dados]);
        } catch (Exception $e) {
            echo json_encode(['erro' => $e->getMessage()]);
        }
    }

}
?>
