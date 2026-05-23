<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? 3;
require_once __DIR__ . '/../conexao/conexao.php';

header('Content-Type: application/json');

$id_tarefa = (int)($_POST['id']     ?? 0);
$titulo    = trim($_POST['titulo']  ?? '');

if (!$id_tarefa || empty($titulo)) {
    echo json_encode(['ok' => false, 'erro' => 'Dados insuficientes.']); exit;
}

// Matricula do aluno
$stmt = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$stmt->execute([':id' => $id_usuario]);
$matricula = $stmt->fetchColumn();
if (!$matricula) { echo json_encode(['ok' => false, 'erro' => 'Matrícula não encontrada.']); exit; }

// Projeto ativo do aluno
$stmt = $pdo->prepare("SELECT id_projeto FROM participacao WHERE id_usuario = :id AND status = 'ativo' LIMIT 1");
$stmt->execute([':id' => $id_usuario]);
$id_projeto = $stmt->fetchColumn();
if (!$id_projeto) { echo json_encode(['ok' => false, 'erro' => 'Nenhum projeto ativo encontrado.']); exit; }

// Validação do arquivo
if (empty($_FILES['arquivo']['name']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'erro' => 'Nenhum arquivo enviado.']); exit;
}
if ($_FILES['arquivo']['size'] > 15 * 1024 * 1024) {
    echo json_encode(['ok' => false, 'erro' => 'Arquivo muito grande (máx. 15 MB).']); exit;
}

// Pasta organizada por matrícula
$pasta = __DIR__ . '/../uploads/alunos/' . $matricula . '/';
if (!is_dir($pasta)) mkdir($pasta, 0755, true);

$nomeOriginal = basename($_FILES['arquivo']['name']);
$ext          = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
$codigo      = bin2hex(random_bytes(16));
$nomeArquivo = $codigo . ($ext ? '.' . $ext : '');
$caminhoRel   = 'uploads/alunos/' . $matricula . '/' . $nomeArquivo;

if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $pasta . $nomeArquivo)) {
    echo json_encode(['ok' => false, 'erro' => 'Falha ao salvar o arquivo.']); exit;
}

// Remover envio anterior desta tarefa se existir
$stmt = $pdo->prepare(
    "SELECT id_producao, caminho FROM producoes
     WHERE titulo = :titulo AND id_projeto = :id_projeto AND status = 'pendente'
     ORDER BY id_producao DESC LIMIT 1"
);
$stmt->execute([':titulo' => $titulo, ':id_projeto' => $id_projeto]);
$anterior = $stmt->fetch(PDO::FETCH_ASSOC);
if ($anterior) {
    $arquivoAntigo = __DIR__ . '/../' . $anterior['caminho'];
    if (file_exists($arquivoAntigo)) unlink($arquivoAntigo);
    $pdo->prepare("DELETE FROM producoes WHERE id_producao = :id")
        ->execute([':id' => $anterior['id_producao']]);
}

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
    'caminho'     => $caminhoRel,
    'nome'        => $nomeOriginal,
]);
