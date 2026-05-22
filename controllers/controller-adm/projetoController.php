<?php
require_once __DIR__ . '/../../model/ProjetoModel.php';
require_once __DIR__ . '/../../lib/Logger.php';

class ProjetoController {
    private $pdo;

    public function __construct($conexao) { $this->pdo = $conexao; Logger::setPDO($conexao); }

    public function index() {
        try {
            $m = new ProjetoModel($this->pdo);
            $estatisticas  = $m->obterEstatisticas();
            $listaProjetos = $m->listarTodos();
            $tiposProjeto  = $m->listarTipos();
            require __DIR__ . '/../../views/view-adm/projetos.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $m = new ProjetoModel($this->pdo);
            $dados = [];
            foreach (['id_tipo','titulo','area','descricao','data_inicio','data_fim'] as $c) {
                $dados[$c] = $_POST[$c] ?? '';
            }
            $dados['status'] = $_POST['status'] ?? 'pendente';
            if (empty($dados['titulo']) || empty($dados['data_inicio'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Título e data de início são obrigatórios.']); return;
            }
            $ok = $m->criar($dados);
            if ($ok) Logger::registrar(Logger::PROJETOS, Logger::CRIAR,
                "Novo projeto criado: \"{$dados['titulo']}\"",
                ['area' => $dados['area'], 'status' => $dados['status']]
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Projeto criado!':'Erro ao criar.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function atualizar() {
        try {
            $id = (int)($_POST['id_projeto'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }
            $m = new ProjetoModel($this->pdo);
            $dados = [
                'id_tipo'     => $_POST['id_tipo']     ?? '',
                'titulo'      => $_POST['titulo']      ?? '',
                'area'        => $_POST['area']        ?? '',
                'descricao'   => $_POST['descricao']   ?? '',
                'data_inicio' => $_POST['data_inicio'] ?? '',
                'data_fim'    => $_POST['data_fim']    ?? null,
                'status'      => $_POST['status']      ?? 'pendente',
            ];
            $ok = $m->atualizar($id, $dados);
            if ($ok) Logger::registrar(Logger::PROJETOS, Logger::EDITAR,
                "Projeto editado: \"{$dados['titulo']}\" (ID:{$id})",
                ['status' => $dados['status']]
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Projeto atualizado!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_projeto'] ?? 0);
            $status = $_POST['status'] ?? '';
            if (!$id || !in_array($status, ['ativo','pendente','concluido','inativo'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Dados inválidos.']); return;
            }
            $m  = new ProjetoModel($this->pdo);
            $ok = $m->alterarStatus($id, $status);
            if ($ok) {
                $acao = match($status) {
                    'ativo'     => Logger::ATIVAR,
                    'inativo'   => Logger::DESATIVAR,
                    'concluido' => Logger::APROVAR,
                    default     => Logger::EDITAR,
                };
                Logger::registrar(Logger::PROJETOS, $acao,
                    "Status do projeto ID:{$id} alterado para \"{$status}\""
                );
            }
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Status alterado!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function buscarPorId() {
        try {
            $id = (int)($_GET['id'] ?? 0);
            $m  = new ProjetoModel($this->pdo);
            echo json_encode($m->buscarPorId($id) ?: ['erro'=>'Não encontrado.']);
        } catch (Exception $e) { echo json_encode(['erro'=>$e->getMessage()]); }
    }
}
?>
