<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) { http_response_code(403); exit; }

require_once __DIR__ . '/../conexao/conexao.php';

$id_certificado = $_GET['id'] ?? '';
if (!$id_certificado) { http_response_code(400); exit; }

$stmt = $pdo->prepare("
    SELECT caminho, nome_arquivo
    FROM certificados
    WHERE id_certificado = :id
      AND id_usuario = :uid
    LIMIT 1
");
$stmt->execute([':id' => $id_certificado, ':uid' => $id_usuario]);
$arquivo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arquivo) { http_response_code(403); exit; }

$baseDir  = realpath(__DIR__ . '/../uploads');
$fullPath = realpath(__DIR__ . '/../' . $arquivo['caminho']);

if (!$fullPath || !$baseDir || !str_starts_with($fullPath, $baseDir)) {
    http_response_code(403); exit;
}
if (!file_exists($fullPath)) { http_response_code(404); exit; }

$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

$mimes = [
    'pdf'  => 'application/pdf',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',
    'svg'  => 'image/svg+xml',
    'txt'  => 'text/plain; charset=utf-8',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'doc'  => 'application/msword',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'xls'  => 'application/vnd.ms-excel',
];
$mime = $mimes[$ext] ?? 'application/octet-stream';

$extsInline  = ['pdf','png','jpg','jpeg','gif','webp','svg','txt'];
$disposition = in_array($ext, $extsInline) ? 'inline' : 'attachment';

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($arquivo['nome_arquivo']) . '"');
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: private, max-age=3600');
header('X-Content-Type-Options: nosniff');
readfile($fullPath);
