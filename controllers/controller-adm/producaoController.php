<?php
require_once __DIR__ . '/../../model/ProducaoModel.php';
require_once __DIR__ . '/../../model/ProjetoModel.php';
require_once __DIR__ . '/../../lib/Logger.php';

class ProducaoController {
    private $pdo;

    public function __construct($conexao) { $this->pdo = $conexao; Logger::setPDO($conexao); }

    public function index() {
        try {
            $pm = new ProducaoModel($this->pdo);
            $prm= new ProjetoModel($this->pdo);
            $estatisticas  = $pm->obterEstatisticas();
            $listaProducoes= $pm->listarTodas();
            $listaProjetos = $prm->listarTodos();
            require __DIR__ . '/../../views/view-adm/documentos.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $pm        = new ProducaoModel($this->pdo);
            $idProjeto = (int)($_POST['id_projeto'] ?? 0);
            $titulo    = trim($_POST['titulo']  ?? '');
            $tipo      = trim($_POST['tipo']    ?? 'outro');
            $caminho   = trim($_POST['caminho'] ?? '');

            if (!$idProjeto || empty($titulo)) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Projeto e título são obrigatórios.']); return;
            }

            if (!empty($_FILES['arquivo']['name']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
                $ext    = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
                $extsOk = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','jpg','jpeg','png'];
                if (!in_array($ext, $extsOk)) { echo json_encode(['sucesso'=>false,'mensagem'=>'Tipo de arquivo não permitido.']); return; }
                if ($_FILES['arquivo']['size'] > 10 * 1024 * 1024) { echo json_encode(['sucesso'=>false,'mensagem'=>'Arquivo muito grande (máx. 10 MB).']); return; }
                $pasta  = __DIR__ . '/../../uploads/documentos/';
                if (!is_dir($pasta)) mkdir($pasta, 0755, true);
                $nome   = time() . '_' . uniqid() . '.' . $ext;
                if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $pasta . $nome)) {
                    echo json_encode(['sucesso'=>false,'mensagem'=>'Falha ao salvar o arquivo.']); return;
                }
                $caminho = 'uploads/documentos/' . $nome;
            }

            $ok = $pm->criar(['id_projeto'=>$idProjeto,'titulo'=>$titulo,'tipo'=>$tipo,'caminho'=>$caminho]);
            if ($ok) Logger::registrar(Logger::DOCUMENTOS, Logger::CRIAR,
                "Documento criado: \"{$titulo}\" no projeto ID:{$idProjeto}",
                ['tipo' => $tipo, 'arquivo' => $caminho ?: 'URL externa']
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Documento cadastrado!':'Erro.','caminho'=>$caminho]);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_producao'] ?? 0);
            $status = $_POST['status'] ?? '';
            if (!$id || !in_array($status, ['ativo','inativo','pendente'])) {
                echo json_encode(['sucesso'=>false,'mensagem'=>'Dados inválidos.']); return;
            }
            $pm = new ProducaoModel($this->pdo);
            $ok = $pm->alterarStatus($id, $status);
            if ($ok) {
                $acao = match($status) { 'ativo'=>Logger::APROVAR, 'inativo'=>Logger::REJEITAR, default=>Logger::EDITAR };
                Logger::registrar(Logger::DOCUMENTOS, $acao,
                    "Documento ID:{$id} " . match($status) { 'ativo'=>'aprovado','inativo'=>'rejeitado',default=>'marcado como pendente' }
                );
            }
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Status atualizado!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }

    public function excluir() {
        try {
            $id = (int)($_POST['id_producao'] ?? 0);
            if (!$id) { echo json_encode(['sucesso'=>false,'mensagem'=>'ID inválido.']); return; }
            $pm = new ProducaoModel($this->pdo);
            $ok = $pm->excluir($id);
            if ($ok) Logger::registrar(Logger::DOCUMENTOS, Logger::EXCLUIR,
                "Documento ID:{$id} excluído (arquivo físico removido)"
            );
            echo json_encode(['sucesso'=>$ok,'mensagem'=>$ok?'Documento excluído!':'Erro.']);
        } catch (Exception $e) { echo json_encode(['sucesso'=>false,'mensagem'=>$e->getMessage()]); }
    }
}
?>
