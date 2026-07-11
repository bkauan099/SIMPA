<?php
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;
$perfil     = strtolower($_SESSION['perfil'] ?? '');
if (!$id_usuario || !str_contains($perfil, 'aluno')) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não autenticado']);
    exit;
}
require_once __DIR__ . '/../conexao/conexao.php';
header('Content-Type: application/json; charset=utf-8');

$acao = $_GET['acao'] ?? '';
$id   = (int)$id_usuario;

if ($acao === 'senha' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $at = $_POST['senha_atual'] ?? '';
    $nv = $_POST['nova_senha']  ?? '';
    $cf = $_POST['confirma']    ?? '';

    if (strlen($nv) < 6) { echo json_encode(['sucesso' => false, 'mensagem' => 'Mínimo 6 caracteres.']); exit; }
    if ($nv !== $cf)      { echo json_encode(['sucesso' => false, 'mensagem' => 'As senhas não conferem.']); exit; }

    $s = $pdo->prepare("SELECT senha FROM usuarios WHERE id_usuario = :id");
    $s->execute([':id' => $id]);
    $row = $s->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($at, $row['senha'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Senha atual incorreta.']);
        exit;
    }

    $h  = password_hash($nv, PASSWORD_DEFAULT);
    $s2 = $pdo->prepare("UPDATE usuarios SET senha = :s WHERE id_usuario = :id");
    $ok = $s2->execute([':s' => $h, ':id' => $id]);
    echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Senha alterada com sucesso!' : 'Erro ao alterar.']);
} else {
    echo json_encode(['erro' => 'Ação inválida.']);
}
?>
