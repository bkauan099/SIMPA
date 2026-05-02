<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

set_error_handler(function($errno, $errstr) {
    echo json_encode(['sucesso' => false, 'mensagem' => $errstr]);
    exit;
});

if (empty($_SESSION['id_usuario'])) {
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

require_once '../conexao/conexao.php';

$acao = $_GET['acao'] ?? '';
$id   = (int)$_SESSION['id_usuario'];

if ($acao === 'dados') {
    $stmt = $pdo->prepare("SELECT id_usuario, nome, email, matricula, CAST(perfil AS TEXT) AS perfil, curso FROM usuarios WHERE id_usuario = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: ['erro' => 'Não encontrado']);

} elseif ($acao === 'atualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $curso = trim($_POST['curso'] ?? '');
    if (empty($nome) || empty($email)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nome e e-mail são obrigatórios.']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, curso = :curso WHERE id_usuario = :id");
    $ok = $stmt->execute([':nome' => $nome, ':email' => $email, ':curso' => $curso, ':id' => $id]);
    if ($ok) {
        $_SESSION['nome']  = $nome;
        $_SESSION['email'] = $email;
    }
    echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Perfil atualizado com sucesso!' : 'Erro ao atualizar.']);

} elseif ($acao === 'senha' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha  = $_POST['nova_senha']  ?? '';
    $confirma   = $_POST['confirma']    ?? '';

    if (strlen($novaSenha) < 6) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A nova senha deve ter ao menos 6 caracteres.']);
        exit;
    }
    if ($novaSenha !== $confirma) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'As senhas não conferem.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id_usuario = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($senhaAtual, $row['senha'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Senha atual incorreta.']);
        exit;
    }

    $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
    $stmt2 = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id_usuario = :id");
    $ok = $stmt2->execute([':senha' => $hash, ':id' => $id]);
    echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Senha alterada com sucesso!' : 'Erro ao alterar senha.']);

} else {
    echo json_encode(['erro' => 'Ação inválida.']);
}
?>
