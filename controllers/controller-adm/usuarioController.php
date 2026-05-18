<?php
require_once __DIR__ . '/../../model/UsuarioModel.php';
require_once __DIR__ . '/../../lib/Logger.php';

class UsuarioController {
    private $pdo;

    public function __construct($conexao) { $this->pdo = $conexao; Logger::setPDO($conexao); }

    public function index() {
        try {
            $m = new UsuarioModel($this->pdo);
            $estatisticas  = $m->obterEstatisticas();
            $listaUsuarios = $m->listarTodos();
            require __DIR__ . '/../../views/view-adm/usuarios.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $m = new UsuarioModel($this->pdo);
            $dados = [
                'nome'           => trim($_POST['nome']      ?? ''),
                'email'          => trim($_POST['email']     ?? ''),
                'senha'          => $_POST['senha']          ?? '',
                'matricula'      => trim($_POST['matricula'] ?? '') ?: null,
                'perfil'         => $_POST['perfil']         ?? 'aluno',
                'curso'          => trim($_POST['curso']     ?? '') ?: null,
                'status'         => $_POST['status']         ?? 'ativo',
                'cadastrado_por' => $_SESSION['id_usuario']  ?? null,
            ];
            if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Nome, e-mail e senha são obrigatórios.']); return;
            }
            $ok = $m->criar($dados);
            if ($ok) Logger::registrar(Logger::USUARIOS, Logger::CRIAR,
                "Novo usuário criado: \"{$dados['nome']}\" ({$dados['email']})",
                ['perfil' => $dados['perfil'], 'status' => $dados['status']]
            );
            echo json_encode(['sucesso'=>$ok, 'mensagem'=>$ok?'Usuário criado com sucesso!':'Erro ao criar.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function atualizar() {
        try {
            $id = (int)($_POST['id_usuario'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }
            $m = new UsuarioModel($this->pdo);
            $dados = [
                'nome'      => trim($_POST['nome']      ?? ''),
                'email'     => trim($_POST['email']     ?? ''),
                'matricula' => trim($_POST['matricula'] ?? '') ?: null,
                'perfil'    => $_POST['perfil']         ?? 'aluno',
                'curso'     => trim($_POST['curso']     ?? '') ?: null,
                'status'    => $_POST['status']         ?? 'ativo',
            ];
            $ok = $m->atualizar($id, $dados);
            if ($ok) Logger::registrar(Logger::USUARIOS, Logger::EDITAR,
                "Usuário editado: \"{$dados['nome']}\" (ID:{$id})",
                ['email' => $dados['email'], 'perfil' => $dados['perfil']]
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Usuário atualizado!':'Erro ao atualizar.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_usuario'] ?? 0);
            $status = $_POST['status'] ?? '';
            if (!$id || !in_array($status, ['ativo','inativo'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Dados inválidos.']); return;
            }
            $m  = new UsuarioModel($this->pdo);
            $ok = $m->alterarStatus($id, $status);
            if ($ok) {
                $acao = $status === 'ativo' ? Logger::ATIVAR : Logger::DESATIVAR;
                Logger::registrar(Logger::USUARIOS, $acao,
                    "Status do usuário ID:{$id} alterado para \"{$status}\""
                );
            }
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Status alterado!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function redefinirSenha() {
        try {
            $id    = (int)($_POST['id_usuario'] ?? 0);
            $senha = $_POST['nova_senha'] ?? '';
            if (!$id || strlen($senha) < 6) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Senha deve ter ao menos 6 caracteres.']); return;
            }
            $m  = new UsuarioModel($this->pdo);
            $ok = $m->redefinirSenha($id, $senha);
            if ($ok) Logger::registrar(Logger::USUARIOS, Logger::ALTERAR_SENHA,
                "Senha redefinida para o usuário ID:{$id} pelo administrador"
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Senha redefinida!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function buscarPorId() {
        try {
            $id = (int)($_GET['id'] ?? 0);
            $m  = new UsuarioModel($this->pdo);
            echo json_encode($m->buscarPorId($id) ?: ['erro'=>'Não encontrado.']);
        } catch (Exception $e) { echo json_encode(['erro'=>$e->getMessage()]); }
    }
}
?>
