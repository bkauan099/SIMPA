<?php
session_start();
require_once '../../conexao/conexao.php';

$id_professor = $_SESSION['id_usuario'] ?? 0;
if (!$id_professor) { http_response_code(403); exit; }

$id_producao = (int)($_GET['id'] ?? 0);
if (!$id_producao) { http_response_code(400); exit; }

try {
    // Busca o arquivo garantindo que o professor tem acesso ao projeto
    $stmt = $pdo->prepare("
        SELECT p.caminho, p.tipo
        FROM producoes p
        WHERE p.id_producao = :id
          AND p.id_projeto IN (
              SELECT id_projeto FROM participacao
              WHERE id_usuario = :prof
                AND (funcao ILIKE '%professor%' OR funcao ILIKE '%coordenador%' OR funcao ILIKE '%orientador%')
          )
        LIMIT 1
    ");
    $stmt->execute([':id' => $id_producao, ':prof' => $id_professor]);
    $arquivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$arquivo) { http_response_code(403); exit; }

    $uploadsRoot = __DIR__ . '/../../';

    $baseDir  = realpath($uploadsRoot . 'uploads');
    $fullPath = realpath($uploadsRoot . $arquivo['caminho']);

    if (!$fullPath || !$baseDir || !str_starts_with($fullPath, $baseDir)) {
        http_response_code(403); exit;
    }
    if (!file_exists($fullPath)) { http_response_code(404); exit; }

    $ext  = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mimes = [
        'pdf'  => 'application/pdf',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'doc'  => 'application/msword',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'txt'  => 'text/plain; charset=utf-8',
    ];
    $mime = $mimes[$ext] ?? 'application/octet-stream';
    $extsInline = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'txt'];
    $disposition = in_array($ext, $extsInline) ? 'inline' : 'attachment';

    header('Content-Type: ' . $mime);
    header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($arquivo['tipo']) . '"');
    header('Content-Length: ' . filesize($fullPath));
    header('Cache-Control: private, max-age=3600');
    header('X-Content-Type-Options: nosniff');
    readfile($fullPath);
} catch (PDOException $e) {
    http_response_code(500); exit;
}
