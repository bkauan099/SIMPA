<?php
require_once __DIR__ . '/../../model/ParticipacaoModel.php';
require_once __DIR__ . '/../../model/UsuarioModel.php';
require_once __DIR__ . '/../../model/ProjetoModel.php';
require_once __DIR__ . '/../../lib/Logger.php';

class ParticipacaoController {
    private $pdo;

    public function __construct($conexao) { $this->pdo = $conexao; Logger::setPDO($conexao); }

    public function index() {
        try {
            $pm = new ParticipacaoModel($this->pdo);
            $um = new UsuarioModel($this->pdo);
            $prm= new ProjetoModel($this->pdo);
            $estatisticas        = $pm->obterEstatisticas();
            $listaParticipacoes  = $pm->listarTodas();
            $listaProjetosEquipe = $pm->listarProjetosComEquipe();
            $listaUsuariosAtivos = $um->listarAtivos();
            $listaProjetos       = $prm->listarTodos();
            require __DIR__ . '/../../views/view-adm/participacoes.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $pm = new ParticipacaoModel($this->pdo);
            $dados = [
                'id_projeto'    => (int)($_POST['id_projeto']   ?? 0),
                'id_usuario'    => (int)($_POST['id_usuario']   ?? 0),
                'funcao'        => trim($_POST['funcao']        ?? ''),
                'carga_horaria' => trim($_POST['carga_horaria'] ?? ''),
                'data_entrada'  => $_POST['data_entrada']       ?? '',
                'data_saida'    => $_POST['data_saida']         ?? null,
                'status'        => $_POST['status']             ?? 'ativo',
            ];
            if (!$dados['id_projeto'] || !$dados['id_usuario'] || empty($dados['funcao'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Projeto, usuário e função são obrigatórios.']); return;
            }
            $resultado = $pm->criar($dados);
            if ($resultado['ok']) Logger::registrar(Logger::PARTICIPACOES, Logger::VINCULAR,
                "Usuário ID:{$dados['id_usuario']} vinculado ao projeto ID:{$dados['id_projeto']}",
                ['funcao' => $dados['funcao'], 'carga_horaria' => $dados['carga_horaria'].'h']
            );
            echo json_encode(['sucesso'=>$resultado['ok'],'mensagem'=>$resultado['msg']]);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function atualizar() {
        try {
            $id = (int)($_POST['id_participacao'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }
            $pm = new ParticipacaoModel($this->pdo);
            $dados = [
                'funcao'        => trim($_POST['funcao']        ?? ''),
                'carga_horaria' => trim($_POST['carga_horaria'] ?? ''),
                'data_entrada'  => $_POST['data_entrada']       ?? '',
                'data_saida'    => $_POST['data_saida']         ?? null,
                'status'        => $_POST['status']             ?? 'ativo',
            ];
            $ok = $pm->atualizar($id, $dados);
            if ($ok) Logger::registrar(Logger::PARTICIPACOES, Logger::EDITAR,
                "Participação ID:{$id} editada — função: \"{$dados['funcao']}\""
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Participação atualizada!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_participacao'] ?? 0);
            $status = $_POST['status'] ?? '';
            if (!$id || !in_array($status, ['ativo','inativo'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Dados inválidos.']); return;
            }
            $pm = new ParticipacaoModel($this->pdo);
            $ok = $pm->alterarStatus($id, $status);
            if ($ok) {
                $acao = $status === 'ativo' ? Logger::ATIVAR : Logger::DESATIVAR;
                Logger::registrar(Logger::PARTICIPACOES, $acao,
                    "Participação ID:{$id} " . ($status === 'ativo' ? 'reativada' : 'encerrada')
                );
            }
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Status alterado!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function excluir() {
        try {
            $id = (int)($_POST['id_participacao'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }
            $pm = new ParticipacaoModel($this->pdo);
            $ok = $pm->excluir($id);
            if ($ok) Logger::registrar(Logger::PARTICIPACOES, Logger::EXCLUIR,
                "Participação ID:{$id} removida permanentemente"
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Vínculo removido!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function listarPorProjeto() {
        try {
            $id = (int)($_GET['id_projeto'] ?? 0);
            if (!$id) { echo json_encode(['erro'=>'ID inválido.']); return; }
            $pm = new ParticipacaoModel($this->pdo);
            echo json_encode(['sucesso'=>true,'participantes'=>$pm->listarPorProjeto($id)]);
        } catch (Exception $e) {
            echo json_encode(['sucesso'=>false,'erro'=>$e->getMessage(),'participantes'=>[]]);
        }
    }
}
?>
