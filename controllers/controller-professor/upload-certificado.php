<?php
session_start();
require_once '../../conexao/conexao.php';
header('Content-Type: application/json');

$id_professor = $_SESSION['id_usuario'] ?? null;
if (!$id_professor) { echo json_encode(['sucesso'=>false,'mensagem'=>'Sessão expirada.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['sucesso'=>false,'mensagem'=>'Método inválido.']); exit; }

$id_projeto = (int)($_POST['id_projeto'] ?? 0);
$id_aluno   = (int)($_POST['id_aluno']   ?? 0);
$titulo     = trim($_POST['titulo']       ?? '');

if (!$id_projeto || !$id_aluno || empty($titulo)) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Preencha todos os campos obrigatórios.']); exit;
}
if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Arquivo inválido ou não enviado.']); exit;
}

// Verifica se o professor coordena o projeto
$stmtCheck = $pdo->prepare("
    SELECT 1 FROM participacao
    WHERE id_usuario = :prof AND id_projeto = :proj
      AND (funcao ILIKE '%professor%' OR funcao ILIKE '%coordenador%' OR funcao ILIKE '%orientador%')
    LIMIT 1
");
$stmtCheck->execute([':prof' => $id_professor, ':proj' => $id_projeto]);
if (!$stmtCheck->fetchColumn()) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Sem permissão neste projeto.']); exit;
}

// Obtém a matrícula do aluno
$stmtMat = $pdo->prepare("SELECT matricula FROM usuarios WHERE id_usuario = :id");
$stmtMat->execute([':id' => $id_aluno]);
$matriculaAluno = $stmtMat->fetchColumn();
if (!$matriculaAluno) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Aluno não encontrado.']); exit;
}

$arquivo  = $_FILES['arquivo'];
$ext      = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
$extsOk   = ['pdf','doc','docx','jpg','jpeg','png'];
if (!in_array($ext, $extsOk)) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Tipo de arquivo não permitido.']); exit;
}
if ($arquivo['size'] > 10 * 1024 * 1024) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Arquivo muito grande (máx. 10 MB).']); exit;
}

// Salva na pasta da matrícula do aluno (minúsculas — padrão do projeto)
$pasta = __DIR__ . '/../../uploads/certificados/aluno/' . $matriculaAluno . '/';
if (!is_dir($pasta)) mkdir($pasta, 0755, true);

$nomeServidor = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
$caminhoFinal = $pasta . $nomeServidor;

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
    echo json_encode(['sucesso'=>false,'mensagem'=>'Falha ao salvar arquivo.']); exit;
}

$caminhoBanco = 'uploads/certificados/aluno/' . $matriculaAluno . '/' . $nomeServidor;

try {
    $stmt = $pdo->prepare("
        INSERT INTO producoes (id_projeto, titulo, tipo, caminho, status, data_registro)
        VALUES (:id_projeto, :titulo, :tipo, :caminho, 'ativo', NOW())
    ");
    $stmt->execute([
        ':id_projeto' => $id_projeto,
        ':titulo'     => $titulo,
        ':tipo'       => $arquivo['name'],
        ':caminho'    => $caminhoBanco,
    ]);
    echo json_encode(['sucesso'=>true]);
} catch (PDOException $e) {
    if (file_exists($caminhoFinal)) unlink($caminhoFinal);
    echo json_encode(['sucesso'=>false,'mensagem'=>'Erro no banco: ' . $e->getMessage()]);
}
