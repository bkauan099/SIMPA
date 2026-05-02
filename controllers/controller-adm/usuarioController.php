<?php
// controllers/controller-adm/usuarioController.php

require_once __DIR__ . '/../../model/UsuarioModel.php';

class UsuarioController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $usuarioModel  = new UsuarioModel($this->pdo);
            $estatisticas  = $usuarioModel->obterEstatisticas();
            $listaUsuarios = $usuarioModel->listarTodos();

            require __DIR__ . '/../../views/view-adm/usuarios.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro ao carregar usuários: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $usuarioModel = new UsuarioModel($this->pdo);
            $dados = [
                'nome'           => trim($_POST['nome']      ?? ''),
                'email'          => trim($_POST['email']     ?? ''),
                'senha'          => $_POST['senha']          ?? '',
                'matricula'      => trim($_POST['matricula'] ?? '') ?: null,
                'perfil'         => $_POST['perfil']         ?? 'aluno',
                'curso'          => trim($_POST['curso']     ?? '') ?: null,
                'status'         => $_POST['status']         ?? 'ativo',
                'cadastrado_por' => $_POST['cadastrado_por'] ?? null,
            ];
            if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Nome, e-mail e senha são obrigatórios.']); return;
            }
            $ok = $usuarioModel->criar($dados);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Usuário criado com sucesso!' : 'Erro ao criar usuário.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function atualizar() {
        try {
            $id = (int)($_POST['id_usuario'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }
            $usuarioModel = new UsuarioModel($this->pdo);
            $dados = [
                'nome'      => trim($_POST['nome']      ?? ''),
                'email'     => trim($_POST['email']     ?? ''),
                'matricula' => trim($_POST['matricula'] ?? '') ?: null,
                'perfil'    => $_POST['perfil']         ?? 'aluno',
                'curso'     => trim($_POST['curso']     ?? '') ?: null,
                'status'    => $_POST['status']         ?? 'ativo',
            ];
            $ok = $usuarioModel->atualizar($id, $dados);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Usuário atualizado!' : 'Erro ao atualizar.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_usuario'] ?? 0);
            $status = $_POST['status'] ?? '';
            if (!$id || !in_array($status, ['ativo','inativo'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Dados inválidos.']); return;
            }
            $usuarioModel = new UsuarioModel($this->pdo);
            $ok = $usuarioModel->alterarStatus($id, $status);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Status alterado!' : 'Erro.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function redefinirSenha() {
        try {
            $id    = (int)($_POST['id_usuario'] ?? 0);
            $senha = $_POST['nova_senha'] ?? '';
            if (!$id || strlen($senha) < 6) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Senha deve ter ao menos 6 caracteres.']); return;
            }
            $usuarioModel = new UsuarioModel($this->pdo);
            $ok = $usuarioModel->redefinirSenha($id, $senha);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Senha redefinida!' : 'Erro.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function buscarPorId() {
        try {
            $id = (int)($_GET['id'] ?? 0);
            $usuarioModel = new UsuarioModel($this->pdo);
            $usuario = $usuarioModel->buscarPorId($id);
            echo json_encode($usuario ?: ['erro' => 'Usuário não encontrado.']);
        } catch (Exception $e) {
            echo json_encode(['erro' => $e->getMessage()]);
        }
    }
}
?>
