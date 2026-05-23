<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) { http_response_code(403); exit; }

require_once __DIR__ . '/../conexao/conexao.php';

$id_producao = (int)($_GET['id'] ?? 0);
if (!$id_producao) { http_response_code(400); exit; }

// Garante que o arquivo pertence ao aluno (via projeto em que participa)
$stmt = $pdo->prepare("
    SELECT p.caminho, p.tipo
    FROM producoes p
    JOIN participacao pa ON pa.id_projeto = p.id_projeto
    WHERE p.id_producao = :id
      AND pa.id_usuario = :uid
      AND p.status != 'inativo'
    LIMIT 1
");
$stmt->execute([':id' => $id_producao, ':uid' => $id_usuario]);
$arquivo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arquivo) { http_response_code(403); exit; }

$baseDir  = realpath(__DIR__ . '/../uploads');
$fullPath = realpath(__DIR__ . '/../' . $arquivo['caminho']);

// Bloqueia path traversal
if (!$fullPath || !$baseDir || !str_starts_with($fullPath, $baseDir)) {
    http_response_code(403); exit;
}
if (!file_exists($fullPath)) { http_response_code(404); exit; }

$ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

$mimes = [
    'pdf'      => 'application/pdf',
    'png'      => 'image/png',
    'jpg'      => 'image/jpeg',
    'jpeg'     => 'image/jpeg',
    'gif'      => 'image/gif',
    'webp'     => 'image/webp',
    'svg'      => 'image/svg+xml',
    'txt'      => 'text/plain; charset=utf-8',
    'js'       => 'text/plain; charset=utf-8',
    'ts'       => 'text/plain; charset=utf-8',
    'php'      => 'text/plain; charset=utf-8',
    'py'       => 'text/plain; charset=utf-8',
    'java'     => 'text/plain; charset=utf-8',
    'c'        => 'text/plain; charset=utf-8',
    'cpp'      => 'text/plain; charset=utf-8',
    'cs'       => 'text/plain; charset=utf-8',
    'go'       => 'text/plain; charset=utf-8',
    'rs'       => 'text/plain; charset=utf-8',
    'rb'       => 'text/plain; charset=utf-8',
    'html'     => 'text/plain; charset=utf-8',
    'css'      => 'text/plain; charset=utf-8',
    'json'     => 'text/plain; charset=utf-8',
    'xml'      => 'text/plain; charset=utf-8',
    'yaml'     => 'text/plain; charset=utf-8',
    'yml'      => 'text/plain; charset=utf-8',
    'md'       => 'text/plain; charset=utf-8',
    'csv'      => 'text/plain; charset=utf-8',
    'sql'      => 'text/plain; charset=utf-8',
    'sh'       => 'text/plain; charset=utf-8',
    'bat'      => 'text/plain; charset=utf-8',
    'log'      => 'text/plain; charset=utf-8',
    'zip'      => 'application/zip',
    'rar'      => 'application/octet-stream',
    'docx'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'doc'      => 'application/msword',
    'xlsx'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'xls'      => 'application/vnd.ms-excel',
];
$mime = $mimes[$ext] ?? 'application/octet-stream';

$extsInline = ['pdf','png','jpg','jpeg','gif','webp','svg',
               'txt','js','ts','php','py','java','c','cpp','cs','go','rs','rb',
               'html','css','json','xml','yaml','yml','md','csv','sql','sh','bat','log'];
$disposition = in_array($ext, $extsInline) ? 'inline' : 'attachment';

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($arquivo['tipo']) . '"');
header('Content-Length: ' . filesize($fullPath));
header('Cache-Control: private, max-age=3600');
header('X-Content-Type-Options: nosniff');
readfile($fullPath);
