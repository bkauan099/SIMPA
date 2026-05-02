<?php
// controllers/controller-adm/producaoController.php

require_once __DIR__ . '/../../model/ProducaoModel.php';
require_once __DIR__ . '/../../model/ProjetoModel.php';

class ProducaoController {
    private $pdo;

    public function __construct($conexao) {
        $this->pdo = $conexao;
    }

    public function index() {
        try {
            $producaoModel = new ProducaoModel($this->pdo);
            $projetoModel  = new ProjetoModel($this->pdo);
            $estatisticas  = $producaoModel->obterEstatisticas();
            $listaProducoes= $producaoModel->listarTodas();
            $listaProjetos = $projetoModel->listarTodos();
            require __DIR__ . '/../../views/view-adm/documentos.view.php';
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erro ao carregar documentos: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function criar() {
        try {
            $producaoModel = new ProducaoModel($this->pdo);

            $idProjeto = (int)($_POST['id_projeto'] ?? 0);
            $titulo    = trim($_POST['titulo']  ?? '');
            $tipo      = trim($_POST['tipo']    ?? 'outro');
            $caminho   = trim($_POST['caminho'] ?? '');

            if (!$idProjeto || empty($titulo)) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Projeto e título são obrigatórios.']);
                return;
            }

            // ── UPLOAD DE ARQUIVO ─────────────────────────────────────
            if (!empty($_FILES['arquivo']['name']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
                $ext     = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
                $extsOk  = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','jpg','jpeg','png'];

                if (!in_array($ext, $extsOk)) {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo de arquivo não permitido.']);
                    return;
                }

                $tamanhoMax = 10 * 1024 * 1024; // 10 MB
                if ($_FILES['arquivo']['size'] > $tamanhoMax) {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Arquivo muito grande (máx. 10 MB).']);
                    return;
                }

                $pasta = __DIR__ . '/../../uploads/documentos/';
                if (!is_dir($pasta)) mkdir($pasta, 0755, true);

                $nomeArquivo = time() . '_' . uniqid() . '.' . $ext;
                $destino     = $pasta . $nomeArquivo;

                if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $destino)) {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha ao salvar o arquivo no servidor.']);
                    return;
                }
                $caminho = 'uploads/documentos/' . $nomeArquivo;
            }

            $ok = $producaoModel->criar([
                'id_projeto' => $idProjeto,
                'titulo'     => $titulo,
                'tipo'       => $tipo,
                'caminho'    => $caminho,
            ]);

            echo json_encode([
                'sucesso'  => $ok,
                'mensagem' => $ok ? 'Documento cadastrado com sucesso!' : 'Erro ao cadastrar documento.',
                'caminho'  => $caminho,
            ]);

        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function alterarStatus() {
        try {
            $id     = (int)($_POST['id_producao'] ?? 0);
            $status = $_POST['status'] ?? '';
            if (!$id || !in_array($status, ['ativo','inativo','pendente'])) {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
                return;
            }
            $producaoModel = new ProducaoModel($this->pdo);
            $ok = $producaoModel->alterarStatus($id, $status);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Status atualizado!' : 'Erro ao atualizar.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public function excluir() {
        try {
            $id = (int)($_POST['id_producao'] ?? 0);
            if (!$id) { echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']); return; }
            $producaoModel = new ProducaoModel($this->pdo);
            $ok = $producaoModel->excluir($id);
            echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Documento excluído!' : 'Erro ao excluir.']);
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }
}
?>
