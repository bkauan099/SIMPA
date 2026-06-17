<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
require_once __DIR__ . '/../conexao/conexao.php';

header('Content-Type: application/json');

if (!$id_usuario) {
    echo json_encode(['ok' => false, 'erro' => 'Sessão expirada. Recarregue a página.']);
    exit;
}

$id_tarefa = $_POST['id']    ?? '';
$titulo    = trim($_POST['titulo'] ?? '');

if (empty($id_tarefa) || empty($titulo)) {
    echo json_encode(['ok' => false, 'erro' => 'Dados insuficientes.']); exit;
}

// Matricula do aluno
$stmt = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$stmt->execute([':id' => $id_usuario]);
$matricula = $stmt->fetchColumn();
if (!$matricula) { echo json_encode(['ok' => false, 'erro' => 'Matrícula não encontrada.']); exit; }

// Busca id_projeto direto da agenda_items (fonte confiável)
$id_projeto = null;
$stmt = $pdo->prepare("SELECT id_projeto FROM agenda_items WHERE id = :id AND id_usuario = :uid");
$stmt->execute([':id' => $id_tarefa, ':uid' => $id_usuario]);
$id_projeto = $stmt->fetchColumn() ?: null;

// Fallback: id_projeto enviado pelo front-end
if (!$id_projeto) {
    $id_projeto_post = (int)($_POST['id_projeto'] ?? 0);
    if ($id_projeto_post > 0) {
        $stmt = $pdo->prepare("SELECT id_projeto FROM participacao WHERE id_usuario = :id AND id_projeto = :proj AND status = 'ativo'");
        $stmt->execute([':id' => $id_usuario, ':proj' => $id_projeto_post]);
        $id_projeto = $stmt->fetchColumn() ?: null;
    }
}

if (!$id_projeto) { echo json_encode(['ok' => false, 'erro' => 'Nenhum projeto ativo encontrado.']); exit; }

// Validação do arquivo
if (empty($_FILES['arquivo']['name']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'erro' => 'Nenhum arquivo enviado.']); exit;
}
if ($_FILES['arquivo']['size'] > 15 * 1024 * 1024) {
    echo json_encode(['ok' => false, 'erro' => 'Arquivo muito grande (máx. 15 MB).']); exit;
}
$ext_permitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'zip', 'txt'];
$ext_upload = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
if (!in_array($ext_upload, $ext_permitidas, true)) {
    echo json_encode(['ok' => false, 'erro' => 'Tipo de arquivo não permitido. Use: PDF, Word, Excel, PowerPoint, imagem ou ZIP.']); exit;
}

// Pasta organizada por matrícula
$pasta = __DIR__ . '/../uploads/producoes/aluno/' . $matricula . '/';
if (!is_dir($pasta)) mkdir($pasta, 0755, true);

$nomeOriginal = basename($_FILES['arquivo']['name']);
$ext          = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
$codigo      = bin2hex(random_bytes(16));
$nomeArquivo = $codigo . ($ext ? '.' . $ext : '');
$caminhoRel   = 'uploads/producoes/aluno/' . $matricula . '/' . $nomeArquivo;

if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $pasta . $nomeArquivo)) {
    echo json_encode(['ok' => false, 'erro' => 'Falha ao salvar o arquivo.']); exit;
}

// Se existia documento 'refazer' para esta tarefa, marcar como inativo
$stmt = $pdo->prepare(
    "UPDATE producoes SET status = 'inativo'
     WHERE id_projeto = :id_projeto AND titulo = :titulo AND status = 'refazer'"
);
$stmt->execute([':id_projeto' => $id_projeto, ':titulo' => $titulo]);

// Salvar em producoes — titulo = nome da tarefa, tipo = nome original do arquivo
$stmt = $pdo->prepare(
    "INSERT INTO producoes (id_projeto, titulo, tipo, caminho, status)
     VALUES (:id_projeto, :titulo, :tipo, :caminho, 'pendente')
     RETURNING id_producao"
);
$stmt->execute([
    ':id_projeto' => $id_projeto,
    ':titulo'     => $titulo,
    ':tipo'       => $nomeOriginal,
    ':caminho'    => $caminhoRel,
]);
$id_producao = $stmt->fetchColumn();

echo json_encode([
    'ok'          => (bool)$id_producao,
    'id_producao' => $id_producao,
    'caminho'     => 'pages-aluno/servir-arquivo.php?id=' . $id_producao,
    'nome'        => $nomeOriginal,
]);
